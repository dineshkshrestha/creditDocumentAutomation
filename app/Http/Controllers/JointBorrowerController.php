<?php

namespace App\Http\Controllers;

use App\District;
use App\Http\Requests\PersonalCreateRequest;
use App\Http\Requests\PersonalGuarantorUpdateRequest;
use App\Http\Requests\PersonalPropertyOwnerUpdateRequest;
use App\Http\Requests\PersonalUpdateRequest;
use App\JointBorrower;
use App\LocalBodies;
use App\PersonalBorrower;
use App\PersonalGuarantor;
use App\PersonalPropertyOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class JointBorrowerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $joint_borrower = PersonalBorrower::where([['status','1']])->get();
        $joint = JointBorrower::all();
        //district
        $district = [];
        $localbody = [];
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }
//        local bodies
        $local = LocalBodies::select('id', 'name', 'body_type')->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name . ' ' . $l->body_type;
        }

        return view('joint_borrower.index', compact('joint_borrower', 'district', 'localbody', 'joint'));
    }

    public function joint_borrower_store(Request $request)
    {

       $data=$request->input('checkbox-1');

        if ($data[0] == null) {
            Session::flash('warning', 'Cannot create Joint Borrower, Please Try Again ');
            return redirect()->route('joint_borrower.index');

        }

        $count = count($request->input('checkbox-1'));
        $borrower = ($request->input('checkbox-1'));
        if ($count > 3 || $count < 2) {
            Session::flash('warning', 'Cannot create Joint Borrower, Please Try Again ');
            return redirect()->route('joint_borrower.index');

        }
        if ($count == 2) {
            $j1 = $borrower[0];
            $j2 = $borrower[1];
            $j3 = null;
            if ($id = JointBorrower::where([['joint1', $j1], ['joint2', $j2], ['joint3', $j3]])->first()) {
                $bid = $id->id;
                Session::flash('success', 'Selected Existing Joint Borrower. Please add Guarantor.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            } elseif ($id = JointBorrower::where([['joint1', $j1], ['joint3', $j2], ['joint2', $j3]])->first()) {
                $bid = $id->id;
                Session::flash('success', 'Selected Existing Joint Borrower. Please add Guarantor.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            } elseif ($id = JointBorrower::where([['joint2', $j1], ['joint1', $j2], ['joint3', $j3]])->first()) {
                $bid = $id->id;
                Session::flash('success', 'Selected Existing Joint Borrower. Please add Guarantor.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            } elseif ($id = JointBorrower::where([['joint2', $j1], ['joint3', $j2], ['joint1', $j3]])->first()) {
                $bid = $id->id;
                Session::flash('success', 'Selected Existing Joint Borrower. Please add Guarantor.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            } elseif ($id = JointBorrower::where([['joint3', $j1], ['joint1', $j2], ['joint2', $j3]])->first()) {
                $bid = $id->id;
                Session::flash('success', 'Selected Existing Joint Borrower. Please add Guarantor.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            } elseif ($id = JointBorrower::where([['joint3', $j1], ['joint2', $j2], ['joint1', $j3]])->first()) {
                $bid = $id->id;
                Session::flash('success', 'Selected Existing Joint Borrower. Please add Guarantor.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            } else {
                $data = JointBorrower::create([
                    'joint1' => $j1,
                    'joint2' => $j2,
                    'created_by' => Auth::user()->id,
                ]);
                if ($data) {
                    $bid = $data->id;
                    Session::flash('success', 'Borrower Created Successfully. Please add Guarantor.');
                    return redirect()->route('joint_guarantor.index', compact('bid'));
                } else {
                    Session::flash('danger', 'Sorry! Failed to add joint borrowers.');
                    return redirect()->route('joint_borrower.index');
                }
            }

        }
        if ($count == 3) {
            $j1 = $borrower[0];
            $j2 = $borrower[1];
            $j3 = $borrower[2];
            if ($id = JointBorrower::where([['joint1', $j1], ['joint2', $j2], ['joint3', $j3]])->first()) {
                $bid = $id->id;
                Session::flash('success', 'Selected Existing Joint Borrower. Please add Guarantor.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            } elseif ($id = JointBorrower::where([['joint1', $j2], ['joint2', $j1], ['joint3', $j3]])->first()) {
                $bid = $id->id;
                Session::flash('success', 'Selected Existing Joint Borrower. Please add Guarantor.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            } elseif ($id = JointBorrower::where([['joint1', $j2], ['joint2', $j3], ['joint3', $j1]])->first()) {
                $bid = $id->id;
                Session::flash('success', 'Selected Existing Joint Borrower. Please add Guarantor.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            } elseif ($id = JointBorrower::where([['joint1', $j3], ['joint2', $j2], ['joint3', $j1]])->first()) {
                $bid = $id->id;
                Session::flash('success', 'Selected Existing Joint Borrower. Please add Guarantor.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            } elseif ($id = JointBorrower::where([['joint1', $j3], ['joint2', $j1], ['joint3', $j2]])->first()) {
                $bid = $id->id;
                Session::flash('success', 'Selected Existing Joint Borrower. Please add Guarantor.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            } elseif ($id = JointBorrower::where([['joint1', $j1], ['joint2', $j3], ['joint3', $j2]])->first()) {
                $bid = $id->id;
                Session::flash('success', 'Selected Existing Joint Borrower. Please add Guarantor.');
                return redirect()->route('joint_guarantor.index', compact('bid'));
            } else {

                $data = JointBorrower::create([
                    'joint1' => $j1,
                    'joint2' => $j2,
                    'joint3' => $j3,
                    'created_by' => Auth::user()->id,
                ]);
                if ($data) {
                    $bid = $data->id;
                    Session::flash('success', 'Borrower Created Successfully. Please add Guarantor.');
                    return redirect()->route('joint_guarantor.index', compact('bid'));
                } else {
                    Session::flash('danger', 'Sorry! Failed to add joint borrowers.');
                    return redirect()->route('joint_borrower.index');
                }

            }


        }


    }


    public function store(PersonalCreateRequest $request)
    {
//        stores joint borrower
        $data = PersonalBorrower::create([
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
            'single' => '1',
            'joint' => '0',
            'status' => '1',
            'created_by' => Auth::user()->id,
        ]);
//        getting id of inserted data
        $bid = $data->id;
        if ($data) {
            Session::flash('success', 'Personal Borrower Created Successfully, Now choose as joint borrower');
        } else {
            Session::flash('danger', 'Personal Borrower Creation Failed, Please Try Again.');
            return redirect()->route('joint_borrower.index');
        }
        return redirect()->route('joint_borrower.index');

    }

    public function joint_property_owner_borrower_index()
    {
        $property_owner = PersonalPropertyOwner::all();
        return view('joint_borrower.property_owner_borrower', compact('property_owner'));
    }

    public function joint_guarantor_borrower_index()
    {
        $guarantor = PersonalGuarantor::all();
        return view('joint_borrower.personal_guarantor_borrower', compact('guarantor'));
    }

    public function joint_guarantor_borrower($gid)
    {
        $guarantor = PersonalGuarantor::find($gid);
//        checking the status
        if ($guarantor->status == 1) {
//            checking whether it exist or not
            $check = PersonalBorrower::where([
                ['english_name', $guarantor->english_name],
                ['grandfather_name', $guarantor->grandfather_name],
                ['father_name', $guarantor->father_name],
                ['citizenship_number', $guarantor->citizenship_number]
            ])->first();
//            if doesnot exist
            if (!$check) {
                $data = PersonalBorrower::create([
                    'english_name' => $guarantor->english_name,
                    'nepali_name' => $guarantor->nepali_name,
                    'client_id' => $guarantor->client_id,
                    'gender' => $guarantor->gender,
                    'grandfather_name' => $guarantor->grandfather_name,
                    'grandfather_relation' => $guarantor->grandfather_relation,
                    'father_name' => $guarantor->father_name,
                    'father_relation' => $guarantor->father_relation,
                    'spouse_name' => $guarantor->spouse_name,
                    'spouse_relation' => $guarantor->spouse_relation,
                    'district_id' => $guarantor->district_id,
                    'local_bodies_id' => $guarantor->local_bodies_id,
                    'wardno' => $guarantor->wardno,
                    'dob_year' => $guarantor->dob_year,
                    'dob_month' => $guarantor->dob_month,
                    'dob_day' => $guarantor->dob_day,
                    'citizenship_number' => $guarantor->citizenship_number,
                    'phone' => $guarantor->phone,
                    'issued_year' => $guarantor->issued_year,
                    'issued_month' => $guarantor->issued_month,
                    'issued_day' => $guarantor->issued_day,
                    'issued_district' => $guarantor->issued_district,
                    'single' => '1',
                    'joint' => '0',
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                ]);
//                getting inserted id
                $bid = $data->id;
                if ($data) {
                } else {
                    Session::flash('danger', 'Personal Borrower Creation Failed, Please Try Again.');
                    return redirect()->route('joint_guarantor_borrower.index');
                }
            } else {
                Session::flash('danger', 'The borrower you are trying to choose is already in borrower list please choose from borrower list.');
                return redirect()->route('joint_guarantor_borrower.index');
            }
            return redirect()->route('joint_borrower.index');

        } else {
            Session::flash('warning', 'The borrower you are trying to choose is deactivated. Please choose another borrower or edit the borrower status.');
            return redirect()->route('joint_guarantor_borrower.index');
        }
    }

    public function joint_property_owner_borrower($pid)
    {
        $property_owner = PersonalPropertyOwner::find($pid);
//        check whether property owner is active or not
        if ($property_owner->status == 1) {
//            if active then it will check whether property owner is present in borrower list or not
            $check = PersonalBorrower::where([
                ['english_name', $property_owner->english_name],
                ['grandfather_name', $property_owner->grandfather_name],
                ['father_name', $property_owner->father_name],
                ['citizenship_number', $property_owner->citizenship_number]
            ])->first();
//            if the property owner is not present in borrower list then it will copy to borrower list
            if (!$check) {
                $data = PersonalBorrower::create([
                    'english_name' => $property_owner->english_name,
                    'nepali_name' => $property_owner->nepali_name,
                    'client_id' => $property_owner->client_id,
                    'gender' => $property_owner->gender,
                    'grandfather_name' => $property_owner->grandfather_name,
                    'grandfather_relation' => $property_owner->grandfather_relation,
                    'father_name' => $property_owner->father_name,
                    'father_relation' => $property_owner->father_relation,
                    'spouse_name' => $property_owner->spouse_name,
                    'spouse_relation' => $property_owner->spouse_relation,
                    'phone' => $property_owner->phone,
                    'district_id' => $property_owner->district_id,
                    'local_bodies_id' => $property_owner->local_bodies_id,
                    'wardno' => $property_owner->wardno,
                    'dob_year' => $property_owner->dob_year,
                    'dob_month' => $property_owner->dob_month,
                    'dob_day' => $property_owner->dob_day,
                    'citizenship_number' => $property_owner->citizenship_number,
                    'issued_year' => $property_owner->issued_year,
                    'issued_month' => $property_owner->issued_month,
                    'issued_day' => $property_owner->issued_day,
                    'issued_district' => $property_owner->issued_district,
                    'single' => '1',
                    'joint' => '0',
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                ]);
                $bid = $data->id;
                if ($data) {
                } else {
                    Session::flash('danger', 'Personal Borrower Creation Failed, Please Try Again.');
                    return redirect()->route('joint_property_owner_borrower.index');
                }
            } else {
                Session::flash('danger', 'The borrower you are trying to choose is already in borrower list, Please choose from borrower list.');
                return redirect()->route('joint_property_owner_borrower.index');
            }
            return redirect()->route('joint_borrower.index');
        } else {
            Session::flash('warning', 'The borrower you are trying to choose is deactivated. Please choose another borrower or edit the borrower status.');
            return redirect()->route('joint_property_owner_borrower.index');
        }
    }

    public function joint_borrower_edit($id)
    {
        $borrower = PersonalBorrower::find($id);
        $district = [];
        $dis = District::select('id', 'name')->get();

        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }

        $localbody = [];
        $local = LocalBodies::select('id', 'name', 'body_type')->where([['district_id', $borrower->district_id]])->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name . ' ' . $l->body_type;
        }


        return view('joint_borrower.edit', compact('borrower', 'district', 'localbody'));

    }

    public function joint_borrower_update(PersonalUpdateRequest $request, $id)
    {
        $borrower = PersonalBorrower::find($id);
        $borrower->english_name = $request->input('english_name');
        $borrower->nepali_name = $request->input('nepali_name');
        $borrower->client_id = $request->input('client_id');
        $borrower->gender = $request->input('gender');
        $borrower->grandfather_name = $request->input('grandfather_name');
        $borrower->grandfather_relation = $request->input('grandfather_relation');
        $borrower->father_name = $request->input('father_name');
        $borrower->father_relation = $request->input('father_relation');
        $borrower->spouse_name = $request->input('spouse_name');
        $borrower->spouse_relation = $request->input('spouse_relation');
        $borrower->district_id = $request->input('district_id');
        $borrower->local_bodies_id = $request->input('local_bodies_id');
        $borrower->wardno = $request->input('wardno');
        $borrower->dob_year = $request->input('dob_year');
        $borrower->dob_month = $request->input('dob_month');
        $borrower->dob_day = $request->input('dob_day');
        $borrower->citizenship_number = $request->input('citizenship_number');
        $borrower->issued_year = $request->input('issued_year');
        $borrower->issued_month = $request->input('issued_month');
        $borrower->issued_day = $request->input('issued_day');
        $borrower->issued_district = $request->input('issued_district');
        $borrower->phone = $request->input('phone');
        $borrower->status = $request->input('status');
        $borrower->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $borrower->update();

        } catch (\Exception $e) {

        }
        if ($status) {
            Session::flash('success', 'Personal Borrower Updated Successfully');
        } else {
            Session::flash('danger', 'Personal Borrower Update Failed');
        }
        return redirect()->route('joint_borrower.index');
    }


    public function joint_guarantor_borrower_edit($id)
    {
        $guarantor = PersonalGuarantor::find($id);
        $district = [];
        $dis = District::select('id', 'name')->get();

        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }

        $localbody = [];
        $local = LocalBodies::select('id', 'name', 'body_type')->where([['district_id', $guarantor->district_id]])->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name . ' ' . $l->body_type;
        }


        return view('joint_borrower.personal_guarantor_borrower_edit', compact('guarantor', 'district', 'localbody'));

    }

    public function joint_guarantor_borrower_update(PersonalGuarantorUpdateRequest $request, $id)
    {
        $guarantor = PersonalGuarantor::find($id);

        $guarantor->english_name = $request->input('english_name');
        $guarantor->nepali_name = $request->input('nepali_name');
        $guarantor->client_id = $request->input('client_id');
        $guarantor->gender = $request->input('gender');
        $guarantor->grandfather_name = $request->input('grandfather_name');
        $guarantor->grandfather_relation = $request->input('grandfather_relation');
        $guarantor->father_name = $request->input('father_name');
        $guarantor->father_relation = $request->input('father_relation');
        $guarantor->spouse_name = $request->input('spouse_name');
        $guarantor->spouse_relation = $request->input('spouse_relation');
        $guarantor->district_id = $request->input('district_id');
        $guarantor->local_bodies_id = $request->input('local_bodies_id');
        $guarantor->wardno = $request->input('wardno');
        $guarantor->dob_year = $request->input('dob_year');
        $guarantor->dob_month = $request->input('dob_month');
        $guarantor->dob_day = $request->input('dob_day');
        $guarantor->citizenship_number = $request->input('citizenship_number');
        $guarantor->issued_year = $request->input('issued_year');
        $guarantor->issued_month = $request->input('issued_month');
        $guarantor->issued_day = $request->input('issued_day');
        $guarantor->issued_district = $request->input('issued_district');
        $guarantor->status = $request->input('status');
        $guarantor->phone = $request->input('phone');
        $guarantor->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $guarantor->update();

        } catch (\Exception $e) {

        }

        if ($status) {
            Session::flash('success', 'Personal Guarantor Updated Successfully');
        } else {

            Session::flash('danger', 'Personal Guarantor Update Failed');
        }
        return redirect()->route('joint_guarantor_borrower.index');
    }

    public function joint_property_owner_borrower_edit($id)
    {
        $property_owner = PersonalPropertyOwner::find($id);
        $district = [];
        $dis = District::select('id', 'name')->get();

        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }
        $localbody = [];
        $local = LocalBodies::select('id', 'name', 'body_type')->where([['district_id', $property_owner->district_id]])->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name . ' ' . $l->body_type;
        }


        return view('joint_borrower.personal_property_owner_borrower_edit', compact('property_owner', 'district', 'localbody'));

    }


    public function joint_property_owner_borrower_update(PersonalPropertyOwnerUpdateRequest $request, $id)
    {
        $property_owner = PersonalPropertyOwner::find($id);
        $property_owner->english_name = $request->input('english_name');
        $property_owner->nepali_name = $request->input('nepali_name');
        $property_owner->client_id = $request->input('client_id');
        $property_owner->gender = $request->input('gender');
        $property_owner->grandfather_name = $request->input('grandfather_name');
        $property_owner->grandfather_relation = $request->input('grandfather_relation');
        $property_owner->father_name = $request->input('father_name');
        $property_owner->father_relation = $request->input('father_relation');
        $property_owner->spouse_name = $request->input('spouse_name');
        $property_owner->spouse_relation = $request->input('spouse_relation');
        $property_owner->district_id = $request->input('district_id');
        $property_owner->local_bodies_id = $request->input('local_bodies_id');
        $property_owner->wardno = $request->input('wardno');
        $property_owner->dob_year = $request->input('dob_year');
        $property_owner->dob_month = $request->input('dob_month');
        $property_owner->dob_day = $request->input('dob_day');
        $property_owner->phone = $request->input('phone');
        $property_owner->citizenship_number = $request->input('citizenship_number');
        $property_owner->issued_year = $request->input('issued_year');
        $property_owner->issued_month = $request->input('issued_month');
        $property_owner->issued_day = $request->input('issued_day');
        $property_owner->issued_district = $request->input('issued_district');
        $property_owner->status = $request->input('status');
        $property_owner->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $property_owner->update();

        } catch (\Exception $e) {

        }

        if ($status) {
            Session::flash('success', 'Personal Property Owner Updated Successfully');
        } else {

            Session::flash('danger', 'Personal Property Owner Update Failed');
        }
        return redirect()->route('joint_property_owner_borrower.index');
    }


    public function joint_borrower_show($id)
    {
        $borrower = PersonalBorrower::find($id);
        return view('joint_borrower.show', compact('borrower'));
    }


}
