<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Developer;
use App\Models\Paymentplan;
use App\Models\Note;
use App\Models\Community;
use App\Models\Subcommunity;

class ManageListings extends Model
{
    protected $table = 'manage_listings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
     'user_id','developer_id', 'project', 'handover_year', 'quarter','community','subcommunity','location', 'up_to_handover', 'post_handover', 'latitude','longitude','property','size','price','bedrooms','bathrooms','rera_permit_no','construction_status','construction_date','title','description','features','image','floor_plan_image','video','pdf','payment_plan','index_key','pre_handover_amount','handover_amount','handover','milestone_price','rf_no','ready_status','sold_out_status','flag',
    ];
    public function developer()
    {
        return $this->belongsTo(Developer::class,'developer_id');
    }

    public function paymentplan(){
        return $this->hasMany(Paymentplan::class,'project_id','id');
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
    public function reminder(){
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
}
