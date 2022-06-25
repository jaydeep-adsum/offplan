<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultipleContacts extends Model
{
    protected $table = 'multiplecontact';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'person', 'phone', 'developer_id',
    ];
}
