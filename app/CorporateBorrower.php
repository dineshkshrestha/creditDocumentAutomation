<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class CorporateBorrower extends Model
{

    use LogsActivity;

    protected $table = 'tbl_corporate_borrower';
    protected $fillable = ['id', 'english_name', 'nepali_name', 'client_id', 'ministry_id', 'department_id'
        , 'registration_number', 'reg_year', 'district_id','phone', 'local_bodies_id', 'wardno', 'authorized_person_id', 'reg_month', 'reg_day', 'status', 'created_by', 'updated_by'];

    protected static $logAttributes = ['id', 'english_name', 'nepali_name', 'client_id', 'ministry_id', 'department_id'
        , 'registration_number', 'reg_year', 'district_id','phone', 'local_bodies_id', 'wardno', 'authorized_person_id', 'reg_month', 'reg_day', 'status', 'created_by', 'updated_by'];

    public function corporate_loan()
    {
        return $this->hasMany('App\Corporateloan', 'borrower_id');
    }



    public function corporate_guarantor_borrower()
    {
        return $this->hasMany('App\CorporateGuarantorBorrower', 'borrower_id');
    }

    public function corporate_land_borrower()
    {
        return $this->hasMany('App\CorporateLandBorrower', 'borrower_id');
    }

    public function corporate_share_borrower()
    {
        return $this->hasMany('App\CorporateShareBorrower', 'borrower_id');
    }

    public function corporate_facilities()
    {
        return $this->hasMany('App\CorporateFacilities', 'borrower_id');
    }

    public function corporate_hire_purchase()
    {
        return $this->hasMany('App\CorporateHirePurchase', 'borrower_id');
    }


    public function borrower_department()
    {
        return $this->hasOne('App\Department', 'department_id');
    }


    public function corporate_borrower_district()
    {
        return $this->hasOne('App\District', 'district_id');
    }
    public function corporate_borrower_local_bodies()
    {
        return $this->hasOne('App\LocalBodies', 'local_bodies_id');
    }



    public function corporateborrowerpersonalland()
    {
        return $this->belongsToMany('App\PersonalLand', 'tbl_corporate_land_borrower', 'borrower_id', 'personal_land_id');
    }

    public function corporateborrowerpersonalshare()
    {
        return $this->belongsToMany('App\PersonalShare', 'tbl_corporate_share_borrower', 'borrower_id', 'personal_share_id');
    }

    public function corporateborrowercorporateland()
    {
        return $this->belongsToMany('App\CorporateLand','tbl_corporate_land_borrower','borrower_id','corporate_land_id');
    }

    public function corporateborrowercorporateshare()
    {
        return $this->belongsToMany('App\CorporateShare','tbl_corporate_share_borrower','borrower_id','corporate_share_id');
    }
    public function corporate_joint_land()
    {
        return $this->hasOne('App\JointPropertyOwner', 'corporate_borrower_id');
    }


    public function corporateborrowerpersonalguarantor()
    {
        return $this->belongsToMany('App\PersonalGuarantor', 'tbl_corporate_guarantor_borrower', 'borrower_id', 'personal_guarantor_id');
    }

    public function corporateborrowercorporateguarantor()
    {
        return $this->belongsToMany('App\CorporateGuarantor', 'tbl_corporate_guarantor_borrower', 'borrower_id', 'corporate_guarantor_id');
    }






}



