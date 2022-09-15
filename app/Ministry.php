<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Ministry extends Model
{
    use LogsActivity;
    protected $table ='tbl_ministry';
    protected $fillable =['id','status','name','created_by','updated_by'];
    protected static $logAttributes =['id','status','name','created_by','updated_by'];

    public function borrower_ministry()
    {
        return $this->hasOne('App\CorporateBorrower', 'ministry_id');
    }



}
