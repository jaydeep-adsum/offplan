<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ManageListings;

class Paymentplan extends Model
{
    protected $table = 'payment_plan';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id','installment_terms','milestone','percentage','amount'
    ];

    public function managelistings() 
    {
        return $this->belongsTo(ManageListings::class,'project_id');
    }
}
