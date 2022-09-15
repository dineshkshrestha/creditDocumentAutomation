<?php

namespace App\Http\Controllers;

use App\AuthorizedPerson;
use App\CorporatePropertyOwner;
use App\Department;
use App\District;
use App\Http\Requests\CorporatePropertyOwnerUpdateRequest;
use App\LocalBodies;
use App\Ministry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CorporatePropertyOwnerShortcutController extends Controller
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
        $propertyowner = CorporatePropertyOwner::all();
        return view('shortcut.corporatepropertyowner.index', compact('propertyowner'));
    }

    public function show($id)
    {
        $propertyowner = CorporatePropertyOwner::find($id);
        return view('shortcut.corporatepropertyowner.show', compact('propertyowner'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $propertyowner = CorporatePropertyOwner::find($id);
        $authorized_person = AuthorizedPerson::find($propertyowner->authorized_person_id);

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
            $district[''] = 'जिल्ला';
            $district[$d->id] = $d->name;
        }

        $localbody = [];
        $local = LocalBodies::select('id', 'name')->get()->sortBy('name');
        foreach ($local as $d) {
            $localbody[''] = 'स्थानिय निकाय';
            $localbody[$d->id] = $d->name;
        }

        return view('shortcut.corporatepropertyowner.edit', compact('propertyowner', 'authorized_person', 'department', 'ministry', 'localbody', 'district'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(CorporatePropertyOwnerUpdateRequest $request, $id)
    {
        $propertyowner = CorporatePropertyOwner::find($id);
        $propertyowner->english_name = $request->input('english_name');
        $propertyowner->nepali_name = $request->input('nepali_name');
        $propertyowner->client_id = $request->input('client_id');
        $propertyowner->reg_year = $request->input('reg_year');
        $propertyowner->reg_month = $request->input('reg_month');
        $propertyowner->reg_day = $request->input('reg_day');
        $propertyowner->ministry_id = $request->input('ministry_id');
        $propertyowner->department_id = $request->input('department_id');
        $propertyowner->registration_number = $request->input('registration_number');
        $propertyowner->district_id = $request->input('district_id');
        $propertyowner->local_bodies_id = $request->input('local_bodies_id');
        $propertyowner->wardno = $request->input('wardno');
        $propertyowner->phone = $request->input('phone');
        $propertyowner->status = $request->input('status');
        $propertyowner->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $propertyowner->update();

        } catch (\Exception $e) {

        }

        $authorized_person = AuthorizedPerson::find($propertyowner->authorized_person_id);
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
            Session::flash('success', 'Corporate Property Owner Updated Successfully');
        } else {

            Session::flash('danger', 'Corporate Property Owner Update Failed');
        }
        return redirect()->route('CorporatePropertyOwner.index');
    }

    public function destroy($id)
    {
        dd('sorry you cannot delete at this time');
        $owner = CorporatePropertyOwner::find($id);
        $status = null;
        try {
            $status = $owner->delete();
        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Property Owner Deleted Successfully');
        }
        return redirect()->route('CorporatePropertyOwner.index');
    }
}
