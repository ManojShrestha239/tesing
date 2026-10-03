<?php

declare(strict_types=1);

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $attendance = $this->attendance->first();
        $visit      = $this->session ?? collect();

        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'role'   => $this->role?->role,
            'area'   => collect([$this->areas, $this->sub_areas])
                ->filter(fn($v) => filled($v))
                ->pipe(fn($c) => $c->isEmpty() ? null : $c->implode(', ')),
            'status' => $this->resolveAttendanceStatus($attendance),
            'time'   => $attendance?->checkin_date
                ? \Carbon\Carbon::parse($attendance->checkin_date)->format('g:i A')
                : null,
            'visits_summary' => [
                'online'   => $visit->where('status', 'online')->count(),
                'offline'  => $visit->where('status', 'offline')->count(),
                'unlisted' => $visit->whereNotIn('status', ['online', 'offline'])->count(),
            ],
        ];
    }

    private function resolveAttendanceStatus($attendance): string
    {
        if (! $attendance) {
            return 'on leave';
        }

        return match ($attendance->status) {
            'breakend'   => 'breakout',
            'breakstart' => 'breakin',
            default      => $attendance->status,
        };
    }
}
