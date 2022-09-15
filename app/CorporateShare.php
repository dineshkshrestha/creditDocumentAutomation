<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CorporateShare extends Model
{
    use LogsActivity;
    protected $table = 'tbl_corporate_share';

    protected $fillable = ['id', 'property_owner_id', 'client_id', 'dpid', 'isin', 'share_type', 'kitta', 'status', 'created_by', 'updated_by'];

    protected static $logAttributes = ['id', 'property_owner_id', 'client_id', 'isin', 'dpid', 'share_type', 'kitta', 'status', 'created_by', 'updated_by'];

    public function corporate_corporate_share()
    {
        return $this->hasMany('App\CorporateShareBorrower', 'corporate_share_id');
    }


    public function corporate_personal_share()
    {
        return $this->hasMany('App\PersonalShareBorrower', 'corporate_share_id');
    }


}
