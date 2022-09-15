<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CorporateFacilities extends Model
{
    use LogsActivity;
    protected $table ='tbl_corporate_facilities';
    protected $fillable =['id','borrower_id','tenure','facility_id','amount','within','rate','remarks','tyear','tmonth','tday','status','created_by','updated_by'];
    protected static $logAttributes =['id','borrower_id','tenure','facility_id','amount','within','rate','remarks','tyear','tmonth','tday','status','created_by','updated_by'];
}