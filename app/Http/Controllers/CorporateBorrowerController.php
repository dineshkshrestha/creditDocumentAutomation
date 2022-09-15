<?php

namespace App\Http\Controllers;

use App\AuthorizedPerson;
use App\CorporateBorrower;
use App\CorporateGuarantor;
use App\CorporatePropertyOwner;
use App\Department;
use App\District;
use App\Http\Requests\CorporateCreateRequest;
use App\Http\Requests\CorporateUpdateRequest;
use App\LocalBodies;
use App\Ministry;
use App\PersonalBorrower;
use App\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CorporateBorrowerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function corporate_borrower_index()
    {

        $corporate_borrower = CorporateBorrower::all();
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }
        $localbody = [];
       $local = LocalBodies::select('id', 'name','body_type')->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }

        $min = Ministry::select('id', 'name')->get();
        foreach ($min as $m) {
            $ministry[$m->id] = $m->name;
        }
        $dep = Department::select('id', 'name')->get();
        foreach ($dep as $d) {
            $department[$d->id] = $d->name;
        }


        return view('corporate_borrower.index', compact('corporate_borrower', 'district', 'localbody', 'ministry', 'department'));
    }


    public function corporate_borrower_store(CorporateCreateRequest $request)
    {

        $check = AuthorizedPerson::where([
            ['english_name', $request->input('a_english_name')],
            ['grandfather_name', $request->input('a_grandfather_name')],
            ['father_name', $request->input('a_father_name')],
            ['citizenship_number', $request->input('a_citizenship_number')]
        ])->first();


        //            if the authorized person is present in authorized list
        if (!$check) {
            $data = AuthorizedPerson::create([
                'english_name' => $request->input('a_english_name'),
                'nepali_name' => $request->input('a_nepali_name'),
                'gender' => $request->input('a_gender'),
                'grandfather_name' => $request->input('a_grandfather_name'),
                'grandfather_relation' => $request->input('a_grandfather_relation'),
                'father_name' => $request->input('a_father_name'),
                'father_relation' => $request->input('a_father_relation'),
                'spouse_name' => $request->input('a_spouse_name'),
                'spouse_relation' => $request->input('a_spouse_relation'),
                'district_id' => $request->input('a_district_id'),
                'local_bodies_id' => $request->input('a_local_bodies_id'),
                'wardno' => $request->input('a_wardno'),
                'dob_year' => $request->input('a_dob_year'),
                'dob_month' => $request->input('a_dob_month'),
                'dob_day' => $request->input('a_dob_day'),
                'citizenship_number' => $request->input('a_citizenship_number'),
                'issued_year' => $request->input('a_issued_year'),
                'issued_month' => $request->input('a_issued_month'),
                'issued_day' => $request->input('a_issued_day'),
                'issued_district' => $request->input('a_issued_district'),
                'post' => $request->input('a_post'),
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);

            $authorized_person_id = $data->id;
            if ($data) {
                $data1 = CorporateBorrower::create([
                    'english_name' => $request->input('english_name'),
                    'nepali_name' => $request->input('nepali_name'),
                    'client_id' => $request->input('client_id'),
                    'district_id' => $request->input('district_id'),
                    'local_bodies_id' => $request->input('local_bodies_id'),
                    'wardno' => $request->input('wardno'),
                    'phone' => $request->input('phone'),
                    'reg_year' => $request->input('reg_year'),
                    'reg_month' => $request->input('reg_month'),
                    'reg_day' => $request->input('reg_day'),
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                    'ministry_id' => $request->input('ministry_id'),
                    'department_id' => $request->input('department_id'),
                    'registration_number' => $request->input('registration_number'),
                    'authorized_person_id' => $authorized_person_id,
                ]);
                if ($data1) {
                    $bid = $data1->id;
                    Session::flash('success', 'Borrower Created successfully, Now add guarantors.');
                    return redirect()->route('corporate_guarantor.index', compact('bid'));
                } else {
                    Session::flash('danger', 'Error in creating Borrower, Please Try again.');
                    return redirect()->route('corporate_borrower.index');
                }
            } else {
                Session::flash('danger', 'Error in creating authorized person, Please Try again.');
                return redirect()->route('corporate_borrower.index');
            }
        } else {

            $data2 = CorporateBorrower::create([
                'english_name' => $request->input('english_name'),
                'nepali_name' => $request->input('nepali_name'),
                'client_id' => $request->input('client_id'),
                'district_id' => $request->input('district_id'),
                'local_bodies_id' => $request->input('local_bodies_id'),
                'wardno' => $request->input('wardno'),
                'phone' => $request->input('phone'),
                'reg_year' => $request->input('reg_year'),
                'reg_month' => $request->input('reg_month'),
                'reg_day' => $request->input('reg_day'),
                'status' => '1',
                'created_by' => Auth::user()->id,
                'ministry_id' => $request->input('ministry_id'),
                'department_id' => $request->input('department_id'),
                'registration_number' => $request->input('registration_number'),
                'authorized_person_id' => $check->id,
            ]);
            if ($data2) {
                $bid = $data2->id;
                Session::flash('success', 'Borrower Created successfully, Now add guarantors.');
                return redirect()->route('corporate_guarantor.index', compact('bid'));
            } else {
                Session::flash('danger', 'Error in creating Borrower, Please Try again.');
                return redirect()->route('corporate_property_owner_borrower.index');
            }

        }
    }


    public function corporate_borrower_edit($id)
    {
        $borrower = CorporateBorrower::find($id);
        $authorized_person = AuthorizedPerson::find($borrower->authorized_person_id);

        $min = Ministry::select('id', 'name')->get();
        foreach ($min as $m) {
            $ministry[$m->id] = $m->name;
        }
        $dep = Department::select('id', 'name')->get();
        foreach ($dep as $d) {
            $department[$d->id] = $d->name;
        }
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }

        $localbody = [];
        $local = LocalBodies::select('id', 'name','body_type')->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }

        return view('corporate_borrower.edit', compact('borrower', 'authorized_person', 'department', 'ministry', 'localbody', 'district'));
    }


    public function corporate_borrower_update(CorporateUpdateRequest $request, $id)
    {


        $borrower = CorporateBorrower::find($id);
        $borrower->english_name = $request->input('english_name');
        $borrower->nepali_name = $request->input('nepali_name');
        $borrower->client_id = $request->input('client_id');
        $borrower->reg_year = $request->input('reg_year');
        $borrower->reg_month = $request->input('reg_month');
        $borrower->reg_day = $request->input('reg_day');
        $borrower->ministry_id = $request->input('ministry_id');
        $borrower->department_id = $request->input('department_id');
        $borrower->registration_number = $request->input('registration_number');
        $borrower->district_id = $request->input('district_id');
        $borrower->local_bodies_id = $request->input('local_bodies_id');
        $borrower->wardno = $request->input('wardno');
        $borrower->phone = $request->input('phone');
        $borrower->status = $request->input('status');
        $borrower->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $borrower->update();

        } catch (\Exception $e) {

        }

        $authorized_person = AuthorizedPerson::find($borrower->authorized_person_id);
        $authorized_person->english_name = $request->input('a_english_name');
        $authorized_person->nepali_name = $request->input('a_nepali_name');
        $authorized_person->gender = $request->input('a_gender');
        $authorized_person->grandfather_name = $request->input('a_grandfather_name');
        $authorized_person->grandfather_relation = $request->input('a_grandfather_relation');
        $authorized_person->father_name = $request->input('a_father_name');
        $authorized_person->father_relation = $request->input('a_father_relation');
        $authorized_person->spouse_name = $request->input('a_spouse_name');
        $authorized_person->spouse_relation = $request->input('a_spouse_relation');
        $authorized_person->district_id = $request->input('a_district_id');
        $authorized_person->local_bodies_id = $request->input('a_local_bodies_id');
        $authorized_person->wardno = $request->input('a_wardno');
        $authorized_person->dob_year = $request->input('a_dob_year');
        $authorized_person->dob_month = $request->input('a_dob_month');
        $authorized_person->dob_day = $request->input('a_dob_day');
        $authorized_person->citizenship_number = $request->input('a_citizenship_number');
        $authorized_person->issued_year = $request->input('a_issued_year');
        $authorized_person->issued_month = $request->input('a_issued_month');
        $authorized_person->issued_day = $request->input('a_issued_day');
        $authorized_person->issued_district = $request->input('a_issued_district');
        $authorized_person->status = $request->input('status');
        $authorized_person->post = $request->input('a_post');
        $authorized_person->updated_by = Auth::user()->id;
        $status1 = null;
        try {
            $status1 = $authorized_person->update();

        } catch (\Exception $e) {

        }

        if ($status && $status1) {
            Session::flash('success', 'Corporate Borrower Updated Successfully');
        } else {

            Session::flash('danger', 'Corporate Borrower Update Failed');
        }
        return redirect()->route('corporate_borrower.index');

    }

    public function corporate_property_owner_borrower_index()
    {
        $property_owner = CorporatePropertyOwner::all();
        return view('corporate_borrower.property_owner_borrower', compact('property_owner'));
    }

    public function corporate_guarantor_borrower_index()
    {
        $guarantor = CorporateGuarantor::all();
        return view('corporate_borrower.corporate_guarantor_borrower', compact('guarantor'));
    }

    public function Corporate_guarantor_borrower($gid)
    {

        $guarantor = CorporateGuarantor::find($gid);
        $check = CorporateBorrower::where([
            ['registration_number', $guarantor->registration_number],
            ['english_name', $guarantor->english_name]
        ])->first();
        if (!$check) {
            $data = CorporateBorrower::create([
                'english_name' => $guarantor->english_name,
                'nepali_name' => $guarantor->nepali_name,
                'client_id' => $guarantor->client_id,
                'district_id' => $guarantor->district_id,
                'local_bodies_id' => $guarantor->local_bodies_id,
                'wardno' => $guarantor->wardno,
                'phone' => $guarantor->phone,
                'reg_year' => $guarantor->reg_year,
                'reg_month' => $guarantor->reg_month,
                'reg_day' => $guarantor->reg_day,
                'ministry_id' => $guarantor->ministry_id,
                'department_id' => $guarantor->department_id,
                'registration_number' => $guarantor->registration_number,
                'authorized_person_id' => $guarantor->authorized_person_id,
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
            $bid = $data->id;

            if ($data) {
            } else {
                Session::flash('danger', 'Corporate Borrower Creation Failed, Please Try Again.');
                return redirect()->route('corporate_guarantor_borrower.index');
            }
        } else {
            Session::flash('danger', 'The borrower you are trying to choose is already in borrower list please choose from borrower list.');
            return redirect()->route('corporate_guarantor_borrower.index');
        }
        return redirect()->route('corporate_guarantor.index', compact('bid'));
    }

    public function corporate_property_owner_borrower($pid)
    {

        $property_owner = CorporatePropertyOwner::find($pid);
        $check = CorporateBorrower::where([
            ['registration_number', $property_owner->registration_number],
            ['english_name', $property_owner->english_name]
        ])->first();
        if (!$check) {
            $data = CorporateBorrower::create([
                'english_name' => $property_owner->english_name,
                'nepali_name' => $property_owner->nepali_name,
                'client_id' => $property_owner->client_id,
                'district_id' => $property_owner->district_id,
                'local_bodies_id' => $property_owner->local_bodies_id,
                'wardno' => $property_owner->wardno,
                'phone' => $property_owner->phone,
                'reg_year' => $property_owner->reg_year,
                'reg_month' => $property_owner->reg_month,
                'reg_day' => $property_owner->reg_day,
                'ministry_id' => $property_owner->ministry_id,
                'department_id' => $property_owner->department_id,
                'registration_number' => $property_owner->registration_number,
                'authorized_person_id' => $property_owner->authorized_person_id,
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
            $bid = $data->id;
            if ($data) {

            } else {
                Session::flash('danger', 'Corporate Borrower Creation Failed, Please Try Again.');
                return redirect()->route('corporate_property_owner_borrower.index');
            }
        } else {
            Session::flash('danger', 'The borrower you are trying to choose is already in borrower list, Please choose from borrower list.');
            return redirect()->route('corporate_property_owner_borrower.index');

        }
        return redirect()->route('corporate_guarantor.index', compact('bid'));


    }
    public function corporate_borrower_destroy(Request $request, $id){
dd('sorry you cannot delete at this time');
        $owner = CorporateBorrower::find($id);
        $status = null;
        try {
            $status = $owner->delete();
        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Data Deleted Successfully');
        }
        return redirect()->route('corporate_borrower.index');

    }


}
