<?php

namespace App\Http\Services;

use App\Models\Group;
use App\Models\Hole;
use App\Models\Tournament;
use App\Models\TournamentHole;
use App\Models\TournamentPace;
use Carbon\Carbon;

class PaceService
{
    public function getCurrentTournamentPace($round): ?object
    {
        return Group::select('id', 'name', 'session', 'tee', 'time', 'tournament_round_id')
            ->where('tournament_round_id', $round)
            ->with(['tournamentPaces' => function ($query) {
                $query->select('tournament_paces.time', 'finish_at', 'group_id', 'tournament_paces.status', 'tournament_holes.number as number', 'tournament_rounds.date', 'tournament_holes.allowed_time')
                    ->join('tournament_holes', 'tournament_paces.hole_id', '=', 'tournament_holes.id')
                    ->join('tournament_rounds', 'tournament_paces.tournament_round_id', '=', 'tournament_rounds.id')
                    ->orderBy('tournament_holes.number', 'asc');
            }, 'players'])
            ->orderBy('id', 'asc')
            ->get();
    }

    public function holeType($course_id, $round): array
    {
        $holes = TournamentHole::where('tournament_round_id', $round)->where('course_id', $course_id)->orderBy('number', 'asc')->get();

        $holes->map(function ($hole) {
            $hole->allowed_time = Carbon::parse($hole->allowed_time)->format('H:i');
            return $hole;
        });

        return [
            1 => $holes->where('number', '<=', 9),
            10 => $holes->where('number', '>', 9),
        ];
    }

    public function getPacesByHoles($tournament_round_id, $session = 'morning'): ?object
    {
        return TournamentPace::select('tournament_paces.time', 'type', 'finish_at', 'status', 'group_id', 'groups.name', 'tournament_holes.number as hole_number', 'tournament_holes.allowed_time')
            ->join('tournament_holes', 'tournament_paces.hole_id', '=', 'tournament_holes.id')
            ->join('groups', 'tournament_paces.group_id', '=', 'groups.id')
            ->where('tournament_paces.tournament_round_id', $tournament_round_id)
            ->whereIn('tournament_paces.status', ['unmonitored', 'finish'])
            ->when(in_array($session, ['morning', 'afternoon']), fn($query) => $query->where('groups.session', $session))
            ->orderBy('tournament_holes.number', 'asc')
            ->get();
    }

    public function getPacesByTee($tournament_round_id, $tee = null): ?object
    {
        return TournamentPace::select('tournament_paces.time', 'type', 'finish_at', 'status', 'groups.name', 'groups.tee', 'tournament_holes.number as hole_number', 'tournament_holes.allowed_time')
            ->join('tournament_holes', 'tournament_paces.hole_id', '=', 'tournament_holes.id')
            ->join('groups', 'tournament_paces.group_id', '=', 'groups.id')
            ->where('tournament_paces.tournament_round_id', $tournament_round_id)
            ->when(in_array($tee, [1, 10]), fn($query) => $query->where('groups.tee', $tee))
            ->where('tournament_paces.time', '<', Carbon::now()->timezone('Asia/Jakarta')->addMinutes(15)->format('H:i:s'))
            ->where('tournament_paces.time', '>', Carbon::now()->timezone('Asia/Jakarta')->subMinutes(15)->format('H:i:s'))
            ->whereNotIn('tournament_paces.status', ['unmonitored', 'finish'])
            ->orderBy('tournament_paces.time', 'desc')
            ->get();
    }
}
