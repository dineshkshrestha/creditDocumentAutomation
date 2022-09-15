<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PersonalFacilities extends Model
{
    use LogsActivity;
    protected $table ='tbl_personal_facilities';
    protected $fillable =['id','borrower_id','tenure','within','facility_id','amount','rate','remarks','tyear','tmonth','tday','status','created_by','updated_by'];
    protected static $logAttributes =['id','borrower_id','tenure','within','facility_id','amount','rate','remarks','tyear','tmonth','tday','status','created_by','updated_by'];
}
