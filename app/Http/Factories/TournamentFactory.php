<?php

namespace App\Http\Factories;

use Carbon\Carbon;

class TournamentFactory
{
    public static function get($data)
    {
        $data->tee_area = json_decode($data->tee_area);

        $data->start_interval_hour = date('H', strtotime($data->start_interval));
        $data->start_interval_minute = date('i', strtotime($data->start_interval));

        $data->morning_one_hour = date('H', strtotime($data->morning_one));
        $data->morning_one_minute = date('i', strtotime($data->morning_one));

        $data->morning_ten_hour = date('H', strtotime($data->morning_ten));
        $data->morning_ten_minute = date('i', strtotime($data->morning_ten));

        $data->afternoon_one_hour = date('H', strtotime($data->afternoon_one));
        $data->afternoon_one_minute = date('i', strtotime($data->afternoon_one));

        $data->afternoon_ten_hour = date('H', strtotime($data->afternoon_ten));
        $data->afternoon_ten_minute = date('i', strtotime($data->afternoon_ten));

        $data->crossover_one_hour = date('H', strtotime($data->crossover_one));
        $data->crossover_one_minute = date('i', strtotime($data->crossover_one));

        $data->crossover_ten_hour = date('H', strtotime($data->crossover_ten));
        $data->crossover_ten_minute = date('i', strtotime($data->crossover_ten));

        return $data;
    }

    public static function dashboard($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->tournament->name,
            'location' => $data->tournament->location,
            'organizer' => $data->tournament->organizer,
            'date_start' => Carbon::createFromFormat('Y-m-d', $data->date)->format('d F Y'),
            'round_number' => $data->round_number,
            'course' => $data->tournament->course->name,
            'total_hole' => $data->tournament->course->total_holes,
            'status' => $data->status,
            'tee_area' => count(json_decode($data->tee_area)) ? collect(json_decode($data->tee_area))->map(fn($value) => ucfirst($value))->values()->implode(', ') : '-',
            'ball' => $data->ball ? $data->ball . ' Balls' : '-',
            'transportation' =>  $data->transportation ? ucfirst($data->transportation) : '-',
            'groups' => $data->groups,
            'group_total' => $data->groups->count(),
            'updated_at_date' => Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at, 'UTC')->setTimezone('Asia/Jakarta')->format('Y-m-d'),
            'updated_at_time' => Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at, 'UTC')->setTimezone('Asia/Jakarta')->format('H:i'),
            'rounds' => $data->tournament->rounds->map(function ($round) {
                return [
                    'id' => $round->id,
                    'name' => 'Round ' . $round->round_number,
                ];
            })->toArray(),
        ];
    }

    public static function call($datas)
    {
        return collect($datas)
            ->map(function ($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'status' => $data->status,
                ];
            })->toArray();
    }

    public static function group($datas, $session = null)
    {
        $datas = $datas->load('groups.players');

        $datas->group_maps = $datas->groups->count() == 0
            ? []
            : $datas->groups->when($session !== null, fn($query) => $query->where('session', $session))
            ->map(function($group) {
                $group->time = Carbon::parse($group->time)->format('H:i');
                $group->total_player = $group->players->count();
                return $group;
            })
            ->groupBy('session')
            ->map(function ($groups) {
                return $groups->groupBy('tee');
            });

        return $datas;
    }

    public static function referee($datas)
    {
        $result = [];

        foreach ($datas as $data) {
            if ($data->groups->isNotEmpty()) {
                $tempGroup = [
                    'user_id' => $data->id,
                    'observer_type' => 'group',
                    'observer_id' => [],
                ];

                foreach ($data->groups as $group) {
                    $tempGroup['observer_id'][] = $group->id;
                }

                $result[] = $tempGroup;
            }

            if ($data->tournamentHoles->isNotEmpty()) {
                $tempHole = [
                    'user_id' => $data->id,
                    'observer_type' => 'hole',
                    'observer_id' => [],
                ];

                foreach ($data->tournamentHoles as $hole) {
                    $tempHole['observer_id'][] = $hole->id;
                }

                $result[] = $tempHole;
            }
        }

        return $result;
    }
}
