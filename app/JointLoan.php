<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class JointLoan extends Model
{
    use LogsActivity;
    protected $table = 'tbl_joint_loan';
    protected $fillable = ['id', 'borrower_id',  'loan_amount', 'loan_amount_words', 'offerletter_day', 'offerletter_month', 'offerletter_year', 'branch_id',
        'approved_by', 'document_status', 'document_remarks', 'created_by', 'updated_by','submitted_by','submitted_at','approvedby','approved_at','rejected_by','rejected_at'];

    protected static $logAttributes = ['id', 'borrower_id', 'loan_amount', 'loan_amount_words', 'offerletter_day', 'offerletter_month', 'offerletter_year', 'branch_id',
        'approved_by', 'document_status', 'document_remarks',  'created_by', 'updated_by'];
}
