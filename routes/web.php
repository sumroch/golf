<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\RefereeController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TournamentRoundActionController;
use App\Http\Controllers\TournamentRoundController;
use App\Http\Controllers\TournamentRoundPaceController;
use App\Http\Controllers\TournamentRoundGroupController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'login'])->name('login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login-page');
    Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
    Route::post('/authenticate-qr', [AuthController::class, 'authenticateQr'])->name('authenticate-qr');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/sign-success', [MobileController::class, 'success'])->name('sign-success');

    Route::get('/referee', [MobileController::class, 'index'])->name('referee');
    Route::get('/referee/{id}', [MobileController::class, 'showMember'])->name('referee.observer');
    Route::post('/referee/{id}/finish', [MobileController::class, 'finish'])->name('referee.finish');
    Route::post('/referee/{id}/unmonitored', [MobileController::class, 'unmonitored'])->name('referee.unmonitored');

    Route::get('/change-language/{lang}', [AuthController::class, 'changeLanguage'])->name('change-language');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'role:admin|technician|director'])->prefix('admin')->group(function () {
    Route::middleware('check_tournament')->group(function () {
        Route::get('/group/template-download', [TournamentRoundGroupController::class, 'downloadTemplate'])->name('round.group.template-download');

        Route::get('/dashboard/{round}', [TournamentRoundController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard-table/{round}', [TournamentRoundController::class, 'dashboardTable'])->name('dashboard-table');
        Route::get('/dashboard-table-print/{round}', [TournamentRoundController::class, 'dashboardTablePrint'])->name('dashboard-table-print');
        Route::get('/setup/{round}', [TournamentRoundController::class, 'setup'])->name('round.setup');
        Route::get('/group/{round}', [TournamentRoundGroupController::class, 'group'])->name('round.group');
        Route::get('/pace/{round}', [TournamentRoundPaceController::class, 'pace'])->name('round.pace');
        Route::get('/pace-print/{round}', [TournamentRoundPaceController::class, 'pacePrint'])->name('round.pace-print');
        Route::get('/referee/{round}', [TournamentRoundController::class, 'referee'])->name('round.referee');

        Route::get('/start/{round}', [TournamentRoundActionController::class, 'start'])->name('round.start');
        Route::get('/pause/{round}', [TournamentRoundActionController::class, 'pause'])->name('round.pause');
        Route::get('/resume/{round}', [TournamentRoundActionController::class, 'resume'])->name('round.resume');
        Route::get('/stop/{round}', [TournamentRoundActionController::class, 'stop'])->name('round.stop');

        Route::post('/setup/{round}', [TournamentRoundController::class, 'storeSetup'])->name('round.setup.store');
        Route::post('/group/{round}', [TournamentRoundGroupController::class, 'storeGroup'])->name('round.group.store');
        Route::post('/referee/{round}', [TournamentRoundController::class, 'storeReferee'])->name('round.referee.store');

        Route::put('/group/{round}/setting', [TournamentRoundGroupController::class, 'updateHole'])->name('round.group.setting');

        Route::delete('/group/{round}', [TournamentRoundGroupController::class, 'deleteGroup'])->name('round.group.delete');
    });


    Route::prefix('master')->group(function () {
        Route::get('/tournament/active/{tournament}', [TournamentController::class, 'active'])->name('tournament.active');
        Route::resource('/tournament', TournamentController::class)->only(['index', 'create', 'store', 'update', 'edit', 'destroy'])->names('tournament');
        Route::resource('/referee', RefereeController::class)->only(['index', 'store', 'update', 'destroy'])->names('referee');

        Route::get('/users', function () {
            return view('admin.master.users');
        });
    });

    Route::prefix('grandmaster')->group(function () {
        Route::resource('/courses-and-holes', CourseController::class)
            ->names('course')
            ->parameter('courses-and-holes', 'course')
            ->only([
                'index',
                'create',
                'store',
                'edit',
                'update',
                'destroy'
            ]);
    });
});
