<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PersonalBorrower extends Model
{
    use LogsActivity;
    protected $table = 'tbl_personal_borrower';
    protected $fillable = ['id', 'english_name', 'nepali_name', 'client_id', 'grandfather_name', 'grandfather_relation'
        , 'father_name', 'father_relation', 'spouse_name', 'spouse_relation', 'district_id', 'local_bodies_id', 'wardno', 'gender', 'citizenship_number',
        'issued_district', 'issued_day', 'issued_month','phone', 'issued_year', 'dob_year', 'dob_month', 'dob_day', 'single',
        'joint', 'joint_id', 'status', 'created_by', 'updated_by'];
    protected static $logAttributes = ['id', 'english_name','phone', 'nepali_name', 'client_id', 'grandfather_name', 'grandfather_relation'
        , 'father_name', 'father_relation', 'spouse_name', 'spouse_relation', 'district_id', 'local_bodies_id', 'wardno', 'gender', 'citizenship_number',
        'issued_district', 'issued_day', 'issued_month', 'issued_year', 'dob_year', 'dob_month', 'dob_day', 'single',
        'joint', 'joint_id', 'status', 'created_by', 'updated_by'];

    public function personal_loan()
    {
        return $this->hasMany('App\Personalloan', 'borrower_id');
    }

    public function personal_joint_land()
    {
        return $this->hasOne('App\JointPropertyOwner', 'personal_borrower_id');
    }

    public function personal_guarantor_borrower()
    {
        return $this->hasMany('App\PersonalGuarantorBorrower', 'borrower_id');
    }

    public function personal_land_borrower()
    {
        return $this->hasMany('App\PersonalLandBorrower', 'borrower_id');
    }

    public function personal_share_borrower()
    {
        return $this->hasMany('App\PersonalShareBorrower', 'borrower_id');
    }

    public function personal_facilities()
    {
        return $this->hasMany('App\PersonalFacilities', 'borrower_id');
    }

    public function personal_hire_purchase()
    {
        return $this->hasMany('App\PersonalHirePurchase', 'borrower_id');
    }

    public function personal_borrower_district()
    {
        return $this->hasOne('App\District', 'district_id');
    }

    public function personal_borrower_local_bodies()
    {
        return $this->hasOne('App\LocalBodies', 'local_bodies_id');
    }

    public function personal_borrower_issued_district()
    {
        return $this->hasOne('App\District', 'issued_district');
    }

    public function personalborrowerpersonalland()
    {
        return $this->belongsToMany('App\PersonalLand', 'tbl_personal_land_borrower', 'borrower_id', 'personal_land_id');
    }

    public function personalborrowercorporateland()
    {
        return $this->belongsToMany('App\CorporateLand', 'tbl_personal_land_borrower', 'borrower_id', 'corporate_land_id');
    }

    public function personalborrowerpersonalshare()
    {
        return $this->belongsToMany('App\PersonalShare', 'tbl_personal_share_borrower', 'borrower_id', 'personal_share_id');
    }

    public function personalborrowercorporateshare()
    {
        return $this->belongsToMany('App\CorporateShare', 'tbl_personal_share_borrower', 'borrower_id', 'corporate_share_id');
    }

    public function personalborrowerpersonalguarantor()
    {
        return $this->belongsToMany('App\PersonalGuarantor', 'tbl_personal_guarantor_borrower', 'borrower_id', 'personal_guarantor_id');
    }

    public function personalborrowercorporateguarantor()
    {
        return $this->belongsToMany('App\CorporateGuarantor', 'tbl_personal_guarantor_borrower', 'borrower_id', 'corporate_guarantor_id');
    }
}