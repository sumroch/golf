<?php

namespace App\Http\Controllers;

use App\Http\Factories\TournamentFactory;
use App\Http\Requests\UpdateGroupHoleRequest;
use App\Http\Services\GenerateTournamentData;
use App\Imports\GroupImport;
use App\Imports\GroupShotgunImport;
use App\Imports\GroupStandardImport;
use App\Models\TournamentHole;
use App\Models\TournamentRound;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

        $tournament = TournamentRound::where('id', $round)->whereHas('tournament', fn($x) => $x->where('status', 'active'))->first();

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

    public function downloadTemplate($round)
    {
        $tournamentRound = TournamentRound::whereHas('tournament', fn($x) => $x->where('status', 'active'))->findOrFail($round);

        return response()->download(
            $tournamentRound->type == 'tee'
                ? asset('document/template_tee.xlsx')
                : asset('document/template_shotgun.xlsx')
        );
    }

    /**
     * Display the tournament group page.
     */
    public function storeGroup($round, Request $request, GenerateTournamentData $generateTournamentData)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $tournamentRound = TournamentRound::whereHas('tournament', fn($x) => $x->where('status', 'active'))->findOrFail($round);

        if ($tournamentRound->status === 'setup') {
            return redirect()->route('round.setup', ['round' => $round])->withErrors(['Error' => 'Cannot import groups for an empty setup tournament round.']);
        }

        if (in_array($tournamentRound->status, ['finish', 'active', 'pause'])) {
            return redirect()->back()->withErrors(['Error' => 'Cannot import groups for a finished, active, or paused tournament round.']);
        }

        if ($tournamentRound->groups->count() > 0) {
            $tournamentRound->groups->each(function ($group) {
                $group->players()->delete();
            });

            $tournamentRound->groups()->delete();

            if ($tournamentRound->tournamentPaces()->count() > 0)
                $tournamentRound->tournamentPaces()->delete();
        }

        try {
            Excel::import(
                new GroupStandardImport(['round_id' => $round, 'file' => $request->file('file')]),
                $request->file('file')
            );
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            dd($failures);

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }

        }


        // Excel::import(
        //     $tournamentRound->type == 'shotgun'
        //         ? new GroupShotgunImport(['round' => $tournamentRound])
        //         : new GroupImport(['round_id' => $round]),
        //     $request->file('file')
        // );

        $tournamentRound->load(['groups.tournamentPaces', 'tournament.course', 'tournamentHoles' => function ($query) {
            $query->orderBy('number', 'asc');
        }]);

        if ($tournamentRound->status !== 'finish') {
            $generateTournamentData->generatePace($tournamentRound, $tournamentRound->groups, $tournamentRound->tournament->course, $tournamentRound->tournamentHoles);
        }

        $tournamentRound->update(['status' => 'pace']);

        return redirect()->back();
    }

    public function updateHole($round, UpdateGroupHoleRequest $request)
    {
        $tournament = TournamentRound::whereHas('tournament', fn($x) => $x->where('status', 'active'))->findOrFail($round);

        if (in_array($tournament->status, ['finish', 'active', 'pause'])) {
            return redirect()->back()->withErrors(['Error' => 'Cannot update holes for a finished, active, or paused tournament round.']);
        }

        if ($tournament->tournamentHoles()->count() == 0) {
            return redirect()->back()->withErrors(['Error' => 'No holes found for this tournament round.']);
        }

        if ($tournament->groups()->count() > 0) {
            return redirect()->back()->withErrors(['Error' => 'Cannot update holes for a tournament round with existing groups. Please delete the groups first.']);
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
        $tournament = TournamentRound::whereHas('tournament', fn($x) => $x->where('status', 'active'))->find($round);

        if (in_array($tournament->status, ['finish', 'active', 'pause'])) {
            return redirect()->back()->withErrors(['Error' => 'Cannot delete groups for a finished, active, or paused tournament round.']);
        }

        $tournament->groups()->delete();
        $tournament->update(['status' => 'group']);

        return redirect()->back();
    }
}
