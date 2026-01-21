<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    //soft delete trait can be added here if needed
    use SoftDeletes, HasFactory;

    protected $table = 'courses';
    protected $fillable = [
        'name',
        'location',
        'par',
        'total_holes',
    ];

    public function holes()
    {
        return $this->hasMany(Hole::class);
    }

    public function tournamentHoles()
    {
        return $this->hasMany(TournamentHole::class);
    }

    public function tournament()
    {
        return $this->hasMany(Tournament::class);
    }
}
