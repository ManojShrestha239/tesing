<?php
namespace App\Utilities;

use App\Models\Message;
use App\Models\Order;
use App\Models\Payment;
use App\Traits\CustomNepaliCalendarConverter;
use App\Traits\Nepali_Calendar;

class Helpers {
    use Nepali_Calendar,CustomNepaliCalendarConverter;

    public function convertDate($date){
        return $this->custom_AD_to_BS($date);
    }
    public static function todayorders()
    {
        $today = date('Y-m-d');
        $count = Order::where('date', $today)->count();
        return $count;
    }
    public static function todaycollections()
    {
        $today = date('Y-m-d');
        $count = Payment::where('collection_date', $today)->count();
        return $count;
    }
    public static function todaymessages()
    {
        $count = Message::where(['is_read'=> 0])->where('from', '!=' , '0')->groupBy('from')->get('id');
        return count($count);
    }
}
