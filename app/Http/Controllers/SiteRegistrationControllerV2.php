<?php

namespace App\Http\Controllers;

use App\Exports\SiteRegistrationV2Export;
use App\Models\ConstructionStage;
use App\Models\Contractor;
use App\Models\Dealer;
use App\Models\Mobileuser;
use App\Models\SiteActivityLog;
use App\Models\SiteContact;
use App\Models\SiteFollowUp;
use App\Models\SiteNote;
use App\Models\SitePhotoV2;
use App\Models\SiteRegistrationV2;
use App\Models\SiteStageLog;
use App\Models\SiteProduct;
use App\Models\SiteType;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class SiteRegistrationControllerV2 extends Controller
{
    use CustomNepaliCalendarConverter;

    protected $todayNepaliDate;

    public function __construct()
    {
        $this->todayNepaliDate = $this->todayNepaliDate();
    }

    public function index(Request $request)
    {
        $request->validate([
            'stage_id'   => 'nullable|exists:v2_site_construction_stages,id',
            'type_id'    => 'nullable|exists:v2_site_types,id',
            'status'     => 'nullable|in:new_registration,active,inactive,lost,completed,cancelled',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable',
            'search'     => 'nullable|string|max:255',
        ]);

        try {
            $sites = SiteRegistrationV2::query()
                ->with([
                    'dealer:code,name',
                    'contractor:id,name',
                    'siteType:id,name',
                    'currentStage:id,name',
                    'photos:id,site_id,file_path',
                    'products:id,name,types',
                ]);

            if ($request->filled('stage_id')) {
                $sites->where('current_stage_id', $request->get('stage_id'));
            }

            if ($request->filled('type_id')) {
                $sites->where('site_type_id', $request->get('type_id'));
            }

            if ($request->filled('status')) {
                $sites->where('status', $request->get('status'));
            }

            if ($request->filled('start_date')) {
                $sites->whereDate(
                    'created_at',
                    '>=',
                    $this->custom_BS_to_AD($request->get('start_date'))
                );
            }

            if ($request->filled('end_date')) {
                $sites->whereDate(
                    'created_at',
                    '<=',
                    $this->custom_BS_to_AD($request->get('end_date'))
                );
            }

            if ($request->filled('search')) {
                $term = addcslashes($request->get('search'), '%_\\');

                $sites->where(function ($q) use ($term) {
                    $q->where('site_registration_no', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('next_followup_at', 'like', "%{$term}%")
                        ->orWhereHas('dealer', function ($d) use ($term) {
                            $d->where('name', 'like', "%{$term}%")
                                ->orWhere('code', 'like', "%{$term}%");
                        });
                });
            }

            $paginatedSites = $sites
                ->orderByDesc('created_at')
                ->paginate(15)
                ->onEachSide(1)
                ->withQueryString();

            if ($request->wantsJson()) {
                return response()->json([
                    'data'  => $paginatedSites->items(),
                    'links' => [
                        'first' => $paginatedSites->url(1),
                        'last'  => $paginatedSites->url($paginatedSites->lastPage()),
                        'prev'  => $paginatedSites->previousPageUrl(),
                        'next'  => $paginatedSites->nextPageUrl(),
                    ],
                    'meta' => [
                        'current_page' => $paginatedSites->currentPage(),
                        'from'         => $paginatedSites->firstItem(),
                        'to'           => $paginatedSites->lastItem(),
                        'total'        => $paginatedSites->total(),
                        'per_page'     => $paginatedSites->perPage(),
                        'last_page'    => $paginatedSites->lastPage(),
                    ],
                ]);
            }

            $data['sites']     = $paginatedSites;
            $data['siteTypes'] = SiteType::select('id', 'name')->orderBy('sort_order')->get();
            $data['stages']    = ConstructionStage::select('id', 'name')->orderBy('sort_order')->get();

            return view('dashboards.new-site.sites', $data);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => 'Server error occurred',
                    'message' => $e->getMessage(),
                ], 500);
            }
            throw $e;
        }
    }

    public function export()
    {
        return Excel::download(new SiteRegistrationV2Export, 'site_registrations_' . time() . '.xlsx');
    }

    public function create()
    {
        return view('dashboards.new-site.create-site', $this->baseFormData());
    }

    private function baseFormData(?SiteRegistrationV2 $site = null): array
    {
        $data['siteTypes'] = SiteType::select('id', 'name')
            ->orderBy('sort_order', 'asc')
            ->get();
        $data['stages'] = ConstructionStage::select('id', 'name')
            ->orderBy('sort_order', 'asc')
            ->get();
        $data['dealers'] = Dealer::select('code', 'name')
            ->where('active_status', 'Active')
            ->orderBy('name')
            ->get();
        $data['contractors'] = Contractor::select('id', 'name')
            ->orderBy('name')
            ->get();
        $data['products'] = SiteProduct::select('id', 'name', 'types')
            ->orderBy('name')
            ->get();
        $data['mobileusers'] = Mobileuser::select('id', 'name')
            ->where('status', 'Active')
            ->orderBy('name')->get();

        if ($site) {
            $data['site'] = $site->loadMissing([
                'contacts' => function ($query) {
                    $query->orderBy('id');
                },
                'followUps' => function ($query) {
                    $query->latest();
                },
                'products',
                'photos' => function ($query) {
                    $query->latest();
                },
                'notes' => function ($query) {
                    $query->latest();
                },
            ]);
        }

        return $data;
    }

    private function validateSiteRequest(Request $request, ?SiteRegistrationV2 $site = null): array
    {
        $rules = [
            'site_name' => 'required|string|max:120',
            'site_type_id' => 'required|integer|exists:v2_site_types,id',
            'address' => 'nullable|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'dealer_code' => 'required|string|max:60|exists:dealers,code',
            'contractor_id' => 'nullable|integer|exists:contractors,id',
            'stakeholders' => 'nullable|array',
            'construction_status_id' => 'required|integer|exists:v2_site_construction_stages,id',
            'number_of_floors' => 'nullable|numeric',
            'approximate_area_sqft' => 'nullable|numeric',
            'preferred_brand' => 'nullable|string|max:80',
            'competitor_brands' => 'nullable|string|max:80',
            'estimated_paint_litres' => 'nullable|integer',
            'products' => 'nullable|array',
            'products.*.id' => 'required_with:products|integer|exists:v2_site_products,id',
            'products.*.quantity' => 'required_with:products|numeric|min:1',
            'next_followup_at' => 'nullable',
            'assign_to_user_id' => 'nullable|integer|exists:mobileusers,id',
            'follow_up_type' => 'nullable|string|in:call,visit,demo,sampling,others',
            'follow_up_status' => 'nullable|string|max:255',
            'follow_up_notes' => 'nullable|string',
            'auto_reminder_enabled' => 'nullable|boolean',
            'site_photos' => $site ? 'nullable|array|max:5' : 'nullable|array|min:1|max:5',
            'site_photos.*' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048',
            'progress_photos' => 'nullable|array|max:5',
            'progress_photos.*' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048',
            'additional_notes' => 'nullable|string',
        ];

        foreach ((array) $request->input('stakeholders', []) as $index => $stakeholder) {
            $uniqueRule = Rule::unique('v2_site_contacts', 'phone')->whereNull('deleted_at');

            if ($site && !empty($stakeholder['id'])) {
                $uniqueRule->ignore($stakeholder['id']);
            }

            $rules["stakeholders.$index.name"] = 'required_with:stakeholders|string|max:100';
            $rules["stakeholders.$index.role"] = 'required_with:stakeholders|string|max:60';
            $rules["stakeholders.$index.contact_number"] = [
                'required_with:stakeholders',
                'string',
                'digits:10',
                'regex:/^(98|97)\d{8}$/',
                $uniqueRule,
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $next = $request->input('next_followup_at');
            if ($next && !$this->isFutureNepaliDate($next)) {
                $validator->errors()->add('next_followup_at', 'The next follow-up date must be in the future.');
            }
        });

        return $validator->validate();
    }

    public function store(Request $request)
    {
        $validated = $this->validateSiteRequest($request);

        $authId = auth('web')->id();

        return DB::transaction(function () use ($validated, $request, $authId) {
            $site = SiteRegistrationV2::create([
                'name' => $validated['site_name'],
                'dealer_code' => $validated['dealer_code'],
                'date' => $this->todayNepaliDate(),
                'contractor_id' => $validated['contractor_id'] ?? null,
                'web_created_by_id' => $authId,
                'site_type_id' => $validated['site_type_id'],
                'current_stage_id' => $validated['construction_status_id'],
                'address' => $validated['address'] ?? null,
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'floors' => $validated['number_of_floors'] ?? null,
                'approx_area_sqft' => $validated['approximate_area_sqft'] ?? null,
                'preferred_brand' => $validated['preferred_brand'] ?? null,
                'competitor_brand_observed' => $validated['competitor_brands'] ?? null,
                'estimated_paint_litres' => $validated['estimated_paint_litres'] ?? null,
                'status' => 'new_registration',
                'next_followup_at' => $validated['next_followup_at'] ?? null,
            ]);

            if (!empty($validated['stakeholders'])) {
                $stakeholderRows = collect($validated['stakeholders'])->map(fn($s) => [
                    'site_id' => $site->id,
                    'name' => $s['name'],
                    'role' => $s['role'],
                    'phone' => $s['contact_number'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all();

                if (!empty($stakeholderRows)) {
                    SiteContact::insert($stakeholderRows);
                }
            }

            if (!empty($validated['next_followup_at'])) {
                SiteFollowUp::create([
                    'site_id' => $site->id,
                    'follow_up_date' => $validated['next_followup_at'],
                    'follow_up_type' => $validated['follow_up_type'] ?? null,
                    'status' => 'pending',
                    'notes' => $validated['follow_up_notes'] ?? null,
                    'auto_reminder_enabled' => $validated['auto_reminder_enabled'] ?? true,
                    'assigned_to_user_id' => $validated['assign_to_user_id'] ?? 1,
                    'web_changed_by_id' => $authId,
                ]);
            }

            if (!empty($validated['products'])) {
                $pivotData = collect($validated['products'])->mapWithKeys(fn($p) => [
                    $p['id'] => ['quantity' => $p['quantity']],
                ])->all();

                if (!empty($pivotData)) {
                    $site->products()->sync($pivotData);
                }
            }

            $photoRows = [];

            if (!empty($request->site_photos)) {
                foreach ($request->site_photos as $photo) {
                    $path = $photo->store('site_photos', 'public');
                    $photoRows[] = [
                        'site_id' => $site->id,
                        'file_path' => $path,
                        'photo_type' => 'site_photo',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($request->progress_photos)) {
                foreach ($request->progress_photos as $photo) {
                    $path = $photo->store('progress_photos', 'public');
                    $photoRows[] = [
                        'site_id' => $site->id,
                        'file_path' => $path,
                        'photo_type' => 'progress_photo',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($photoRows)) {
                SitePhotoV2::insert($photoRows);
            }

            if (!empty($validated['additional_notes'])) {
                SiteNote::create([
                    'site_id' => $site->id,
                    'note' => $validated['additional_notes'],
                    'web_user_id' => $authId,
                ]);
            }

            SiteStageLog::create([
                'site_id' => $site->id,
                'stage_id' => $site->current_stage_id,
                'stage_date' => $this->custom_AD_to_BS(now()->format('Y-m-d')),
                'web_changed_by_user_id' => $authId,
                'remarks' => 'Initial stage set during site registration.',
            ]);

            SiteActivityLog::create([
                'site_id' => $site->id,
                'web_causer_id' => $authId,
                'event' => 'Site Registration Created',
                'description' => 'A new site registration has been created.',
                'properties' => json_encode($site->toArray()),
            ]);

            $site->load(['contacts', 'followUps', 'products', 'photos', 'notes']);

            return redirect()->route('site-registration-v2.show', $site->id)->with('success', 'Site registered successfully.');
        });
    }

    public function update(Request $request, SiteRegistrationV2 $siteRegistrationV2)
    {
        $site = $siteRegistrationV2->loadMissing([
            'contacts' => function ($query) {
                $query->orderBy('id');
            },
            'followUps' => function ($query) {
                $query->latest();
            },
            'products',
            'photos' => function ($query) {
                $query->latest();
            },
            'notes' => function ($query) {
                $query->latest();
            },
        ]);

        $validated = $this->validateSiteRequest($request, $site);

        $authId = auth('web')->id();

        return DB::transaction(function () use ($validated, $request, $authId, $site) {
            $previousStageId = $site->current_stage_id;
            $latestNote = $site->notes->first();

            $site->update([
                'name' => $validated['site_name'],
                'dealer_code' => $validated['dealer_code'],
                'contractor_id' => $validated['contractor_id'] ?? null,
                'site_type_id' => $validated['site_type_id'],
                'current_stage_id' => $validated['construction_status_id'],
                'address' => $validated['address'] ?? null,
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'floors' => $validated['number_of_floors'] ?? null,
                'approx_area_sqft' => $validated['approximate_area_sqft'] ?? null,
                'preferred_brand' => $validated['preferred_brand'] ?? null,
                'competitor_brand_observed' => $validated['competitor_brands'] ?? null,
                'estimated_paint_litres' => $validated['estimated_paint_litres'] ?? null,
                'next_followup_at' => $validated['next_followup_at'] ?? null,
            ]);

            $submittedContacts = collect($validated['stakeholders'] ?? []);
            $keptContactIds = [];

            foreach ($submittedContacts as $contactData) {
                $payload = [
                    'site_id' => $site->id,
                    'name' => $contactData['name'],
                    'role' => $contactData['role'],
                    'phone' => $contactData['contact_number'],
                ];

                if (!empty($contactData['id'])) {
                    $contact = SiteContact::where('site_id', $site->id)
                        ->where('id', $contactData['id'])
                        ->first();

                    if ($contact) {
                        $contact->update($payload);
                        $keptContactIds[] = $contact->id;
                        continue;
                    }
                }

                $contact = SiteContact::create($payload);
                $keptContactIds[] = $contact->id;
            }

            if (!empty($keptContactIds)) {
                SiteContact::where('site_id', $site->id)
                    ->whereNotIn('id', $keptContactIds)
                    ->delete();
            } else {
                SiteContact::where('site_id', $site->id)->delete();
            }

            if (!empty($validated['products'])) {
                $pivotData = collect($validated['products'])->mapWithKeys(fn($p) => [
                    $p['id'] => ['quantity' => $p['quantity']],
                ])->all();

                $site->products()->sync($pivotData);
            } else {
                $site->products()->sync([]);
            }

            if (!empty($validated['next_followup_at'])) {
                $followUpData = [
                    'follow_up_date' => $validated['next_followup_at'],
                    'follow_up_type' => $validated['follow_up_type'] ?? null,
                    'status' => $validated['follow_up_status'] ?? 'pending',
                    'notes' => $validated['follow_up_notes'] ?? null,
                    'auto_reminder_enabled' => $validated['auto_reminder_enabled'] ?? true,
                    'assigned_to_user_id' => 1,
                    'web_changed_by_id' => $authId,
                ];

                $latestFollowUp = $site->followUps()->latest()->first();
                if ($latestFollowUp) {
                    $latestFollowUp->update($followUpData);
                } else {
                    $site->followUps()->create($followUpData);
                }
            }

            $photoRows = [];

            if (!empty($request->site_photos)) {
                foreach ($request->site_photos as $photo) {
                    $path = $photo->store('site_photos', 'public');
                    $photoRows[] = [
                        'site_id' => $site->id,
                        'file_path' => $path,
                        'photo_type' => 'site_photo',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($request->progress_photos)) {
                foreach ($request->progress_photos as $photo) {
                    $path = $photo->store('progress_photos', 'public');
                    $photoRows[] = [
                        'site_id' => $site->id,
                        'file_path' => $path,
                        'photo_type' => 'progress_photo',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($photoRows)) {
                SitePhotoV2::insert($photoRows);
            }

            $newNote = trim((string) ($validated['additional_notes'] ?? ''));
            $existingNote = trim((string) ($latestNote->note ?? ''));

            if ($newNote !== '' && $newNote !== $existingNote) {
                SiteNote::create([
                    'site_id' => $site->id,
                    'note' => $newNote,
                    'web_user_id' => $authId,
                ]);
            }

            if ((int) $previousStageId !== (int) $site->current_stage_id) {
                SiteStageLog::create([
                    'site_id' => $site->id,
                    'stage_id' => $site->current_stage_id,
                    'stage_date' => $this->custom_AD_to_BS(now()->format('Y-m-d')),
                    'web_changed_by_user_id' => $authId,
                    'remarks' => 'Site stage updated from web registration form.',
                ]);
            }

            SiteActivityLog::create([
                'site_id' => $site->id,
                'web_causer_id' => $authId,
                'event' => 'Site Registration Updated',
                'description' => 'A site registration has been updated.',
                'properties' => json_encode($site->fresh()->toArray()),
            ]);

            return redirect()->route('site-registration-v2.show', $site->id)->with('success', 'Site updated successfully.');
        });
    }

    public function show(SiteRegistrationV2 $siteRegistrationV2)
    {
        $data['site'] = $siteRegistrationV2->loadMissing([
            'dealer:code,name,mobile',
            'contractor:id,name,pass_book_no,contact',
            'siteType:id,name',
            'currentStage:id,name',
            'products:id,name,types',
            'contacts:id,site_id,name,role,phone',
            'followUps',
            'photos',
            'notes',
            'activityLogs' => function ($query) {
                $query->select('id', 'site_id', 'causer_id', 'web_causer_id', 'event', 'description', 'created_at')
                    ->latest()
                    ->take(5);
            },
            'activityLogs.causer:id,name',
            'activityLogs.causerDashboard:id,name',
        ]);

        return view('dashboards.new-site.siteDetail', $data);
    }

    public function edit(SiteRegistrationV2 $siteRegistrationV2)
    {
        return view('dashboards.new-site.create-site', $this->baseFormData($siteRegistrationV2));
    }

    public function getActivityLogs(SiteRegistrationV2 $site)
    {
        $lastId = request()->query('last_id');

        $query = $site->activityLogs()
            ->with('causer:id,name')
            ->select('id', 'site_id', 'causer_id', 'event', 'description', 'created_at')
            ->latest();

        // If lastId is provided, get records with id < lastId
        if ($lastId) {
            $query->where('id', '<', $lastId);
        }

        $logs = $query->take(5)->get();

        return response()->json([
            'data' => $logs,
            'hasMore' => $logs->count() === 5,
        ]);
    }

    public function siteMaps(Request $request)
    {
        $query = SiteRegistrationV2::with([
            'currentStage:id,name',
            'siteType:id,name',
            'photos' => function ($query) {
                $query->select('id', 'site_id', 'file_path', 'photo_type')
                    ->where('photo_type', 'site_photo')
                    ->limit(1); //site_photo or progress_photo
            }
        ])
            ->whereNotNull('lat')
            ->whereNotNull('lng');

        // Handle search
        $search = $request->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        // Handle stage filter
        $stage = $request->get('stage_id');
        if ($stage && $stage !== 'All Sites') {
            $query->where('current_stage_id', $stage);
        }

        // Handle cursor-based pagination
        $cursor = $request->get('cursor');
        if ($cursor) {
            $query->where('id', '<', $cursor);
        }

        $sites = $query->orderBy('id', 'desc')->limit(10)->get();

        $data['data'] = $sites;
        $data['hasMore'] = count($sites) >= 10;
        $data['lastId'] = $sites->last()->id ?? null;

        $data['stages'] = ConstructionStage::select('id', 'name')
            ->orderBy('id', 'asc')
            ->get();

        // Pass filter parameters to view
        $data['search'] = $search;
        $data['activeStage'] = $stage;
        $data['cursor'] = $cursor;
        // return $data;
        // For AJAX requests, return partial view
        if ($request->ajax()) {
            $html = view('dashboards.new-site.map-partials.site-discovery', $data)->render();
            return response()->json([
                'html' => $html,
                'hasMore' => $data['hasMore'],
                'lastId' => $data['lastId']
            ]);
        }

        return view('dashboards.new-site.siteMaps', $data);
    }
}
