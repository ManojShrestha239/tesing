<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TadaEntry;
use App\Models\TadaDealerVisit;
use App\Models\TadaDealerExpense;
use App\Models\TadaOtherExpense;
use App\Models\TadaActivityLog;
use App\Models\TadaImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\TadaAuthorizedUser;
use App\Models\TadaExpenseAuthorizedUser;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Traits\Nepali_Calendar;
use App\Traits\CustomNepaliCalendarConverter;

class TadaReportController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter;

    private function combinedWith(Request $request, $fallback = null): ?string
    {
        if (!$request->hasAny(['companion_names', 'combined_with'])) {
            return $fallback;
        }

        $value = $request->input('companion_names', $request->input('combined_with'));
        $value = is_string($value) ? trim($value) : $value;

        return $value && strcasecmp($value, 'alone') !== 0 ? $value : null;
    }

    private function isCombinedVisit(Request $request, $fallback = false): bool
    {
        $combinedWith = $this->combinedWith($request);
        if ($request->hasAny(['companion_names', 'combined_with']) && !$combinedWith) {
            return false;
        }

        if ($request->has('is_combined_visit')) {
            return filter_var($request->input('is_combined_visit'), FILTER_VALIDATE_BOOLEAN);
        }

        return $combinedWith ? true : (bool) $fallback;
    }

    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $storedPath = Storage::disk('public')->putFileAs('uploads/tada/receipts', $file, $filename);

            return response()->json([
                'status' => 'success',
                'message' => 'Image uploaded successfully',
                'path' => $storedPath,
                'url' => Storage::disk('public')->url($storedPath)
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'No file uploaded'], 400);
    }

    public function deleteExpenseImage(Request $request, $imageId = null)
    {
        $imageId = $imageId ?? $request->input('image_id');
        if (empty($imageId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'image_id is required.'
            ], 422);
        }

        $user = Auth::user();
        $image = TadaImage::whereHas('entry', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->find($imageId);

        if (!$image) {
            return response()->json([
                'status' => 'error',
                'message' => 'Expense image not found.'
            ], 404);
        }

        $entry = $image->entry;
        if ($entry && $entry->status !== 'PENDING' && $entry->status !== 'REJECTED') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only PENDING or REJECTED reports can remove images.'
            ], 422);
        }

        $this->deleteImageRecord($image);

        return response()->json([
            'status' => 'success',
            'message' => 'Expense image deleted successfully.'
        ]);
    }

    public function authorizedUsers()
    {
        $users = TadaAuthorizedUser::where('status', 'ACTIVE')
            ->orderByRaw("CASE WHEN name = ? THEN 0 ELSE 1 END", ['As per Itinerary'])
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function expenseAuthorizedUsers()
    {
        $users = TadaExpenseAuthorizedUser::where('status', 'ACTIVE')
            ->orderBy('name')
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function index()
    {
        $user = Auth::user();
        $entries = TadaEntry::with([
            'visits.dealer',
            'dealerExpenses.dealer',
            'dealerExpenses.images',
            'dealerExpenses.expenseAuthorizedUser',
            'otherExpenses.images',
            'otherExpenses.expenseAuthorizedUser',
            'images',
            'authorizedUser'
        ])
            ->whereHas('authorizedUser')
            ->where('user_id', $user->id)
            ->orderBy('entry_date', 'desc')
            ->get();

        $formatted = $entries->map(function ($entry) {
            return $this->formatTadaEntry($entry);
        });

        return response()->json([
            'status' => 'success',
            'data' => $formatted
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $entry = TadaEntry::with(['visits.dealer', 'dealerExpenses.dealer', 'dealerExpenses.images', 'dealerExpenses.expenseAuthorizedUser', 'otherExpenses.images', 'otherExpenses.expenseAuthorizedUser', 'images', 'authorizedUser'])
            ->where('user_id', $user->id)
            ->find($id);

        if (!$entry) {
            return response()->json([
                'status' => 'error',
                'message' => 'TADA Report not found or you do not have permission to access it.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->formatTadaEntry($entry)
        ]);
    }

    public function store(Request $request)
    {
        $this->normalizeRequestData($request);

        $validator = Validator::make($request->all(), [
            'entry_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:' . now()->subDays(6)->toDateString(),
            ],
            'route' => 'nullable|required_without:travel_route|string',
            'travel_route' => 'nullable|required_without:route|string',

            'visit_category' => 'nullable|required_without:travel_type|string|in:Local,Up-country,Ex-up country,local,up-country,ex-up-country',
            'travel_type' => 'nullable|required_without:visit_category|string|in:Local,Up-Country,Ex-Up-Country,Up-country,Ex-up country,local,up-country,ex-up-country',

            'stay_at' => 'nullable|string',
            'staying_at' => 'nullable|string',

            'is_combined_visit' => 'boolean',
            'combined_with' => 'nullable|string',
            'companion_names' => 'nullable|string',

            'starting_km' => 'nullable|numeric',
            'ending_km' => 'nullable|numeric',
            'remarks' => 'nullable|string|max:500',
            'authorized_user_id' => 'nullable|exists:tada_authorized_users,id',
            'images' => 'nullable|array',
            'images.*' => 'nullable',

            'visits' => 'nullable|array',
            'visits.*.dealer_code' => 'nullable|string',
            'visits.*.unlisted_dealer_name' => 'nullable|string',
            'visits.*.visit_type' => 'required|in:online,offline,unlisted',
            'visits.*.is_physical_visit' => 'nullable|boolean',
            'visits.*.expenses_authorized_by' => 'nullable|exists:tada_expense_authorized_users,id',
            'visits.*.remarks' => 'nullable|string|max:500',

            'expenses' => 'nullable|array',
            'expenses.*.type' => 'required|string',
            'expenses.*.dealer_code' => 'nullable|string',
            'expenses.*.is_physical_visit' => 'nullable|boolean',
            'expenses.*.dealer_id' => 'nullable|string',
            'expenses.*.expense_type' => 'nullable|string',
            'expenses.*.expense_details' => 'nullable|string',
            'expenses.*.amount' => 'required|numeric',
            'expenses.*.remarks' => 'nullable|string|max:500',
            'expenses.*.expenses_authorized_by' => 'nullable|exists:tada_expense_authorized_users,id',
            'expenses.*.images' => 'nullable|array',
            'expenses.*.images.*' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $entryDate = $request->entry_date;
            $attendanceKm = TadaEntry::attendanceKilometersForUserDate($user->id, $entryDate);

            $entryData = [
                'user_id' => $user->id,
                'entry_date' => $entryDate,
                'route' => $request->travel_route ?? $request->route,
                'visit_category' => str_replace('Up-Country', 'Up-country', str_replace('Ex-Up-Country', 'Ex-up country', $request->travel_type ?? $request->visit_category)),
                'stay_at' => $request->staying_at ?? $request->stay_at,
                'is_combined_visit' => $this->isCombinedVisit($request),
                'combined_with' => $this->combinedWith($request),
                'starting_km' => $request->filled('starting_km')
                    ? $request->starting_km
                    : ($attendanceKm['starting_km'] ?? null),
                'ending_km' => $request->filled('ending_km')
                    ? $request->ending_km
                    : ($attendanceKm['ending_km'] ?? null),
                'remarks' => $request->remarks,
                'authorized_user_id' => $request->authorized_user_id,
                'status' => 'PENDING',
            ];

            $entry = TadaEntry::create($entryData);

            if ($request->has('images')) {
                foreach ($request->images as $img) {
                    $this->saveImage($img, $entry->id);
                }
            }

            if ($request->has('visits')) {
                foreach ($request->visits as $visit) {
                    $entry->visits()->create([
                        'dealer_code' => $visit['dealer_code'] ?? null,
                        'unlisted_dealer_name' => $visit['unlisted_dealer_name'] ?? null,
                        'is_physical_visit' => $visit['is_physical_visit'] ?? null,
                        'visit_type' => $visit['visit_type'],
                        'remarks' => $visit['remarks'] ?? null,
                    ]);

                    if (!empty($visit['amount']) || !empty($visit['images'])) {
                        $de = $entry->dealerExpenses()->create([
                            'dealer_code' => $visit['dealer_code'] ?? null,
                            'expense_type' => 'Dealer Visit',
                            'amount' => $visit['amount'] ?? 0,
                            'is_physical_visit' => $visit['is_physical_visit'] ?? null,
                            'remarks' => $visit['remarks'] ?? null,
                            'expenses_authorized_by' => $visit['expenses_authorized_by'] ?? null,
                            'authorized_user_id' => $request->authorized_user_id,
                        ]);

                        if (!empty($visit['images']) && is_array($visit['images'])) {
                            foreach ($visit['images'] as $img) {
                                $this->saveImage($img, $entry->id, $de->id);
                            }
                        }
                    }
                }
            }

            if ($request->has('expenses')) {
                foreach ($request->expenses as $expense) {
                    $isDealer = !empty($expense['dealer_code']) || !empty($expense['dealer_id']) || ($expense['type'] === 'dealer');

                    if ($isDealer) {
                        $dCode = $expense['dealer_code'] ?? $expense['dealer_id'] ?? 'UNKNOWN';
                        $model = $entry->dealerExpenses()->create([
                            'dealer_code' => $dCode,
                            'expense_type' => $expense['expense_type'] ?? $expense['type'] ?? 'Dealer Visit',
                            'amount' => $expense['amount'],
                            'is_physical_visit' => $expense['is_physical_visit'] ?? null,
                            'remarks' => $expense['remarks'] ?? null,
                            'expenses_authorized_by' => $expense['expenses_authorized_by'] ?? null,
                        ]);
                    } else {
                        $model = $entry->otherExpenses()->create([
                            'expense_details' => $expense['expense_details'] ?? $expense['type'] ?? 'Other Expense',
                            'amount' => $expense['amount'],
                            'remarks' => $expense['remarks'] ?? null,
                            'expenses_authorized_by' => $expense['expenses_authorized_by'] ?? null,
                        ]);
                    }

                    if (!empty($expense['images'])) {
                        foreach ($expense['images'] as $img) {
                            $this->saveImage(
                                $img,
                                $entry->id,
                                ($isDealer) ? $model->id : null,
                                (!$isDealer) ? $model->id : null
                            );
                        }
                    }
                }
            }

            // Activity Log
            TadaActivityLog::create([
                'tada_entry_id'     => $entry->id,
                'action_by_user_id' => $user->id,
                'action'            => 'CREATED',
                'new_values'        => $entry->toArray(),
                'ip_address'        => request()->ip(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'TADA Entry submitted successfully',
                'data' => $this->formatTadaEntry($entry->load(['visits.dealer', 'dealerExpenses.dealer', 'dealerExpenses.images', 'dealerExpenses.expenseAuthorizedUser', 'otherExpenses.images', 'otherExpenses.expenseAuthorizedUser', 'images', 'authorizedUser']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save entry: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $this->parsePutOrPatch($request);
        $this->normalizeRequestData($request);

        $user = Auth::user();
        $entry = TadaEntry::where('user_id', $user->id)->find($id);

        if (!$entry) {
            return response()->json([
                'status' => 'error',
                'message' => 'TADA Report not found.'
            ], 404);
        }

        if ($entry->status !== 'PENDING' && $entry->status !== 'REJECTED') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only PENDING or REJECTED reports can be modified. This report is currently ' . $entry->status . '.'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'entry_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:' . min(now()->subDays(6)->toDateString(), $entry->entry_date),
            ],
            'route' => 'nullable|required_without:travel_route|string',
            'travel_route' => 'nullable|required_without:route|string',

            'visit_category' => 'nullable|required_without:travel_type|string|in:Local,Up-country,Ex-up country,local,up-country,ex-up-country',
            'travel_type' => 'nullable|required_without:visit_category|string|in:Local,Up-Country,Ex-Up-Country,Up-country,Ex-up country,local,up-country,ex-up-country',

            'stay_at' => 'nullable|string',
            'staying_at' => 'nullable|string',

            'is_combined_visit' => 'boolean',
            'combined_with' => 'nullable|string',
            'companion_names' => 'nullable|string',

            'starting_km' => 'nullable|numeric',
            'ending_km' => 'nullable|numeric',
            'remarks' => 'nullable|string|max:500',
            'authorized_user_id' => 'nullable|exists:tada_authorized_users,id',
            'images' => 'nullable|array',
            'images.*' => 'nullable',

            'visits' => 'nullable|array',
            'visits.*.dealer_code' => 'nullable|string',
            'visits.*.unlisted_dealer_name' => 'nullable|string',
            'visits.*.visit_type' => 'required|in:online,offline,unlisted',
            'visits.*.is_physical_visit' => 'nullable|boolean',
            'visits.*.expenses_authorized_by' => 'nullable|exists:tada_expense_authorized_users,id',
            'visits.*.remarks' => 'nullable|string|max:500',

            'expenses' => 'nullable|array',
            'expenses.*.type' => 'required|string',
            'expenses.*.dealer_code' => 'nullable|string',
            'expenses.*.is_physical_visit' => 'nullable|boolean',
            'expenses.*.dealer_id' => 'nullable|string',
            'expenses.*.expense_type' => 'nullable|string',
            'expenses.*.expense_details' => 'nullable|string',
            'expenses.*.amount' => 'required|numeric',
            'expenses.*.remarks' => 'nullable|string|max:500',
            'expenses.*.expenses_authorized_by' => 'nullable|exists:tada_expense_authorized_users,id',
            'expenses.*.images' => 'nullable|array',
            'expenses.*.images.*' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $oldValues = $entry->toArray();

            $updatedUserId = $user->id;
            $updatedEntryDate = $request->entry_date ?? $entry->entry_date;
            $attendanceKm = TadaEntry::attendanceKilometersForUserDate($updatedUserId, $updatedEntryDate);

            // Reject status Edit garesi and submit garesi Feri Pending status ma rakhni
            $newStatus = $entry->status;
            if ($entry->status === 'REJECTED') {
                $newStatus = 'PENDING';
            }

            $entry->update([
                'entry_date' => $updatedEntryDate,
                'route' => $request->travel_route ?? $request->route ?? $entry->route,
                'visit_category' => str_replace('Up-Country', 'Up-country', str_replace('Ex-Up-Country', 'Ex-up country', $request->travel_type ?? $request->visit_category ?? $entry->visit_category)),
                'stay_at' => $request->staying_at ?? $request->stay_at ?? $entry->stay_at,
                'is_combined_visit' => $this->isCombinedVisit($request, $entry->is_combined_visit),
                'combined_with' => $this->combinedWith($request, $entry->combined_with),
                'starting_km' => $request->filled('starting_km')
                    ? $request->starting_km
                    : ($attendanceKm['starting_km'] ?? $entry->starting_km),
                'ending_km' => $request->filled('ending_km')
                    ? $request->ending_km
                    : ($attendanceKm['ending_km'] ?? $entry->ending_km),
                'remarks' => $request->remarks,
                'authorized_user_id' => $request->authorized_user_id,
                'status' => $newStatus,
            ]);

            // Sync Images (General)
            // Preserve existing entry-level images unless explicitly deleted through the delete endpoint.
            if ($request->has('images')) {
                foreach ($request->images as $img) {
                    $this->saveImage($img, $entry->id);
                }
            }

            if ($request->has('visits')) {
                // Sync visits only when the client supplied the visits collection.
                $entry->visits()->delete();
                foreach ($request->visits as $visit) {
                    $entry->visits()->create([
                        'dealer_code' => $visit['dealer_code'] ?? null,
                        'unlisted_dealer_name' => $visit['unlisted_dealer_name'] ?? null,
                        'is_physical_visit' => $visit['is_physical_visit'] ?? null,
                        'visit_type' => $visit['visit_type'],
                        'remarks' => $visit['remarks'] ?? null,
                    ]);
                }
            }

            // Sync Expenses (and their specific images)
            $existingDealerExpenses = $entry->dealerExpenses->keyBy('id');
            $existingOtherExpenses = $entry->otherExpenses->keyBy('id');
            $usedDealerExpenseIds = [];
            $usedOtherExpenseIds = [];

            if ($request->has('visits')) {
                foreach ($request->visits as $visit) {
                    if (!empty($visit['amount']) || !empty($visit['images'])) {
                        $de = null;

                        if (!empty($visit['id'])) {
                            $de = $existingDealerExpenses->get($visit['id']);
                        }

                        if (!$de && !empty($visit['dealer_code'])) {
                            $de = $existingDealerExpenses->firstWhere('dealer_code', $visit['dealer_code']);
                        }

                        if (!$de) {
                            $de = $entry->dealerExpenses()->create([
                                'dealer_code' => $visit['dealer_code'] ?? null,
                                'expense_type' => 'Dealer Visit',
                                'amount' => $visit['amount'] ?? 0,
                                'is_physical_visit' => $visit['is_physical_visit'] ?? null,
                                'remarks' => $visit['remarks'] ?? null,
                                'expenses_authorized_by' => $visit['expenses_authorized_by'] ?? null,
                                'authorized_user_id' => $request->authorized_user_id,
                            ]);
                        } else {
                            $de->update([
                                'dealer_code' => $visit['dealer_code'] ?? $de->dealer_code,
                                'expense_type' => 'Dealer Visit',
                                'amount' => $visit['amount'] ?? $de->amount,
                                'is_physical_visit' => array_key_exists('is_physical_visit', $visit)
                                    ? $visit['is_physical_visit']
                                    : $de->is_physical_visit,
                                'remarks' => $visit['remarks'] ?? $de->remarks,
                                'expenses_authorized_by' => array_key_exists('expenses_authorized_by', $visit)
                                    ? $visit['expenses_authorized_by']
                                    : $de->expenses_authorized_by,
                                'authorized_user_id' => $request->authorized_user_id,
                            ]);
                        }

                        $usedDealerExpenseIds[] = $de->id;

                        if (!empty($visit['images']) && is_array($visit['images'])) {
                            foreach ($visit['images'] as $img) {
                                $this->saveImage($img, $entry->id, $de->id);
                            }
                        }
                    }
                }
            }

            if ($request->has('expenses')) {
                foreach ($request->expenses as $expense) {
                    $isDealer = !empty($expense['dealer_code']) || !empty($expense['dealer_id']) || ($expense['type'] === 'dealer');

                    if ($isDealer) {
                        $dCode = $expense['dealer_code'] ?? $expense['dealer_id'] ?? 'UNKNOWN';
                        $model = null;

                        if (!empty($expense['id'])) {
                            $model = $existingDealerExpenses->get($expense['id']);
                        }

                        if (!$model) {
                            $model = $existingDealerExpenses->firstWhere(function ($item) use ($dCode, $expense) {
                                return $item->dealer_code === $dCode && $item->expense_type === ($expense['expense_type'] ?? $expense['type'] ?? 'Dealer Visit');
                            });
                        }

                        if (!$model) {
                            $model = $entry->dealerExpenses()->create([
                                'dealer_code' => $dCode,
                                'expense_type' => $expense['expense_type'] ?? $expense['type'] ?? 'Dealer Visit',
                                'amount' => $expense['amount'],
                                'is_physical_visit' => $expense['is_physical_visit'] ?? null,
                                'remarks' => $expense['remarks'] ?? null,
                                'expenses_authorized_by' => $expense['expenses_authorized_by'] ?? null,
                            ]);
                        } else {
                            $model->update([
                                'dealer_code' => $dCode,
                                'expense_type' => $expense['expense_type'] ?? $expense['type'] ?? 'Dealer Visit',
                                'amount' => $expense['amount'],
                                'is_physical_visit' => array_key_exists('is_physical_visit', $expense)
                                    ? $expense['is_physical_visit']
                                    : $model->is_physical_visit,
                                'remarks' => $expense['remarks'] ?? $model->remarks,
                                'expenses_authorized_by' => array_key_exists('expenses_authorized_by', $expense)
                                    ? $expense['expenses_authorized_by']
                                    : $model->expenses_authorized_by,
                            ]);
                        }

                        $usedDealerExpenseIds[] = $model->id;
                    } else {
                        $model = null;

                        if (!empty($expense['id'])) {
                            $model = $existingOtherExpenses->get($expense['id']);
                        }

                        if (!$model) {
                            $model = $existingOtherExpenses->firstWhere(function ($item) use ($expense) {
                                return $item->expense_details === ($expense['expense_details'] ?? $expense['type'] ?? 'Other Expense');
                            });
                        }

                        if (!$model) {
                            $model = $entry->otherExpenses()->create([
                                'expense_details' => $expense['expense_details'] ?? $expense['type'] ?? 'Other Expense',
                                'amount' => $expense['amount'],
                                'remarks' => $expense['remarks'] ?? null,
                                'expenses_authorized_by' => $expense['expenses_authorized_by'] ?? null,
                            ]);
                        } else {
                            $model->update([
                                'expense_details' => $expense['expense_details'] ?? $expense['type'] ?? $model->expense_details,
                                'amount' => $expense['amount'],
                                'remarks' => $expense['remarks'] ?? $model->remarks,
                                'expenses_authorized_by' => array_key_exists('expenses_authorized_by', $expense)
                                    ? $expense['expenses_authorized_by']
                                    : $model->expenses_authorized_by,
                            ]);
                        }

                        $usedOtherExpenseIds[] = $model->id;
                    }

                    if (!empty($expense['images'])) {
                        foreach ($expense['images'] as $img) {
                            $this->saveImage(
                                $img,
                                $entry->id,
                                ($isDealer) ? $model->id : null,
                                (!$isDealer) ? $model->id : null
                            );
                        }
                    }
                }
            }

            $entry->dealerExpenses()->whereNotIn('id', $usedDealerExpenseIds)->get()->each(function ($exp) {
                foreach ($exp->images as $img) {
                    $this->deleteImageRecord($img);
                }
                $exp->delete();
            });

            $entry->otherExpenses()->whereNotIn('id', $usedOtherExpenseIds)->get()->each(function ($exp) {
                foreach ($exp->images as $img) {
                    $this->deleteImageRecord($img);
                }
                $exp->delete();
            });

            // Activity Log
            TadaActivityLog::create([
                'tada_entry_id'    => $entry->id,
                'action_by_user_id' => $user->id,
                'action'           => 'UPDATED',
                'old_values'       => $oldValues,
                'new_values'       => $entry->fresh()->toArray(),
                'ip_address'       => request()->ip(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'TADA Entry updated successfully',
                'data' => $this->formatTadaEntry($entry->load(['visits.dealer', 'dealerExpenses.dealer', 'dealerExpenses.images', 'dealerExpenses.expenseAuthorizedUser', 'otherExpenses.images', 'otherExpenses.expenseAuthorizedUser', 'images', 'authorizedUser']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $entry = TadaEntry::where('user_id', $user->id)->find($id);

        if (!$entry) {
            return response()->json([
                'status' => 'error',
                'message' => 'TADA Report not found.'
            ], 404);
        }

        if ($entry->status !== 'PENDING') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only PENDING reports can be deleted.'
            ], 422);
        }

        $oldValues = $entry->toArray();
        $entry->delete();

        TadaActivityLog::create([
            'tada_entry_id'    => $id,
            'action_by_user_id' => $user->id,
            'action'           => 'DELETED',
            'old_values'       => $oldValues,
            'ip_address'       => request()->ip(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'TADA Entry moved to trash successfully'
        ]);
    }

    private function formatTadaEntry($entry)
    {
        $expenses = [];
        $visits = [];

        foreach ($entry->dealerExpenses as $de) {
            $images = [];
            foreach ($de->images as $img) {
                $imageUrl = $img->receipt_image_path
                    ? Storage::disk('public')->url($img->receipt_image_path)
                    : null;
                $images[] = [
                    'id' => $img->id,
                    'url' => $imageUrl,
                    'path' => $img->receipt_image_path,
                ];
            }
            $expenses[] = [
                'type' => $de->expense_type ?? 'Dealer Visit',
                'dealer_id' => $de->dealer_code,
                'dealer_name' => $de->dealer->name ?? null,
                'amount' => (float)$de->amount,
                'remarks' => $de->remarks ?? '',
                'expenses_authorized_by' => $de->expenses_authorized_by,
                'expenses_authorized_by_name' => $de->expenseAuthorizedUser->name ?? null,
                'images' => $images,
                'is_physical_visit' => $de->is_physical_visit !== null
                    ? (bool) $de->is_physical_visit
                    : null,
            ];
        }

        foreach ($entry->otherExpenses as $oe) {
            $images = [];
            foreach ($oe->images as $img) {
                $imageUrl = $img->receipt_image_path
                    ? Storage::disk('public')->url($img->receipt_image_path)
                    : null;
                $images[] = [
                    'id' => $img->id,
                    'url' => $imageUrl,
                    'path' => $img->receipt_image_path,
                ];
            }
            $expenses[] = [
                'type' => $oe->expense_details ?? 'Other',
                'dealer_id' => null,
                'amount' => (float)$oe->amount,
                'remarks' => $oe->remarks ?? '',
                'expenses_authorized_by' => $oe->expenses_authorized_by,
                'expenses_authorized_by_name' => $oe->expenseAuthorizedUser->name ?? null,
                'images' => $images,
                'is_physical_visit' => null,
            ];
        }

        $attachments = [];
        foreach ($entry->images as $img) {
            $imageUrl = $img->receipt_image_path
                ? Storage::disk('public')->url($img->receipt_image_path)
                : null;
            $attachments[] = [
                'id' => $img->id,
                'url' => $imageUrl,
                'path' => $img->receipt_image_path,
            ];
        }

        foreach ($entry->visits as $visit) {
            $visits[] = [
                'dealer_code' => $visit->dealer_code,
                'dealer_name' => $visit->dealer->name ?? ($visit->unlisted_dealer_name ?? null),
                'unlisted_dealer_name' => $visit->unlisted_dealer_name ?? null,
                'visit_type' => $visit->visit_type,
                'is_physical_visit' => $visit->is_physical_visit !== null
                    ? (bool) $visit->is_physical_visit
                    : null,
                'remarks' => $visit->remarks ?? '',
            ];
        }

        $totalExpense = $entry->dealerExpenses->sum('amount') + $entry->otherExpenses->sum('amount');
        $totalDistance = max(0, ($entry->ending_km ?? 0) - ($entry->starting_km ?? 0));

        $formattedStatus = $entry->status === 'INREVIEW' ? 'in-review' : strtolower($entry->status);

        $formattedTravelType = str_replace(' ', '-', strtolower($entry->visit_category));

        return [
            'id' => $entry->id,
            'user_id' => $entry->user_id,
            'created_at' => $entry->created_at->toIso8601String(),
            'updated_at' => $entry->updated_at->toIso8601String(),
            'status' => $formattedStatus,
            'status_remarks' => $entry->admin_remarks,
            'entry_date' => $this->convertAdToBs($entry->entry_date),
            'travel_type' => $formattedTravelType,
            'staying_at' => $entry->stay_at,
            'remarks' => $entry->remarks,
            'travel_route' => $entry->route,
            'companion_names' => $entry->combined_with,
            'starting_km' => (float)$entry->starting_km,
            'ending_km' => (float)$entry->ending_km,
            'total_distance' => (float)$totalDistance,
            'total_expense' => (float)$totalExpense,
            'visits' => $visits,
            'expenses' => $expenses,
            'attachments' => $attachments,
            'approved_by' => $entry->authorizedUser ? [
                'id' => $entry->authorizedUser->id,
                'name' => $entry->authorizedUser->name,
                'phone' => $entry->authorizedUser->phone ?? '',
                'email' => $entry->authorizedUser->email ?? '',
                'designation' => $entry->authorizedUser->designation ?? 'Authorized User',
                'department' => $entry->authorizedUser->department ?? 'Operations',
                'status' => strtolower($entry->authorizedUser->status),
                'created_at' => $entry->authorizedUser->created_at->toIso8601String(),
                'updated_at' => $entry->authorizedUser->updated_at->toIso8601String(),


            ] : null,
        ];
    }

    private function saveImage($image, $entry_id, $dealer_expense_id = null, $other_expense_id = null)
    {
        if (empty($image)) return;

        if (is_string($image)) {
            $cleanedPath = $image;
            if (preg_match('/^https?:\/\//i', $image)) {
                $parsed = parse_url($image);
                if (isset($parsed['path'])) {
                    $cleanedPath = $parsed['path'];
                    $pos = strpos($cleanedPath, 'uploads/');
                    if ($pos !== false) {
                        $cleanedPath = substr($cleanedPath, $pos);
                    } else {
                        $cleanedPath = ltrim($cleanedPath, '/');
                    }
                }
            }

            $existingImage = TadaImage::where('tada_entry_id', $entry_id)
                ->where('receipt_image_path', $cleanedPath)
                ->first();

            if ($existingImage) {
                $existingImage->update([
                    'tada_dealer_expense_id' => $dealer_expense_id,
                    'tada_other_expense_id' => $other_expense_id,
                ]);
            } else {
                TadaImage::create([
                    'tada_entry_id' => $entry_id,
                    'tada_dealer_expense_id' => $dealer_expense_id,
                    'tada_other_expense_id' => $other_expense_id,
                    'receipt_image_path' => $cleanedPath
                ]);
            }
        } elseif ($image instanceof \Illuminate\Http\UploadedFile) {
            $file = $image;
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $storedPath = Storage::disk('public')->putFileAs('uploads/tada/receipts', $file, $filename);

            TadaImage::create([
                'tada_entry_id' => $entry_id,
                'tada_dealer_expense_id' => $dealer_expense_id,
                'tada_other_expense_id' => $other_expense_id,
                'receipt_image_path' => $storedPath
            ]);
        }
    }

    private function deleteImageRecord(TadaImage $image): void
    {
        $storagePath = $this->resolveStoragePath($image->receipt_image_path);
        if ($storagePath) {
            $hasOtherReferences = TadaImage::where('receipt_image_path', $image->receipt_image_path)
                ->where('id', '!=', $image->id)
                ->exists();

            if (!$hasOtherReferences && Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }
        }

        $image->delete();
    }

    private function resolveStoragePath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $cleanedPath = $path;
        if (preg_match('/^https?:\/\//i', $path)) {
            $parsed = parse_url($path);
            if (isset($parsed['path'])) {
                $cleanedPath = $parsed['path'];
                $pos = strpos($cleanedPath, 'uploads/');
                if ($pos !== false) {
                    $cleanedPath = substr($cleanedPath, $pos);
                } else {
                    $cleanedPath = ltrim($cleanedPath, '/');
                }
            }
        }

        if (preg_match('~^/|^[A-Za-z]:\\\~', $cleanedPath)) {
            return ltrim($cleanedPath, '/');
        }

        return $cleanedPath;
    }

    private function convertBsToAd($bsDate)
    {
        if (empty($bsDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $bsDate)) {
            return $bsDate;
        }

        try {
            $parts = explode('-', $bsDate);
            $engDay = $this->BS_to_AD((int)$parts[0], (int)$parts[1], (int)$parts[2]);
            if ($engDay && is_array($engDay)) {
                $day = str_pad($engDay['date'], 2, '0', STR_PAD_LEFT);
                $month = str_pad($engDay['month'], 2, '0', STR_PAD_LEFT);
                $year = $engDay['year'];
                return "{$year}-{$month}-{$day}";
            }
        } catch (\Exception $e) {
            // Fail silent, return original
        }

        return $bsDate;
    }

    private function convertAdToBs($adDate)
    {
        if (empty($adDate)) {
            return null;
        }

        try {
            $parts = explode('-', $adDate);
            if (count($parts) === 3) {
                $nepDay = $this->AD_to_BS((int)$parts[0], (int)$parts[1], (int)$parts[2]);
                if ($nepDay && is_array($nepDay)) {
                    $day = str_pad($nepDay['date'], 2, '0', STR_PAD_LEFT);
                    $month = str_pad($nepDay['month'], 2, '0', STR_PAD_LEFT);
                    $year = $nepDay['year'];
                    return "{$year}-{$month}-{$day}";
                }
            }
        } catch (\Exception $e) {
            // Fail silent, return original
        }

        return $adDate;
    }

    private function parsePutOrPatch(Request $request)
    {
        if (!$request->isMethod('PUT') && !$request->isMethod('PATCH')) {
            return;
        }

        $rawContent = $request->getContent();
        if (empty($rawContent)) {
            return;
        }

        $parsedData = [];

        if (substr($rawContent, 0, 2) === '--') {
            $firstLine = strtok($rawContent, "\r\n");
            $boundary = ltrim($firstLine, '-');
            $boundary = rtrim($boundary, '-');

            if (!empty($boundary)) {
                $parsedData = $this->parseMultipartContent($rawContent, $boundary);
            }
        } else {
            $decoded = json_decode($rawContent, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $parsedData = $decoded;
            } else {
                parse_str($rawContent, $parsedData);
            }
        }

        if (!empty($parsedData)) {
            $request->merge($parsedData);
        }
    }

    private function parseMultipartContent($rawContent, $boundary)
    {
        $data = [];
        $blocks = preg_split("/-+" . preg_quote($boundary, '/') . "/", $rawContent);

        foreach ($blocks as $block) {
            if (empty($block) || trim($block) === '' || trim($block) === '--') {
                continue;
            }

            if (substr($block, 0, 2) === "\r\n") {
                $block = substr($block, 2);
            } elseif (substr($block, 0, 1) === "\n") {
                $block = substr($block, 1);
            }

            $parts = explode("\r\n\r\n", $block, 2);
            if (count($parts) < 2) {
                continue;
            }

            $headersStr = $parts[0];
            $body = $parts[1];

            if (substr($body, -2) === "\r\n") {
                $body = substr($body, 0, -2);
            } elseif (substr($body, -1) === "\n") {
                $body = substr($body, 0, -1);
            }

            if (!preg_match('/name="([^"]+)"/', $headersStr, $nameMatches)) {
                continue;
            }
            $name = $nameMatches[1];

            if (preg_match('/filename="([^"]+)"/', $headersStr, $fileMatches)) {
                $filename = $fileMatches[1];

                preg_match('/Content-Type:\s*([^\s\r\n]+)/i', $headersStr, $ctMatches);
                $contentType = $ctMatches[1] ?? 'application/octet-stream';

                $tempPath = tempnam(sys_get_temp_dir(), 'php_put_pkg_');
                file_put_contents($tempPath, $body);

                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tempPath,
                    $filename,
                    $contentType,
                    null,
                    true
                );

                $this->setNestedKey($data, $name, $uploadedFile);
            } else {
                $this->setNestedKey($data, $name, $body);
            }
        }

        return $data;
    }

    private function setNestedKey(&$arr, $key, $value)
    {
        if (strpos($key, '[') === false) {
            $arr[$key] = $value;
            return;
        }

        $parts = explode('[', $key);
        $parts = array_map(function ($part) {
            return rtrim($part, ']');
        }, $parts);

        $current = &$arr;
        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                $current[$part] = $value;
            } else {
                if ($part === '') {
                    $current[] = [];
                    $keys = array_keys($current);
                    $part = end($keys);
                }
                if (!isset($current[$part]) || !is_array($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
        }
    }

    private function normalizeRequestData(Request $request)
    {
        if ($request->has('entry_date')) {
            $request->merge(['entry_date' => $this->convertBsToAd($request->entry_date)]);
        }

        // Normalize incoming camelCase keys to snake_case so mobile clients
        // sending camelCase (e.g. isPhysicalVisit) are accepted by validators
        // and internal logic which expects snake_case keys.
        $all = $request->all();
        if (!empty($all) && is_array($all)) {
            $converted = $this->convertArrayKeysToSnake($all);
            $converted = $this->normalizeBooleanFields($converted);
            $request->merge($converted);
        }

        $travelVal = $request->travel_type ?? $request->visit_category;
        if (!empty($travelVal)) {
            $normalizedTravel = strtolower(str_replace('_', '-', $travelVal));
            if ($normalizedTravel === 'local') {
                $dbVal = 'Local';
            } elseif ($normalizedTravel === 'up-country' || $normalizedTravel === 'upcountry') {
                $dbVal = 'Up-country';
            } elseif ($normalizedTravel === 'ex-up-country' || $normalizedTravel === 'ex-upcountry' || $normalizedTravel === 'excountry' || $normalizedTravel === 'ex-country') {
                $dbVal = 'Ex-up country';
            } else {
                $dbVal = $travelVal;
            }

            if ($request->has('travel_type')) {
                $request->merge(['travel_type' => $dbVal]);
            }
            if ($request->has('visit_category')) {
                $request->merge(['visit_category' => $dbVal]);
            }
        }

        if ($request->has('expenses') && is_array($request->expenses)) {
            $expenses = $request->expenses;
            foreach ($expenses as $i => $expense) {
                if (isset($expense['type'])) {
                    $expenses[$i]['type'] = strtolower($expense['type']);
                }
                if (isset($expense['expense_type'])) {
                    $expenses[$i]['expense_type'] = strtolower($expense['expense_type']);
                }
            }
            $request->merge(['expenses' => $expenses]);
        }
    }

    /**
     * Recursively convert all array keys from camelCase to snake_case.
     * Preserves numeric-indexed arrays and only converts associative keys.
     */
    private function convertArrayKeysToSnake($data)
    {
        if (!is_array($data)) return $data;

        // detect associative array
        $isAssoc = array_keys($data) !== range(0, count($data) - 1);

        if (!$isAssoc) {
            // numeric-indexed array: convert each element
            $result = [];
            foreach ($data as $v) {
                $result[] = $this->convertArrayKeysToSnake($v);
            }
            return $result;
        }

        $result = [];
        foreach ($data as $k => $v) {
            $newKey = $k;
            if (is_string($k)) {
                // convert dashed keys and camelCase/PascalCase to snake_case
                $normalized = str_replace(['-', ' '], '_', $k);
                $newKey = strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $normalized));
            }
            if (is_array($v)) {
                $result[$newKey] = $this->convertArrayKeysToSnake($v);
            } else {
                $result[$newKey] = $v;
            }
        }

        return $result;
    }

    private function normalizeBooleanFields($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $booleanKeys = [
            'is_physical_visit',
            'is_combined_visit',
        ];

        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->normalizeBooleanFields($value);
                continue;
            }

            if (in_array($key, $booleanKeys, true)) {
                if (is_bool($value) || $value === null) {
                    $result[$key] = $value;
                    continue;
                }

                $lower = strtolower(trim((string)$value));
                if (in_array($lower, ['1', 'true', 'yes', 'y', 'on'], true)) {
                    $result[$key] = true;
                    continue;
                }
                if (in_array($lower, ['0', 'false', 'no', 'n', 'off'], true)) {
                    $result[$key] = false;
                    continue;
                }
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
