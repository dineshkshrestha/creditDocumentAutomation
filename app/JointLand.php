<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class JointLand extends Model
{
    use LogsActivity;
    protected $table = 'tbl_joint_land';
    protected $fillable = ['id', 'joint_id',  'malpot', 'district_id', 'local_bodies_id', 'wardno', 'sheet_no', 'kitta_no', 'area', 'remarks', 'status', 'created_by', 'updated_by'];
    protected static $logAttributes = ['id', 'joint_id', 'malpot', 'district_id', 'local_bodies_id', 'wardno', 'sheet_no', 'kitta_no', 'area', 'remarks', 'status', 'created_by', 'updated_by'];


}
