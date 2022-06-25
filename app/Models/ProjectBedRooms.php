<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectBedRooms extends Model
{
    protected $table = 'project_bedrooms';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id', 'bed_rooms', 'min_price', 'max_price',
    ];
}
