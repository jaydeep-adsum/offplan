<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitorization extends Model
{
    protected $table = 'monitorization';

    protected $with = ['user'];

    protected $fillable = [
        'user_id','last_login','last_logout','login_time',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
