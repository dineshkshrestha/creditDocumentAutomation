<?php

namespace App\Http\Controllers;

use App\District;
use App\Http\Requests\PersonalGuarantorUpdateRequest;
use App\LocalBodies;
use App\PersonalGuarantor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PersonalGuarantorShortcutController extends Controller
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
        $guarantor = PersonalGuarantor::all();
        return view('shortcut.personalguarantor.index', compact('guarantor'));

    }


    public function show($id)
    {
        $guarantor = PersonalGuarantor::find($id);
        return view('shortcut.personalguarantor.show', compact('guarantor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $guarantor = PersonalGuarantor::find($id);
        $district = [];
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }
        $localbody = [];
        $local=LocalBodies::select('id','name','body_type')->where([['district_id',$guarantor->district_id]])->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }

        return view('shortcut.personalguarantor.edit', compact('guarantor', 'district', 'localbody'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PersonalGuarantorUpdateRequest $request, $id)
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
        $guarantor->phone = $request->input('phone');
        $guarantor->dob_year = $request->input('dob_year');
        $guarantor->dob_month = $request->input('dob_month');
        $guarantor->dob_day = $request->input('dob_day');
        $guarantor->citizenship_number = $request->input('citizenship_number');
        $guarantor->issued_year = $request->input('issued_year');
        $guarantor->issued_month = $request->input('issued_month');
        $guarantor->issued_day = $request->input('issued_day');
        $guarantor->issued_district = $request->input('issued_district');
        $guarantor->status = $request->input('status');
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
        return redirect()->route('PersonalGuarantor.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        dd('sorry you cannot delete this at this moment.');
        $owner = PersonalGuarantor::find($id);
        $status = null;
        try {
            $status = $owner->delete();
        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Guarantor Deleted Successfully');
        }
        return redirect()->route('PersonalGuarantor.index');
    }
}
