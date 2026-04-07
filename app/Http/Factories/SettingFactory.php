<?php

namespace App\Http\Factories;

use Carbon\Carbon;

class SettingFactory
{
    public static function call($datas)
    {
        foreach ($datas as $data) {
            $data->periode = Carbon::parse($data->date_start)->diffInDays(Carbon::parse($data->date_end)) . ' days';
        }

        return $datas;
    }
}
