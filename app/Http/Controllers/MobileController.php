<?php

namespace App\Http\Controllers;

use App\Http\Factories\MobileFactories;
use App\Http\Services\MobileService;
use App\Models\TournamentPace;
use App\Models\TournamentRound;
use Illuminate\Http\RedirectResponse;
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
    public function index(Request $request, MobileService $mobileService): View|RedirectResponse
    {
        $tournamentRound = TournamentRound::where('status', 'active')->whereHas('tournament', fn($x) => $x->where('status', 'active'))->first();
        $check = $request->user()->hasRole(['referee', 'observer']);

        if (!$check)
            return redirect()->route('access');

        if (!$tournamentRound) {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['message' => 'Tournament not found'], 404)
                : view('mobile.tournament-not-found');
        }

        $holes = $mobileService->getRefereObserver($request->user()->id, $tournamentRound->id);

        $observerType = '';
        $observerTarget = '';

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

        $observers = $mobileService->getRefereObserver($request->user()->id, $pace->tournament_round_id);


        if ($mobileService->checkObserverIsValid($observers, $pace)) {

            $pace->update([
                'status' => 'finish',
                'finish_at' => now(),
            ]);

            $mobileService->actionObserver(
                $mobileService->getListObserverReverse($observers->first()->observer_type ?? null, $pace),
                $pace
            );

            return response()->json(['data' => $pace]);
        }

        return response()->json(['message' => 'Not Found'], 404);
    }

    public function unmonitored($id, Request $request, MobileService $mobileService)
    {
        $pace = TournamentPace::where('id', $id)->first();
        $observers = $mobileService->getRefereObserver($request->user()->id, $pace->tournament_round_id);

        if ($mobileService->checkObserverIsValid($observers, $pace)) {
            $pace->update([
                'status' => 'unmonitored',
            ]);

            return response()->json([
                'data' => $pace,
            ]);
        }

        return response()->json(['message' => 'Not Found'], 404);
    }

    public function showMember($observer, Request $request, MobileService $mobileService)
    {
        $duty = $mobileService->getUserDuties($observer, $request->user());

        if (!$duty)
            return response()->json(['message' => 'Not Found'], 404);

        return response()->json([
            'data' => MobileFactories::showMember($mobileService->getObserverMember($duty), $duty->observer_type, $duty->id),
        ]);
    }
}
