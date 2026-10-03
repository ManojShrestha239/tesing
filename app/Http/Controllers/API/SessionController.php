<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AlertReminder;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\Schedulevisit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\SchedulePurpose;
use App\Models\Dealer;
use App\Models\Mobileuser;
use App\Models\Purpose;
use App\Models\ReminderPurpose;
use App\Models\UnlistedDealer;
use App\Models\VisitPurpose;
use App\Traits\FCM;
use App\Traits\Geocodable;

class SessionController extends Controller
{
    use Geocodable, FCM;

    public function store(Request $request)
    {
        // 1. Authenticated user check
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.'], 401);
        }

        // 2. Attendance status validation
        $attendanceStatus = $user->latestAttendance?->status ?? null;
        $allowedStatuses = ['checkin', 'breakend'];
        if (!in_array(strtolower($attendanceStatus), $allowedStatuses)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must check-in/break-end before placing an order.'
            ], 403);
        }

        // 3. Input validation (enhanced)
        $rules = [
            '*.dealer_code' => 'required|exists:dealers,code',
            '*.user_id' => 'required|exists:mobileusers,id',
            '*.purpose_id' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $ids = explode(' :: ', $value);
                    foreach ($ids as $id) {
                        if (!ctype_digit($id)) {
                            $fail("The purpose_id field must contain numeric IDs separated by ' :: '.");
                            return;
                        }
                    }
                },
            ],
            '*.start_time' => 'required|date_format:H:i:s',
            '*.end_time' => 'required|date_format:H:i:s',
            '*.date' => 'required|date_format:Y-m-d',
            '*.status' => 'required|in:offline,online', // Adjust allowed statuses as needed
            '*.remarks' => 'required|string|max:500',
            '*.latitude' => 'nullable|numeric|between:-90,90',
            '*.longitude' => 'nullable|numeric|between:-180,180',
             '*.is_physical_visit' => 'nullable|boolean',
            '*.physical_visit' => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $inputs = $request->all();

        // 4. Fetch dealers in bulk
        $dealerCodes = collect($inputs)->pluck('dealer_code')->unique();
        $dealers = Dealer::whereIn('code', $dealerCodes)
            ->where('active_status', 'Active')
            ->get()->keyBy('code');

        // 5. Gather all purpose IDs and validate existence
        $allPurposeIds = [];
        foreach ($inputs as $req) {
            $ids = explode(' :: ', $req['purpose_id']);
            $allPurposeIds = array_merge($allPurposeIds, $ids);
        }

        $validPurposeIds = Purpose::whereIn('id', array_unique($allPurposeIds))
            ->pluck('id')
            ->map(fn($id) => (string) $id) // Ensure string keys for array access
            ->flip();

        // 6. Group inputs by (user_id, dealer_code, date) for efficient schedule fetching
        $combinations = [];
        $groupedInputs = collect($inputs)->groupBy(function ($item) {
            return $item['user_id'] . '|' . $item['dealer_code'] . '|' . $item['date'];
        });

        // Prepare for schedule queries
        $scheduleMap = [];
        if ($groupedInputs->isNotEmpty()) {
            // Build query for all relevant schedules
            $scheduleQuery = Schedulevisit::where('is_visit', 0);
            $scheduleQuery->where(function ($query) use ($groupedInputs) {
                foreach ($groupedInputs as $key => $items) {
                    [$userId, $dealerCode, $date] = explode('|', $key);
                    $query->orWhere(function ($q) use ($userId, $dealerCode, $date) {
                        $q->where('user_id', $userId)
                            ->where('dealer_code', $dealerCode)
                            ->where('date', $date);
                    });
                }
            });

            $schedules = $scheduleQuery->get();

            // Map schedules by composite key for quick lookup
            foreach ($schedules as $schedule) {
                $key = $schedule->user_id . '|' . $schedule->dealer_code . '|' . $schedule->date;
                $scheduleMap[$key][] = $schedule;
            }
        }

        // 7. Begin transaction
        DB::beginTransaction();

        try {
            $createdSessions = [];
            $visitPurposesBatch = [];
            $now = now();

            foreach ($inputs as $req) {
                // Skip if no valid purpose exists in this entry
                $purposeIds = explode(' :: ', $req['purpose_id']);
                $hasValidPurpose = false;
                foreach ($purposeIds as $pid) {
                    if ($validPurposeIds->has($pid)) {
                        $hasValidPurpose = true;
                        break;
                    }
                }
                if (!$hasValidPurpose) {
                    continue;
                }

                $dealer = $dealers->get($req['dealer_code']);
                if (!$dealer) {
                    continue; // Skip if dealer not found (shouldn't happen due to validation)
                }

                // Get schedules for this combination
                $compositeKey = $req['user_id'] . '|' . $req['dealer_code'] . '|' . $req['date'];
                $schedules = $scheduleMap[$compositeKey] ?? [];

                // Update schedule purposes and track which schedules become fully visited
                $scheduleIdsToUpdate = [];
                if (!empty($schedules)) {
                    $scheduleIds = collect($schedules)->pluck('id')->all();

                    // Batch update SchedulePurpose for all related schedules
                    SchedulePurpose::whereIn('visitschedule_id', $scheduleIds)
                        ->whereIn('purpose_id', $purposeIds)
                        ->where('status', 'pending')
                        ->update(['status' => 'visit']);

                    // Fetch pending counts for all affected schedules
                    $pendingCounts = SchedulePurpose::whereIn('visitschedule_id', $scheduleIds)
                        ->where('status', 'pending')
                        ->select('visitschedule_id', DB::raw('count(*) as count'))
                        ->groupBy('visitschedule_id')
                        ->pluck('count', 'visitschedule_id')
                        ->toArray();

                    // Identify schedules with no pending purposes left
                    foreach ($schedules as $schedule) {
                        if (($pendingCounts[$schedule->id] ?? 0) == 0) {
                            $scheduleIdsToUpdate[] = $schedule->id;
                        }
                    }

                    // Batch update schedules
                    if (!empty($scheduleIdsToUpdate)) {
                        Schedulevisit::whereIn('id', $scheduleIdsToUpdate)->update(['is_visit' => 1]);
                    }
                }

                // Create session
                $location = null;

                $session = Session::create([
                    'dealer_code' => $req['dealer_code'],
                    'user_id' => $req['user_id'],
                    'areacode' => $dealer->area_code,
                    'purpose_id' => $req['purpose_id'],
                    'start_time' => $req['start_time'],
                    'end_time' => $req['end_time'],
                    'date' => $req['date'],
                    'status' => $req['status'],
                    'remarks' => $req['remarks'],
                    'is_physical_visit'  => $req['is_physical_visit'] ?? null,
                    'lat_lng' => !empty($req['latitude']) && !empty($req['longitude'])
                        ? $req['latitude'] . ',' . $req['longitude']
                        : null,
                    'location' => $location,
                ]);

                $createdSessions[] = $session;

                // Prepare VisitPurpose batch insert
                foreach ($purposeIds as $purposeId) {
                    $visitPurposesBatch[] = [
                        'user_id' => $session->user_id,
                        'visit_id' => $session->id,
                        'purpose_id' => $purposeId,
                        'date' => $req['date'],
                        'status' => $req['status'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // Batch insert all VisitPurpose records
            if (!empty($visitPurposesBatch)) {
                VisitPurpose::insert($visitPurposesBatch);
            }


            // Send one FCM per user after commit — aggregated across all their sessions
            try {
                $sessionsByUser = collect($createdSessions)->groupBy('user_id');

                foreach ($sessionsByUser as $userId => $userSessions) {
                    $user = Mobileuser::find($userId);
                    if (! $user) {
                        continue;
                    }

                    // Unique dealer names for this user's batch
                    $dealerNames = $userSessions
                        ->map(fn($s) => $dealers->get($s->dealer_code)?->name)
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    $status = $userSessions->first()->status;
                    $time   = $userSessions->first()->start_time;

                    $this->sendFCMSenior($user, $status, $time, $dealerNames, count($dealerNames));
                }
            } catch (\Throwable $e) {
                // FCM failure must not roll back the DB write
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Sessions created successfully.',
                'data' => $createdSessions, // Optional: return created sessions
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request. Please try again later.'
            ], 500);
        }
    }

    public function v5negotiation(Request $request)
    {
        // 1. Authentication
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.'], 401);
        }

        // 2. Attendance check
        $attendanceStatus = $user->latestAttendance?->status ?? null;
        $allowedStatuses = ['checkin', 'breakend'];
        if (!in_array(strtolower($attendanceStatus), $allowedStatuses)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must check-in/break-end before placing an order.'
            ], 403);
        }

        // 3. Enhanced validation
        $rules = [
            'unlisted_dealer_id' => 'nullable|required_without:dealer_name|exists:unlisted_dealers,id',
            'dealer_name'        => 'nullable|required_without:unlisted_dealer_id|string|max:191',
            'address'            => 'nullable|required_without:unlisted_dealer_id|string|max:191',
            'contact'            => 'nullable|required_without:unlisted_dealer_id|string|max:20',     // Added max length
            'area_code'          => 'nullable|required_without:unlisted_dealer_id|string|max:20',     // Added max length
            'subarea'            => 'nullable|required_without:unlisted_dealer_id',     // Added max length
            'user_id'            => 'required|exists:mobileusers,id',
            'purpose_id'         => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $ids = explode(' :: ', $value);
                    foreach ($ids as $id) {
                        if (!ctype_digit($id)) {
                            $fail("The purpose_id field must contain numeric IDs separated by ' :: '.");
                            return;
                        }
                    }
                },
            ],
            'date'             => 'nullable|date_format:Y-m-d',
            'start_time'       => 'nullable|date_format:H:i',
            'end_time'         => 'nullable|date_format:H:i|after:start_time',
            'remarks'          => 'required|string|max:255',
            'latitude'         => 'nullable|nullable|required_without:unlisted_dealer_id|numeric|between:-90,90',   // Standard lat range
            'longitude'        => 'nullable|nullable|required_without:unlisted_dealer_id|numeric|between:-180,180', // Standard lng range
            'next_visit_date'  => 'nullable|date_format:Y-m-d',
            'contact_person'   => 'nullable|max:191',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // 5. Extract purpose IDs and validate existence
        $purposeIds = explode(' :: ', $validated['purpose_id']);
        $existingPurposes = Purpose::whereIn('id', $purposeIds)->pluck('id')->toArray();
        $invalidPurposes = array_diff($purposeIds, $existingPurposes);
        if (!empty($invalidPurposes)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid purpose_id(s): ' . implode(', ', $invalidPurposes)
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 6. Create the session (manual assignment for clarity)
            if (!empty($validated['unlisted_dealer_id'])) {
                $unlistedDealer = UnlistedDealer::findOrFail($validated['unlisted_dealer_id']);
            } else {
                $unlistedDealer = UnlistedDealer::firstOrCreate(
                    ['contact' => $validated['contact']],
                    [
                        'name'    => $validated['dealer_name'],
                        'address' => $validated['address'],
                        'area_code' => $validated['area_code'],
                        'subarea' => $validated['subarea'],
                        'lat'     => $validated['latitude']  ?? null,
                        'lng'     => $validated['longitude'] ?? null,
                    ]
                );
            }

            $session = new Session();
            $session->user_id          = $user->id;   // Always use authenticated user
            $session->dealer_name      = $unlistedDealer->name;
            $session->dealer_address   = $unlistedDealer->address;
            $session->contact          = $unlistedDealer->contact;
            $session->purpose_id       = $validated['purpose_id']; // Keep concatenated string
            $session->start_time       = $validated['start_time'] ?? null;
            $session->end_time         = $validated['end_time'] ?? null;
            $session->date             = $validated['date'] ?? null;
            $session->status           = 'Unlisted';
            $session->next_visit_date  = $validated['next_visit_date'] ?? null;
            $session->contact_person   = $validated['contact_person'] ?? null;
            $session->remarks          = $validated['remarks'];
            $session->latitude         = $unlistedDealer->latitude;
            $session->longitude        = $unlistedDealer->longitude;
            $session->save();

            // 7. Create alert and batch-insert reminder purposes if next_visit_date provided
            if (!empty($validated['next_visit_date'])) {
                $alert = AlertReminder::create([
                    'dealer_name'    => $unlistedDealer->name,
                    'dealer_contact' => $unlistedDealer->contact,
                    'dealer_address' => $unlistedDealer->address,
                    'date'           => $validated['next_visit_date'],
                    'remarks'        => $validated['remarks'],
                    'status'         => 'Pending',
                    'flag'           => 'Alert',
                    'mobileuser_id'  => $user->id,
                ]);

                // Batch insert reminder purposes
                $now = now();
                $reminderPurposes = [];
                foreach ($purposeIds as $purposeId) {
                    $reminderPurposes[] = [
                        'purpose_id'        => $purposeId,
                        'alertreminder_id'  => $alert->id,
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ];
                }
                ReminderPurpose::insert($reminderPurposes);
            }

            // 8. Batch insert visit purposes
            $now = now();
            $visitPurposes = [];
            foreach ($purposeIds as $purposeId) {
                $visitPurposes[] = [
                    'user_id'    => $session->user_id,
                    'visit_id'   => $session->id,
                    'purpose_id' => $purposeId,
                    'date'       => $session->date,
                    'status'     => 'Unlisted',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            VisitPurpose::insert($visitPurposes);

            DB::commit();

            // Send FCM to senior — separate function for unlisted visits
            try {
                $this->sendFCMUnlistedSenior(
                    $user,
                    $unlistedDealer->name,
                    $session->start_time
                );
            } catch (\Throwable $e) {
            }

            // $data = $session;
            $data['unlisted_dealer'] = $unlistedDealer;

            return response()->json([
                'status'    => 'success',
                'message'   => 'Session created successfully.',
                'data'      => $data
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while creating the session. Please try again later.',
                'error_details' => $e->getMessage(),
            ], 500);
        }
    }

    private function sendFCMSenior(
        Mobileuser $user,
        string $status,
        ?string $time = null,
        array $dealerNames = [],
        int $dealerCount = 0
    ): void {
        $user->loadMissing(['teamLeaders:id,name,fcm_id']);

        $tokens = $user->teamLeaders
            ->pluck('fcm_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($tokens)) {
            return;
        }

        $dateTime = $time ? date('g:i A', strtotime($time)) : date('g:i A');

        // ---- Build dealer list string ----
        $count = count($dealerNames);

        if ($count === 0) {
            $dealerList = 'no dealers';
        } elseif ($count === 1) {
            $dealerList = $dealerNames[0];
        } elseif ($count === 2) {
            $dealerList = $dealerNames[0] . ' and ' . $dealerNames[1];
        } else {
            $others     = $count - 2;
            $dealerList = $dealerNames[0] . ', ' . $dealerNames[1]
                . ', and ' . $others . ' other' . ($others > 1 ? 's' : '');
        }

        $isOnline  = strtolower($status) === 'online';
        $visitType = $isOnline ? 'onsite' : 'offsite';

        $title = 'Team Attendance Update';
        $body  = "{$user->name} conducted an {$visitType} visit to {$dealerList} at {$dateTime}.";

        $data = [
            'route_channel' => 'my-team',
            'user_id'       => $user->id,
        ];

        $report = $this->sendMulticast($tokens, $title, $body, $data);

        // Remove dead tokens
        if (! empty($report['invalid_tokens'])) {
            Mobileuser::whereIn('fcm_id', $report['invalid_tokens'])
                ->update(['fcm_id' => null]);
        }
    }

    private function sendFCMUnlistedSenior(
        Mobileuser $user,
        string $dealerName,
        ?string $time = null
    ): void {
        $user->loadMissing(['teamLeaders:id,name,fcm_id']);

        $tokens = $user->teamLeaders
            ->pluck('fcm_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($tokens)) {
            return;
        }

        $dateTime = $time ? date('g:i A', strtotime($time)) : date('g:i A');

        $title = 'Team Visit Update';
        $body  = "{$user->name} conducted an unlisted visit to {$dealerName} at {$dateTime}.";

        $data = [
            'route_channel' => 'my-team',
            'user_id'       => $user->id,
            'visit_type'    => 'unlisted',
        ];

        $report = $this->sendMulticast($tokens, $title, $body, $data);

        // Remove dead tokens
        if (! empty($report['invalid_tokens'])) {
            Mobileuser::whereIn('fcm_id', $report['invalid_tokens'])
                ->update(['fcm_id' => null]);
        }
    }
}
