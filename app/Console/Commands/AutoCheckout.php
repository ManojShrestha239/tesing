<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Location;
use App\Models\Mobileuser;
use App\Traits\FCM;
use Illuminate\Console\Command;

class AutoCheckout extends Command
{
    use FCM;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true);
        $mobileusers = Mobileuser::with('latestAttendance')->get();

        $attendances = [];
        $attendanceStatuses = [];
        $fcmids = [];

        foreach ($mobileusers as $user) {
            $isUserNotCheckout = ($user->latestAttendance && $user->latestAttendance->status === 'checkout');

            if ($isUserNotCheckout) {
                continue;
            }

            $fcmids[] = $user->fcm_id ?? null;

            $date = $user->latestAttendance->date ?? date('Y-m-d');
            $attendance = Attendance::where(['mobileuser_id' => $user->id, 'date' => $date])->first();

            if (!$attendance) {
                $attendances[] = [
                    'mobileuser_id' => $user->id,
                    'checkin_date' => date('H:i:s'),
                    'date' => $date,
                    'status' => 'checkout',
                    'address' => null,
                    'checkinkm' => 0,
                    'visit_method' => "Single",
                    'remarks' => "Auto checkout by system.",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                $attendance->checkoutkm = 0;
                $attendance->status = 'checkout';
                $attendance->remarks = "Auto checkout by system.";
                $attendance->save();
            }

            $this->info('Processing attendance status...');
            $attendanceStatuses[] = [
                'attendance_id' => $attendance->id,
                'mobileuser_id' => $attendance->mobileuser_id,
                'date' => $date,
                'time' => date('H:i:s'),
                'status' => 'checkout',
                'address' => null,
                'km' => 0,
                'visit_method' => "Single",
                'remarks' => "Auto checkout by system.",
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->info('Processing location data...');
            $latestLocation = Location::where('user_id', $user->id)->latest()->first();
            $coordinates = $latestLocation->coordinates ?? [];
            $latestCoordinate = !empty($coordinates) ? end($coordinates) : null;
            $latitude = $latestCoordinate['latitude'] ?? 'N/A';
            $longitude = $latestCoordinate['longitude'] ?? 'N/A';

            $location = Location::where(['day' => date('Y-m-d'), 'user_id' => $user->id])->first();


            if (!$location) {
                $cordinates[] = array(
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'time' => date('H:i:s')
                );

                $location = new Location();

                $location->user_id = $user->id;
                $location->day = date('Y-m-d');
                $location->coordinates = $cordinates;
                $location->user_id = $user->id;
                $location->role_id = $user->role_id;

                $location->save();
            } else {
                $cordinates[] = array(
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'time' => date('H:i:s')
                );
                $location->coordinates = array_merge($location->coordinates, $cordinates);

                $location->save();
            }
        }

        $this->info('Inserting auto checkout...');
        if (!empty($attendances)) {
            Attendance::insert($attendances);
        }

        $this->info('Inserting attendance status...');
        if (!empty($attendanceStatuses)) {
            AttendanceStatus::insert($attendanceStatuses);
        }

        $this->info('Sending notification...');
        $fcmid = Mobileuser::first()->fcm_id;
        $topic = null;
        $flag = "Auto Checkout";
        $title = "Auto Checkout Notice";
        $body = "Your attendance has been auto checked out by the system.";
        $data = [
            'title' => "Auto Checkout Notice",
            'msg' => $body,
            'flag' => $flag,
        ];
        foreach ($fcmids as $fcmid) {
            $this->runFirebaseNotification($fcmid, $topic, $title, $body, $data);
        }

        $this->info('Auto checkout notification sent successfully.');
        $endTime = microtime(true);
        $responseTime = $endTime - $startTime;
        $this->info('Response time: ' . $responseTime . ' seconds');
    }
}
