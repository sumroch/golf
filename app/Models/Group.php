<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'groups';
    protected $fillable = [
        'name',
        'time',
        'tee',
        'session',
        'tournament_round_id',
    ];

    public function round()
    {
        return $this->belongsTo(TournamentRound::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function tournamentPaces()
    {
        return $this->hasMany(TournamentPace::class);
    }

    public function tournamentRefereeDuties()
    {
        return $this->morphMany(TournamentRefereeDuty::class, 'observer');
    }
}
