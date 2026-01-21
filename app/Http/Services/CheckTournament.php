<?php

namespace App\Http\Services;

use App\Models\Tournament;
use App\Models\TournamentRound;

class CheckTournament
{
    private ?Tournament $tournament = null;
    private ?TournamentRound $tournamentRound = null;

    private bool $load = false;

    public function call(): void
    {
        if ($this->load === false) {
            $this->tournament = Tournament::where('status', '!=', 'finish')->first();
            $this->tournamentRound = $this->tournament ? $this->tournament->rounds()->where('status', '!=', 'finish')->first() : null;
            $this->load = true;
        }
    }

    public function exist(): bool
    {
        $this->call();

        return $this->tournament ? true : false;
    }

    public function get(): ?Tournament
    {
        $this->call();

        return $this->tournament;
    }

    public function round(): ?int
    {
        $this->call();

        return $this->tournament ? $this->tournament->round : null;
    }

    public function roundActive(): ?int
    {
        $this->call();

        return $this->tournamentRound;
    }

    public function roundActiveId(): ?int
    {
        $this->call();

        return $this->tournamentRound ? $this->tournamentRound->id : null;
    }
}
