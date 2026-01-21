<?php

namespace App\View\Composers;

use App\Http\Services\CheckTournament;
use Illuminate\View\View;

class SidebarComposer
{
    /**
     * Create a new profile composer.
     */
    public function __construct(
        protected CheckTournament $tournament,
    ) {}

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $rounds = $this->tournament->get();

        $view->with('rounds', $rounds ? $rounds->rounds : []);
    }
}
