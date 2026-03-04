<?php

namespace App\Http\Controllers;

use App\Http\Factories\PaceFactory;
use App\Http\Factories\TournamentFactory;
use App\Http\Requests\StoreTournamentRefereeRequest;
use App\Http\Requests\UpdateTournamentRoundRequest;
use App\Http\Services\PaceService;
use App\Models\Group;
use App\Models\TournamentHole;
use App\Models\TournamentRefereeDuty;
use App\Models\TournamentRound;
use App\Models\User;
use Illuminate\Http\Request;

class TournamentRoundController extends Controller
{

    /**
     * Display the tournament dashboard.
     */
    public function dashboard($round, Request $request, PaceService $paceService)
    {
        if (!$request->wantsJson() && !$request->ajax()) {
            return view('admin.dashboard');
        }

        $tournamentRound = TournamentRound::where('id', $round)
            ->with(['groups.players', 'tournament.course', 'tournament.rounds'])
            ->first();

        $tournamentByHole = $paceService->getPacesByHoles($round, $request->input('session', 'morning'));
        $tournamentByTee = $paceService->getPacesByTee($round, $request->input('tee'));

        return $this->apiResponseSuccess(
            [
                'round' => TournamentFactory::dashboard($tournamentRound),
                'holes' => PaceFactory::byHole($tournamentByHole),
                'tees' => PaceFactory::byTee($tournamentByTee),
                'updated_at' => now()->timezone($tournamentRound->tournament->timezone)->format('Y-m-d H:i'),
            ]
        );
    }

    /**
     * Display the tournament dashboard.
     */
    public function dashboardTable($round, PaceService $paceService)
    {
        $tournamentRound = TournamentRound::where('id', $round)->with(['groups.players', 'tournament.course', 'tournament.rounds'])->first();

        $holes = $paceService->holeType($tournamentRound->tournament->course_id, $round);

        return view('admin.dashboard-table', [
            'round' => TournamentFactory::dashboard($tournamentRound),
            'tee_one' => $holes[1],
            'tee_ten' => $holes[10],
            'total_one' => $holes['total_one'],
            'total_ten' => $holes['total_ten'],
            'paces' => PaceFactory::callWithDetail($paceService->getCurrentTournamentPace($round)),
        ]);
    }

    /**
     * Display the tournament dashboard.
     */
    public function dashboardTablePrint($round, PaceService $paceService)
    {
        $tournamentRound = TournamentRound::where('id', $round)
            ->with(['groups.players', 'tournament.course', 'tournament.rounds'])
            ->first();

        $holes = $paceService->holeType($tournamentRound->tournament->course_id, $round);

        return view('admin.print.dashboard', [
            'round' => TournamentFactory::dashboard($tournamentRound),
            'tee_one' => $holes[1],
            'tee_ten' => $holes[10],
            'total_one' => $holes['total_one'],
            'total_ten' => $holes['total_ten'],
            'paces' => PaceFactory::callWithDetail($paceService->getCurrentTournamentPace($round)),
        ]);
    }

    /**
     * Display the tournament setup page.
     */
    public function setup($round)
    {
        return view('admin.setup', [
            'round' => TournamentFactory::get(TournamentRound::where('id', $round)->first()),
        ]);
    }

    /**
     * Display the tournament setup page.
     */
    public function storeSetup($round, UpdateTournamentRoundRequest $request)
    {
        $tournament = TournamentRound::findOrFail($round);
        $reqs = $request->only(['start_interval', 'morning', 'afternoon', 'crossover_one', 'crossover_ten', 'ball', 'transportation', 'timezone']);

        if ($tournament->status === 'setup') {
            $reqs['status'] = 'group';
        }

        $tournament->update($reqs);

        return redirect()->route('round.group', $round);
    }

    /**
     * Display the tournament referee page.
     */
    public function referee($round)
    {
        return view('admin.referee', [
            'round' => TournamentFactory::get(TournamentRound::where('id', $round)->first()),
            'listRefereeDuties' => TournamentFactory::referee(
                User::with(['groups', 'tournamentHoles'])->whereHas('refereeDuties', function ($query) use ($round) {
                    $query->where('tournament_round_id', $round)
                        ->whereHasMorph('observer', [Group::class, TournamentHole::class]);
                })
                    ->get()
            ),
            'referees' => User::select('id', 'name')->role(['referee', 'observer'])->paginate(10),
            'groups' => Group::where('tournament_round_id', $round)->pluck('name', 'id'),
            'holes' => TournamentHole::whereHas('tournamentPaces', function ($query) use ($round) {
                $query->where('tournament_round_id', $round);
            })->orderBy('number', 'asc')->get(),
        ]);
    }

    /**
     * Display the tournament group page.
     */
    public function storeReferee($round, StoreTournamentRefereeRequest $request)
    {
        $tournamentRound = TournamentRound::findOrFail($round);

        if ($tournamentRound->status !== 'pace' && !in_array($tournamentRound->status, ['finish', 'active', 'pause'])) {
            return redirect()->route('round.setup', ['round' => $round])->withErrors(['Error' => 'Cannot assign referees for a tournament round that is not in referee status.']);
        }

        if (in_array($tournamentRound->status, ['finish', 'active', 'pause'])) {
            return redirect()->back()->withErrors(['Error' => 'Cannot assign referees for a finished, active, or paused tournament round.']);
        }

        TournamentRefereeDuty::where('tournament_round_id', $round)->delete();

        $data = [];
        foreach ($request->referees as $referee) {
            foreach ($referee['observer_id'] as $observerId) {
                $data[] = [
                    'tournament_round_id' => $round,
                    'user_id' => $referee['user_id'],
                    'observer_type' => $referee['observer_type'],
                    'observer_id' => $observerId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $tournamentRound->update(['status' => 'referee']);

        TournamentRefereeDuty::insert($data);

        return redirect()->route('dashboard', ['round' => $round]);
    }
}
