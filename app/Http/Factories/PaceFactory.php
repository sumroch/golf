<?php

namespace App\Http\Factories;

use Carbon\Carbon;

class PaceFactory
{
    public static function call($datas, $session = null)
    {
        $data = collect($datas);

        $data = $session !== null
            ? $data->where('session', $session)
            : $data->groupBy('session');

        return $data->map(function ($session) {
            return [
                1 => [self::formatPace($session, 1, 'early'), self::formatPace($session, 10, 'early')],
                10 => [self::formatPace($session, 10, 'late'), self::formatPace($session, 1, 'late')]
            ];
        });
    }

    public static function callWithDetail($datas, $type = 'tee')
    {
        $data = collect($datas)->groupBy('session');

        return $data->map(function ($session) use ($type) {
            return [
                1 => [self::formatPace($session, 1, 'early', $type), self::formatPace($session, 10, 'early', $type)],
                10 => [self::formatPace($session, $type == 'tee' ? 10 : 1, 'late', $type), self::formatPace($session, $type == 'tee' ? 1 : 10, 'late', $type)]
            ];
        });
    }

    protected static function formatPace($data, $tee = 1, $paceComparison = 'early', $type = 'tee')
    {
        return $data
            ->when($type == 'tee', function ($query) use ($tee) {
                return $query->where('tee', $tee);
            }, function ($query) use ($tee) {
                return $query->where('tee', $tee == 1 ? '<=' : '>', 9);
            })
            ->map(function ($group) use ($paceComparison, $type) {
                $paceData = $group->tournamentPaces
                    ->where('number', $paceComparison == 'early' ? '<=' : '>', 9)
                    ->map(function ($pace) {

                        $pace->progress = null;
                        $pace->time_diff = '-';
                        $pace->finish_class = '';
                        $pace->finish_text_class = '';
                        $allow = Carbon::parse($pace->date . ' ' . $pace->time, 'Asia/Jakarta');

                        $pace->progress_class = 'bg-white';

                        if ($pace->finish_at && $pace->status != 'unmonitored') {

                            $finish = Carbon::createFromFormat('Y-m-d H:i:s', $pace->finish_at, 'UTC')->setTimezone('Asia/Jakarta');

                            $pace->time_diff_float = $allow->diffInMinutes($finish, false);
                            $pace->time_diff = (int) ceil($pace->time_diff_float);
                            $pace->time_diff_integer = ($pace->time_diff > 0 ? '+' : '') . $pace->time_diff;
                            $pace->time_diff = '( ' . ($pace->time_diff > 0 ? '+' : '') . $pace->time_diff . ' mins )';

                            if ($pace->time_diff_float <= 0) {
                                $pace->progress = 'ontime';
                                $pace->finish_class = 'bg-green-600';
                                $pace->finish_text_class = 'text-green-600';
                            } elseif ($pace->time_diff_float > 0 && $pace->time_diff_float <= 3) {
                                $pace->progress = 'late';
                                $pace->finish_class = 'bg-yellow-400';
                                $pace->finish_text_class = 'text-yellow-400';
                            } elseif ($pace->time_diff_float > 3) {
                                $pace->progress = 'overdue';
                                $pace->finish_class = 'bg-red-500';
                                $pace->finish_text_class = 'text-red-500';
                            }

                            $pace->finish_at = $finish->ceilMinute()->format('H:i');

                            $pace->progress_class = 'bg-gray-300/50';
                        }

                        $now = Carbon::now()->timezone('Asia/Jakarta');

                        if ($allow->diffInMinutes($now, false) > -10 && $allow->diffInMinutes($now, false) < 10) {
                            $pace->progress_class = 'bg-red-200 text-red-800';
                        }

                        if ($pace->status == 'unmonitored') {
                            $pace->progress_class = 'bg-red-700';
                            $pace->finish_at = null;
                        }

                        $pace->time = Carbon::parse($pace->time)->format('H:i');

                        return $pace;
                    })->values();

                $allow = Carbon::parse($paceData[0]->allowed_time);

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'time' => $type == 'tee' ? Carbon::parse($paceData[0]->time)->subMinutes($allow->minute)->format('H:i') : Carbon::parse($group->time)->format('H:i'),
                    'tee' => $group->tee,
                    'session' => $group->session,
                    'players' => $group->players,
                    'paces' => $paceData,
                ];
            })->values();
    }

    public static function byHole($datas)
    {
        $result = collect($datas)
            ->map(function ($data) {

                $data->keys = 'Hole ' . $data->hole_number;
                $data->name = $data->group_name;

                return $data;
            })
            ->groupBy('hole_number')
            ->map(function ($items) {

                return $items->sortBy('group_id')
                    ->values()
                    ->map(function ($data) {
                        return self::formatPaceByData($data);
                    });
            })
            ->toArray();

        return $result;
    }

    public static function byGroup($datas)
    {
        $result = collect($datas)
            ->map(function ($data) {

                $data->keys = $data->group_name;
                $data->name = 'Hole ' . $data->hole_number;

                return $data;
            })
            ->groupBy('group_name')
            ->map(function ($items) {

                return $items->sortBy('hole_number')
                    ->values()
                    ->map(function ($data) {
                        return self::formatPaceByData($data);
                    });
            })
            ->toArray();
        return $result;
    }

    protected static function formatPaceByData($data)
    {
        $data->progress = null;
        $data->time_diff = '-';

        if ($data->finish_at) {

            $finish = Carbon::createFromFormat('Y-m-d H:i:s', $data->finish_at, 'UTC')->setTimezone('Asia/Jakarta');

            $allow = Carbon::parse($finish->copy()->format('Y-m-d') . ' ' . $data->time, 'Asia/Jakarta');
            $data->time_diff_float = $allow->diffInMinutes($finish, false);
            $data->time_diff = (int) ceil($data->time_diff_float);
            $data->time_diff = '( ' . ($data->time_diff > 0 ? '+' : '') . $data->time_diff . ' mins )';

            if ($data->time_diff_float <= 0) {
                $data->progress = 'ontime';
            } elseif ($data->time_diff_float > 0 && $data->time_diff_float <= 3) {
                $data->progress = 'late';
            } elseif ($data->time_diff_float > 3) {
                $data->progress = 'overdue';
            }

            $data->finish_at = $finish->ceilMinute()->format('H:i');
        }

        return $data;
    }


    public static function byTee($datas)
    {
        $result = collect($datas)
            ->map(function ($data) {


                $data->progress = null;
                $data->time_diff = '-';


                $finish = $data->finish_at
                    ? Carbon::createFromFormat('Y-m-d H:i:s', $data->finish_at, 'UTC')->setTimezone('Asia/Jakarta')
                    : Carbon::now()->timezone('Asia/Jakarta');

                $allow = Carbon::parse($finish->copy()->format('Y-m-d') . ' ' . $data->time, 'Asia/Jakarta');
                $data->time_diff_float = $allow->diffInMinutes($finish, false);
                $data->time_diff_number = (int) ceil($data->time_diff_float);
                $data->time_percentage = max(0, min(100, round((($data->time_diff_number + 15) / 15) * 100)));
                $data->time_diff = ($data->time_diff_number > 0 ? '+' : '') . $data->time_diff_number . ' (' . $finish->format('H:i') . ')';

                if ($data->time_diff_float <= 0) {
                    $data->progress = 'ontime';
                } elseif ($data->time_diff_float > 0 && $data->time_diff_float <= 3) {
                    $data->progress = 'late';
                } elseif ($data->time_diff_float > 3) {
                    $data->progress = 'overdue';
                }

                if ($data->finish_at) {
                    $data->finish_at = $finish->ceilMinute()->format('H:i');
                }

                $data->allowed_time = Carbon::parse($data->allowed_time)->format('H:i');
                $data->time = Carbon::parse($data->time)->format('H:i');

                return $data;
            })
            ->toArray();

        return $result;
    }
}
