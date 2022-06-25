<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientNotes extends Model
{
    protected $table = 'client_notes';

    protected $fillable = [
        'client_id', 'note',
    ];
}
