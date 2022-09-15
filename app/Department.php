<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Department extends Model
{
    use LogsActivity;
    protected $table = 'tbl_department';
    protected $fillable = ['id', 'status', 'name', 'created_by', 'updated_by'];
    protected static $logAttributes = ['id', 'status', 'name', 'created_by', 'updated_by'];
}
