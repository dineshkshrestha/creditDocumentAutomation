<?php

namespace App\Http\Controllers;

use App\AuthorizedPerson;
use App\CorporateBorrower;
use App\CorporateGuarantor;
use App\CorporatePropertyOwner;
use App\Department;
use App\District;
use App\Http\Requests\CorporateCreateRequest;
use App\Http\Requests\CorporateGuarantorCreateRequest;
use App\Http\Requests\PersonalCreateRequest;
use App\LocalBodies;
use App\Ministry;
use App\PersonalBorrower;
use App\PersonalGuarantor;
use App\JointGuarantorBorrower;
use App\PersonalPropertyOwner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class JointGuarantorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function joint_guarantor_index($bid)
    {

        $personal_borrower = PersonalBorrower::all();
        $personal_guarantor = PersonalGuarantor::all();
        $personal_property_owner = PersonalPropertyOwner::all();
//list of available guarantors for borrower
        $personal_guarantor_list = JointGuarantorBorrower::where([
            ['borrower_id', $bid],
        ])->get();
        $district = [];
        $localbody = [];
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }
        $local = LocalBodies::select('id', 'name','body_type')->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }

        return view('joint_guarantor.index', compact('bid', 'personal_guarantor_list', 'localbody', 'district', 'personal_borrower', 'personal_guarantor', 'personal_property_owner'));
    }

    public function joint_borrower_guarantor_select($bid, $id)
    {

        $borrower = PersonalBorrower::find($id);
//            checking whether it exist or not
        $check = PersonalGuarantor::where([
            ['english_name', $borrower->english_name],
            ['grandfather_name', $borrower->grandfather_name],
            ['father_name', $borrower->father_name,],
            ['citizenship_number', $borrower->citizenship_number]
        ])->first();
//            if doesnot exist
        if (!$check) {
            $data = PersonalGuarantor::create([
                'english_name' => $borrower->english_name,
                'nepali_name' => $borrower->nepali_name,
                'client_id' => $borrower->client_id,
                'gender' => $borrower->gender,
                'grandfather_name' => $borrower->grandfather_name,
                'grandfather_relation' => $borrower->grandfather_relation,
                'father_name' => $borrower->father_name,
                'father_relation' => $borrower->father_relation,
                'spouse_name' => $borrower->spouse_name,
                'spouse_relation' => $borrower->spouse_relation,
                'district_id' => $borrower->district_id,
                'local_bodies_id' => $borrower->local_bodies_id,
                'wardno' => $borrower->wardno,
                'dob_year' => $borrower->dob_year,
                'phone' => $borrower->phone,
                'dob_month' => $borrower->dob_month,
                'dob_day' => $borrower->dob_day,
                'citizenship_number' => $borrower->citizenship_number,
                'issued_year' => $borrower->issued_year,
                'issued_month' => $borrower->issued_month,
                'issued_day' => $borrower->issued_day,
                'issued_district' => $borrower->issued_district,
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
//                getting inserted id
            $gid = $data->id;
            $data1 = JointGuarantorBorrower::create([
                'borrower_id' => $bid,
                'personal_guarantor_id' => $gid,
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
            if ($data && $data1) {
                Session::flash('success', 'Guarantor added successfully.');
            } else {
                Session::flash('danger', 'Personal Guarantor Creation Failed, Please Try Again.');
            }
            return redirect()->route('joint_guarantor.index', compact('bid'));

        } else {

//                if name already exist in guarantor
            $check1 = JointGuarantorBorrower::where([
                ['borrower_id', $bid],
                ['personal_guarantor_id', $check->id]
            ])->first();

//            if doesnot exist
            if (!$check1) {

                $data2 = JointGuarantorBorrower::create([
                    'borrower_id' => $bid,
                    'personal_guarantor_id' => $check->id,
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                ]);
                if ($data2) {
                    Session::flash('success', 'Guarantor added successfully.');
                } else {
                    Session::flash('danger', 'Personal Guarantor Creation Failed, Please Try Again.');
                }

                return redirect()->route('joint_guarantor.index', compact('bid'));


            } else {
                //if exist
                Session::flash('warning', 'Personal Guarantor Already Exist.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            }
        }
    }

    public function joint_guarantor_select($bid, $id)
    {

//            checking whether it exist or not

        $check = JointGuarantorBorrower::where([
            ['borrower_id', $bid],
            ['personal_guarantor_id', $id],
        ])->first();
        //            if doesnot exist
        if (!$check) {
            $data = JointGuarantorBorrower::create([
                'borrower_id' => $bid,
                'personal_guarantor_id' => $id,
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
            if ($data) {
                Session::flash('success', 'Guarantor added successfully.');
            } else {
                Session::flash('danger', 'Personal Guarantor Creation Failed, Please Try Again.');
            }
            return redirect()->route('joint_guarantor.index', compact('bid'));
        } else {
            //if exist
            Session::flash('warning', 'Personal Guarantor Already Exist.');
            return redirect()->route('joint_guarantor.index', compact('bid'));
        }

    }

    public function joint_property_owner_select($bid, $id)
    {

        $borrower = PersonalPropertyOwner::find($id);
//            checking whether it exist or not
        $check = PersonalGuarantor::where([
            ['english_name', $borrower->english_name],
            ['grandfather_name', $borrower->grandfather_name],
            ['father_name', $borrower->father_name,],
            ['citizenship_number', $borrower->citizenship_number]
        ])->first();
//            if doesnot exist
        if (!$check) {
            $data = PersonalGuarantor::create([
                'english_name' => $borrower->english_name,
                'nepali_name' => $borrower->nepali_name,
                'client_id' => $borrower->client_id,
                'gender' => $borrower->gender,
                'grandfather_name' => $borrower->grandfather_name,
                'grandfather_relation' => $borrower->grandfather_relation,
                'father_name' => $borrower->father_name,
                'father_relation' => $borrower->father_relation,
                'spouse_name' => $borrower->spouse_name,
                'spouse_relation' => $borrower->spouse_relation,
                'district_id' => $borrower->district_id,
                'local_bodies_id' => $borrower->local_bodies_id,
                'wardno' => $borrower->wardno,
                'phone' => $borrower->phone,
                'dob_year' => $borrower->dob_year,
                'dob_month' => $borrower->dob_month,
                'dob_day' => $borrower->dob_day,
                'citizenship_number' => $borrower->citizenship_number,
                'issued_year' => $borrower->issued_year,
                'issued_month' => $borrower->issued_month,
                'issued_day' => $borrower->issued_day,
                'issued_district' => $borrower->issued_district,
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
//                getting inserted id
            $gid = $data->id;
            $data1 = JointGuarantorBorrower::create([
                'borrower_id' => $bid,
                'personal_guarantor_id' => $gid,
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
            if ($data && $data1) {
                Session::flash('success', 'Guarantor added successfully.');
            } else {
                Session::flash('danger', 'Personal Guarantor Creation Failed, Please Try Again.');
            }
            return redirect()->route('joint_guarantor.index', compact('bid'));
        } else {
//                if name already exist in guarantor
            $check1 = JointGuarantorBorrower::where([
                ['borrower_id', $bid],
                ['personal_guarantor_id', $check->id],
            ])->first();

//            if doesnot exist
            if (!$check1) {

                $data2 = JointGuarantorBorrower::create([
                    'borrower_id' => $bid,
                    'personal_guarantor_id' => $check->id,
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                ]);
                if ($data2) {
                    Session::flash('success', 'Guarantor added successfully.');
                } else {
                    Session::flash('danger', 'Personal Guarantor Creation Failed, Please Try Again.');
                }

                return redirect()->route('joint_guarantor.index', compact('bid'));


            } else {
                //if exist
                Session::flash('warning', 'Personal Guarantor Already Exist.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            }

        }

    }


    public function guarantor_destroy($bid, $gid)
    {
        $guarantor = JointGuarantorBorrower::find($gid);
        $status=null;
        try {
            $status = $guarantor->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Guarantor Removed Successfully');
        } else {

            Session::flash('danger', 'Failed to remove Guarantor, Please Try again.');
        }
        return redirect()->route('joint_guarantor.index', compact('bid'));
    }

    public function guarantor_edit($bid, $gid)
    {
        $guarantor = JointGuarantorBorrower::find($gid);
        if ($guarantor->status == 1) {
            $guarantor->status = '0';
            $status = null;
            try {
                $status = $guarantor->update();

            } catch (\Exception $e) {

            }
        } else {
            $guarantor->status = '1';
            $status = null;
            try {
                $status = $guarantor->update();

            } catch (\Exception $e) {

            }
        }

        if ($status) {
            Session::flash('success', 'Status Changed Successfully');
        } else {

            Session::flash('danger', 'Failed Change Status, Please Try again.');
        }
        return redirect()->route('joint_guarantor.index', compact('bid'));

    }

    public function proceed($bid)
    {
        Session::flash('success', 'Please Choose property owner and property.');
        return redirect()->route('joint_property.index', compact('bid'));

    }



    public function joint_guarantor_store(PersonalCreateRequest $request, $bid)
    {
        //        stores personal borrower
        $data = PersonalGuarantor::create([
            'english_name' => $request->input('english_name'),
            'nepali_name' => $request->input('nepali_name'),
            'client_id' => $request->input('client_id'),
            'gender' => $request->input('gender'),
            'grandfather_name' => $request->input('grandfather_name'),
            'grandfather_relation' => $request->input('grandfather_relation'),
            'father_name' => $request->input('father_name'),
            'father_relation' => $request->input('father_relation'),
            'spouse_name' => $request->input('spouse_name'),
            'spouse_relation' => $request->input('spouse_relation'),
            'district_id' => $request->input('district_id'),
            'local_bodies_id' => $request->input('local_bodies_id'),
            'wardno' => $request->input('wardno'),
            'phone' => $request->input('phone'),
            'dob_year' => $request->input('dob_year'),
            'dob_month' => $request->input('dob_month'),
            'dob_day' => $request->input('dob_day'),
            'citizenship_number' => $request->input('citizenship_number'),
            'issued_year' => $request->input('issued_year'),
            'issued_month' => $request->input('issued_month'),
            'issued_day' => $request->input('issued_day'),
            'issued_district' => $request->input('issued_district'),
            'status' => '1',
            'created_by' => Auth::user()->id,
        ]);
//        getting id of inserted data

        $data2 = JointGuarantorBorrower::create([
            'borrower_id' => $bid,
            'personal_guarantor_id' => $data->id,
            'status' => '1',
            'created_by' => Auth::user()->id,
        ]);


        if ($data && $data2) {
            Session::flash('success', 'Guarantor Created Successfully');
        } else {
            Session::flash('danger', 'Guarantor Creation Failed, Please Try Again.');
            return redirect()->route('joint_guarantor.personal_create', compact('bid'));
        }
        return redirect()->route('joint_guarantor.index', compact('bid'));


    }

}
