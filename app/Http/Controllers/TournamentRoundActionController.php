<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResumeRequest;
use App\Http\Services\PaceActionService;
use App\Models\TournamentRound;
use Carbon\Carbon;

class TournamentRoundActionController extends Controller
{
    /**
     * Pause the tournament.
     */
    public function start($round)
    {
        $tournament = TournamentRound::findOrFail($round);

        if ($tournament->status !== 'referee') {
            return redirect()->back()->withErrors(['Error' => 'Cannot start tournament round if the referee is empty.']);
        }

        if (TournamentRound::whereIn('status', ['active', 'pause'])->exists()) {
            return redirect()->back()->withErrors(['Error' => 'Another tournament round is already active.']);
        }

        $tournament->update(['status' => 'active']);

        return redirect()->back();
    }

    /**
     * Stop the tournament.
     */
    public function stop($round)
    {
        $tournament = TournamentRound::findOrFail($round);

        if ($tournament->status !== 'active') {
            return redirect()->back()->withErrors(['Error' => 'Cannot stop tournament round that is not active.']);
        }

        $tournament->update(['status' => 'finish']);

        return redirect()->back();
    }

    /**
     * Pause the tournament.
     */
    public function pause($round)
    {
        $tournament = TournamentRound::findOrFail($round);

        if ($tournament->status !== 'active') {
            return redirect()->back()->withErrors(['Error' => 'Cannot pause tournament round that is not active.']);
        }

        $tournament->update(['status' => 'pause']);

        return redirect()->back();
    }

    /**
     * Resume the tournament.
     */
    public function resume(ResumeRequest $request, $round, PaceActionService $paceActionService)
    {
        $tournament = TournamentRound::findOrFail($round);

        // dd(Carbon::parse($request->date, $tournament->timezone ?? 'Asia/Jakarta')->format('Y-m-d H:i:s'));

        if ($tournament->status !== 'pause') {
            return redirect()->back()->withErrors(['Error' => 'Cannot resume tournament round that is not paused.']);
        }

        $paceActionService->regeneratePace($tournament);

        $tournament->update(['status' => 'active', 'date' => $request->date]);

        return redirect()->back();
    }
}
