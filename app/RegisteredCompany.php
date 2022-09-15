<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class RegisteredCompany extends Model
{
    use LogsActivity;
    protected $table ='tbl_registered_company';
    protected $fillable =['id','isin','name','created_by','updated_by'];
    protected static $logAttributes =['id','isin','name','created_by','updated_by'];
}
