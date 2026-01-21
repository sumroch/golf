<?php

namespace App\Providers;

use App\View\Composers\SidebarComposer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Facades\View::composer('layouts.sidebar', SidebarComposer::class);

        Relation::morphMap([
            'hole'  => \App\Models\TournamentHole::class,
            'group' => \App\Models\Group::class,
        ]);
    }
}
