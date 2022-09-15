<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PersonalShareBorrower extends Model
{
    use LogsActivity;
    protected $table ='tbl_personal_share_borrower';
    protected $fillable =['id','borrower_id','personal_share_id','corporate_share_id','property_type','status','created_by','updated_by'];
    protected static $logAttributes =['id','borrower_id','personal_share_id','corporate_share_id','property_type','status','created_by','updated_by'];
}
