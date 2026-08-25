<?php

namespace App\Http\Services;

use App\Models\Group;
use App\Models\TournamentHole;
use App\Models\TournamentPace;
use App\Models\TournamentRefereeDuty;
use App\Models\User;
use Carbon\Carbon;

class MobileAllAccessService
{
    public function getAllObserver($tournamentRoundId, $observerType): ?object
    {
        // $group = Group::get();
        // foreach ($group as $g) {
        //     $g->group_number = (int)(explode(' ', $g->name)[1] ?? null);
        //     // dd($g->group_number);
        //     $g->save();
        // }

        return $observerType == 'hole'
            ? TournamentHole::where('tournament_round_id', $tournamentRoundId)->get()
            : Group::where('tournament_round_id', $tournamentRoundId)->get();
    }

    public function getCurrentHoleWithGroups($tournamentRoundId, $holeId = null): ?object
    {
        return TournamentPace::where('hole_id', $holeId)
            ->where('tournament_round_id', $tournamentRoundId)
            ->get();
    }

    public function actionObserver($listObserver, $targetPace): bool
    {
        $now = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        foreach ($listObserver as $pace) {

            if (Carbon::parse($now . ' ' . $pace->time)->greaterThan(Carbon::parse($now . ' ' . $targetPace->time))) {
                continue;
            }

            if ($pace->status !== 'finish' && $pace->status !== 'unmonitored') {
                $pace->status = 'unmonitored';
                $pace->save();
            }
        }

        return true;
    }

    public function getListObserverReverse($observerType, $targetPace, $reverse = true)
    {
        $session = Carbon::now('Asia/Jakarta')->greaterThan(Carbon::parse('12:30', 'Asia/Jakarta')) ? 'afternoon' : 'morning';
        return TournamentPace::select('tournament_paces.id', 'tournament_paces.time', 'tournament_paces.status', 'group_id', 'hole_id', 'tournament_holes.number', 'groups.session')
            ->leftJoin('groups', 'tournament_paces.group_id', '=', 'groups.id')
            ->leftJoin('tournament_holes', 'tournament_paces.hole_id', '=', 'tournament_holes.id')
            ->where('group_id', $targetPace?->group_id)
            ->where('session', $session)
            ->where('tournament_paces.tournament_round_id', $targetPace?->tournament_round_id)
            ->orderBy('tournament_paces.time', 'asc')
            ->get();
    }

    public function getObserverMember($observerType, $tournamentRoundId, $observerId): ?object
    {
        $session = Carbon::now('Asia/Jakarta')->greaterThan(Carbon::parse('12:30', 'Asia/Jakarta')) ? 'afternoon' : 'morning';

        return TournamentPace::when($observerType === 'hole', function ($query) use ($tournamentRoundId, $observerId) {
            $query->select('tournament_paces.id', 'groups.name as name', 'tournament_paces.time', 'hole_id', 'groups.tee', 'group_id', 'groups.session', 'groups.group_number', 'tournament_paces.finish_at', 'tournament_paces.status', 'tournament_holes.allowed_time', 'tournament_holes.par')
                ->leftJoin('groups', 'tournament_paces.group_id', '=', 'groups.id')
                ->leftJoin('tournament_holes', 'tournament_paces.hole_id', '=', 'tournament_holes.id')
                ->where('hole_id', $observerId)
                ->where('tournament_paces.tournament_round_id', $tournamentRoundId)
                ->orderBy('tournament_paces.time', 'asc');
        }, function ($query) use ($tournamentRoundId, $observerId) {
            $query->select('tournament_paces.id', 'tournament_holes.number as name', 'tournament_paces.time', 'hole_id', 'groups.tee', 'group_id', 'groups.session', 'groups.group_number', 'tournament_paces.finish_at', 'tournament_paces.status', 'tournament_holes.allowed_time', 'tournament_holes.par')
                ->leftJoin('tournament_holes', 'tournament_paces.hole_id', '=', 'tournament_holes.id')
                ->leftJoin('groups', 'tournament_paces.group_id', '=', 'groups.id')
                ->where('group_id', $observerId)
                ->where('tournament_paces.tournament_round_id', $tournamentRoundId)
                ->orderBy('tournament_holes.number', 'asc');
        })
            ->get();
    }
}
