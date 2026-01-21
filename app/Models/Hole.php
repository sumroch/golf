<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hole extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'holes';
    protected $fillable = [
        'number',
        'allowed_time',
        'par',
        'course_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
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
