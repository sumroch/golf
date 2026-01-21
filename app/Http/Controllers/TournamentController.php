<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Http\Requests\StoreTournamentRequest;
use App\Http\Requests\UpdateTournamentRequest;
use App\Http\Services\CheckTournament;
use App\Models\Course;
use App\Models\TournamentRefereeDuty;
use App\Models\TournamentRound;
use Carbon\Carbon;

class TournamentController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.master.tournament', [
            'courses' => Course::orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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

        return redirect()->route('round.setup', $tournament->rounds->first()->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tournament $tournament)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tournament $tournament)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTournamentRequest $request, Tournament $tournament)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tournament $tournament)
    {
        //
    }
}
