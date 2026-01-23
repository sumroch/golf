<?php

namespace App\Http\Services;

use App\Models\TournamentHole;
use App\Models\TournamentRound;
use Carbon\Carbon;

class GenerateTournamentData
{
    public function generatePace(TournamentRound $tournamentRound): void
    {
        $course = $tournamentRound->tournament->course;

        if ($tournamentRound->tournamentHoles()->count() == 0) {
            $this->generateHoles($course->holes()->orderBy('number')->get(), $tournamentRound->id);
        }

        $holes = $tournamentRound->tournamentHoles()->orderBy('number', 'asc')->get();

        $orderHoles = $holes;

        foreach ($tournamentRound->groups as $group) {
            if ($group->tournamentPaces->count() == 0) {

                $previousTime = $group->time;

                if ((int) $group->tee !== 1) {
                    $orderHoles = $this->orderHoles($holes, (int) $group->tee);
                } else {
                    $orderHoles = $holes;
                }

                foreach ($orderHoles as $hole) {
                    $start = Carbon::parse($previousTime);
                    $allow = Carbon::parse($hole->allowed_time);

                    $actual = $start->addHour($allow->hour)->addMinute($allow->minute)->format('H:i:s');

                    $group->tournamentPaces()->create([
                        'hole_id' => $hole->id,
                        'time' => $actual,
                        'type' => 'tee',
                        'tournament_round_id' => $tournamentRound->id,
                    ]);

                    $previousTime = $actual;
                }
            }
        }
    }

    protected function orderHoles($holes, $start = 1)
    {
        $bigger = [];
        $lower = [];

        foreach ($holes as $hole) {
            if ($hole->number >= $start) {
                $bigger[] = $hole;
            } else {
                $lower[] = $hole;
            }
        }

        return array_merge($bigger, $lower);
    }

    public function generateHoles($holes, $round)
    {
        if (TournamentHole::where('tournament_round_id', $round)->count() == 0) {
            $cloneHoles = [];
    
            foreach ($holes as $hole) {
                $cloneHoles[] = [
                    'tournament_round_id' => $round,
                    'number' => $hole->number,
                    'allowed_time' => $hole->allowed_time,
                    'par' => $hole->par,
                    'course_id' => $hole->course_id,
                    'updated_at' => $hole->updated_at,
                    'created_at' => $hole->created_at,
                ];
            }
    
            TournamentHole::insert($cloneHoles);
        }
        
    }
}
