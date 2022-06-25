<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Community;
use App\Models\ManageListings;


class Subcommunity extends Model
{
    protected $table = 'subcommunity';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','com_id'
    ];
    public function community()
    {
        return $this->belongsTo(Community::class,'com_id');
    }
     public function managelistings(){
        return $this->hashOne(ManageListings::class);
    }
}
