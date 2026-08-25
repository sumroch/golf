<?php

namespace App\Http\Services;

use App\Models\TournamentHole;
use App\Models\TournamentPace;
use App\Models\TournamentRefereeDuty;
use App\Models\User;
use Carbon\Carbon;

class MobileService
{
    public function getAllAccessObserver($tournamentRoundId): ?object
    {
        return TournamentHole::where('tournament_round_id', $tournamentRoundId)->get();
    }

    public function getRefereObserver($userId, $tournamentRoundId): ?object
    {
        return TournamentRefereeDuty::where('tournament_round_id', $tournamentRoundId)
            ->when(request()->user()->hasRole(['referee', 'observer']), function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['observer'])
            ->get();
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

    public function checkObserverIsValid($listObserver, $targetPace): bool
    {
        foreach ($listObserver as $pace) {

            if ($pace->observer_type == 'hole') {
                if ($pace->observer_id == $targetPace?->hole_id) {
                    return true;
                }
            } else {
                if ($pace->observer_id == $targetPace?->group_id) {
                    return true;
                }
            }
        }

        return false;
    }



    public function getListObserverReverse($observerType, $targetPace, $reverse = true)
    {
        $session = Carbon::now('Asia/Jakarta')->greaterThan(Carbon::parse('12:30', 'Asia/Jakarta')) ? 'afternoon' : 'morning';
        return TournamentPace::select('tournament_paces.id', 'tournament_paces.time', 'tournament_paces.status', 'group_id', 'hole_id', 'tournament_holes.number', 'groups.session')
            ->leftJoin('groups', 'tournament_paces.group_id', '=', 'groups.id')
            ->leftJoin('tournament_holes', 'tournament_paces.hole_id', '=', 'tournament_holes.id')
            // ->when($reverse, function ($query) use ($observerType, $targetPace) {
            //     $query->where($observerType == 'hole' ? 'group_id' : 'hole_id', $targetPace?->{$observerType == 'hole' ? 'group_id' : 'hole_id'});
            // }, function ($query) use ($observerType, $targetPace) {
            //     $query->where($observerType == 'hole' ? 'hole_id' : 'group_id', $targetPace?->{$observerType == 'hole' ? 'hole_id' : 'group_id'});
            // })
            ->where('group_id', $targetPace?->group_id)
            // ->where('session', $session)
            ->where('tournament_paces.tournament_round_id', $targetPace?->tournament_round_id)
            ->orderBy('tournament_paces.time', 'asc')
            ->get();
    }

    public function getUserDuties($id, User $user): ?object
    {
        return TournamentRefereeDuty::where('id', $id)
            ->whereHas('round', function ($query) {
                $query->whereIn('status', ['active', 'pause'])
                    ->whereHas('tournament', function ($query) {
                        $query->where('status', 'active');
                    });
            })
            ->when($user->hasRole(['referee', 'observer']), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();
    }

    public function getObserverMember(TournamentRefereeDuty $duty): ?object
    {
        $session = Carbon::now('Asia/Jakarta')->greaterThan(Carbon::parse('13:30', 'Asia/Jakarta')) ? 'afternoon' : 'morning';

        return TournamentPace::when($duty->observer_type === 'hole', function ($query) use ($duty, $session) {
            $query->select('tournament_paces.id', 'groups.name as name', 'tournament_paces.time', 'hole_id', 'groups.tee', 'group_id', 'groups.session', 'groups.group_number', 'tournament_paces.finish_at', 'tournament_paces.status', 'tournament_holes.allowed_time', 'tournament_holes.par')
                ->leftJoin('groups', 'tournament_paces.group_id', '=', 'groups.id')
                ->leftJoin('tournament_holes', 'tournament_paces.hole_id', '=', 'tournament_holes.id')
                ->where('hole_id', $duty->observer_id)
                ->where('tournament_paces.tournament_round_id', $duty->tournament_round_id)
                ->orderBy('tournament_paces.time', 'asc');
        }, function ($query) use ($duty, $session) {
            $query->select('tournament_paces.id', 'tournament_holes.number as name', 'tournament_paces.time', 'hole_id', 'group_id', 'groups.session', 'tournament_paces.finish_at', 'tournament_paces.status', 'tournament_holes.allowed_time', 'tournament_holes.par')
                ->leftJoin('tournament_holes', 'tournament_paces.hole_id', '=', 'tournament_holes.id')
                ->leftJoin('groups', 'tournament_paces.group_id', '=', 'groups.id')
                ->where('group_id', $duty->observer_id)
                ->where('tournament_paces.tournament_round_id', $duty->tournament_round_id)
                ->orderBy('tournament_holes.number', 'asc');
        })
            ->get();
    }
}
