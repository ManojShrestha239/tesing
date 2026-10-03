<?php

declare(strict_types=1);

namespace App\Services\API;

use App\Models\Mobileuser;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Support\Facades\DB;

class TeamMemberService
{
    use CustomNepaliCalendarConverter;

    /**
     * Get team members for a given senior user, with optional search and date filters.
     *
     * @param Mobileuser $user The senior user whose team members are to be fetched.
     * @param int $perPage The number of results per page for pagination.
     * @param string|null $search Optional search term to filter team members by name, email, phone, areas, or sub_areas.
     * @param string|null $date Optional date in BS format to filter attendance status. Defaults to today if not provided or invalid.
     *
     * @return array An associative array containing summary statistics and a paginated list of team members.
     */
    public function getTeamMembers(
        Mobileuser $user,
        int $perPage = 20,
        ?string $search = null,
        ?string $date = null,
        ?string $type = null
    ): array {
        // ---- Resolve target date safely (BS → AD) ----
        $targetDate = today()->toDateString();
        if ($date) {
            try {
                $targetDate = $this->custom_BS_to_AD($date);
            } catch (\Exception $e) {
                $targetDate = today()->toDateString();
            }
        }

        // ---- Normalize type ----
        // Accepts: 'all' | 'checkin' | 'checkout' | 'breakin' | 'breakout' | 'on leave'
        // Also tolerates 'on_leave', 'On Leave', etc.
        $type = $type !== null ? strtolower(trim($type)) : 'all';
        $type = str_replace('_', ' ', $type);
        if ($type === '') {
            $type = 'all';
        }

        // ---- Base query: senior's full team (closure table) ----
        $baseQuery = $user->teamMembers();

        // ============================================================
        // Summary — never filtered by $search or $type, respects $targetDate
        // ============================================================
        $total = (clone $baseQuery)->count();

        $onLeave = (clone $baseQuery)
            ->whereDoesntHave('attendance', fn($q) => $q->whereDate('date', $targetDate))
            ->count();

        $presentToday = max(0, $total - $onLeave);

        $checkin = (clone $baseQuery)
            ->whereHas('attendance', function ($q) use ($targetDate) {
                $q->whereDate('date', $targetDate)->where('status', 'checkin');
            })
            ->count();

        // ============================================================
        // List — filtered by $search and $type
        // ============================================================
        $listQuery = (clone $baseQuery);

        // ---- Search filter ----
        if ($search !== null && trim($search) !== '') {
            $term = addcslashes(trim($search), '%_\\');

            $listQuery->where(function ($q) use ($term) {
                $q->where('name',      'like', "%{$term}%")
                    ->orWhere('email',   'like', "%{$term}%")
                    ->orWhere('phone',   'like', "%{$term}%")
                    ->orWhere('areas',   'like', "%{$term}%")
                    ->orWhere('sub_areas', 'like', "%{$term}%");
            });
        }

        $relatedTable = $user->teamMembers()->getRelated()->getTable();

        // ---- Derived subquery: latest attendance row per user for the target date ----
        $latestAttendance = DB::table('attendances')
            ->select('mobileuser_id', 'status')
            ->whereDate('date', $targetDate)
            ->whereIn('id', function ($q) use ($targetDate) {
                $q->selectRaw('MAX(id)')
                    ->from('attendances')
                    ->whereDate('date', $targetDate)
                    ->groupBy('mobileuser_id');
            });

        // Always join — needed both for the type filter and for correct on-leave detection
        $listQuery
            ->leftJoinSub($latestAttendance, 'latest_att', function ($join) use ($relatedTable) {
                $join->on('latest_att.mobileuser_id', '=', "{$relatedTable}.id");
            })
            ->select("{$relatedTable}.*");

        // ---- Apply type filter ----
        switch ($type) {
            case 'on leave':
                $listQuery->whereNull('latest_att.mobileuser_id');
                break;

            case 'checkin':
            case 'checkout':
                $listQuery->where('latest_att.status', $type);
                break;

            // UI labels → DB values
            case 'breakin':
                $listQuery->where('latest_att.status', 'breakstart');
                break;

            case 'breakout':
                $listQuery->where('latest_att.status', 'breakend');
                break;

            case 'all':
            default:
                break;
        }

        // ---- Eager loads + pagination ----
        $members = $listQuery
            ->with([
                'role:id,role',
                'attendance' => function ($q) use ($targetDate) {
                    $q->whereDate('date', $targetDate)
                        ->orderByDesc('id')
                        ->select('id', 'mobileuser_id', 'status', 'date', 'checkin_date');
                },
                // Visits for the target date
                'session' => function ($q) use ($targetDate) {
                    $q->whereDate('date', $targetDate)
                        ->orderByDesc('id')
                        ->select(
                            'id',
                            'status',
                            'user_id'
                        );
                },
            ])
            ->orderBy("{$relatedTable}.name")
            ->cursorPaginate($perPage);

        return [
            'summary' => [
                'total_team_members' => $total,
                'present_today'      => $presentToday,
                'checkin'            => $checkin,
                'on_leave'           => $onLeave,
            ],
            'members' => $members,
            'filters' => [
                'search' => $search,
                'date'   => $targetDate,
                'type'   => $type,
            ],
        ];
    }

    /**
     * Load a team member's detail for the given date and return
     * the hydrated model plus the resolved target date.
     *
     * @param  Mobileuser  $user  Target mobilizer (route-bound).
     * @param  string|null $date  BS or AD date. Null = today.
     * @return array{user: Mobileuser, target_date: string}
     */
    public function getTeamMemberDetail(Mobileuser $user, ?string $date = null): array
    {
        $targetDate = $this->resolveTargetDate($date);

        $this->loadDetail($user, $targetDate);

        return [
            'user'        => $user,
            'target_date' => $targetDate,
        ];
    }

    /**
     * Convert the incoming BS/AD date to a canonical AD (Y-m-d).
     * Falls back to today on any failure.
     */
    protected function resolveTargetDate(?string $date): string
    {
        if ($date === null || $date === '') {
            return today()->toDateString();
        }

        try {
            return $this->custom_BS_to_AD($date);
        } catch (\Throwable $e) {
            return today()->toDateString();
        }
    }

    /**
     * Eager-load every relation the resource needs, filtered by date.
     */
    protected function loadDetail(Mobileuser $user, string $targetDate): void
    {
        $user
            ->loadCount([
                'orders as today_orders_count'     => fn($q) => $q->whereDate('date', $targetDate),
                'payments as today_payments_count' => fn($q) => $q->whereDate('collection_date', $targetDate),
            ])
            ->loadSum(
                ['orders as todays_order_total' => fn($q) => $q->whereDate('date', $targetDate)],
                'total_price'
            )
            ->loadSum(
                ['payments as todays_payments_total' => fn($q) => $q->whereDate('collection_date', $targetDate)],
                'amount'
            )
            ->loadMissing([
                'session' => function ($q) use ($targetDate) {
                    $q->whereDate('date', $targetDate)
                        ->orderByDesc('id')
                        ->select(
                            'id',
                            'dealer_code',
                            'dealer_name',
                            'dealer_address',
                            'purpose_id',
                            'status',
                            'user_id',
                            'start_time',
                            'end_time',
                            'date'
                        );
                },
                'session.dealer:code,name,area_code,subarea',

                'locations' => function ($q) use ($targetDate) {
                    $q->whereDate('day', $targetDate)
                        ->select('user_id', 'day', 'coordinates');
                },

                'attendance' => function ($q) use ($targetDate) {
                    $q->whereDate('date', $targetDate)
                        ->orderByDesc('id')
                        ->select('id', 'mobileuser_id', 'status', 'date', 'checkin_date');
                },

                'role:id,role',
            ]);
    }
}
