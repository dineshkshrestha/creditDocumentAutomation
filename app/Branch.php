<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Branch extends Model
{

    use LogsActivity;
    protected $table = 'tbl_branch';
    protected $fillable = ['id', 'location', 'district', 'local_body', 'status', 'created_by', 'updated_by'];
    protected static $logAttributes = ['id', 'location', 'district', 'local_body', 'status', 'created_by', 'updated_by'];

    public function personal_branch()
    {
        return $this->hasMany('App\PersonalLoan', 'branch_id');
    }
    public function corporate_branch()
    {
        return $this->hasMany('App\CorporateLoan', 'branch_id');
    }
}
