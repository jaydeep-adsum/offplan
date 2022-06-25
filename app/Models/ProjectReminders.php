<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectReminders extends Model
{
    protected $table = "project_reminders";
    
    protected $fillable = [
        'project_id', 'user_id', 'title', 'comment', 'description', 'reminder_date', 'status', 'is_delete',
    ];

    protected $data = ['reminder_date'];

    public function user()
    {
        return $this->hasOne('App\Models\User','id','user_id');
    }
}
