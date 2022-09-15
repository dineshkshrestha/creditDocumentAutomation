<?php

namespace App\Http\Controllers;

use App\District;
use App\Http\Requests\DistrictCreateRequest;
use App\Http\Requests\DistrictUpdateRequest;
use App\Http\Requests\localBodiesCreateRequest;
use App\Http\Requests\LocalBodyUpdateRequest;
use App\LocalBodies;
use App\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use League\Flysystem\Adapter\Local;

class DistrictController extends Controller
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

            $district = District::all();
            return view('district.index', compact('district'));
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
    public
    function create()
    {
        if (Auth::user()->user_type == 'admin') {


            $pro = Province::select('id', 'name')->get();

            $province = [];
            foreach ($pro as $p) {
                $province[$p->id] = $p->name;

            }
            return view('district.create', compact('province'));
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
    public
    function store(DistrictCreateRequest $request)
    {
        if (Auth::user()->user_type == 'admin') {


            $district = District::create([
                'name' => $request->input('name'),
                'province_id' => $request->input('province_id'),
                'created_by' => Auth::user()->id,
            ]);
            $count1 = count($request->input('local_type'));
            $count = $count1 - 1;
            for ($i = 0; $i < $count; $i++) {
                $local_name = $request->get('local_name');
                $local_type = $request->get('local_type');

                LocalBodies::create([
                    'name' => $local_name[$i],
                    'district_id' => $district->id,
                    'body_type' => $local_type[$i],
                    'created_by' => Auth::user()->id,
                ]);
            }

            Session::flash('success', 'Districts and local government Inserted Successfully');

            return redirect()->route('district.create');
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


            $local_body = LocalBodies::where('district_id', $id)->get();
//        dd('kh');
            return view('district.show', compact('local_body', 'id'));
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


            $district = District::find($id);
            $pro = Province::select('id', 'name')->get();
            $province[] = '';
            foreach ($pro as $p) {
                $province[$p->id] = $p->name;
            }


            return view('district.edit', compact('district', 'province'));
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
    function update(DistrictUpdateRequest $request, $id)
    {
        if (Auth::user()->user_type == 'admin') {
            $min = District::find($id);
            $min->name = $request->input('name');
            $min->province_id = $request->input('province_id');
            $min->updated_by = Auth::user()->id;
            $status = null;
            try {
                $status = $min->update();

            } catch (\Exception $e) {

            }
            if ($status) {
                Session::flash('success', 'District Updated Successfully');
            } else {
                Session::flash('danger', 'District Update Failed');
            }
            return redirect()->route('district.index');
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
            $dis = District::find($id);
            $status = null;
            try {
                $status = $dis->delete();

            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }

            if ($status) {
                Session::flash('success', 'District and Local bodies Deleted Successfully');
            }
            return redirect()->route('district.index');

        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }


    public
    function local_body_store(localBodiesCreateRequest $request, $did)
    {
        if (Auth::user()->user_type == 'admin') {


            $status = LocalBodies::create([
                'name' => $request->input('name'),
                'district_id' => $did,
                'body_type' => $request->input('body_type'),
                'created_by' => Auth::user()->id,
            ]);
            if ($status) {
                Session::flash('success', 'LocalBody Created Successfully');
            } else {
                Session::flash('danger', 'LocalBody Creation Failed');
            }
            return redirect()->route('district.show', compact('did'));
        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }


    public
    function local_edit($id, $did)
    {
        if (Auth::user()->user_type == 'admin') {


            $local = LocalBodies::find($id);
            return view('district.local_edit', compact('local', 'did'));
        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }

    public
    function local_update(LocalBodyUpdateRequest $request, $id)
    {
        if (Auth::user()->user_type == 'admin') {


            $local = LocalBodies::find($id);
            $local->name = $request->input('name');
            $did = $request->input('did');
            $local->body_type = $request->input('local_body');
            $local->updated_by = Auth::user()->id;
            $status = null;
            try {
                $status = $local->update();

            } catch (\Exception $e) {

            }
            if ($status) {
                Session::flash('success', 'Local Body Updated Successfully');
            } else {
                Session::flash('danger', 'Local Body Update Failed..Please Try again.');
            }
            return redirect()->route('district.show', compact('did'));
        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }

    public
    function local_delete($id, $did)
    {
        if (Auth::user()->user_type == 'admin') {
            $status = null;
            $local = Localgov::find($id);
            try {
                $status = $local->delete();
            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }

            if ($status) {
                Session::flash('success', 'Local body Deleted Successfully');
            }

            return redirect()->route('district.show', compact('did'));
        } else {
            Session::flash('danger', 'Sorry, You donot have permission to access this.');
            return view('home');
        }
    }

    function select_local_body(Request $request)
    {
        $district_id = $request->input('district_id');
        $localbodies = LocalBodies::where('district_id', '=', $district_id)->get();
        $ht = "<option value=''>:yflgo lgsfo</option>";
        foreach ($localbodies as $localbody) {
            $ht .= "<option value=$localbody->id>$localbody->name $localbody->body_type</option>";
        }
        return $ht;
    }

}