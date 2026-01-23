<?php

namespace App\Http\Controllers;

use App\Http\Factories\TournamentFactory;
use App\Http\Services\GenerateTournamentData;
use App\Imports\GroupImport;
use App\Models\TournamentHole;
use App\Models\TournamentRound;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TournamentRoundGroupController extends Controller
{

    /**
     * Display the tournament group page.
     */
    public function group($round, Request $request, GenerateTournamentData $generateTournamentData)
    {
        $request->validate([
            'session' => ['string', 'in:morning,afternoon'],
        ]);

        $tournament = TournamentRound::where('id', $round)->first();

        if ($tournament->tournamentHoles()->count() == 0) {
            $generateTournamentData->generateHoles($tournament->tournament->course->holes()->orderBy('number')->get(), $tournament->id);
        }

        $holes = $tournament->tournamentHoles()->orderBy('number')->get()
            ->map(function ($hole) {
                return [
                    'id' => $hole->id,
                    'number' => 'Hole ' . $hole->number,
                    'par' => $hole->par,
                    'allowed_time' => Carbon::parse($hole->allowed_time)->format('i'),
                ];
            });

        return view('admin.group', [
            'round' => TournamentFactory::group($tournament, $request->input('session', 'morning')),
            'holes' => $holes,
        ]);
    }

    /**
     * Display the tournament group page.
     */
    public function storeGroup($round, Request $request, GenerateTournamentData $generateTournamentData)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx'],
        ]);

        $tournamentRound = TournamentRound::findOrFail($round);

        if ($tournamentRound->status === 'setup') {
            return redirect()->route('round.setup', ['round' => $round])->withErrors(['Error' => 'Cannot import groups for an empty setup tournament round.']);
        }

        if (in_array($tournamentRound->status, ['finish', 'active', 'pause'])) {
            return redirect()->back()->withErrors(['Error' => 'Cannot import groups for a finished, active, or paused tournament round.']);
        }

        if ($tournamentRound->groups->count() > 0) {
            $tournamentRound->groups()->delete();
            $tournamentRound->tournamentPaces()->delete();
        }

        Excel::import(
            new GroupImport(['round_id' => $round]),
            $request->file('file')
        );

        $generateTournamentData->generatePace(
            TournamentRound::where('id', $round)->where('status', '!=', 'finish')->with(['groups.players', 'groups.tournamentPaces', 'tournament.course'])->first()
        );

        $tournamentRound->update(['status' => 'pace']);

        return redirect()->back();
    }

    public function updateHole($round, Request $request)
    {
        $tournament = TournamentRound::find($round);

        if (in_array($tournament->status, ['finish', 'active', 'pause'])) {
            return redirect()->back()->withErrors(['Error' => 'Cannot update holes for a finished, active, or paused tournament round.']);
        }

        foreach ($request->holes as $hole) {
            TournamentHole::where('id', $hole['id'])
                ->where('tournament_round_id', $round)
                ->update([
                    'par' => $hole['par'],
                    'allowed_time' => '00:' . str_pad($hole['allowed_time'], 2, '0', STR_PAD_LEFT) . ':00',
                ]);
        }

        return redirect()->back();
    }

    /**
     * Display the tournament group page.
     */
    public function deleteGroup($round)
    {
        $tournament = TournamentRound::find($round);

        if (in_array($tournament->status, ['finish', 'active', 'pause'])) {
            return redirect()->back()->withErrors(['Error' => 'Cannot delete groups for a finished, active, or paused tournament round.']);
        }

        $tournament->groups()->delete();
        $tournament->update(['status' => 'group']);

        return redirect()->back();
    }
}
