<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Facility;
use App\Http\Requests\PersonalFacilityUpdateRequest;
use App\JointFacilities;
use App\JointLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class JointFacilitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($bid)
    {

        $loan = JointLoan::where([
            ['borrower_id', $bid],
        ])->first();
        $branch = [];
        $branches = Branch::select('id', 'location')->get();
        foreach ($branches as $b) {
            $branch[''] = 'zfvf';
            $branch[$b->id] = $b->location;
        }

        $facility = [];
        $fac = Facility::select('id', 'name')->get();
        foreach ($fac as $f) {
            $facility[$f->id] = $f->name;
        }

        $facilities = JointFacilities::where([
            ['borrower_id', $bid]])->get();
        $total = 0;
        foreach ($facilities as $f) {
            $a = $f->amount;
            $b = str_replace(',', '', $a);
            if (is_numeric($b)) {
                $a = $b;
                if ($f->within == '1') {
                } else {
                    $total = $total + $a;
                }
            }

        }


        return view('joint_facilities.index', compact('bid', 'facilities', 'facility', 'loan', 'branch', 'total'));
    }


    public function store(Request $request)
    {

        $result = false;

        $tenure = $request->get('tenure');
        $tyear = $request->get('tyear');
        $tmonth = $request->get('tmonth');
        $tday = $request->get('tday');
        $amount = $request->get('amount');
        $rate = $request->get('rate');
        $remarks = $request->get('remarks');
        $facility = $request->get('facility');
        $within = $request->get('within');


        function Dinesh_money_format($money)
        {
            $len = strlen($money);
            $m = '';
            $money = strrev($money);
            for ($i = 0; $i < $len; $i++) {
                if (($i == 3 || ($i > 3 && ($i - 1) % 2 == 0)) && $i != $len) {
                    $m .= ',';
                }
                $m .= $money[$i];
            }
            return strrev($m);
        }


        if ($amount && $rate) {

            $amt = Dinesh_money_format($amount);

            $result = JointFacilities::create([
                'borrower_id' => $request->input('bid'),
                'facility_id' => $facility,
                'tenure' => $tenure,
                'tyear' => $tyear,
                'tmonth' => $tmonth,
                'tday' => $tday,
                'amount' => $amt,
                'within' => $within,
                'rate' => $rate,
                'remarks' => $remarks,
                'status' => '1',
                'created_by' => Auth::user()->id


            ]);
        }

        $bid = $request->input('bid');


        if ($result) {
            Session::flash('success', 'Facilities Added Successfully');
        } else {
            Session::flash('danger', 'Unable to add Facilities, Please Try again');
        }
        return redirect()->route('joint_facilities.index', compact('bid'));
    }


    public function edit($bid, $id)
    {
        $facility = JointFacilities::find($id);

        $facilities = [];
        $fac = Facility::select('id', 'name')->get();
        foreach ($fac as $f) {
            $facilities[$f->id] = $f->name;
        }
        $a = $facility->amount;
        $b = str_replace(',', '', $a);
        if (is_numeric($b)) {
            $a = $b;
        }


        return view('joint_facilities.edit_facilities', compact('bid', 'facility', 'facilities', 'a'));


    }

    public function update(PersonalFacilityUpdateRequest $request, $id)
    {

        function Dinesh_money_format($money)
        {
            $len = strlen($money);
            $m = '';
            $money = strrev($money);
            for ($i = 0; $i < $len; $i++) {
                if (($i == 3 || ($i > 3 && ($i - 1) % 2 == 0)) && $i != $len) {
                    $m .= ',';
                }
                $m .= $money[$i];
            }
            return strrev($m);
        }


        $amt = Dinesh_money_format($request->input('amount'));


        $bid = $request->input('bid');
        $id = $request->input('id');
        $facility = JointFacilities::find($id);

        $facility->facility_id = $request->input('facility');
        $facility->amount = $amt;
        $facility->rate = $request->input('rate');
        $facility->remarks = $request->input('remarks');
        $facility->tenure = $request->input('tenure');
        $facility->tyear = $request->input('tyear');
        $facility->tmonth = $request->input('tmonth');
        $facility->within = $request->input('within');
        $facility->tday = $request->input('tday');
        $result = null;
        try {
            $result = $facility->update();

        } catch (\Exception $e) {

        }

        if ($result) {
            Session::flash('success', 'Facility Updated Successfully.');

        } else {
            Session::flash('warning', 'Updating Facility Failed. Please Try again.');
            return redirect()->route('joint_facilities.edit', compact('bid', 'id'));

        }
        return redirect()->route('joint_facilities.index', compact('bid'));
    }


    public function destroy(Request $request, $id)
    {
        $bid = $request->input('bid');

        $facility = JointFacilities::find($id);
        $result=null;
        try {
            $result = $facility->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($result) {
            Session::flash('success', 'Facility Deleted Successfully.');
        }
        return redirect()->route('joint_facilities.index', compact('bid'));
    }


}