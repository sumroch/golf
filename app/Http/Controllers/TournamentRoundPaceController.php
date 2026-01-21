<?php

namespace App\Http\Controllers;

use App\Http\Factories\PaceFactory;
use App\Http\Factories\TournamentFactory;
use App\Http\Services\PaceService;
use App\Models\TournamentRound;

class TournamentRoundPaceController extends Controller
{
    /**
     * Display the tournament pace page.
     */
    public function pace($round, PaceService $paceService)
    {
        $tournament = TournamentFactory::get(TournamentRound::where('id', $round)->first());

        $holes = $paceService->holeType($tournament->tournament->course_id, $round);

        return view('admin.pace', [
            'round' => $tournament,
            'tee_one' => $holes[1],
            'tee_ten' => $holes[10],
            'paces' => PaceFactory::callWithDetail($paceService->getCurrentTournamentPace($round)),
        ]);
    }

    /**
     * Display the tournament pace page.
     */
    public function pacePrint($round, PaceService $paceService)
    {
        $tournament = TournamentFactory::get(TournamentRound::where('id', $round)->first());

        $holes = $paceService->holeType($tournament->tournament->course_id, $round);

        return view('admin.print.pace', [
            'round' => $tournament,
            'tee_one' => $holes[1],
            'tee_ten' => $holes[10],
            'paces' => PaceFactory::callWithDetail($paceService->getCurrentTournamentPace($round)),
        ]);
    }
}
