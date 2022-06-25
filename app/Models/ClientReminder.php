<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientReminder extends Model
{
    protected $table = 'client_reminders';

    protected $fillable = [
        'client_id', 'title','comment','reminder_date','status','is_active',
    ];
}
