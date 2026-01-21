<?php

namespace App\Http\Controllers;

use App\Http\Factories\TournamentFactory;
use App\Http\Services\GenerateTournamentData;
use App\Imports\GroupImport;
use App\Models\TournamentRound;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TournamentRoundGroupController extends Controller
{

    /**
     * Display the tournament group page.
     */
    public function group($round, Request $request)
    {
        $request->validate([
            'session' => ['string', 'in:morning,afternoon'],
        ]);

        return view('admin.group', [
            'round' => TournamentFactory::group(TournamentRound::where('id', $round)->first(), $request->input('session', 'morning')),
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
