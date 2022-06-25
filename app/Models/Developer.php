<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ManageListings;
class Developer extends Model
{
    protected $table = 'developer';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company', 'person', 'phone', 'note', 'email', 'date', 'pdf'
    ];
    public function managelistings(){
        return $this->hashOne(ManageListings::class);
    }
    public function multiplecontact(){
        return $this->hasMany(MultipleContacts::class,'developer_id','id');
    }

    public function singleContact()
    {
        return $this->hasOne(MultipleContacts::class,'developer_id','id');
    }
}
