<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectAssignAgents extends Model
{
    protected $table = 'project_assign_agents';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id', 'agent_id',
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User','id','agent_id');
    }
}
