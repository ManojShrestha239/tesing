<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\MobileuserDetailResource;
use App\Http\Resources\API\TeamMemberResource;
use App\Http\Responses\API\ApiResponse;
use App\Models\Mobileuser;
use App\Services\API\TeamMemberService;
use App\Traits\CustomNepaliCalendarConverter;
use Illuminate\Http\Request;

class MyTeamController extends Controller
{
    use ApiResponse, CustomNepaliCalendarConverter;

    public function __construct(
        protected TeamMemberService $teamService
    ) {}


    public function index(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 20), 100);

        $result  = $this->teamService->getTeamMembers(
            $request->user(),
            $perPage,
            $request->input('search'),
            $request->input('date'),
            $request->input('type'),
        );

        $members = $result['members'];

        return $this->success(
            data: [
                'summary' => $result['summary'],
                'members' => TeamMemberResource::collection($members->items()),
            ],
            message: 'Team members fetched successfully',
            description: null,
            type: 'team_members',
            code: 200,
            meta: [
                'next_cursor' => optional($members->nextCursor())->encode(),
                'prev_cursor' => optional($members->previousCursor())->encode(),
            ],
        );
    }

    public function show(Request $request, Mobileuser $user, ?string $date = null)
    {
        $auth = $request->user();

        // ---- Authorization (kept in controller) ----
        if (! $auth->isJunior($user->id)) {
            return $this->error(
                message: 'Unauthorized',
                description: 'The requested user is not under your team.',
                type: 'authorization',
                code: 403,
            );
        }

        // ---- Delegate to service ----
        $result = $this->teamService->getTeamMemberDetail($user, $date);

        return $this->success(
            data: new MobileuserDetailResource($result['user']),
            message: 'Team member fetched successfully',
            description: null,
            type: 'team_member',
            code: 200,
            meta: [
                'date'        => $result['target_date'],
                'next_cursor' => null,
                'prev_cursor' => null,
            ],
        );
    }
}
