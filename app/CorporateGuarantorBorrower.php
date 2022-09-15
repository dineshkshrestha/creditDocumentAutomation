<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CorporateGuarantorBorrower extends Model
{

    use LogsActivity;
    protected $table ='tbl_corporate_guarantor_borrower';
    protected $fillable =['id','borrower_id','personal_guarantor_id','corporate_guarantor_id','guarantor_type','status','created_by','updated_by'];
    protected static $logAttributes =['id','borrower_id','personal_guarantor_id','corporate_guarantor_id','guarantor_type','status','created_by','updated_by'];
}
