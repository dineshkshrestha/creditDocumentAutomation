<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PersonalShare extends Model
{
    use LogsActivity;
    protected $table = 'tbl_personal_share';

    protected $fillable = ['id', 'property_owner_id', 'client_id', 'dpid', 'kitta','isin','share_type', 'status', 'created_by', 'updated_by'];

    protected static $logAttributes = ['id', 'property_owner_id', 'client_id', 'dpid', 'kitta','isin','share_type', 'status', 'created_by', 'updated_by'];



    public function personal_corporate_share()
    {
        return $this->hasMany('App\CorporateShareBorrower', 'personal_share_id');
    }


    public function personal_personal_share()
    {
        return $this->hasMany('App\PersonalShareBorrower', 'personal_share_id');
    }
}
