<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CorporateLandBorrower extends Model
{
    use LogsActivity;
    protected $table ='tbl_corporate_land_borrower';
    protected $fillable =['id','borrower_id','personal_land_id','corporate_land_id','property_type','status','created_by','updated_by'];
    protected static $logAttributes =['id','borrower_id','personal_land_id','corporate_land_id','property_type','status','created_by','updated_by'];
}
