<?php

namespace App\Exports;

use App\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;

// class AttendanceReport implements FromCollection,WithHeadings
class AttendanceReport implements FromView
{
    protected $attendances, $user, $data;

    function __construct($attendances, $user, $data)
    {
        $this->attendances = $attendances;
        $this->user = $user;
        $this->data = $data;
    }

    public function view(): View
    {
        return view('dashboards.attendanceReport.attendance-report', [
            'attendances' => $this->attendances,
            'user' => $this->user,
            'data' => $this->data,
        ]);
    }
}
//     protected $attendances,$user,$from,$to,$total,$absent;

//     function __construct($attendances,$user,$from,$to,$total,$absent,$present) {
//         $this->attendances = $attendances;
//         $this->user = $user;
//         $this->from = $from;
//         $this->to = $to;
//         $this->total = $total;
//         $this->absent = $absent;
//         $this->present = $present;
//     }
//     /**
//     * @return \Illuminate\Support\Collection
//     */
//     public function collection()
//     {
//         return $this->attendances;
//     }
//     public function headings():array{
//         return [
//         [
//             "Attendance Report"
//         ],
//         [
//             "User ",$this->user
//         ],
//         [
//             "Date From ",$this->from
//         ],
//         [
//             "Date to ",$this->to
//         ],
//         [
//             "Total days ",$this->total+1
//         ],
//         [
//             "Total Present",$this->present
//         ],
//         [
//             "Total Absent",$this->absent
//         ],[],[]
//         ,[
//             'User',
//             'Date',
//             'Checkin Time',
//             'Last Punch Time',
//             'Punch Count',
//             'EOD Status'
//             ]];
//     }
// }
