<?php

namespace App\Http\Controllers;

use App\District;
use App\Http\Requests\PersonalPropertyOwnerUpdateRequest;
use App\LocalBodies;
use App\PersonalPropertyOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PersonalPropertyOwnerShortcutController extends Controller
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
        $propertyowner = PersonalPropertyOwner::all();
        return view('shortcut.personalpropertyowner.index', compact('propertyowner'));

    }


    public function show($id)
    {
        $propertyowner = PersonalPropertyOwner::find($id);
        return view('shortcut.personalpropertyowner.show', compact('propertyowner'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $propertyowner = PersonalPropertyOwner::find($id);
        $district = [];
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }
        $localbody = [];
        $local=LocalBodies::select('id','name','body_type')->where([['district_id',$propertyowner->district_id]])->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }

        return view('shortcut.personalpropertyowner.edit', compact('propertyowner', 'district', 'localbody'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PersonalPropertyOwnerUpdateRequest $request, $id)
    {
        $propertyowner = PersonalPropertyOwner::find($id);
        $propertyowner->english_name = $request->input('english_name');
        $propertyowner->nepali_name = $request->input('nepali_name');
        $propertyowner->client_id = $request->input('client_id');
        $propertyowner->gender = $request->input('gender');
        $propertyowner->grandfather_name = $request->input('grandfather_name');
        $propertyowner->grandfather_relation = $request->input('grandfather_relation');
        $propertyowner->father_name = $request->input('father_name');
        $propertyowner->father_relation = $request->input('father_relation');
        $propertyowner->spouse_name = $request->input('spouse_name');
        $propertyowner->spouse_relation = $request->input('spouse_relation');
        $propertyowner->district_id = $request->input('district_id');
        $propertyowner->local_bodies_id = $request->input('local_bodies_id');
        $propertyowner->wardno = $request->input('wardno');
        $propertyowner->phone = $request->input('phone');
        $propertyowner->dob_year = $request->input('dob_year');
        $propertyowner->dob_month = $request->input('dob_month');
        $propertyowner->dob_day = $request->input('dob_day');
        $propertyowner->citizenship_number = $request->input('citizenship_number');
        $propertyowner->issued_year = $request->input('issued_year');
        $propertyowner->issued_month = $request->input('issued_month');
        $propertyowner->issued_day = $request->input('issued_day');
        $propertyowner->issued_district = $request->input('issued_district');
        $propertyowner->status = $request->input('status');
        $propertyowner->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $propertyowner->update();

        } catch (\Exception $e) {

        }
        if ($status) {
            Session::flash('success', 'Personal Property Owner Updated Successfully');
        } else {
            Session::flash('danger', 'Personal Property Owner Update Failed');
        }
        return redirect()->route('PersonalPropertyOwner.index');
    }

    
    public function destroy($id)
    {
        dd('sorry you cannot delete this at this moment.');
        $owner = PersonalPropertyOwner::find($id);
        $status = null;
        try {
            $status = $owner->delete();
        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Owner Deleted Successfully');
        }
        return redirect()->route('PersonalPropertyOwner.index');
    }
}
