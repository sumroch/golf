<?php

namespace App\Http\Services;

use App\Models\TournamentPace;
use App\Models\TournamentRound;
use Carbon\Carbon;

class PaceActionService
{
    /**
     * Regenerate pace times when tournament is resumed.
     * 
     * @param TournamentRound $tournamentRound
     * @param string $newStartDateTime - The new resume datetime from user input
     */
    public function regeneratePace(TournamentRound $tournamentRound, string $newStartDateTime): void
    {
        $timezone = $tournamentRound->timezone ?? 'Asia/Jakarta';
        $pausedAt = Carbon::parse($tournamentRound->action_date, 'UTC')->setTimezone($timezone);
        $firstPace = Carbon::parse($pausedAt->copy()->format('Y-m-d') . ' ' . $tournamentRound->tournamentPace()->whereNotIn('status', ['unmonitored', 'finish'])->orderBy('time', 'asc')->first()->time);

        $resumeAt = Carbon::parse($newStartDateTime);

        // Calculate time difference (pause duration)
        $pauseDuration = $firstPace->diffInSeconds($resumeAt, false);

        foreach ($tournamentRound->tournamentPace()->whereNotIn('status', ['unmonitored', 'finish'])->get() as $paces) {
            $paces->update([
                'time' => Carbon::parse($paces->time)
                    ->addSeconds((int) $pauseDuration)
                    ->format('H:i:s'),
            ]);
        }
    }

    /**
     * Order holes starting from a specific hole number (for crossover handling).
     */
    protected function orderHoles($holes, $start = 1): array
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
}
