<?php

namespace App\Http\Factories;

use App\Models\TournamentPace;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MobileFactories
{
    public static function showMember(Collection $data, $observer_type, $duty_id = null): array
    {
        $data = $data->map(function ($item) use ($observer_type, $duty_id) {

            $progress = 'created';
            $time_diff = '-';
            $finish = null;

            if ($item->finish_at) {
                $finish = Carbon::createFromFormat('Y-m-d H:i:s', $item->finish_at, 'UTC')->setTimezone('Asia/Jakarta');
                $allow = Carbon::parse($finish->copy()->format('Y-m-d') . ' ' . $item->time, 'Asia/Jakarta');


                $time_diff = (int) $allow->diffInMinutes($finish, false);

                $time_diff = ($time_diff >= 0 ? '+' : '') . $time_diff . ' mins';
                $progress = $finish->greaterThan($allow) ? 'late' : 'ontime';
            }

            return [
                'id' => $item->id,
                'duty_id' => $duty_id,
                'name' => $observer_type === 'group' ? 'Hole ' . $item->name : $item->name,
                'time' => Carbon::parse($item->time)->format('H:i'),
                'finish_at' => $finish ?? null,
                'finish_time' => $finish ? $finish->format('H:i') : '-',
                'allowed_time' => Carbon::parse($item->allowed_time)->format('H:i'),
                "status" => $item->status,
                "progress" => $progress,
                "time_diff" => $time_diff,
                "par" => 'Par ' . $item->par,
            ];
        });

        $clone = $data;

        return [
            'all' => $data,
            'first' => $clone->slice(0, 3)->values(),
            'second' => $clone->slice(3)->values(),
        ];
    }
}
