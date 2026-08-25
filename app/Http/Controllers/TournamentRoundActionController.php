<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResumeRequest;
use App\Http\Requests\StartTimeEditRequest;
use App\Http\Services\PaceActionService;
use App\Http\Services\EditStartPacesService;
use App\Models\TournamentRound;
use Carbon\Carbon;

class TournamentRoundActionController extends Controller
{
    /**
     * Pause the tournament.
     */
    public function start($round)
    {
        $tournament = TournamentRound::whereHas('tournament', fn($x) => $x->where('status', 'active'))->with('tournament')->findOrFail($round);

        if ($tournament->status !== 'referee') {
            return redirect()->back()->withErrors(['Error' => 'Cannot start tournament round if the referee is empty.']);
        }

        $check = TournamentRound::whereIn('status', ['active', 'pause'])
            ->whereHas('tournament', function ($query) {
                $query->where('status', 'active');
            })
            ->exists();

        if ($check) {
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
        $tournament = TournamentRound::whereHas('tournament', fn($x) => $x->where('status', 'active'))->findOrFail($round);

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
        $tournament = TournamentRound::whereHas('tournament', fn($x) => $x->where('status', 'active'))->findOrFail($round);

        if ($tournament->status !== 'active') {
            return redirect()->back()->withErrors(['Error' => 'Cannot pause !, this tournament round is not active.']);
        }

        $tournament->update([
            'status' => 'pause',
            'action_date' => now(),
        ]);

        return redirect()->back();
    }

    /**
     * Resume the tournament.
     */
    public function resume(ResumeRequest $request, $round, PaceActionService $paceActionService)
    {
        $tournament = TournamentRound::whereHas('tournament', fn($x) => $x->where('status', 'active'))->findOrFail($round);

        if ($tournament->status !== 'pause') {
            return redirect()->back()->withErrors(['Error' => 'Cannot resume tournament round that is not paused.']);
        }

        $paceActionService->regeneratePace($tournament, $request->date);

        $tournament->update(['status' => 'active', 'date' => $request->date]);

        return redirect()->back();
    }

    /**
     * Change start time of the tournament.
     */
    public function reset(StartTimeEditRequest $request, $round, EditStartPacesService $editStartPacesService)
    {
        $tournament = TournamentRound::findOrFail($round);

        if ($tournament->morning !== $request->morning) {
            $morningMinutes = Carbon::parse($request->morning)->diffInMinutes(Carbon::parse($tournament->morning));

            $tournament->morning = $request->morning;
            $tournament->save();

            $editStartPacesService->reset($tournament->id, 'morning', $morningMinutes);
        }

        if ($tournament->afternoon !== $request->afternoon) {
            $afternoonMinutes = Carbon::parse($request->afternoon)->diffInMinutes(Carbon::parse($tournament->afternoon));

            $tournament->afternoon = $request->afternoon;
            $tournament->save();

            $editStartPacesService->reset($tournament->id, 'afternoon', $afternoonMinutes);
        }

        return redirect()->back();
    }
}
