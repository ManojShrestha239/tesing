<?php

declare(strict_types=1);

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileuserDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $visit = $this->session ?? collect();

        return [
            'id'                        => $this->id,
            'name'                      => $this->name,
            'phone'                     => $this->phone,
            'address'                   => collect([$this->areas, $this->sub_areas])
                ->filter(fn($v) => filled($v))
                ->pipe(fn($c) => $c->isEmpty() ? null : $c->implode(', ')),
            'attendance_status'          => $this->attendance->first()?->status ?? 'on leave',
            'attendance_in_time'         => $this->attendance->first()?->checkin_date
                ? \Carbon\Carbon::parse($this->attendance->first()?->checkin_date)->format('g:i A')
                : null,
            'role_name'                 => $this->role?->role,
            'today_orders_count'        => (int) $this->today_orders_count,
            'today_payments_count'      => (int) $this->today_payments_count,
            'todays_order_total_amt'    => (float) $this->todays_order_total,
            'todays_payments_total_amt' => (float) $this->todays_payments_total,

            'visits_summary' => [
                'online'   => $visit->where('status', 'online')->count(),
                'offline'  => $visit->where('status', 'offline')->count(),
                'unlisted' => $visit->whereNotIn('status', ['online', 'offline'])->count(),
            ],

            'visits' => $visit->map(fn($s) => [
                'dealer_name' => $s->dealer ? $s->dealer->name : $s->dealer_name,
                'dealer_address' => $s->dealer ? $s->dealer->area_code . ', ' . $s->dealer->subarea : $s->dealer_address,
                'status'      => $s->status,
                'start_time'  => $s->start_time,
                'end_time'    => $s->end_time,
                'purposes' => $s->intentions  // Intention means the purpose of the visit
                    ->map(fn($p) => $p->purpose)
                    ->values(),
            ]),

            'locations' => ($this->todayLocations ?? collect())->map(function ($l) {
                $latest = collect($l->coordinates ?? [])
                    ->filter(fn($c) => isset($c['latitude'], $c['longitude'], $c['time']))
                    ->sortBy('time')
                    ->last();

                return [
                    'latitude'  => $latest['latitude']  ?? null,
                    'longitude' => $latest['longitude'] ?? null,
                    'time'      => $latest['time']      ?? null,
                ];
            }),
        ];
    }
}
