<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class JointBorrower extends Model
{
    use LogsActivity;
    protected $table ='tbl_joint_borrower';
    protected $fillable =['id','joint1','joint2','joint3','created_by','updated_by'];
    protected static $logAttributes =['id','joint1','joint2','joint3','created_by','updated_by'];

    public function jointborrowerpersonalland()
    {
        return $this->belongsToMany('App\PersonalLand', 'tbl_joint_land_borrower', 'borrower_id', 'personal_land_id');
    }

    public function jointborrowerpersonalshare()
    {
        return $this->belongsToMany('App\PersonalShare', 'tbl_joint_share_borrower', 'borrower_id', 'personal_share_id');
    }
    public function joint_joint_land()
    {
        return $this->hasOne('App\JointPropertyOwner', 'joint_borrower_id');
    }

    public function joint_facilities()
    {
        return $this->hasMany('App\JointFacilities', 'borrower_id');
    }

    public function joint_hire_purchase()
    {
        return $this->hasMany('App\JointHirePurchase', 'borrower_id');
    }
    public function jointborrowerpersonalguarantor()
    {
        return $this->belongsToMany('App\PersonalGuarantor', 'tbl_joint_guarantor', 'borrower_id', 'personal_guarantor_id');
    }



}
