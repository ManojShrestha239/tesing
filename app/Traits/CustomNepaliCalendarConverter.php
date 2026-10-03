<?php

namespace App\Traits;

use App\Traits\Nepali_Calendar;

trait CustomNepaliCalendarConverter
{
    use Nepali_Calendar;

    public function custom_AD_to_BS($date)
    {
        $d = explode('-', $date);
        $currentYear = $d[0];
        $currentMonth = $d[1];
        $currentDay = $d[2];
        $currentNepaliDay = $this->AD_to_BS($currentYear, $currentMonth, $currentDay);
        $nepaliDay = $currentNepaliDay['date'];
        $nepaliMth = $currentNepaliDay['month'];
        if (strlen($nepaliMth) == 1) {
            $nepaliMth = '0' . $nepaliMth;
        }
        if (strlen($nepaliDay) == 1) {
            $nepaliDay = '0' . $nepaliDay;
        }
        $nepaliYr = $currentNepaliDay['year'];
        return $nepalidate = $nepaliYr . '-' . $nepaliMth . '-' . $nepaliDay;
    }
    public function custom_BS_to_AD($date)
    {
        $d = explode('-', $date);
        $engDay = $this->BS_to_AD($d[0], $d[1], $d[2]);
        $nepaliDay = $engDay['date'];
        $c = strlen($nepaliDay);
        if (!($c == 2)) {
            $nepaliDay =  '0' . $nepaliDay;
        }
        $nepaliMth = $engDay['month'];
        if (!(strlen($nepaliDay) == 2)) {
            $nepaliDay =  '0' . $nepaliDay;
        }
        $nepaliYr = $engDay['year'];
        return $english = $nepaliYr . '-' . $nepaliMth . '-' . $nepaliDay;
    }

    private function getFiscalYear(): string
    {
        // Get today's exact BS Date (Assuming format: "YYYY-MM-DD")
        $bsDate = $this->custom_AD_to_BS(now()->format('Y-m-d'));

        // Split the date to extract Year and Month
        // (Handles both "YYYY-MM-DD" and "YYYY/MM/DD" formats)
        $parts = preg_split('/[-\/]/', $bsDate);
        $bsYear = (int)$parts[0];
        $bsMonth = (int)$parts[1];

        // Fiscal year starts on Shrawan 1 (Month 04)
        if ($bsMonth >= 4) {
            // Month 4 to 12 (Shrawan to Chaitra) -> Belongs to Current / Next Year
            $fyStart = $bsYear;
            $fyEnd = $bsYear + 1;
        } else {
            // Month 1 to 3 (Baisakh to Ashadh) -> Belongs to Previous / Current Year
            $fyStart = $bsYear - 1;
            $fyEnd = $bsYear;
        }

        // Format to "YY/YY" (e.g., 2082 -> 82, 2083 -> 83)
        $firstPart = substr($fyStart, -2);
        $secondPart = substr($fyEnd, -2);

        return "{$firstPart}/{$secondPart}";
    }

    private function checkingNepaliDate(string $bsDate)
    {
        $now = now()->format('Y-m-d');
        $currentNepaliDate = $this->custom_AD_to_BS($now);
        if ($bsDate == $currentNepaliDate) {
            return true;
        }
    }

    private function isFutureNepaliDate(string $bsDate)
    {
        $now = now()->format('Y-m-d');
        $currentNepaliDate = $this->custom_AD_to_BS($now);

        if ($bsDate > $currentNepaliDate) {
            return true;
        }
    }
    private function todayNepaliDate()
    {
        $now = now()->format('Y-m-d');
        return $currentNepaliDate = $this->custom_AD_to_BS($now);
    }



}
