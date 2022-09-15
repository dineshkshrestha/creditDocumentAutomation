<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class JointShareBorrower extends Model
{
    use LogsActivity;
    protected $table ='tbl_joint_share_borrower';
    protected $fillable =['id','borrower_id','personal_share_id','status','created_by','updated_by'];
    protected static $logAttributes =['id','borrower_id','personal_share_id','status','created_by','updated_by'];
}
