<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Province extends Model
{
    use LogsActivity;
    protected $table ='tbl_province';
    protected $fillable =['id','name','created_by','updated_by'];
    protected static $logAttributes =['id','name','created_by','updated_by'];

    public function district()
    {
        return $this->hasMany('App\District','province_id');
    }
}