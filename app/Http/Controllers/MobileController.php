<?php

namespace App\Http\Controllers;

use App\Http\Factories\MobileFactories;
use App\Http\Services\MobileService;
use App\Models\TournamentPace;
use App\Models\TournamentRefereeDuty;
use App\Models\TournamentRound;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MobileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function success(): View
    {
        return view('sign-success');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function index(Request $request, MobileService $mobileService): View
    {
        $tournamentRound = TournamentRound::where('status', 'active')->first();

        if (!$tournamentRound) {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['message' => 'Tournament not found'], 404)
                : view('mobile.tournament-not-found');
        }

        $holes = $mobileService->getRefereObserver($request->user()->id, $tournamentRound->id);
        $holes = $holes->map(function ($item) {
            $item->name = $item->observer_type === 'hole'
                ? 'Hole ' . $item->observer->number
                : $item->observer->name;
            return $item;
        });

        return $request->wantsJson() || $request->ajax()
            ? response()->json(['data' => $holes])
            : view('mobile.index', [
                'status_pause' => false,
                'holes' => $holes,
            ]);
    }

    public function finish($id, Request $request, MobileService $mobileService)
    {
        $pace = TournamentPace::where('id', $id)->first();

        $holes = $mobileService->getRefereObserver($request->user()->id, $pace->tournament_round_id);
        $holeIds = $holes->pluck('observer_id')->toArray();

        if (!in_array($pace->hole_id, $holeIds) && !in_array($pace->group_id, $holeIds)) {
            return response()->json(['message' => 'Not Found'], 404);
        }


        if ($pace) {
            $pace->update([
                'status' => 'finish',
                'finish_at' => now(),
            ]);

            return response()->json([
                'data' => $pace,
            ]);
        }

        return response()->json(['message' => 'Not Found'], 404);
    }

    public function unmonitored($id, Request $request, MobileService $mobileService)
    {
        $pace = TournamentPace::where('id', $id)->first();

        $holes = $mobileService->getRefereObserver($request->user()->id, $pace->tournament_round_id);
        $holeIds = $holes->pluck('observer_id')->toArray();

        if (!in_array($pace->hole_id, $holeIds) && !in_array($pace->group_id, $holeIds)) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        if ($pace) {
            $pace->update([
                'status' => 'unmonitored',
            ]);

            return response()->json([
                'data' => $pace,
            ]);
        }

        return response()->json(['message' => 'Not Found'], 404);
    }

    public function showMember($id, Request $request, MobileService $mobileService)
    {
        $duty = TournamentRefereeDuty::where('id', $id)
            ->whereHas('round', function ($query) {
                $query->whereIn('status', ['active', 'pause']);
            })
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$duty)
            return response()->json(['message' => 'Not Found'], 404);

        return response()->json([
            'data' => MobileFactories::showMember($mobileService->getObserverMember($request->user()->id, $duty), $duty->observer_type),
        ]);
    }
}
