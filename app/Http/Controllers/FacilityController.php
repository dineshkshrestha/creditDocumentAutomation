<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacilityCreateRequest;
use App\Http\Requests\FacilityUpdateRequest;
use App\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Exception;

class FacilityController extends Controller
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
        if (Auth::user()->user_type == 'admin') {

            $facility = Facility::all();
            return view('facility.index', compact('facility'));
        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->user_type == 'admin') {

            return view('facility.create');
        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(FacilityCreateRequest $request)
    {
        if (Auth::user()->user_type == 'admin') {

            $data = Facility::create([
                'name' => $request->input('name'),
                'status' => '1',
//                $request->input('status'),
                'created_by' => Auth::user()->id,
            ]);

            if ($data) {
//            Alert::success('Successful', 'Facility Inserted Successfully');
                Session::flash('success', 'Facility Inserted Successfully');
            } else {
//            Alert::danger('Error', 'Facility Insert Failed');
                Session::flash('danger', 'Facility Insert Failed');
            }

            return redirect()->route('facility.create');
        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->user_type == 'admin') {

            $facility = Facility::find($id);

            return view('facility.edit', compact('facility'));
        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(FacilityUpdateRequest $request, $id)
    {
        if (Auth::user()->user_type == 'admin') {

            $dep = Facility::find($id);
            $dep->name = $request->input('name');
            $dep->status = $request->input('status');
            $dep->updated_by = Auth::user()->id;
            $status = null;
            try {
                $status = $dep->update();

            } catch (\Exception $e) {

            }


            if ($status) {
                Session::flash('success', 'Facility Updated Successfully');
            } else {

                Session::flash('danger', 'Facility Update Failed');
            }
            return redirect()->route('facility.index');
        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->user_type == 'admin') {
dd('sorry you cannot delete this at this moment.');
            $dep = Facility::find($id);
            $status = null;
            try {
                $status = $dep->delete();
            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }
            if ($status) {
                Session::flash('success', 'Facility Deleted Successfully');
            }
            return redirect()->route('facility.index');
        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }
}