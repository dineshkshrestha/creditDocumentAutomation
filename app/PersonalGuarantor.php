<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PersonalGuarantor extends Model
{
    use LogsActivity;
    protected $table ='tbl_personal_guarantor';
    protected $fillable =['id','english_name','nepali_name','client_id','grandfather_name','grandfather_relation','father_name',
        'father_relation','spouse_name','spouse_relation','phone','district_id','local_bodies_id','wardno','gender','citizenship_number','issued_district','issued_day','issued_month',
        'issued_year','dob_year','dob_month','dob_day','status','created_by','updated_by'];
    protected static $logAttributes =['id','english_name','phone','nepali_name','client_id','grandfather_name','grandfather_relation','father_name',
        'father_relation','spouse_name','spouse_relation','district_id','local_bodies_id','wardno','gender','citizenship_number','issued_district','issued_day','issued_month',
        'issued_year','dob_year','dob_month','dob_day','status','created_by','updated_by'];


    public function personal_corporate_guarantor()
    {
        return $this->hasMany('App\CorporateGuarantorBorrower', 'corporate_guarantor_id');
    }
    public function personal_personal_guarantor()
    {
        return $this->hasMany('App\PersonalGuarantorBorrower', 'personal_guarantor_id');
    }
    public function personal_guarantor_district()
    {
        return $this->hasOne('App\District', 'district_id');
    }
    public function personal_guarantor_local_bodies()
    {
        return $this->hasOne('App\LocalBodies', 'local_bodies_id');
    }
    public function personal_guarantor_issued_district()
    {
        return $this->hasOne('App\District', 'issued_district');
    }


}
