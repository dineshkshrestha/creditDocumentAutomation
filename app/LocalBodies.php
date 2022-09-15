<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class LocalBodies extends Model
{
    use LogsActivity;
    protected $table = 'tbl_local_bodies';
    protected $fillable = ['id','name','body_type', 'district_id', 'created_by', 'updated_by'];
    protected static $logAttributes = ['id', 'name','body_type', 'created_by', 'updated_by', 'district_id'];


    public function i_district(){
        return $this->belongsTo('App\District','district_id');
    }

}



