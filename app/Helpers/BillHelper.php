<?php

namespace App\Helpers;

use Carbon\Carbon;

final class BillHelper
{
    static public function addMonth(Carbon|null $date, int $dayOfMonth = null):Carbon|null
    {

        $newDate = $date ? Carbon::make($date) : null;
        if($newDate != null){
            $newDate->addMonthNoOverflow();
            if($dayOfMonth){
                $newDate->setUnitNoOverflow('day',$dayOfMonth,'month');
            }
        }else{
            return null;
        }
        return $newDate;
    }
}
