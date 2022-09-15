<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CorporatePropertyOwner extends Model
{

    use LogsActivity;
    protected $table = 'tbl_corporate_property_owner';
    protected $fillable = ['id', 'english_name', 'nepali_name', 'client_id', 'ministry_id', 'department_id'
        , 'registration_number', 'reg_year', 'district_id', 'local_bodies_id', 'wardno', 'reg_month', 'reg_day',
        'issued_district', 'authorized_person_id','phone','status', 'created_by', 'updated_by'];

    protected static $logAttributes = ['id', 'english_name', 'nepali_name', 'client_id', 'ministry_id', 'department_id'
        , 'registration_number', 'reg_year', 'district_id', 'local_bodies_id', 'wardno', 'reg_month', 'reg_day',
        'issued_district', 'authorized_person_id', 'status', 'created_by', 'updated_by'];
    public function corporate_property_owner_land()
    {
        return $this->hasMany('App\CorporateLand', 'property_owner_id');
    }

    public function corporate_property_owner_share()
    {
        return $this->hasMany('App\CorporateShare', 'property_owner_id`');
    }
    public function property_owner_department()
    {
        return $this->hasOne('App\Department', 'department_id');
    }

    public function property_owner_ministry()
    {
        return $this->hasOne('App\Ministry', 'ministry_id');
    }
    public function corporate_property_owner_district()
    {
        return $this->hasOne('App\District', 'district_id');
    }
    public function corporate_property_owner_local_bodies()
    {
        return $this->hasOne('App\LocalBodies', 'local_bodies_id');
    }



}