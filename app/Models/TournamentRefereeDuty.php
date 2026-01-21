<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentRefereeDuty extends Model
{
    // The table associated with the model.
    protected $table = 'tournament_referee_duties';
    // The attributes that are mass assignable.
    protected $fillable = [
        'tournament_round_id',
        'user_id',
        'observer_id',
        'observer_type',
    ];
    public function round()
    {
        return $this->belongsTo(TournamentRound::class, 'tournament_round_id');
    }

    public function referee()
    {
        return $this->belongsTo(User::class);
    }
    
    public function observer()
    {
        return $this->morphTo();
    }
}
