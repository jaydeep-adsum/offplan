<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subcommunity;
use App\Models\ManageListings;


class Community extends Model
{
    protected $table = 'community';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function subcommunity() {
        return $this->hashMany(Subcommunity::class);
    }
    public function managelistings(){
        return $this->hashOne(ManageListings::class);
    }
}
