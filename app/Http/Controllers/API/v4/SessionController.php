<?php

namespace App\Http\Controllers\API\v4;

use App\Http\Controllers\Controller;
use App\Models\AlertReminder;
use Illuminate\Http\Request;
use App\Models\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;
use App\Models\Mobileuser;
use App\Models\Purpose;
use App\Models\ReminderPurpose;
use App\Models\VisitPurpose;

class SessionController extends Controller
{
    public function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.address' => 'required|string|max:191',
            'data.*.dealer_name' => 'required|string|max:191',
            'data.*.user_id' => 'required|exists:mobileusers,id',
            'data.*.contact' => 'required',
            'data.*.purpose_id' => 'required',
            'data.*.date' => 'date|date_format:Y-m-d',
            'data.*.start_time' => 'date_format:H:i',
            'data.*.end_time' => 'date_format:H:i|after:data.*.start_time',
            'data.*.remarks' => 'required|string|max:255',
            'data.*.latitude' => 'required|numeric|between:0,360',
            'data.*.longitude' => 'required|numeric|between:0,360',
        ]);
        if ($valid->fails()) {
            return response()->json(
                ['status' => 'error', 'message' => $valid->errors()],
                422
            );
        }
        foreach ($request->data as $unlisted) {
            $purpose_id = explode(' :: ', $unlisted['purpose_id']);
            if (Purpose::whereIn('id', $purpose_id)->count() == 0) {
                continue;
            }
            $negotiation = new Session();

            $negotiation->user_id = Auth::user()->id;
            $negotiation->dealer_name = $unlisted['dealer_name'];
            $negotiation->dealer_address = $unlisted['address'];
            $negotiation->contact = $unlisted['contact'];
            $negotiation->purpose_id = $unlisted['purpose_id'];
            $negotiation->start_time = $unlisted['start_time'];
            $negotiation->end_time = $unlisted['end_time'];
            $negotiation->date = $unlisted['date'];
            $negotiation->remarks = $unlisted['remarks'];
            $negotiation->status = 'Unlisted';
            $negotiation->latitude = $unlisted['latitude'];
            $negotiation->longitude = $unlisted['longitude'];

            $negotiation->save();
            $purposes = explode(' :: ', $unlisted['purpose_id']);
            foreach ($purposes as $purpose) {
                $visitpurpose = new VisitPurpose();

                $visitpurpose->user_id = $negotiation->user_id;
                $visitpurpose->visit_id = $negotiation->id;
                $visitpurpose->purpose_id = $purpose;
                $visitpurpose->date = $unlisted['date'];
                $visitpurpose->status = 'Unlisted';

                $visitpurpose->save();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Unlisted Visits have been added',
        ]);
    }

    public function new(Request $request)
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
            'address'          => 'required|string|max:191',
            'dealer_name'      => 'required|string|max:191',
            'user_id'          => 'required|exists:mobileusers,id',
            'contact'          => 'required|string|max:20',       // Added max length
            'purpose_id'       => [
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
            'latitude'         => 'required|numeric|between:-90,90',   // Standard lat range
            'longitude'        => 'required|numeric|between:-180,180', // Standard lng range
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
            $session = new Session();
            $session->user_id          = $user->id;   // Always use authenticated user
            $session->dealer_name      = $validated['dealer_name'];
            $session->dealer_address   = $validated['address'];
            $session->contact          = $validated['contact'];
            $session->purpose_id       = $validated['purpose_id']; // Keep concatenated string
            $session->start_time       = $validated['start_time'] ?? null;
            $session->end_time         = $validated['end_time'] ?? null;
            $session->date             = $validated['date'] ?? null;
            $session->status           = 'Unlisted';
            $session->next_visit_date  = $validated['next_visit_date'] ?? null;
            $session->contact_person   = $validated['contact_person'] ?? null;
            $session->remarks          = $validated['remarks'];
            $session->latitude         = $validated['latitude'];
            $session->longitude        = $validated['longitude'];
            $session->save();

            // 7. Create alert and batch-insert reminder purposes if next_visit_date provided
            if (!empty($validated['next_visit_date'])) {
                $alert = AlertReminder::create([
                    'dealer_name'    => $validated['dealer_name'],
                    'dealer_contact' => $validated['contact'],
                    'dealer_address' => $validated['address'],
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
                    $session->dealer_name,
                    $session->start_time
                );
            } catch (\Throwable $e) {
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Session created successfully.',
                'data'    => $session,
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

    public function loctaion(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'time' => 'required|date_format:H:i:s',
            'date' => 'required|date|date_format:Y-m-d',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'status' => "error",
                'message' => $valid->errors()
            ], 422);
        }

        $user = Auth::user();
        $location = Location::where(['day' => $request->date, 'user_id' => $user->id])->first();

        if (!$location) {
            $cordinates[] = array(
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'time' => $request->time
            );

            $location = new Location();

            $location->user_id = $user->id;
            $location->day = $request->date;
            $location->coordinates = $cordinates;
            $location->user_id = $user->id;
            $location->role_id = $user->role_id;

            $location->save();
        } else {
            $cordinates[] = array(
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'time' => $request->time
            );
            $location->coordinates = array_merge($location->coordinates, $cordinates);

            $location->save();
        }
        return response()->json([
            'status' => 'success',
            'data' => $location
        ]);
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
