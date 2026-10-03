<?php
declare(strict_types=1);

namespace App\DTO\v2\siteRegistration;

use App\Http\Requests\API\v2\siteRegistration\UpdateFollowUpRequest;
use Illuminate\Http\UploadedFile;

final readonly class UpdateFollowUpDTO
{
    /** @param UploadedFile[]|null $sitePhotos */
    public function __construct(
        public int     $followUpId,
        public int     $constructionStatusId,
        public ?string $currentFollowUpType,
        public ?string $currentFollowUpStatus,
        public ?string $currentFollowUpNotes,
        public ?float  $lat,
        public ?float  $lng,
        public ?string $nextFollowUpAt,
        public ?string $followUpType,
        public ?string $followUpStatus,
        public ?string $followUpNotes,
        public ?bool   $autoReminderEnabled,
        public ?array  $sitePhotos,
    )
    {
    }

    /**
     * Build the DTO from a validated FormRequest.
     *
     * @param UpdateFollowUpRequest $request
     * @param int $followUpId
     * @return self
     */
    public static function fromRequest(UpdateFollowUpRequest $request, int $followUpId): self
    {
        /** @var array $validated */
        $validated = $request->validated();

        // Cast numeric values explicitly
        $lat = isset($validated['lat']) ? (float)$validated['lat'] : null;
        $lng = isset($validated['lng']) ? (float)$validated['lng'] : null;

        // Files may be null – keep as null or array of UploadedFile
        $photos = $request->hasFile('site_photos')
            ? $request->file('site_photos')
            : null;

        return new self(
            followUpId: $followUpId,
            constructionStatusId: (int)$validated['construction_status_id'],
            currentFollowUpType: $validated['current_follow_up_type'] ?? null,
            currentFollowUpStatus: $validated['current_follow_up_status'] ?? null,
            currentFollowUpNotes: $validated['current_follow_up_notes'] ?? null,
            lat: $lat,
            lng: $lng,
            nextFollowUpAt: $validated['next_followup_at'] ?? null,
            followUpType: $validated['follow_up_type'] ?? null,
            followUpStatus: $validated['follow_up_status'] ?? null,
            followUpNotes: $validated['follow_up_notes'] ?? null,
            autoReminderEnabled: $validated['auto_reminder_enabled'] ?? null,
            sitePhotos: $photos,
        );
    }

    /** For debugging / logging */
    public function toArray(): array
    {
        return [
            'follow_up_id' => $this->followUpId,
            'construction_status_id' => $this->constructionStatusId,
            'current_follow_up_type' => $this->currentFollowUpType,
            'current_follow_up_status' => $this->currentFollowUpStatus,
            'current_follow_up_notes' => $this->currentFollowUpNotes,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'next_followup_at' => $this->nextFollowUpAt,
            'follow_up_type' => $this->followUpType,
            'follow_up_status' => $this->followUpStatus,
            'follow_up_notes' => $this->followUpNotes,
            'auto_reminder_enabled' => $this->autoReminderEnabled,
            // photos are not serialised here – they are handled by the service
        ];
    }
}

