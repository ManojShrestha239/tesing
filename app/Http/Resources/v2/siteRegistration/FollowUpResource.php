<?php

declare(strict_types=1);

namespace App\Http\Resources\v2\siteRegistration;

use Illuminate\Http\Resources\Json\JsonResource;

class FollowUpResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'site_id' => $this->site_id,
            'follow_up_date' => $this->follow_up_date,
            'follow_up_type' => $this->follow_up_type,
            'status' => $this->status,
            'notes' => $this->notes,
            'auto_reminder_enabled' => $this->auto_reminder_enabled,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'completed_at' => $this->completed_at,
            // Embedded site summary (already eager‑loaded by the service)
            'site' => $this->whenLoaded('site', fn() => [
                'id' => $this->site->id,
                'name' => $this->site->name,
                'current_stage_id' => $this->site->current_stage_id,
            ]),
        ];
    }
}
