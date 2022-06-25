<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;

class Permission_role_mapping extends Model
{
    protected $table = 'permission_role_mappings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'permissions_id', 'read', 'create', 'update', 'delete',
    ];
    public function permission(){
        return $this->belongsTo(Permission::class ,'permissions_id');
    }
    
}