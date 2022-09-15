<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Facility;
use App\Http\Requests\LoanCreateRequest;
use App\JointFacilities;
use App\JointLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class JointLoanController extends Controller
{
    public function __construct()

    {
        $this->middleware('auth');
    }


    public function store(LoanCreateRequest $request)
    {

        $id = $request->get('id');
        $bid = $request->get('bid');

        if($request->input('words')=='s[kof C)f /sd /fVg\'xf]nf .')
        {
            Session::flash('danger', 'Please Convert Loan Amount by clicking "Convert to Word".');
            return redirect()->route('joint_facilities.index', compact('bid'));
        }

        function Dinesh_money_format($money){
            $len = strlen($money);
            $m = '';
            $money = strrev($money);
            for($i=0;$i<$len;$i++){
                if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i!=$len){
                    $m .=',';
                }
                $m .=$money[$i];
            }
            return strrev($m);
        }
        $amount=Dinesh_money_format($request->input('amount'));



        if ($id) {
//    for old
            $loan = JointLoan::find($id);
            $loan->loan_amount = $amount;
            $loan->loan_amount_words = $request->input('words');
            $loan->offerletter_day = $request->input('offerletter_day');
            $loan->offerletter_month = $request->input('offerletter_month');
            $loan->offerletter_year = $request->input('offerletter_year');
            $loan->branch_id = $request->input('branch_id');
            if(Auth::user()->user_type=='user') {
                $loan->document_status = 'Pending';
                $loan->document_remarks = 'Not Submitted Yet.';
            }

            $loan->updated_by = Auth::user()->id;
            $status = null;
            try {
                $status = $loan->update();

            } catch (\Exception $e) {

            }
            if ($status) {
                Session::flash('success', 'Loan Details Updated Successfully');
            } else {

                Session::flash('danger', 'Unable to update loan details');
                return redirect()->route('joint_facilities.index', compact('bid'));
            }

        } else {
            $data = JointLoan::create([
                'borrower_id'=>$bid,
                'loan_amount' => $amount,
                'loan_amount_words' => $request->input('words'),
                'offerletter_day' => $request->input('offerletter_day'),
                'offerletter_month' => $request->input('offerletter_month'),
                'offerletter_year' => $request->input('offerletter_year'),
                'branch_id' => $request->input('branch_id'),
                'document_status' => 'pending',
                'document_remarks'=>'Document not submitted.',
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,

            ]);

            if ($data) {
                Session::flash('success', 'Loan Details Inserted Successfully');
            } else {
                Session::flash('danger', 'Failed to Insert Loan Details, please Try again.');
                return redirect()->route('joint_facilities.index',compact('bid'));
            }
        }

        $status = JointFacilities::where([
            ['borrower_id', $bid],
            ['status', '1']
        ])->first();
        $check = JointLoan::where([
            ['borrower_id', $bid]
        ])->first();

        if ($status && $check) {
            Session::flash('success', 'Please Review the inserted data.');
            return redirect()->route('joint_review.index', compact('bid'));
        } else {
            Session::flash('warning', 'Please add banking facilities and loan details first.');
            return redirect()->route('joint_facilities.index', compact('bid'));
        }





    }



}



