<?php

namespace App\Http\Controllers;

use App\District;
use App\Http\Requests\PersonalUpdateRequest;
use App\LocalBodies;
use App\PersonalBorrower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PersonalBorrowerShortcutController extends Controller
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
        $borrower = PersonalBorrower::all();
        return view('shortcut.personalborrower.index', compact('borrower'));

    }


    public function show($id)
    {
        $borrower = PersonalBorrower::find($id);
        return view('shortcut.personalborrower.show', compact('borrower'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $borrower = PersonalBorrower::find($id);
        $district = [];
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'जिल्ला';
            $district[$d->id] = $d->name;
        }
        $localbody = [];
        $local=LocalBodies::select('id','name','body_type')->where([['district_id',$borrower->district_id]])->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }

        return view('shortcut.personalborrower.edit', compact('borrower', 'district', 'localbody'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PersonalUpdateRequest $request, $id)
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
        $borrower->phone = $request->input('phone');
        $borrower->dob_year = $request->input('dob_year');
        $borrower->dob_month = $request->input('dob_month');
        $borrower->dob_day = $request->input('dob_day');
        $borrower->citizenship_number = $request->input('citizenship_number');
        $borrower->issued_year = $request->input('issued_year');
        $borrower->issued_month = $request->input('issued_month');
        $borrower->issued_day = $request->input('issued_day');
        $borrower->issued_district = $request->input('issued_district');
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
        return redirect()->route('PersonalBorrower.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      
        dd('sorry you cannot delete this at this moment.');
        $owner = PersonalBorrower::find($id);
        $status = null;
        try {
            $status = $owner->delete();
        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Borrower Deleted Successfully');
        }
        return redirect()->route('PersonalBorrower.index');
    }
}
