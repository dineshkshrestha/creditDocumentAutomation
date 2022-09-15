<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CorporateHirePurchase extends Model
{
    use LogsActivity;
    protected $table = 'tbl_corporate_hire_purchase';

    protected $fillable = ['id', 'borrower_id', 'model_number', 'registration_number','engine_number','chassis_number', 'created_by', 'updated_by'];

    protected static $logAttributes = ['id', 'borrower_id', 'model_number', 'registration_number','engine_number','chassis_number', 'created_by', 'updated_by'];

}

