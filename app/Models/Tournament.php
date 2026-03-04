<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tournament extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'tournaments';
    protected $fillable = [
        'name',
        'location',
        'organizer',
        'date_start',
        'round',
        'timezone',
        'course_id',
        'status',
    ];

    public function rounds()
    {
        return $this->hasMany(TournamentRound::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
