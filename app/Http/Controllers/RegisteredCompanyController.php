<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisteredCompanyCreateRequest;
use App\Http\Requests\RegisteredCompanyUpdateRequest;
use App\RegisteredCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RegisteredCompanyController extends Controller
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
            $registered_company = RegisteredCompany::all();
            return view('registered_company.index', compact('registered_company'));
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
            return view('registered_company.create');

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
    public function store(RegisteredCompanyCreateRequest $request)
    {

        if (Auth::user()->user_type == 'admin') {

            $data = RegisteredCompany::create([
                'name' => $request->input('name'),
                'isin' => $request->input('isin'),
                'created_by' => Auth::user()->id,
            ]);

            if ($data) {
//            Alert::success('Successful', 'RegisteredCompany Inserted Successfully');
                Session::flash('success', 'Registered Company Inserted Successfully');
            } else {
//            Alert::danger('Error', 'Registered Company Insert Failed');
                Session::flash('danger', 'Registered Company Insert Failed');
            }

            return redirect()->route('registered_company.create');
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
    public
    function show($id)
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
    public
    function edit($id)
    {
        if (Auth::user()->user_type == 'admin') {

            $registered_company = RegisteredCompany::find($id);

            return view('registered_company.edit', compact('registered_company'));

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
    public
    function update(RegisteredCompanyUpdateRequest $request, $id)
    {
        if (Auth::user()->user_type == 'admin') {
            $dep = RegisteredCompany::find($id);
            $dep->name = $request->input('name');
            $dep->isin = $request->input('isin');
            $dep->updated_by = Auth::user()->id;
            $status = null;
            try {
                $status = $dep->update();

            } catch (\Exception $e) {

            }


            if ($status) {
                Session::flash('success', 'Registered Company Updated Successfully');
            } else {

                Session::flash('danger', 'Registered Company Update Failed');
            }
            return redirect()->route('registered_company.index');
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
    public
    function destroy($id)
    {
        if (Auth::user()->user_type == 'admin') {
            dd('sorry you cannot delete this at this moment.');

            $dep = RegisteredCompany::find($id);
            $status = null;
            try {
                $status = $dep->delete();

            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }
            if ($status) {
                Session::flash('success', 'Registered Company Deleted Successfully');
            }
            return redirect()->route('registered_company.index');
        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }
}