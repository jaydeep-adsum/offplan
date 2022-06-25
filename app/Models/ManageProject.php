<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Developer;
use App\Models\ProjectPaymentPlan;
use App\Models\Note;
use App\Models\Community;
use App\Models\Subcommunity;

class ManageProject extends Model
{
    protected $table = 'manage_project';

    protected $fillable = [
        'user_id', 'developer_id', 'project', 'completion_status', 'quarter', 'handover_year', 'commission', 'location', 'latitude', 'longitude', 'property', 'rf_no', 'rera_permit_no', 'index_key', 'construction_status', 'construction_date', 'community', 'subcommunity', 'description', 'features', 'image', 'floor_plan_image', 'video', 'pdf', 'payment_plan', 'payment_plan_comments', 'ready_status', 'sold_out_status',
    ];

    public function developer() 
    {
        return $this->belongsTo(Developer::class,'developer_id');
    }

    public function paymentPlanDetails()
    {
        return $this->hasMany(ProjectPaymentPlan::class,'project_id','id');
    }

    public function community()
    {
        return $this->belongsTo(Community::class,'community');
    }

    public function subcommunity()
    {
        return $this->belongsTo(SubCommunity::class,'subcommunity');
    }

    public function notes()
    {
       return $this->hasMany(Note::class,'proj_id')->orderBy('updated_at', 'desc');;
    }

    public function reminder()
    {
        return $this->hasMany('App\Models\Reminder','project_id');
    }

    public function communitys()
    {
        return $this->belongsTo(Community::class,'community');
    }

    public function subcommunitys()
    {
        return $this->belongsTo(SubCommunity::class,'subcommunity');
    }

    public function projectBedrooms()
    {
        return $this->hasMany('App\Models\ProjectBedRooms','project_id','id');
    }

    public function multipleContact()
    {
        return $this->hasMany('App\Models\MultipleContacts','developer_id','developer_id');
    }

    public function projectReminders()
    {
        return $this->hasMany('App\Models\ProjectReminders','project_id','id')->where('status',0)->where('is_delete',0);
    }

    public function projectAssignAgents()
    {
        return $this->hasOne('App\Models\ProjectAssignAgents','project_id','id');
    }
}
