<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentPace extends Model
{
    protected $table = 'tournament_paces';
    protected $fillable = [
        'time',
        'type',
        'finish_at',
        'status',
        'tournament_round_id',
        'hole_id',
        'group_id',
    ];

    public function round()
    {
        return $this->belongsTo(TournamentRound::class, 'tournament_round_id');
    }

    public function hole()
    {
        return $this->belongsTo(TournamentHole::class, 'hole_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
    public function players()
    {
        return $this->hasMany(Player::class, 'group_id', 'group_id');
    }
}
