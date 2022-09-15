<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class JointLandBorrower extends Model
{
    use LogsActivity;
    protected $table ='tbl_joint_land_borrower';
    protected $fillable =['id','borrower_id','personal_land_id','status','created_by','updated_by'];
    protected static $logAttributes =['id','borrower_id','personal_land_id','status','created_by','updated_by'];





}
