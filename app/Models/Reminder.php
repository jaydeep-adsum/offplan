<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Reminder extends Model
{
    protected $table = 'reminders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id','user_id',
		'title',
		'reminder_date',
		'is_active',
		'comment','status'
    ];
    protected $data=['reminder_date'];
    public function getCreatedAttribute()
    {
        return "{$this->created_at}";        
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

}
