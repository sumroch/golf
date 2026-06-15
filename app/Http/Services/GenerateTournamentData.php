<?php

namespace App\Http\Services;

use App\Models\Course;
use App\Models\TournamentHole;
use App\Models\TournamentRound;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GenerateTournamentData
{
    public function generatePace(TournamentRound $tournamentRound, Collection $groups, Course $course, Collection $holes): void
    {
        if ($holes->count() == 0)
            $this->generateHoles($course->holes()->orderBy('number')->get(), $tournamentRound->id);

        $orderHoles = $holes;
        $count = $holes->count();

        foreach ($groups as $group) {
            if ($group->tournamentPaces->count() == 0) {

                $previousTime = $group->time;

                $orderHoles = (int) $group->tee !== 1
                    ? $this->orderHoles($holes, (int) $group->tee)
                    : $holes;

                foreach ($orderHoles as $key => $hole) {
                    $start = Carbon::parse($previousTime);
                    $allow = Carbon::parse($hole->allowed_time);

                    $actual = $start->addHour($allow->hour)->addMinute($allow->minute);

                    if (($hole->number == 1 && $key != 0) || ($hole->number == ((int)($count / 2) + 1) && $key != 0)) {
                        $crossover = Carbon::parse($hole->number == 1
                            ? $tournamentRound->crossover_ten
                            : $tournamentRound->crossover_one);

                        $actual = $actual->addMinutes($crossover->minute);
                    }

                    $group->tournamentPaces()->create([
                        'hole_id' => $hole->id,
                        'time' => $actual->format('H:i:s'),
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
