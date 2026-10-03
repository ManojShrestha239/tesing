<?php

namespace App\Http\Controllers;

use App\Models\Mobileuser;
use Illuminate\Http\Request;
use App\Models\Notice;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;
use App\Models\Area;
use App\Models\NoticeImage;
use Illuminate\Support\Facades\Auth;
use App\Traits\FCM;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class NoticeController extends Controller
{
    use Nepali_Calendar, CustomNepaliCalendarConverter, FCM;

    public function index()
    {
        $data['notices'] = Notice::orderBy('created_at', 'desc')->get();
        $data['users'] = Mobileuser::where('status', 'Active')->get();
        return view('dashboards.messaging.list', $data);
    }

    public function create()
    {
        $data['users'] = Mobileuser::where('status', 'Active')->get();
        $data['todayNepali'] = $this->custom_AD_to_BS(date('Y-m-d'));

        return view('dashboards.messaging.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'notice' => 'required',
            'title' => 'required|string|max:191',
            'expiry_date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'user' => 'nullable|exists:mobileusers,id',
            'file' => 'nullable|array|max:5',
            'file.*' => 'required|mimes:pdf,jpg,jpeg,png,webp,svg|max:4096'
        ]);
        $today = $this->custom_AD_to_BS(date('Y-m-d'));
        if ($request['expiry_date'] < $today) {

            return redirect('notice/create')->with('error', 'The expiry-date must be a date after or equal to today')->withInput();
        }
        $dates = $this->custom_BS_to_AD($request->expiry_date);

        $notice = new Notice();

        $notice->user_id = $request->user;
        $notice->title =  $request->title;
        $notice->notice = $request->notice;
        $notice->date = date('Y-m-d');
        $notice->expiry_date = $dates;
        $notice->save();
        if ($request->file) {
            foreach ($request->file as $file) {
                $filecontent = File::get($file);
                $filename = time() . '_' . $file->getClientOriginalName();

                Storage::disk('public')->put("notice/$filename", $filecontent);

                $noticeImage = new NoticeImage();

                $noticeImage->notice_id = $notice->id;
                $noticeImage->file = $filename;

                $noticeImage->save();
            }
        }
        return redirect('/notice')->with('Status', 'New Notice Created.');
    }

    public function show($id)
    {
        $data['notices'] = Notice::with('noticeimage')->findorFail($id);

        return view('dashboards.messaging.view', $data);
    }

    public function edit($id)
    {
        $notice = Notice::findorFail($id);

        $notice->expiry_date = $this->custom_AD_to_BS($notice->expiry_date);

        $data['notice'] = $notice;
        $data['users'] = Mobileuser::all();
        return view('dashboards.messaging.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'notice' => 'required',
            'title' => 'required|string|max:191',
            'expiry_date' => ['required', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'user' => 'nullable|exists:mobileusers,id',
            'file' => 'nullable|array|max:5',
            'file.*' => 'required|mimes:pdf,jpg,jpeg,png,webp,svg|max:4096'
        ]);
        $today = $this->custom_AD_to_BS(date('Y-m-d'));

        if ($request['expiry_date'] < $today) {
            return back()->with('error', 'The expiry-date must be a date after or equal to today')->withInput();
        }

        $dates = $this->custom_BS_to_AD($request->expiry_date);

        $notice = Notice::with('noticeimage')->findorFail($id);

        if ($request->file) {
            foreach ($request->file as $file) {
                $filecontent = File::get($file);
                $filename = time() . '_' . $file->getClientOriginalName();

                Storage::disk('public')->put("notice/$filename", $filecontent);

                $noticeImage = new NoticeImage();

                $noticeImage->notice_id = $notice->id;
                $noticeImage->file = $filename;

                $noticeImage->save();
            }
        }

        $notice->title = $request->title;
        $notice->notice = $request->notice;
        $notice->expiry_date = $dates;
        $notice->user_id = $request->user;
        $notice->save();
        return redirect('/notice')->with('Status', 'Notice Edited.');
    }

    public function destroy($id)
    {
        $notice = Notice::findorFail($id);
        foreach ($notice->noticeimage as $file) {
            if (Storage::disk('public')->exists("notice/$file->file")) {
                Storage::disk('public')->delete("notice/$file->file");
            }
            NoticeImage::findorFail($file->id)->delete();
        }
        $notice->delete();
        return redirect('/notice')->with('Status', 'Notice Deleted.');
    }

    public function noticePush($id)
    {
        $notice = Notice::findorFail($id);

        $notice->publish = '1';
        $notice->save();

        if ($notice->user_id == '') {
            Cache::flush();
            $title = $notice->title;
            $body  = $notice->notice;
            $topic = strtolower(config('app.env')) === 'production' ? config('app.prod-topic') : config('app.dev-topic');

            $data = [
                'route_channel' => 'notice',
            ];
            $this->runFirebaseNotification($fcmid = null, $topic, $title, $body, $data);
        } else {
            Cache::forget("notices_user_{$notice->user_id}");

            $user = Mobileuser::find($notice->user_id);

            if ($user && $user->fcm_id) {
                $title = $notice->title;
                $body  = $notice->notice;

                $data = [
                    'route_channel' => 'notice',
                ];

                $this->runFirebaseNotification($user->fcm_id, null, $title, $body, $data);
            }
        }
        return redirect('/notice')->with('Status', 'Notice Sent.');
    }

    public function search(Request $request)
    {
        $this->validate($request, [
            'date' => ['nullable', 'regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-2])$/'],
            'user' => 'nullable|exists:mobileusers,id'
        ]);
        $mr = '';
        $today = $this->custom_AD_to_BS(date('Y-m-d'));
        if ($request['start-date'] > $today) {
            return redirect('dashboard/collection-cheque')->with('status', 'The start-date must be a date before or equal to today');
        }


        $id = $request->user;
        $date_req = $request->date;
        if (!empty($date_req)) {
            $dates = $this->custom_BS_to_AD($request->date);
        }
        if (Auth::user()->hasRole('ASM')) {
            $admin_area = collect(Auth::user()->area);
            $admin_area = $admin_area->pluck('areacode');
            $mobileuser = Area::join('area_mobileuser', 'areas.areacode', 'area_mobileuser.area_code')
                ->join('mobileusers', 'mobileusers.id', 'area_mobileuser.mobileuser_id')
                ->whereIn('area_code', $admin_area)
                ->groupBy('mobileusers.id')
                ->pluck('mobileusers.id');
            $mobileuser = array_merge($mobileuser->toArray(), [null]);
            $users = Mobileuser::whereIn('id', $mobileuser)->get();
        } else {
            $users = Mobileuser::get();
        }
        if (empty($date_req) && empty($id)) {
            if (Auth::user()->hasRole('ASM')) {
                $notices = Notice::whereNull('user_id')->orWhereIn('user_id', $mobileuser)->orderBy('date', 'ASC')->get();
            } else {
                $notices = Notice::orderBy('date', 'ASC')->get();
            }
        } elseif (empty($id) && !empty($date_req)) {
            if (Auth::user()->hasRole('ASM')) {
                $notices = Notice::whereNull('user_id')->orWhereIn('user_id', $mobileuser)->where('date', $dates)->orderBy('date', 'ASC')->get();
            } else {
                $notices = Notice::orderBy('date', 'ASC')->where('date', $dates)->get();
            }
        } elseif (!empty($id) && empty($date_req)) {
            $officer = Mobileuser::where('id', $id)->first();
            $mr = $officer->name;
            if (Auth::user()->hasRole('ASM')) {
                $notices = Notice::whereNull('user_id')->orWhereIn('user_id', $mobileuser)->orderBy('date', 'ASC')->get();
            } else {
                $notices = Notice::orderBy('date', 'ASC')->whereNull('user_id')->orWhere('user_id', $request->user)->get();
            }
        } else {
            $officer = Mobileuser::where('id', $id)->first();
            $mr = $officer->name;
            if (Auth::user()->hasRole('ASM')) {
                $notices = Notice::whereNull('user_id')->orWhereIn('user_id', $mobileuser)->where('date', $dates)->orderBy('date', 'ASC')->get();
            } else {
                $notices = Notice::orderBy('date', 'ASC')->whereNull('user_id')->orWhere('user_id', $request->user)->where('date', $dates)->get();
            }
        }
        $data['notices'] = $notices;
        $data['users'] = $users;
        $data['from'] = $date_req;
        $data['start'] = "";
        $data['mr'] = $mr;
        $data['userid'] = $request->user;

        return view('dashboards.messaging.list', $data);
    }
}
