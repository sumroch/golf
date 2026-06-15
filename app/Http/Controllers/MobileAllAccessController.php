<?php

namespace App\Http\Controllers;

use App\Http\Factories\MobileFactories;
use App\Http\Services\MobileAllAccessService;
use App\Models\TournamentPace;
use App\Models\TournamentRound;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MobileAllAccessController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function index(Request $request, MobileAllAccessService $mobileAllService): View
    {
        $tournamentRound = TournamentRound::where('status', 'active')->whereHas('tournament', fn($x) => $x->where('status', 'active'))->first();

        if (!$tournamentRound) {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['message' => 'Tournament not found'], 404)
                : view('mobile.tournament-not-found');
        }

        $holes = $mobileAllService->getAllObserver($tournamentRound->id, $tournamentRound->observer_type);

        $observerTarget = '';

        foreach ($holes as $hole) {
            $hole->name = $tournamentRound->observer_type === 'hole'
                ? 'Hole ' . $hole->number
                : $hole->name;

            $hole->observer_number = $tournamentRound->observer_type === 'hole' ? $hole->number : $hole->name;

            $observerTarget = 'All Access';
        }

        return $request->wantsJson() || $request->ajax()
            ? response()->json(['data' => $holes])
            : view('mobile.all-access', [
                'status_pause' => false,
                'holes' => $holes,
                'course_name' => $tournamentRound->tournament->course->name,
                'observer_target' => '(' . $tournamentRound->observer_type . ' ' . $observerTarget . ')',
            ]);
    }

    public function finish($id, MobileAllAccessService $mobileAllService)
    {
        $tournamentRound = TournamentRound::where('status', 'active')->whereHas('tournament', fn($x) => $x->where('status', 'active'))->first();
        $pace = TournamentPace::where('id', $id)->first();

        if (in_array($pace->status, ['finish', 'unmonitored'])) {
            return response()->json(['message' => 'Already Finished'], 400);
        }

        $pace->update([
            'status' => 'finish',
            'finish_at' => now(),
        ]);

        $mobileAllService->actionObserver(
            $mobileAllService->getListObserverReverse($tournamentRound->observer_type ?? null, $pace),
            $pace
        );
        return response()->json([
            'data' => $pace,
        ]);
    }

    public function unmonitored($id)
    {
        $pace = TournamentPace::where('id', $id)->first();

        if (in_array($pace->status, ['finish', 'unmonitored'])) {
            return response()->json(['message' => 'Already Finished'], 400);
        }

        $pace->update([
            'status' => 'unmonitored',
            'finish_at' => now(),
        ]);

        return response()->json([
            'data' => $pace,
        ]);
    }

    public function showMember($observer, MobileAllAccessService $mobileAllService)
    {
        $tournamentRound = TournamentRound::where('status', 'active')->whereHas('tournament', fn($x) => $x->where('status', 'active'))->first();

        return response()->json([
            'data' => MobileFactories::showMember($mobileAllService->getObserverMember($tournamentRound->observer_type, $tournamentRound->id, $observer), $tournamentRound->observer_type, $observer),
        ]);
    }
}
