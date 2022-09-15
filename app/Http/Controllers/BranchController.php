<?php

namespace App\Http\Controllers;

use App\Branch;
use App\District;
use App\Http\Requests\BranchCreateRequest;
use App\Http\Requests\BranchUpdateRequest;
use App\LocalBodies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $branch = Branch::all();
//foreach ($branch as $b){
//    try {
//        LocalBodies::find($b->local_body)->name;
//
//    } catch (\Exception $e) {
//
//        dd($b->);
//    }
//    }

return view('branch.index', compact('branch'));
}

/**
 * Show the form for creating a new resource.
 *
 * @return \Illuminate\Http\Response
 */
public
function create()
{

    $district = [];
    $localbody = [];
    $dis = District::select('id', 'name')->get();
    foreach ($dis as $d) {
        $district[''] = 'जिल्ला';
        $district[$d->id] = $d->name;
    }
//        local body
    $localbody[''] = 'स्थानिय निकाय';


    return view('branch.create', compact('localbody', 'district'));
}

/**
 * Store a newly created resource in storage.
 *
 * @param  \Illuminate\Http\Request $request
 * @return \Illuminate\Http\Response
 */
public
function store(BranchCreateRequest $request)
{

    $data = Branch::create([
        'location' => $request->input('location'),
        'district' => $request->input('district'),
        'local_body' => $request->input('local_body'),
        'status' => '1',
        'created_by' => Auth::user()->id,
    ]);

    if ($data) {

        Session::flash('success', 'Branch Created Successfully');
    } else {

        Session::flash('danger', 'Branch Creating Failed');
    }

    return redirect()->route('branch.create');

}

/**
 * Display the specified resource.
 *
 * @param  int $id
 * @return \Illuminate\Http\Response
 */
public
function show($id)
{

}

/**
 * Show the form for editing the specified resource.
 *
 * @param  int $id
 * @return \Illuminate\Http\Response
 */
public
function edit($id)
{
    $branch = Branch::find($id);
    $district = [];
    $localbody = [];
    $dis = District::select('id', 'name')->get();
    foreach ($dis as $d) {
        $district[''] = 'जिल्ला';
        $district[$d->id] = $d->name;
    }
    $localbody[''] = 'स्थानिय निकाय';
    $local = Branch::select('local_body')->find($id);
    $gov = LocalBodies::select('id', 'name')->find($local);
    foreach ($gov as $g) {
        $localbody[$g->id] = $g->name;
    }

    return view('branch.edit', compact('branch', 'district', 'localbody'));
    //
}

/**
 * Update the specified resource in storage.
 *
 * @param  \Illuminate\Http\Request $request
 * @param  int $id
 * @return \Illuminate\Http\Response
 */
public
function update(BranchUpdateRequest $request, $id)
{
    $branch = Branch::find($id);
    $branch->location = $request->input('location');
    $branch->district = $request->input('district');
    $branch->local_body = $request->input('local_body');
    $branch->status = $request->input('status');
    $branch->updated_by = Auth::user()->id;
    $status = null;
    try {
        $status = $branch->update();

    } catch (\Exception $e) {

    }

    if ($status) {
        Session::flash('success', 'Branch Updated Successfully');
    } else {

        Session::flash('danger', 'Branch Update Failed');
    }
    return redirect()->route('branch.index');

    //
}

/**
 * Remove the specified resource from storage.
 *
 * @param  int $id
 * @return \Illuminate\Http\Response
 */
public
function destroy($id)
{
    dd('sorry you cannot delete at this time');
    $branch = Branch::find($id);

    $status = null;
    try {
        $status = $branch->delete();

    } catch (\Exception $e) {
        Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
    }


    if ($status) {
        Session::flash('success', 'Branch Deleted Successfully');
    }

    return redirect()->route('branch.index');

}
}
