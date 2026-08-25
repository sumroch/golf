<?php

namespace App\Http\Factories;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class MobileFactories
{
    public static function showMember(Collection $data, string $observer_type, $duty_id = null): Collection
    {
        $isGroup = $observer_type === 'group';

        return $data->map(function ($item) use ($duty_id, $isGroup) {

            $progress = 'created';
            $timeDiffLabel = '-';
            $finish = null;
            $finish_text_class = null;

            if ($item->finish_at) {
                $finish = Carbon::createFromFormat('Y-m-d H:i:s', $item->finish_at, 'UTC')->setTimezone('Asia/Jakarta');
                $allow = Carbon::parse($finish->copy()->format('Y-m-d') . ' ' . $item->time, 'Asia/Jakarta');


                $time_diff_float = $allow->diffInMinutes($finish, false);
                $time_diff = (int) ceil($time_diff_float);



                if ($item->status == 'unmonitored') {
                    $progress = 'unmonitored';
                    $finish_text_class = 'text-red-700';
                } elseif ($time_diff_float <= 0) {
                    $progress = 'ontime';
                    $finish_text_class = 'text-green-600';
                } elseif ($time_diff_float > 0 && $time_diff_float <= 3) {
                    $progress = 'late';
                    $finish_text_class = 'text-yellow-400';
                } elseif ($time_diff_float > 3) {
                    $progress = 'overdue';
                    $finish_text_class = 'text-red-500';
                }

                $timeDiffLabel = ($time_diff > 0 ? '+' : '') . $time_diff . ' mins';
            }

            return [
                'id' => $item->id,
                'group_id' => $item->group_id ?? null,
                'duty_id' => $duty_id,
                'name' => $isGroup ? 'Hole ' . $item->name : $item->name,
                'number' => $isGroup ? $item->name : $item->group_number,
                'time' => Carbon::parse($item->time)->format('H:i'),
                'finish_at' => $finish ?? null,
                'finish_time' => $finish ? $finish->copy()->ceilMinute()->format('H:i') : '-',
                'allowed_time' => Carbon::parse($item->allowed_time)->format('H:i'),
                "status" => $item->status,
                "progress" => $progress,
                'observer_type' => $isGroup ? 'Hole' : 'Group',
                "time_diff" => $timeDiffLabel,
                "par" => 'Par ' . $item->par,
                'finish_text_class' => $finish_text_class,
            ];
        });
    }

    public static function groups(Collection $data, string $observer_type): Collection
    {
        $isGroup = $observer_type === 'group';
        
        return $data->groupBy('session')
            ->map(function ($group) use ($isGroup) {
                return $group->groupBy('tee')
                    ->map(function ($teeGroup) use ($isGroup, $group) {
                        return $teeGroup->map(function ($item, $index) use ($isGroup, $teeGroup) {
                            return [
                                'id' => $item->id,
                                'name' => $isGroup ? 'Hole ' . $item->name : $item->name,
                                'tee' => $item->tee,
                                'head' => $index === 0 ? 'H' . $item->tee : '',
                                'tail' => $index === count($teeGroup) - 1 ? 'T' . $item->tee : '',
                            ];
                        });
                    });
            })->values();
    }
}
