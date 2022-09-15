<?php

namespace App\Http\Controllers;

use App\Department;
use App\Http\Requests\DepartmentCreateRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class DepartmentController extends Controller
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
        if (Auth::user()->user_type == 'admin') {
            $department = Department::all();
            return view('department.index', compact('department'));
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


            return view('department.create');
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
    public function store(DepartmentCreateRequest $request)
    {
        if (Auth::user()->user_type == 'admin') {


            $data = Department::create([
                'name' => $request->input('name'),
                'status' => '1',
//                $request->input('status'),
                'created_by' => Auth::user()->id,
            ]);

            if ($data) {

                Session::flash('success', 'Department Inserted Successfully');
            } else {

                Session::flash('danger', 'Department Insert Failed');
            }

            return redirect()->route('department.create');
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
        if (Auth::user()->user_type == 'admin') {


        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
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


            $department = Department::find($id);

            return view('department.edit', compact('department'));
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
    public function update(DepartmentUpdateRequest $request, $id)
    {
        if (Auth::user()->user_type == 'admin') {


            $dep = Department::find($id);
            $dep->name = $request->input('name');
            $dep->status = $request->input('status');
            $dep->updated_by = Auth::user()->id;
            $status = null;
            try {
                $status = $dep->update();

            } catch (\Exception $e) {

            }


            if ($status) {
                Session::flash('success', 'Department Updated Successfully');
            } else {

                Session::flash('danger', 'Department Update Failed');
            }
            return redirect()->route('department.index');
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
            dd('sorry you cannot delete at this time');

            $dep = Department::find($id);
            $status=null;
            try {
                $status = $dep->delete();

            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }

            if ($status) {
                Session::flash('success', 'Department Deleted Successfully');
            }
            return redirect()->route('department.index');
        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }
}