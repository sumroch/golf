<?php

namespace App\Http\Controllers;

use App\Models\TournamentPace;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TournamentTestController extends Controller
{

    /**
     * Reset the tournament pace times.
     */
    public function reset(Request $request)
    {
        if (!$request->filled('round_id')) {
            return response()->json(['message' => 'Round ID is required.'], 400);
        }

        $data = TournamentPace::select('tournament_paces.*', 'groups.session')
            ->join('groups', 'tournament_paces.group_id', '=', 'groups.id')
            ->when($request->filled('round_id'), function ($query) use ($request) {
                $query->where('tournament_paces.tournament_round_id', $request->round_id);
            })
            ->when($request->filled('session'), function ($query) use ($request) {
                $query->where('groups.session', $request->session);
            })
            ->where('groups.session', 'afternoon')
            ->orderBy('tournament_paces.time', 'asc')
            ->get();

        foreach ($data as $item) {
            $item->time = Carbon::parse($item->time)->addMinutes(15)->format('H:i:s');
            $item->save();
        }

        return response()->json(['message' => 'Tournament pace times reset successfully.']);
    }

    
}
