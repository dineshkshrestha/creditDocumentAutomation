<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class District extends Model
{
    use LogsActivity;
    protected $table = 'tbl_district';
    protected $fillable = ['id', 'name', 'province_id', 'created_by', 'updated_by'];
    protected static $logAttributes = ['id', 'name', 'created_by', 'updated_by', 'province_id'];


    public function province(){
        return $this->belongsTo('App\Province','province_id');
    }


    public function local_bodies()
    {
        return $this->hasMany('App\LocalBodies','district_id');
    }




}