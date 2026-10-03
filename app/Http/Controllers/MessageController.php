<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use App\Models\Mobileuser;
use App\Models\MessageGroup;
use App\Models\MessageGroupUser;
use App\Models\MessageImages;
use App\Models\User;
use App\Traits\FCM;
use App\Utilities\Helpers;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    use FCM;
    public function index()
    {
        $userIds = collect([
            Message::where('from', '!=', 0)->where('type', 'individual')->pluck('from'),
            Message::where('to', '!=', 0)->where('type', 'individual')->pluck('to')
        ])->flatten()->unique()->values();

        $individual = collect();
        foreach ($userIds as $userId) {
            $mobileUser = Mobileuser::select('id', 'name')->find($userId);
            if ($mobileUser) {
                $messagesQuery = Message::where('type', 'individual')
                    ->where(function ($query) use ($userId) {
                        $query->where('from', $userId)->orWhere('to', $userId);
                    })
                    ->orderByDesc('created_at');
                $latestMessage = $messagesQuery->first(['messages.id as message_id', 'message', 'is_read', 'created_at']);
                $unreadCount = $messagesQuery->where('from', '!=', 0)->where('is_read', 0)->count();
                $individual->push((object) [
                    'id'         => $mobileUser->id,
                    'name'  => $mobileUser->name,
                    'message'    => $latestMessage,
                    'unread'     => $unreadCount,
                    'created_at' => $latestMessage ? $latestMessage->created_at : null,
                    'type'       => 'individual',
                ]);
            }
        }

        $groups = MessageGroup::whereHas('groupuser', function ($query) {
            $query->where('message_group_users.user_id', Auth::id())
                ->where('user_type', 'admin');
        })
            ->where('status', 'Active')
            ->get(['id', 'group_name as name']);

        $group = collect();
        foreach ($groups as $groupItem) {
            $messagesQuery = Message::where('type', 'group')
                ->where('groupid', $groupItem->id)
                ->orderByDesc('created_at');
            $latestMessage = $messagesQuery->first(['messages.id as message_id', 'message', 'created_at']);
            $unreadCount = $messagesQuery->where('from', '!=', 0)->where('is_read', 0)->count();
            $group->push((object) [
                'id'         => $groupItem->id,
                'name'  => $groupItem->name,
                'message'    => $latestMessage,
                'unread'     => $unreadCount,
                'created_at' => $latestMessage ? $latestMessage->created_at : $groupItem->created_at,
                'type'       => 'group',
            ]);
        }

        $chatList = $individual->merge($group)->sortByDesc('created_at')->values();
        return view('dashboards.chat.chats', ['users' => $chatList]);
    }

    public function show(Request $request, $user_id)
    {
        $this->validate($request, [
            'type' => 'required|in:individual,group'
        ]);
        $my_id = 0;

        if ($request->type == 'individual') {
            Message::where(['type' => $request->type, 'from' => $user_id, 'to' => $my_id])->update(['is_read' => 1]);

            $messages = Message::with('messageImages')->where('type', $request->type)->where(function ($query) use ($user_id, $my_id) {
                $query->where('from', $my_id)->where('to', $user_id);
            })->orWhere(function ($query) use ($user_id, $my_id) {
                $query->where('from', $user_id)->where('to', $my_id);
            })->get();

            $user = Mobileuser::where('id', $user_id)->first();
        } else {
            Message::where(['type' => $request->type, 'groupid' => $user_id])->update(['is_read' => 1]);
            $user = MessageGroup::select('id', 'group_name as name')->where('id', $user_id)->first();
            $messages = Message::where('groupid', $user->id)->get();
        }
        foreach ($messages as $message) {
            if ($message->admin != null) {
                $u = User::where('id', $message->admin)->first();
                if ($u) {
                    $message->admin = $u->name;
                } else {
                    $message->admin = '';
                }
            } else {
                $message->admin = Auth::user()->name;
            }
        }
        return view('dashboards.chat.create', ['messages' => $messages, 'user' => $user]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required',
            'type'        => 'required|in:individual,group',
            'message'     => 'nullable|string',
            'files'       => 'nullable|array|max:5',
            'files.*'     => 'file|mimes:jpg,jpeg,png,tiff|max:10240',
        ]);
        DB::beginTransaction();

        try {
            $message = Message::create([
                'from'     => 0,
                'to'       => $validated['type'] === 'individual' ? $validated['receiver_id'] : null,
                'groupid'  => $validated['type'] === 'group' ? $validated['receiver_id'] : null,
                'message'  => $validated['message'] ?? '',
                'admin'    => Auth::id(),
                'is_read'  => 0,
                'type'     => $validated['type'],
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('messageimg', 'public');

                    MessageImages::create([
                        'chat_id' => $message->id,
                        'image'   => $path,
                    ]);
                }
            }

            $companyName = config('app.company_name');
            $isGroup     = strtolower($request->type) === 'group';

            $data = [
                'route_channel' => 'chat',
                'file_path'     => null,
            ];

            $title = $companyName;
            $body  = $validated['message'] ?? 'You have a new message';

            if ($isGroup) {
                $groupId   = $validated['receiver_id'];
                $groupName = MessageGroup::where('id', $groupId)->value('group_name');

                $title = "{$companyName} - {$groupName}";
                $groupUserIds = MessageGroupUser::where('message_group_id', $groupId)
                    ->where('user_type', 'user')
                    ->pluck('user_id');

                $fcmIds = Mobileuser::whereNotNull('fcm_id')
                    ->whereIn('id', $groupUserIds)
                    ->pluck('fcm_id');

                foreach ($fcmIds as $fcmId) {
                    $this->runFirebaseNotification($fcmId, null, $title, $body, $data);
                }
            } else {
                $mobileUser = Mobileuser::find($validated['receiver_id']);
                if ($mobileUser && $mobileUser->fcm_id) {
                    $this->runFirebaseNotification($mobileUser->fcm_id, null, $title, $body, $data);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Message sent successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:91',
        ]);

        $search = $request->input('search');

        $getLatestMessageData = function ($query, $isGroup = false, $groupCreatedAt = null) {
            $latestMessage = (clone $query)->first(['messages.id as message_id', 'message', 'is_read', 'created_at']);
            $unreadCount = (clone $query)->where('from', '!=', 0)->where('is_read', 0)->count();
            return [
                'message'    => $latestMessage,
                'unread'     => $unreadCount,
                'created_at' => $latestMessage ? $latestMessage->created_at : ($isGroup ? $groupCreatedAt : null),
            ];
        };

        if (!$search) {
            $userIds = Message::where('type', 'individual')
                ->where(function ($q) {
                    $q->where('from', '!=', 0)->orWhere('to', '!=', 0);
                })
                ->pluck('from', 'to')
                ->flatten()
                ->unique()
                ->values();

            $mobileUsers = Mobileuser::select('id', 'name')
                ->whereIn('id', $userIds)
                ->get();

            $individual = $mobileUsers->map(function ($mobileUser) use ($getLatestMessageData) {
                $messagesQuery = Message::where('type', 'individual')
                    ->where(function ($query) use ($mobileUser) {
                        $query->where('from', $mobileUser->id)->orWhere('to', $mobileUser->id);
                    })
                    ->orderByDesc('created_at');
                $data = $getLatestMessageData($messagesQuery);
                return (object) [
                    'id'         => $mobileUser->id,
                    'name'  => $mobileUser->name,
                    'message'    => $data['message'],
                    'unread'     => $data['unread'],
                    'created_at' => $data['created_at'],
                    'type'       => 'individual',
                ];
            });

            $groups = MessageGroup::whereHas('groupuser', function ($query) {
                $query->where('message_group_users.user_id', Auth::id())
                    ->where('user_type', 'admin');
            })
                ->where('status', 'Active')
                ->get(['id', 'group_name as name', 'created_at']);

            $group = $groups->map(function ($groupItem) use ($getLatestMessageData) {
                $messagesQuery = Message::where('type', 'group')
                    ->where('groupid', $groupItem->id)
                    ->orderByDesc('created_at');
                $data = $getLatestMessageData($messagesQuery, true, $groupItem->created_at);
                return (object) [
                    'id'         => $groupItem->id,
                    'name'  => $groupItem->name,
                    'message'    => $data['message'],
                    'unread'     => $data['unread'],
                    'created_at' => $data['created_at'],
                    'type'       => 'group',
                ];
            });

            $list = $individual->merge($group)->sortByDesc('created_at')->values();
            return view('dashboards.chat.users', ['users' => $list]);
        }

        $mobileUsers = Mobileuser::select('id', 'name')
            ->where('name', 'like', "%$search%")
            ->get()
            ->map(function ($user) {
                return (object) [
                    'id'         => $user->id,
                    'name'  => $user->name,
                    'type'       => 'individual',
                    'unread'     => 0,
                    'created_at' => null,
                    'message'    => '',
                ];
            });

        $groups = MessageGroup::whereHas('groupuser', function ($query) {
            $query->where('message_group_users.user_id', Auth::id())
                ->where('user_type', 'admin');
        })
            ->where('status', 'Active')
            ->where('group_name', 'like', "%$search%")
            ->get(['id', 'group_name as name'])
            ->map(function ($group) {
                return (object) [
                    'id'         => $group->id,
                    'name'  => $group->name,
                    'type'       => 'group',
                    'unread'     => 0,
                    'created_at' => null,
                    'message'    => '',
                ];
            });

        $userList = $mobileUsers->merge($groups)->values();
        return view('dashboards.chat.users', ['users' => $userList]);
    }
}
