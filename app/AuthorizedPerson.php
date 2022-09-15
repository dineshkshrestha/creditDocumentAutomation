<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class AuthorizedPerson extends Model
{

    use LogsActivity;

    protected $table = 'tbl_authorized_person';

    protected $fillable = ['id', 'english_name', 'nepali_name', 'client_id', 'grandfather_name', 'grandfather_relation'
        , 'father_name', 'father_relation', 'spouse_name', 'spouse_relation', 'district_id', 'local_bodies_id', 'post', 'wardno', 'gender', 'citizenship_number',
        'issued_district', 'issued_day', 'issued_month', 'issued_year', 'dob_year', 'dob_month', 'dob_day',
        'status', 'created_by', 'updated_by'];

    protected static $logAttributes = ['id', 'english_name', 'nepali_name', 'client_id', 'grandfather_name', 'grandfather_relation'
        , 'father_name', 'father_relation', 'spouse_name', 'spouse_relation', 'district_id', 'local_bodies_id', 'wardno', 'post', 'gender', 'citizenship_number',
        'issued_district', 'issued_day', 'issued_month', 'issued_year', 'dob_year', 'dob_month', 'dob_day', 'status', 'created_by', 'updated_by'];

    public function authorized_person_district()
    {
        return $this->hasOne('App\District', 'district_id');
    }

    public function authorized_person_local_bodies()
    {
        return $this->hasOne('App\LocalBodies', 'local_bodies_id');
    }

    public function authorized_person_issued_district()
    {
        return $this->hasOne('App\District', 'issued_district');
    }

}