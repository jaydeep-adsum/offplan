<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestones extends Model
{
    protected $table = 'milestones';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'milestone'
    ];
}
