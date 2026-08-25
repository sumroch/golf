<?php

namespace App\Http\Services;

use App\Models\TournamentPace;
use Carbon\Carbon;

class EditStartPacesService
{
    public function reset($round, $session, $minutes)
    {

        $data = TournamentPace::select('tournament_paces.*', 'groups.session')
            ->join('groups', 'tournament_paces.group_id', '=', 'groups.id')
            ->where('tournament_paces.tournament_round_id', $round)
            ->where('groups.session', $session)
            ->orderBy('tournament_paces.time', 'asc')
            ->get();

        foreach ($data as $item) {
            $item->time = Carbon::parse($item->time)->addMinutes((int) $minutes)->format('H:i:s');
            $item->save();
        }

        return true;
    }
}
