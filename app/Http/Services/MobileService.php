<?php

namespace App\Http\Services;

use App\Models\TournamentPace;
use App\Models\TournamentRefereeDuty;
use App\Models\TournamentRound;
use App\Models\User;
use Carbon\Carbon;

class MobileService
{
    public function getRefereObserver($user_id, $tournament_round_id): ?object
    {
        return TournamentRefereeDuty::where('user_id', $user_id)
            ->where('tournament_round_id', $tournament_round_id)
            ->with(['observer'])
            ->get();
    }

    public function getCurrentHoleWithGroups($tournament_round_id, $hole_id = null): ?object
    {
        return TournamentPace::where('hole_id', $hole_id)
            ->where('tournament_round_id', $tournament_round_id)
            ->get();
    }

    public function getObserverMember($user_id, TournamentRefereeDuty $duty): ?object
    {
        $session = Carbon::now('Asia/Jakarta')->greaterThan(Carbon::parse('12:30', 'Asia/Jakarta')) ? 'afternoon' : 'morning';

        return TournamentPace::when($duty->observer_type === 'hole', function ($query) use ($duty, $session) {
            $query->select('tournament_paces.id', 'groups.name as name', 'tournament_paces.time', 'hole_id', 'group_id', 'tournament_paces.finish_at', 'tournament_paces.status', 'tournament_holes.allowed_time')
                ->leftJoin('groups', 'tournament_paces.group_id', '=', 'groups.id')
                ->leftJoin('tournament_holes', 'tournament_paces.hole_id', '=', 'tournament_holes.id')
                ->where('hole_id', $duty->observer_id) 
                ->where('tournament_paces.tournament_round_id', $duty->tournament_round_id)
                ->where('groups.session', $session)
                ->orderBy('tournament_paces.time', 'asc');
        }, function ($query) use ($duty, $session) {
            $query->select('tournament_paces.id', 'tournament_holes.number as name', 'tournament_paces.time', 'hole_id', 'group_id', 'tournament_paces.finish_at', 'tournament_paces.status', 'tournament_holes.allowed_time')
                ->leftJoin('tournament_holes', 'tournament_paces.hole_id', '=', 'tournament_holes.id')
                ->leftJoin('groups', 'tournament_paces.group_id', '=', 'groups.id')
                ->where('group_id', $duty->observer_id)
                ->where('groups.session', $session)
                ->where('tournament_paces.tournament_round_id', $duty->tournament_round_id)
                ->orderBy('tournament_holes.number', 'asc');
        })
            ->get();
    }
}
