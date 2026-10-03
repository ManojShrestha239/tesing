<?php

declare(strict_types=1);

namespace App\Services\v2\siteRegistration;

use App\DTO\v2\siteRegistration\ListSiteRegistrationsDTO;
use App\DTO\v2\siteRegistration\StoreSiteRegistrationDTO;
use App\DTO\v2\siteRegistration\UpdateFollowUpDTO;
use App\Models\SiteActivityLog;
use App\Models\SiteContact;
use App\Models\SiteFollowUp;
use App\Models\SiteNote;
use App\Models\SitePhotoV2;
use App\Models\SiteProduct;
use App\Models\SiteRegistrationV2;
use App\Models\SiteStageLog;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

final class SiteRegistrationService
{
    use CustomNepaliCalendarConverter;

    private int $authId;

    public function __construct()
    {
        $this->authId = auth('api')->id();
    }

    /**
     * @throws Throwable
     */
    public function getRegistrations(ListSiteRegistrationsDTO $dto): CursorPaginator
    {
        return DB::transaction(function () use ($dto) {
            $query = SiteRegistrationV2::query()
                ->with('photos')
                ->select(['id', 'name', 'status', 'current_stage_id', 'site_type_id', 'next_followup_at']);

            if ($dto->currentStageId !== null) {
                $query->whereIn('current_stage_id', $dto->currentStageId);
            }
            if ($dto->siteTypeId !== null) {
                $query->whereIn('site_type_id', $dto->siteTypeId);
            }
            if ($dto->search !== null) {
                $searchTerm = '%' . $dto->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                        ->orWhere('next_followup_at', 'like', $searchTerm);
                });
            }

            $query->orderBy('id', 'desc');

            return $query->cursorPaginate(
                $dto->perPage,
                ['*'],
                'cursor',
                $dto->cursor
            );
        });
    }

    /**
     * @throws Throwable
     */
    public function create(StoreSiteRegistrationDTO $dto): SiteRegistrationV2
    {
        return DB::transaction(function () use ($dto) {
            // 1️⃣ Main site record
            $site = SiteRegistrationV2::create([
                'name' => $dto->siteName,
                'dealer_code' => $dto->dealerCode,
                'date' => $this->todayNepaliDate(), // Replace with actual Nepali date conversion if needed
                'contractor_id' => $dto->contractorId,
                'registered_by_user_id' => $this->authId,
                'site_type_id' => $dto->siteTypeId,
                'current_stage_id' => $dto->constructionStatusId,
                'lat' => $dto->lat,
                'lng' => $dto->lng,
                'floors' => $dto->numberOfFloors,
                'approx_area_sqft' => $dto->approximateAreaSqft,
                'preferred_brand' => $dto->preferredBrand,
                'competitor_brand_observed' => $dto->competitorBrands,
                'estimated_paint_litres' => $dto->estimatedPaintLitres,
                'status' => 'new_registration',
                'next_followup_at' => $dto->nextFollowupAt,
            ]);

            // 2️⃣ Stakeholders (bulk insert)
            $stakeholderRows = $dto->stakeholders->map(fn($s) => [
                'site_id' => $site->id,
                'name' => $s->name,
                'role' => $s->role,
                'phone' => $s->contactNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            if (!empty($stakeholderRows)) {
                SiteContact::insert($stakeholderRows);
            }

            // 3️⃣ Follow‑up (optional)
            if ($dto->nextFollowupAt) {
                SiteFollowUp::create([
                    'site_id' => $site->id,
                    'follow_up_date' => $dto->nextFollowupAt,
                    'follow_up_type' => $dto->followUpType?->value,
                    'status' => $dto->followUpStatus ?? 'pending',
                    'notes' => $dto->followUpNotes,
                    'auto_reminder_enabled' => $dto->autoReminderEnabled ?? true,
                    'assigned_to_user_id' => $this->authId,
                    'created_by_user_id' => $this->authId,
                ]);
            }

            // 4️⃣ Products pivot sync (quantity stored on pivot)
            $pivotData = $dto->products->mapWithKeys(fn($p) => [
                $p->id => ['quantity' => $p->quantity],
            ])->all();

            if (!empty($pivotData)) {
                $site->products()->sync($pivotData);
            }

            // 5️⃣ Photo handling (single INSERT batch)
            $photoRows = [];

            if (!empty($dto->sitePhotos)) {
                foreach ($dto->sitePhotos as $photo) {
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

            if (!empty($dto->progressPhotos)) {
                foreach ($dto->progressPhotos as $photo) {
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

            // 6️⃣ Additional notes
            if (!empty($dto->additionalNotes)) {
                SiteNote::create([
                    'site_id' => $site->id,
                    'note' => $dto->additionalNotes,
                    'user_id' => $this->authId,
                ]);
            }

            // 7️⃣ Stage & activity logs
            $today = now(); // Replace with Nepali date if needed

            SiteStageLog::create([
                'site_id' => $site->id,
                'stage_id' => $site->current_stage_id,
                'stage_date' => $this->custom_AD_to_BS($today->format('Y-m-d')),
                'changed_by_user_id' => $this->authId,
                'remarks' => 'Initial stage set during site registration.',
            ]);

            SiteActivityLog::create([
                'site_id' => $site->id,
                'causer_id' => $this->authId,
                'event' => 'Site Registration Created',
                'description' => 'A new site registration has been created.',
                'properties' => json_encode($site->toArray()),
            ]);

            // Return the model with all eager‑loaded relations required by the API
            return $site->load(['contacts', 'followUps', 'products', 'photos', 'notes']);
        });
    }

    public function getById(SiteRegistrationV2 $site): SiteRegistrationV2
    {
        return $site->loadMissing([
            'dealer:code,name,mobile',
            'contractor:id,name,pass_book_no,contact',
            'siteType:id,name',
            'currentStage:id,name',
            'products:id,name,types',
            'contacts:id,site_id,name,role,phone',
            'followUps',
            'photos',
            'notes',
        ]);
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateFollowUpDTO $dto, SiteFollowUp $followUp): SiteFollowUp
    {
        return DB::transaction(function () use ($dto, $followUp) {
            $disableFollowUpStatusChange = in_array($followUp->status, ['completed', 'cancelled']);
            if ($disableFollowUpStatusChange) {
                throw new HttpException(
                    400,
                    'The status of this follow up cannot be changed because it is already marked as completed, or cancelled.'
                );
            }
            // -----------------------------------------------------------------
            // 1️⃣ Load parent site (if not already loaded) and update its stage
            // -----------------------------------------------------------------
            $followUp->loadMissing('site');
            $todaydate = $this->custom_AD_to_BS(now()->format('Y-m-d'));

            /** @var SiteRegistrationV2 $site */
            $site = $followUp->site;

            $disableStatusChange = in_array($site->status, ['lost', 'completed', 'cancelled']);

            if ($disableStatusChange) {
                throw new HttpException(
                    400,
                    'The status of this site registration cannot be changed because it is already marked as lost, completed, or cancelled.'
                );
            }

            $site->update([
                'current_stage_id' => $dto->constructionStatusId,
                'next_followup_at' => $dto->nextFollowUpAt ?? $site->next_followup_at
            ]);

            // Stage log
            SiteStageLog::create([
                'site_id' => $site->id,
                'stage_id' => $dto->constructionStatusId,
                'stage_date' => $todaydate,
                'changed_by_user_id' => $this->authId,
                'remarks' => "Stage updated to $dto->constructionStatusId via follow‑up update.",
            ]);

            // -----------------------------------------------------------------
            // 2️⃣ Update the *current* follow‑up record
            // -----------------------------------------------------------------
            $status = $dto->currentFollowUpStatus ?? $followUp->status;
            $oldFollow = $followUp;
            $now = now();
            $followUp->update([
                'follow_up_type' => $dto->currentFollowUpType ?? $followUp->follow_up_type,
                'status' => $status,
                'notes' => $dto->currentFollowUpNotes ?? $followUp->notes,
                'completed_at' => $dto->currentFollowUpStatus === 'completed' ? $todaydate : $followUp->completed_at,
                'lat' => $dto->lat ?? $followUp->lat,
                'lng' => $dto->lng ?? $followUp->lng,
            ]);

            $bulkActivityLogs = [
                'site_id' => $site->id,
                'causer_id' => $this->authId,
                'event' => "{{$status}} Follow Up",
                'description' => 'The follow‑up status has been updated to ' . $status . '. from ' . $oldFollow->status,
                'properties' => json_encode($oldFollow->getOriginal()), // old values
                'created_at' => $now,
            ];

            // -----------------------------------------------------------------
            // 3️⃣ Optionally schedule the *next* follow‑up
            // -----------------------------------------------------------------
            if ($dto->nextFollowUpAt !== null) {
                $nowFollowUp = SiteFollowUp::create([
                    'site_id' => $site->id,
                    'follow_up_date' => $dto->nextFollowUpAt,
                    'follow_up_type' => $dto->followUpType,
                    'status' => $dto->followUpStatus ?? 'pending',
                    'notes' => $dto->followUpNotes,
                    'auto_reminder_enabled' => $dto->autoReminderEnabled ?? true,
                    'assigned_to_user_id' => $this->authId,
                    'created_by_user_id' => $this->authId,
                ]);

                $bulkActivityLogs = [
                    'site_id' => $site->id,
                    'causer_id' => $this->authId,
                    'event' => 'Next Follow Up Scheduled',
                    'description' => 'The next follow‑up has been scheduled for ' . $dto->nextFollowUpAt . '.',
                    'properties' => json_encode($nowFollowUp->getOriginal()), // old values
                    'created_at' => $now,
                ];
            }

            // -----------------------------------------------------------------
            // 4️⃣ Store any uploaded photos (bulk insert)
            // -----------------------------------------------------------------
            if (!empty($dto->sitePhotos)) {
                $photoRows = [];
                foreach ($dto->sitePhotos as $photo) {
                    $path = $photo->store('site_photos', 'public');

                    $photoRows[] = [
                        'site_id' => $site->id,
                        'file_path' => $path,
                        'photo_type' => 'site_photo',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($photoRows)) {
                    SitePhotoV2::insert($photoRows);
                }
            }

            // -----------------------------------------------------------------
            // 5️⃣ Activity log – store *old* follow‑up state for audit
            // -----------------------------------------------------------------
            if (!empty($bulkActivityLogs)) {
                SiteActivityLog::insert($bulkActivityLogs);
            }

            // Return the **fresh** model (with any newly created next follow‑uploaded if needed)
            return $followUp->fresh(['site']);
        });
    }

    public function allSiteProducts(): Collection
    {
        return SiteProduct::get();
    }
}
