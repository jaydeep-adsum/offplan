<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectNotes extends Model
{
    protected $table = 'project_notes';

    public $timestamps = true;

    protected $fillable = [
        'user_id', 'proj_id', 'note',
    ];

    public function agentName()
    {
        return $this->hasOne('App\Models\User','id','user_id');
    }
}
