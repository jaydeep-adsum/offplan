<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultipleImages extends Model
{
    protected $table = 'multipleimage';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attachment_name', 'attachment_multiple', 'developer_id',
    ];
}
