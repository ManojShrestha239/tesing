<?php

namespace App\Http\Controllers\API\v3;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageGroup;
use App\Models\MessageGroupUser;
use App\Models\MessageImages;
use App\Models\Mobileuser;
use App\Utilities\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function userlist()
    {
        $my_id = Auth::id();
        $user_id = 0;

        $individual = Message::select('created_at')
            ->where('type', 'individual')
            ->where(function ($query) use ($user_id, $my_id) {
                $query->where(['from' => $my_id, 'to' => $user_id])
                    ->orWhere(['from' => $user_id, 'to' => $my_id]);
            })
            ->latest('created_at')
            ->first();

        $admin = [
            'id' => 0,
            'group_name' => env('ADMIN', 'Admin User'),
            'type' => 'individual',
            'created_at' => $individual ? (string)$individual->created_at : (string)now(),
            'message' => $individual ? [$individual] : []
        ];
        $messages = MessageGroup::select('message_groups.id', 'message_groups.group_name', 'message_groups.created_at', DB::raw("'group' as type"), 'latest_messages.latest_created_at')
            ->join('message_group_users', function ($join) use ($my_id) {
                $join->on('message_groups.id', '=', 'message_group_users.message_group_id')
                    ->where('message_group_users.user_id', '=', $my_id)
                    ->where('message_group_users.user_type', '=', 'user')
                    ->where('message_group_users.status', '=', 'added');
            })
            ->leftJoin(
                DB::raw('(SELECT groupid, MAX(created_at) as latest_created_at FROM messages WHERE type = "group" GROUP BY groupid) as latest_messages'),
                'message_groups.id',
                '=',
                'latest_messages.groupid'
            )
            ->where('message_groups.status', 'Active')
            ->get()
            ->map(function ($group) {
                $group->created_at = $group->latest_created_at ?? $group->created_at;
                unset($group->latest_created_at);
                $group->message = [];
                return $group;
            });

        $messages->push((object)$admin);
        $messages = $messages->sortByDesc('created_at')->values();

        return response()->json(['status' => 'success', 'users' => $messages]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric',
            'message' => 'required_without:files|string|nullable',
            'files'   => 'nullable|array|max:5',
            'files.*' => 'file|mimes:jpg,jpeg,png,tiff|max:10280'
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $userId = $user->id;
            $isIndividual = ($request->user_id == 0);

            if (!$isIndividual) {
                $userExist = MessageGroupUser::where('message_group_id', $request->user_id)
                    ->where('user_type', 'user')
                    ->where('status', 'added')
                    ->where('user_id', $userId)
                    ->first();

                if (!$userExist) {
                    return response()->json(['status' => 'error', 'message' => 'You are not a member of this group.'], 403);
                }
            }

            $message = Message::create([
                'from'    => $userId,
                'to'      => $isIndividual ? 0 : null,
                'groupid' => $isIndividual ? null : $request->user_id,
                'type'    => $isIndividual ? 'individual' : 'group',
                'message' => $request->message ?? '',
                'is_read' => 0,
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = 'messageimg/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('public', $filename);

                    MessageImages::create([
                        'image'   => $filename,
                        'chat_id' => $message->id,
                    ]);
                }
            }

            if ($isIndividual) {
                $messages = Message::with(['messageImages', 'user', 'mobileuser'])
                    ->where(function ($q) use ($user) {
                        $q->where('from', $user->id)->where('to', 0)
                            ->orWhere(function ($q2) use ($user) {
                                $q2->where('from', 0)->where('to', $user->id);
                            });
                    })
                    ->where('type', 'individual')
                    ->orderByDesc('id')
                    ->limit(10)
                    ->get()
                    ->reverse()
                    ->values();
            } else {
                $data = [
                    'route_channel' => 'chat',
                    'file_path'     => null,
                ];

                $helpers = new Helpers;

                $messages = Message::with(['messageImages', 'user', 'mobileuser', 'group'])
                    ->where('groupid', $request->user_id)
                    ->where('type', 'group')
                    ->orderByDesc('id')
                    ->limit(10)
                    ->get()
                    ->reverse()
                    ->values();
                $groupId   = $message->groupid;
                $groupName = MessageGroup::where('id', $groupId)->value('group_name');

                $title = "{$user->full_name} - {$groupName}";
                $body  = $request->message ?? 'You have a new message';

                $groupUserIds = MessageGroupUser::where('message_group_id', $groupId)
                    ->where('user_type', 'user')
                    ->where('status', 'added')
                    ->pluck('user_id');

                $exceptUserIds = array_diff($groupUserIds->toArray(), [$userId]);


                $fcmIds = Mobileuser::whereNotNull('fcm_id')
                    ->whereIn('id', $exceptUserIds)
                    ->pluck('fcm_id');

                foreach ($fcmIds as $fcmId) {
                    $helpers->runFirebaseNotification($fcmId, null, $title, $body, $data);
                }
            }

            DB::commit();
            return response()->json(['messages' => $messages]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed to send message.' . $e->getMessage()], 500);
        }
    }


    public function index(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'type' => 'required|in:individual,group',
            'id' => 'required_if:type,group'
        ]);
        if ($valid->fails()) {
            return response()->json(['status' => 'error', 'message' => $valid->errors()], 422);
        }
        $id = Auth::user();
        $my_id = $id->id;
        $user_id = 0;
        if ($request->type == 'individual') {
            Message::where(['from' => $my_id, 'to' => $user_id, 'is_read' => 0])->update(['is_read' => 1]);
            $messages = Message::with(['messageImages', 'user', 'mobileuser'])->where(function ($query) use ($user_id, $my_id) {
                $query->where('from', $my_id)->where('to', $user_id);
            })->orWhere(function ($query) use ($user_id, $my_id) {
                $query->where('from', $user_id)->where('to', $my_id);
            })
                ->where(['type' => $request->type])
                ->get();
        } else {
            Message::where(['groupid' => $request->id, 'is_read' => 0])->update(['is_read' => 1]);
            $messages = Message::with(['messageImages', 'user', 'mobileuser'])
                ->where(['groupid' => $request->id, 'type' => $request->type])
                ->get();
        }

        return response()->json(['messages' => $messages]);
    }
}
