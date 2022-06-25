<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitMultipleAttachment extends Model
{
    protected $table = 'unit_multiple_attachments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'project_id', 'attachment_name', 'attachment_multiple',
    ];
}
