<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;

// class staffSummary implements FromCollection, WithHeadings
class staffSummary implements FromView
{
    protected $from, $to, $attendances, $total;

    public function __construct($attendances, $from, $to, $total)
    {
        $this->attendances = $attendances;
        $this->from = $from;
        $this->to = $to;
        $this->total = $total;
    }

    public function view(): View
    {
        return view('dashboards.attendanceReport.summary-report', [
            'attendances' => $this->attendances,
            'from' => $this->from,
            'to' => $this->to,
            'total' => $this->total,
        ]);
    }
}
/**
 * @return \Illuminate\Support\Collection
 */
    // protected $from, $to, $attendance, $total;

//     public function __construct($attendance, $from, $to, $total)
//     {
//         $this->attendance = $attendance;
//         $this->from = $from;
//         $this->to = $to;
//         $this->total = $total;
//     }

//     public function collection()
//     {
//         return $this->attendance;
//     }
//     public function headings(): array
//     {
//         return [
//             ['Date From', $this->from],
//             ['Date From', $this->to],
//             ['Total Days', $this->total],
//             [], [], [],
//             [
//                 'id',
//                 'Name',
//                 'Normal',
//                 'Actual',
//                 'Late',
//                 'Early',
//                 'Absent',
//                 'OT',
//                 'N_Days',
//                 'Weekend',
//                 'Holiday',
//                 'Total Attendance',
//                 'Total Leave'
//             ],

//         ];
//     }
// }
