<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Dispatch extends Model
{
    use LogsActivity;
    protected $table = 'tbl_dispatch';
    protected $fillable = ['id', 'date', 'reference_number', 'subject','remarks', 'receiver','branch', 'created_by', 'updated_by'];
    protected static $logAttributes = ['id', 'date', 'reference_number', 'subject','remarks','branch', 'receiver', 'created_by', 'updated_by'];
}