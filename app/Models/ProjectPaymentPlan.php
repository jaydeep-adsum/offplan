<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ManageProject;

class ProjectPaymentPlan extends Model
{
    protected $table = 'project_payment_plan';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id', 'installment_terms', 'milestone', 'percentage',
    ];

    public function manageProject() 
    {
        return $this->belongsTo(ManageProject::class,'project_id');
    }
}
