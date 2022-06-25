<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDocuments extends Model
{
    protected $table = 'project_documents';

    public $timestamps = true;
    
    protected $fillable = [
        'user_id', 'project_id', 'document_name', 'document_file',
    ];
}
