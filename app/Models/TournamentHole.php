<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TournamentHole extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'tournament_holes';
    protected $fillable = [
        'number',
        'allowed_time',
        'par',
        'course_id',
        'tournament_id',
    ];

    public function round()
    {
        return $this->belongsTo(TournamentRound::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function tournamentPaces()
    {
        return $this->hasMany(TournamentPace::class, 'hole_id');
    }

    public function tournamentRefereeDuties()
    {
        return $this->morphMany(TournamentRefereeDuty::class, 'observer');
    }
}
