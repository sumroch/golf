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

        $observerType = '';
        $observerTarget = '';
        $check = $request->user()->hasRole(['referee', 'observer']);

        foreach ($holes as $key => $hole) {
            $hole->name = $hole->observer_type === 'hole'
                ? 'Hole ' . $hole->observer->number
                : $hole->observer->name;
            $hole->observer_number = $hole->observer_type === 'hole' ? $hole->observer->number : $hole->observer->name;

            $observerType = $hole->observer_type === 'hole' ? 'Hole' : 'Group';

            if ($check) {
                $observerTarget = $key == 0 ? $hole->observer->number : $observerTarget . ', ' . $hole->observer->number;
            } else {
                $observerTarget = 'All Access';
            }
        }

        return $request->wantsJson() || $request->ajax()
            ? response()->json(['data' => $holes])
            : view('mobile.index', [
                'status_pause' => false,
                'holes' => $holes,
                'course_name' => $tournamentRound->tournament->course->name,
                'observer_target' => '(' . $observerType . ' ' . $observerTarget . ')',
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

        if ($pace && !in_array($pace->status, ['finish', 'unmonitored'])) {
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

        if ($pace && !in_array($pace->status, ['finish', 'unmonitored'])) {
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
                $query->whereIn('status', ['active', 'pause'])
                    ->whereHas('tournament', function ($query) {
                        $query->where('status', 'active');
                    });
            })
            ->when($request->user()->hasRole(['referee', 'observer']), function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->first();

        if (!$duty)
            return response()->json(['message' => 'Not Found'], 404);

        return response()->json([
            'data' => MobileFactories::showMember($mobileService->getObserverMember($duty), $duty->observer_type, $duty->id),
        ]);
    }
}
