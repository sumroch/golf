<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Services\CheckTournament;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request, CheckTournament $checkTournament)
    {
        if (Auth::check()) {
            if ($request->user()->hasRole(['referee', 'observer']))
                return redirect()->route('referee');

            if ($checkTournament->exist())
                return redirect()->route('dashboard', ['round' => $checkTournament->roundActiveId()]);

            return redirect()->route('tournament.index');
        }

        return view('login');
    }

    public function authenticate(AuthRequest $request, CheckTournament $checkTournament): RedirectResponse
    {
        $credential = $request->safe()->only(['username', 'password']);

        if (filter_var($request->input('username'), FILTER_VALIDATE_EMAIL)) {
            $credential = [
                'email' => $credential['username'],
                'password' => $credential['password']
            ];
        }

        if (Auth::attempt($credential, $request->remember ?? 0)) {

            $request->session()->regenerate();

            User::where('id', $request->user()->id)->update(['lang' => 'ja']);

            if ($request->user()->hasRole(['referee', 'observer']))
                return redirect()->route('sign-success');

            if ($checkTournament->exist())
                return redirect()->route('dashboard', ['round' => $checkTournament->roundActiveId()]);

            return redirect()->route('tournament.index');
        }

        return redirect()->route('login')->withErrors([
            'username' => __('trans.error_login'),
        ]);
    }

    public function authenticateQr(Request $request, CheckTournament $checkTournament)
    {
        $qr_code = $request->file('qr_code');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }

    public function changeLanguage($lang, Request $request): RedirectResponse
    {
        if (!in_array($lang, ['ja', 'en'])) {
            abort(404);
        }

        User::where('id', $request->user()->id)->update(['lang' => $lang]);

        return redirect()->back();
    }
}
