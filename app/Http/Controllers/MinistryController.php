<?php

namespace App\Http\Controllers;

use App\Http\Requests\MinistryCreateRequest;
use App\Http\Requests\MinistryUpdateRequest;
use App\Ministry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MinistryController extends Controller
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


            $ministry = Ministry::all();
            return view('ministry.index', compact('ministry'));  }else{
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public
        function create()
        {
            if (Auth::user()->user_type == 'admin') {


                return view('ministry.create');  }else{
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
            public
            function store(MinistryCreateRequest $request)
            {
                if (Auth::user()->user_type == 'admin') {


                    $data = Ministry::create([
                        'name' => $request->input('name'),
                        'status' => '1',
//                $request->input('status'),
                        'created_by' => Auth::user()->id,
                    ]);

                    if ($data) {
//            Alert::success('Successful', 'Ministry Inserted Successfully');
                        Session::flash('success', 'Ministry Inserted Successfully');
                    } else {
//            Alert::danger('Error', 'Ministry Insert Failed');
                        Session::flash('danger', 'Ministry Insert Failed');
                    }

                    return redirect()->route('ministry.create');  }else{
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
                    }else{
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


                            $ministry = Ministry::find($id);

                            return view('ministry.edit', compact('ministry'));
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
                    function update(MinistryUpdateRequest $request, $id)
                    {
                        if (Auth::user()->user_type == 'admin') {


                            $dep = Ministry::find($id);
                            $dep->name = $request->input('name');
                            $dep->status = $request->input('status');
                            $dep->updated_by = Auth::user()->id;
                            $status = null;
                            try {
                                $status = $dep->update();

                            } catch (\Exception $e) {

                            }

                            if ($status) {
                                Session::flash('success', 'Ministry Updated Successfully');
                            } else {

                                Session::flash('danger', 'Ministry Update Failed');
                            }
                            return redirect()->route('ministry.index');
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
                        dd('sorry you cannot delete this at this moment.');
                        if (Auth::user()->user_type == 'admin') {

$status=null;
                            $dep = Ministry::find($id);
                            try {
                                $status = $dep->delete();

                            } catch (\Exception $e) {
                                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                            }


                            if ($status) {
                                Session::flash('success', 'Ministry Deleted Successfully');
                            } else {

                                Session::flash('danger', 'Failed to Delete Ministry.');
                            }
                            return redirect()->route('ministry.index');
                        } else {
                            Session::flash('danger', 'Sorry, You donot have permission to access this.');
                            return view('home');
                        }
                    }
                }