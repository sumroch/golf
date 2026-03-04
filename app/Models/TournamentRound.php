<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TournamentRound extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'tournament_rounds';
    protected $fillable = [
        'start_interval',
        'morning',
        'afternoon',
        'crossover_one',
        'crossover_ten',
        'ball',
        'round_number',
        'date',
        'action_date',
        'status',
        'transportation',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function tournamentHoles()
    {
        return $this->hasMany(TournamentHole::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function tournamentPace()
    {
        return $this->hasMany(TournamentPace::class);
    }

    public function tournamentReferee()
    {
        return $this->hasMany(TournamentRefereeDuty::class);
    }

    public function tournamentRefereeDuty()
    {
        return $this->hasMany(TournamentRefereeDuty::class);
    }
}
