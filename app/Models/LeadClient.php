<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadClient extends Model
{
    protected $table = 'lead_clients';

    protected $with = ['notes','reminder'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','email','phone','note','project_id','is_delete','user_id'
    ];

    public function notes(){
        return $this->hasMany(ClientNotes::class,'client_id');
    }
    public function reminder(){
        return $this->hasone(ClientReminder::class,'client_id');
    }
}
