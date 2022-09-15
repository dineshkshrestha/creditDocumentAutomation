<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PersonalPropertyOwner extends Model
{
    use LogsActivity;
    protected $table = 'tbl_personal_property_owner';
    protected $fillable = ['id', 'english_name', 'nepali_name', 'client_id', 'grandfather_name', 'grandfather_relation', 'father_name',
        'father_relation', 'spouse_name','phone', 'spouse_relation', 'district_id', 'local_bodies_id', 'wardno', 'gender', 'citizenship_number', 'issued_district', 'issued_day', 'issued_month',
        'issued_year', 'dob_year', 'dob_month', 'dob_day', 'status', 'created_by', 'updated_by'];
    protected static $logAttributes = ['id','phone', 'english_name', 'nepali_name', 'client_id', 'grandfather_name', 'grandfather_relation', 'father_name',
        'father_relation', 'spouse_name', 'spouse_relation', 'district_id', 'local_bodies_id', 'wardno', 'gender', 'citizenship_number', 'issued_district', 'issued_day', 'issued_month',
        'issued_year', 'dob_year', 'dob_month', 'dob_day', 'status', 'created_by', 'updated_by'];

    public function personal_property_owner_land()
    {
        return $this->hasMany('App\PersonalLand', 'property_owner_id');
    }

    public function personal_property_owner_share()
    {
        return $this->hasMany('App\PersonalShare', 'property_owner_id`');
    }

    public function personal_property_owner_district()
    {
        return $this->hasOne('App\District', 'district_id');
    }

    public function personal_property_owner_local_bodies()
    {
        return $this->hasOne('App\LocalBodies', 'local_bodies_id');
    }

    public function personal_property_issued_district()
    {
        return $this->hasOne('App\District', 'issued_district');
    }


}
