<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Features extends Model
{
    protected $table = 'features';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fname'];
}
