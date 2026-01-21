<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'players';
    protected $fillable = [
        'name',
        'origin',
        'group_id',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
