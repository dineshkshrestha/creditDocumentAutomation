<?php

namespace App\Http\Controllers;

use App\AuthorizedPerson;
use App\CorporateGuarantor;
use App\Department;
use App\District;
use App\Http\Requests\CorporateGuarantorUpdateRequest;
use App\Http\Requests\CorporateUpdateRequest;
use App\LocalBodies;
use App\Ministry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CorporateGuarantorShortcutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $guarantor = CorporateGuarantor::all();
        return view('shortcut.corporateguarantor.index', compact('guarantor'));
    }

    public function show($id)
    {
        $guarantor = CorporateGuarantor::find($id);


        return view('shortcut.corporateguarantor.show', compact('guarantor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $guarantor = CorporateGuarantor::find($id);
        $authorized_person = AuthorizedPerson::find($guarantor->authorized_person_id);

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


        return view('shortcut.corporateguarantor.edit', compact('guarantor', 'authorized_person', 'department', 'ministry', 'localbody', 'district'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(CorporateGuarantorUpdateRequest $request, $id)
    {
        $guarantor = CorporateGuarantor::find($id);
        $guarantor->english_name = $request->input('english_name');
        $guarantor->nepali_name = $request->input('nepali_name');
        $guarantor->client_id = $request->input('client_id');
        $guarantor->reg_year = $request->input('reg_year');
        $guarantor->reg_month = $request->input('reg_month');
        $guarantor->reg_day = $request->input('reg_day');
        $guarantor->ministry_id = $request->input('ministry_id');
        $guarantor->department_id = $request->input('department_id');
        $guarantor->registration_number = $request->input('registration_number');
        $guarantor->district_id = $request->input('district_id');
        $guarantor->local_bodies_id = $request->input('local_bodies_id');
        $guarantor->wardno = $request->input('wardno');
        $guarantor->phone = $request->input('phone');
        $guarantor->status = $request->input('status');
        $guarantor->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $guarantor->update();

        } catch (\Exception $e) {

        }

        $authorized_person = AuthorizedPerson::find($guarantor->authorized_person_id);
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
            Session::flash('success', 'Corporate Guarantor Updated Successfully');
        } else {

            Session::flash('danger', 'Corporate Guarantor Update Failed');
        }
        return redirect()->route('CorporateGuarantor.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        dd('sorry you cannot delete at this time');
        $owner = CorporateGuarantor::find($id);
        $status = null;
        try {
            $status = $owner->delete();
        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Guarantor Deleted Successfully');
        }
        return redirect()->route('CorporateGuarantor.index');
    }
}
