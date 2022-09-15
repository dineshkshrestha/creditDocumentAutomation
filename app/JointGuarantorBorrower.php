<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class JointGuarantorBorrower extends Model
{

    use LogsActivity;
    protected $table ='tbl_joint_guarantor';
    protected $fillable =['id','borrower_id','personal_guarantor_id','status','created_by','updated_by'];
    protected static $logAttributes =['id','borrower_id','personal_guarantor_id','status','created_by','updated_by'];




}
