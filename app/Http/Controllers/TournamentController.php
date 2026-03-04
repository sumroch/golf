<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Http\Requests\StoreTournamentRequest;
use App\Models\Course;
use Carbon\Carbon;

class TournamentController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.master.tournament', [
            'tournaments' => Tournament::select('id', 'name', 'location', 'organizer', 'date_start', 'round', 'course_id', 'status')
                ->with('course')
                ->orderBy('id', 'asc')
                ->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.master.tournament-create', [
            'courses' => Course::orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function active($tournament)
    {
        $tournament = Tournament::findOrFail($tournament);
        $tournament->update(['status' => 'active']);

        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTournamentRequest $request)
    {
        $tournament = Tournament::create($request->validated());

        $tournament->rounds()->createMany(
            collect(range(1, $request->input('round')))->map(function ($roundNumber) use ($request) {
                return [
                    'date' => Carbon::parse($request->input('date'))->addDays($roundNumber - 1)->format('Y-m-d'),
                    'round_number' => $roundNumber,
                    'status' => 'setup',
                    'timezone' => $request->input('timezone') ?? 'Asia/Jakarta',
                ];
            })->toArray()
        );

        return redirect()->route('tournament.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function edit($tournament)
    {
        return view('admin.master.tournament-edit', [
            'courses' => Course::orderBy('name', 'asc')->get(),
            'tournament' => Tournament::findOrFail($tournament),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function update(StoreTournamentRequest $request, $tournament)
    {
        $tournament = Tournament::findOrFail($tournament);
        $tournament->update($request->validated());

        $different = $tournament->round - $request->input('round');

        if ($different > 0) {

            $tournament->rounds()
                ->orderBy('round_number', 'asc')
                ->each(function ($round) use ($request) {

                    if (!$round->round_number > $request->input('round')) {
                        $round->groups()->each(function ($group) {
                            $group->players()->delete();
                        });
                        $round->groups()->delete();
                        $round->tournamentRefereeDuty()->delete();
                        $round->tournamentPace()->delete();
                        $round->delete();
                    }

                });
        } elseif ($different < 0) {

            $tournament->rounds()->createMany(
                collect(range($tournament->round + 1, $request->input('round')))->map(function ($roundNumber) use ($request) {
                    return [
                        'date' => Carbon::parse($request->input('date'))->addDays($roundNumber - 1)->format('Y-m-d'),
                        'round_number' => $roundNumber,
                        'status' => 'setup',
                        'timezone' => $request->input('timezone') ?? 'Asia/Jakarta',
                    ];
                })->toArray()
            );
        }

        return redirect()->route('tournament.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($tournament)
    {
        $tournament = Tournament::findOrFail($tournament);

        $tournament->rounds()
            ->orderBy('round_number', 'asc')
            ->each(function ($round) {

                $round->groups()->each(function ($group) {
                    $group->players()->delete();
                });
                $round->groups()->delete();
                $round->tournamentRefereeDuty()->delete();
                $round->tournamentPace()->delete();
                $round->delete();
            });

        $tournament->rounds()->delete();
        $tournament->delete();

        return redirect()->route('tournament.index');
    }
}
