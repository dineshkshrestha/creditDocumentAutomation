<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class JointPropertyOwner extends Model
{
    use LogsActivity;
    protected $table ='tbl_joint_property_owner';
    protected $fillable =['id','joint1','joint2','joint3','personal_borrower_id', 'joint_borrower_id', 'corporate_borrower_id','created_by','updated_by'];
    protected static $logAttributes =['id','joint1','joint2','joint3','personal_borrower_id', 'joint_borrower_id', 'corporate_borrower_id','created_by','updated_by'];


    public function joint_land(){
        return $this->hasMany('App\JointLand','joint_id');
    }





}
