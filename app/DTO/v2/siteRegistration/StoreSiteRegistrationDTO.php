<?php

declare(strict_types=1);

namespace App\DTO\v2\siteRegistration;


use App\Enums\v2\siteRegistration\FollowUpType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

/**
 * Immutable DTO that represents a complete site‑registration payload.
 *
 * All scalar values are strictly typed.  Nullable fields use `?type`.
 * Collections hold other DTOs (StakeholderDTO, SiteProductDTO).
 */
final readonly class StoreSiteRegistrationDTO
{
    /** @var Collection<int, StakeholderDTO> */
    public Collection $stakeholders;

    /** @var Collection<int, SiteProductDTO> */
    public Collection $products;

    /** @var UploadedFile[]|null */
    public ?array $sitePhotos;

    /** @var UploadedFile[]|null */
    public ?array $progressPhotos;

    private function __construct(
        public string        $siteName,
        public int           $siteTypeId,
        public float         $lat,
        public float         $lng,
        public string        $dealerCode,
        public ?int          $contractorId,
        public int           $constructionStatusId,
        public ?float        $numberOfFloors,
        public ?float        $approximateAreaSqft,
        public ?string       $preferredBrand,
        public ?string       $competitorBrands,
        public ?int          $estimatedPaintLitres,
        public ?string       $nextFollowupAt,
        public ?FollowUpType $followUpType,
        public ?string       $followUpStatus,
        public ?string       $followUpNotes,
        public ?bool         $autoReminderEnabled,
        public ?string       $additionalNotes,
        Collection           $stakeholders,
        Collection           $products,
        ?array               $sitePhotos,
        ?array               $progressPhotos,
    )
    {
        $this->stakeholders = $stakeholders;
        $this->products = $products;
        $this->sitePhotos = $sitePhotos;
        $this->progressPhotos = $progressPhotos;
    }

    /**
     * Build the DTO from a *validated* FormRequest.
     *
     * @param FormRequest $request
     * @return self
     */
    public static function fromRequest(FormRequest $request): self
    {
        /** @var array $data validated by the FormRequest */
        $data = $request->validated();

        // --------- Collections -------------------------------------------------
        $stakeholderDTOs = collect($data['stakeholders'] ?? [])
            ->map(fn(array $item) => StakeholderDTO::fromArray($item));

        $productDTOs = collect($data['products'] ?? [])
            ->map(fn(array $item) => SiteProductDTO::fromArray($item));

        // --------- Follow‑up type (enum) ------------------------------------
        $followUpType = null;
        if (!empty($data['follow_up_type'])) {
            $followUpType = FollowUpType::tryFrom($data['follow_up_type']);
        }

        // --------- Boolean conversion -----------------------------------------
        // Laravel may give us the string "true"/"false"/"1"/"0".  We need a real bool.
        $autoReminderEnabled = null;
        if (array_key_exists('auto_reminder_enabled', $data)) {
            // FILTER_NULL_ON_FAILURE returns null when the value cannot be interpreted.
            $autoReminderEnabled = filter_var(
                $data['auto_reminder_enabled'],
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );
        }

        // --------- Return the fully typed DTO ---------------------------------
        return new self(
            siteName: $data['site_name'],
            siteTypeId: (int)$data['site_type_id'],
            lat: (float)$data['lat'],
            lng: (float)$data['lng'],
            dealerCode: $data['dealer_code'],
            contractorId: isset($data['contractor_id']) ? (int)$data['contractor_id'] : null,
            constructionStatusId: (int)$data['construction_status_id'],
            numberOfFloors: isset($data['number_of_floors']) ? (float)$data['number_of_floors'] : null,
            approximateAreaSqft: isset($data['approximate_area_sqft']) ? (float)$data['approximate_area_sqft'] : null,
            preferredBrand: $data['preferred_brand'] ?? null,
            competitorBrands: $data['competitor_brands'] ?? null,
            estimatedPaintLitres: isset($data['estimated_paint_litres']) ? (int)$data['estimated_paint_litres'] : null,
            nextFollowupAt: $data['next_followup_at'] ?? null,
            followUpType: $followUpType,
            followUpStatus: $data['follow_up_status'] ?? null,
            followUpNotes: $data['follow_up_notes'] ?? null,
            autoReminderEnabled: $autoReminderEnabled,          // <-- guaranteed bool|null
            additionalNotes: $data['additional_notes'] ?? null,
            stakeholders: $stakeholderDTOs,
            products: $productDTOs,
            sitePhotos: $request->file('site_photos') ?? null,
            progressPhotos: $request->file('progress_photos') ?? null,
        );
    }

    /** Helpful for debugging / logging (never used for persistence). */
    public function toArray(): array
    {
        return [
            'site_name' => $this->siteName,
            'site_type_id' => $this->siteTypeId,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'dealer_code' => $this->dealerCode,
            'contractor_id' => $this->contractorId,
            'construction_status_id' => $this->constructionStatusId,
            'number_of_floors' => $this->numberOfFloors,
            'approximate_area_sqft' => $this->approximateAreaSqft,
            'preferred_brand' => $this->preferredBrand,
            'competitor_brands' => $this->competitorBrands,
            'estimated_paint_litres' => $this->estimatedPaintLitres,
            'next_followup_at' => $this->nextFollowupAt,
            'follow_up_type' => $this->followUpType?->value,
            'follow_up_status' => $this->followUpStatus,
            'follow_up_notes' => $this->followUpNotes,
            'auto_reminder_enabled' => $this->autoReminderEnabled,
            'additional_notes' => $this->additionalNotes,
            'stakeholders' => $this->stakeholders->map(fn(StakeholderDTO $s) => $s->toArray())->all(),
            'products' => $this->products->map(fn(SiteProductDTO $p) => $p->toArray())->all(),
        ];
    }
}
