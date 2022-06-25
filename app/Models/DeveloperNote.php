<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeveloperNote extends Model
{
    protected $table = 'developer_notes';

    public $timestamps = true;
    
    protected $fillable = [
        'user_id', 'developer_id', 'note',
    ];

    public function agentName()
    {
        return $this->hasOne('App\Models\User','id','user_id');
    }
}
