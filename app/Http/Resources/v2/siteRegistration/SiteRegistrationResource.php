<?php

declare(strict_types=1);

namespace App\Http\Resources\v2\siteRegistration;

use Illuminate\Http\Resources\Json\JsonResource;

final class SiteRegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            //'date' => $this->date?->format('Y-m-d'),
            'current_stage_id' => $this->current_stage_id,
            'site_type_id' => $this->site_type_id,
            'next_followup_at' => $this->next_followup_at,
            'site_image' => $this->whenLoaded('photos', function () {
                $firstPhoto = $this->photos->where('photo_type', 'site_photo')->first();
                return $firstPhoto ? $firstPhoto->file_path : null;
            }),
            // Additional computed fields can be added here as needed
        ];
    }
}
