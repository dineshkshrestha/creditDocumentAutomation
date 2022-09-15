<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CorporateGuarantor extends Model
{

    use LogsActivity;
    protected $table = 'tbl_corporate_guarantor';
    protected $fillable = ['id', 'english_name', 'nepali_name', 'client_id', 'ministry_id', 'department_id'
        , 'registration_number', 'reg_year', 'district_id', 'local_bodies_id', 'wardno', 'reg_month', 'reg_day',
        'issued_district', 'authorized_person_id','phone','status', 'created_by', 'updated_by'];

    protected static $logAttributes = ['id', 'english_name', 'nepali_name', 'client_id', 'ministry_id', 'department_id'
        , 'registration_number', 'reg_year', 'district_id', 'local_bodies_id', 'wardno', 'reg_month', 'reg_day',
        'issued_district', 'authorized_person_id','phone', 'status', 'created_by', 'updated_by'];

    public function corporate_corporate_guarantor()
    {
        return $this->hasMany('App\CorporateGuarantorBorrower', 'corporate_guarantor_id');
    }
    public function corporate_personal_guarantor()
    {
        return $this->hasMany('App\PersonalGuarantorBorrower', 'personal_guarantor_id');
    }
    public function guarantor_department()
    {
        return $this->hasOne('App\Department', 'department_id');
    }
    public function guarantor_ministry()
    {
        return $this->hasOne('App\Ministry', 'ministry_id');
    }
    public function corporate_guarantor_district()
    {
        return $this->hasOne('App\District', 'district_id');
    }
    public function corporate_guarantor_local_bodies()
    {
        return $this->hasOne('App\LocalBodies', 'local_bodies_id');
    }


}




