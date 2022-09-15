<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProvinceUpdateRequest;
use App\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProvinceController extends Controller
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


            $province = Province::all();
            return view('province.index', compact('province'));
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


            return redirect()->route('province.index');
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
    public function store(Request $request)
    {
        if (Auth::user()->user_type == 'admin') {


            return redirect()->route('province.index');
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


            $province = Province::find($id);

            return view('province.edit', compact('province'));

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
    public function update(ProvinceUpdateRequest $request, $id)
    {
        if (Auth::user()->user_type == 'admin') {


            $dep = Province::find($id);
            $dep->name = $request->input('name');
            $dep->updated_by = Auth::user()->id;
            $status = null;
            try {
                $status = $dep->update();

            } catch (\Exception $e) {

            }


            if ($status) {
                Session::flash('success', 'Province Updated Successfully');
            } else {

                Session::flash('danger', 'Province Update Failed');
            }
            return redirect()->route('province.index');
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
        dd('sorry you cannot delete this at this moment.');
        if (Auth::user()->user_type == 'admin') {
            $data=Province::find($id);
            $status = null;
            try {
                $status=$data->delete();

            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }
            if($status){

                Session::flash('success', 'Province Deleted Successfully');
            }
            return redirect()->route('province.index');

        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }



}