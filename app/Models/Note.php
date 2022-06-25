<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ManageListings;

class Note extends Model
{
    protected $table = 'notes';
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'note','user_id','proj_id'
    ];
    public function users()
    {
       return $this->belongsTo(User::class,'user_id');
   	}
    public function project()
    {
       return $this->belongsTo(ManageListings::class);
    }
 
     public function getUpdatedAttribute()
    {
        return $this->updated_at->toDateString();
    }
}
