<?php
declare(strict_types=1);

namespace App\Http\Resources\v2\siteRegistration;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteRegistrationDetailResource extends JsonResource
{

    /**
     * @param Request $request
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dealer' => $this->whenLoaded('dealer', fn() => [
                'code' => $this->dealer->code,
                'name' => $this->dealer->name,
                'mobile' => $this->dealer->mobile,
            ]),
            'contractor' => $this->whenLoaded('contractor', fn() => [
                'id' => $this->contractor->id,
                'name' => $this->contractor->name,
                'pass_book_number' => $this->contractor->pass_book_no,
                'contact' => $this->contractor->contact,
            ]),
            'site_type' => $this->whenLoaded('siteType', fn() => [
                'id' => $this->siteType->id,
                'name' => $this->siteType->name,
            ]),
            'current_stage' => $this->whenLoaded('currentStage', fn() => [
                'id' => $this->currentStage->id,
                'name' => $this->currentStage->name,
            ]),
            'lat' => $this->lat,
            'lng' => $this->lng,
            'floors' => $this->floors,
            'approx_area_sqft' => $this->approx_area_sqft,
            'preferred_brand' => $this->preferred_brand,
            'competitor_brands' => $this->competitor_brand_observed,
            'estimated_paint_litres' => $this->estimated_paint_litres,
            'status' => $this->status,
            'next_followup_at' => $this->next_followup_at,

            // Collections ----------------------------------------------------
            'contacts' => $this->whenLoaded('contacts', fn() => $this->contacts->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'role' => $c->role,
                'phone' => $c->phone,
            ])),

            'products' => $this->whenLoaded('products', fn() => $this->products->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'type' => $p->types,
                'quantity' => $p->pivot->quantity,
            ])),

            'follow_ups' => $this->whenLoaded('followUps', fn() => $this->followUps->map(fn($f) => [
                'id' => $f->id,
                'date' => $f->follow_up_date,
                'type' => $f->follow_up_type,
                'status' => $f->status,
                'notes' => $f->notes,
                'auto_reminder_enabled' => $f->auto_reminder_enabled,
            ])),

//            'photos' => $this->whenLoaded('photos', fn() => $this->photos->map(fn($ph) => [
//                'id' => $ph->id,
//                'url' => Storage::disk('public')->url($ph->file_path),
//                'type' => $ph->photo_type,
//            ])),

            'site_photos' => $this->whenLoaded('photos', fn() => $this->photos->where('photo_type', 'site_photo')->map(fn($ph) => [
                'id' => $ph->id,
                'url' => $ph->file_path,
                'type' => $ph->photo_type,
            ])->values()
            ),

            'progress_photos' => $this->whenLoaded('photos', fn() => $this->photos->where('photo_type', 'progress_photo')->map(fn($ph) => [
                'id' => $ph->id,
                'url' => $ph->file_path,
                'type' => $ph->photo_type,
            ])->values()
            ),

            'notes' => $this->whenLoaded('notes', fn() => $this->notes->map(fn($n) => [
                'id' => $n->id,
                'note' => $n->note,
                'created_by' => $n->user_id,
                'created_at' => $n->created_at,
            ])),
        ];
    }
}
