<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PersonalLand extends Model
{
    use LogsActivity;
    protected $table = 'tbl_personal_land';
    protected $fillable = ['id', 'property_owner_id', 'malpot', 'district_id', 'local_bodies_id', 'wardno', 'sheet_no', 'kitta_no', 'area', 'remarks', 'status', 'created_by', 'updated_by'];
    protected static $logAttributes = ['id', 'property_owner_id', 'malpot', 'district_id', 'local_bodies_id', 'wardno', 'sheet_no', 'kitta_no', 'area', 'remarks', 'status', 'created_by', 'updated_by'];

    public function personal_corporate_land()
    {
        return $this->hasMany('App\CorporateLandBorrower', 'personal_land_id');
    }


    public function personal_personal_land()
    {
        return $this->hasMany('App\PersonalLandBorrower', 'personal_land_id');
    }

}
