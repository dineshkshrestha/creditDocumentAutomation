<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Dispatch;
use App\Http\Requests\DispatchCreateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DispatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dispatch = Dispatch::all()->sortByDesc('id');
        $branch = [];
        $branches = Branch::select('id', 'location')->get();
        foreach ($branches as $b) {
            $branch[''] = 'शाखा';
            $branch[$b->id] = $b->location;
        }
        return view('dispatch.index', compact('dispatch', 'branch'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(DispatchCreateRequest $request)
    {

        $l=Dispatch::all()->last();
        $last=$l->id;
        if($last){

        }else{
            $last=0;
        }
        $date=date("Y");
        $ref='CiBL/CAD/'.$last++.'/'.$date;


        $dispatch = Dispatch::create([
            'date' => date('Y/m/d'),
            'reference_number' => $ref,
            'subject' => $request->input('subject'),
            'remarks' => $request->input('remarks'),
            'receiver' => $request->input('receiver'),
            'branch' => $request->input('branch'),
            'created_by' => Auth::user()->id,
        ]);
        if ($dispatch) {
            Session::flash('success', 'Dispatch Created Successfully. Reference Number::'.$dispatch->reference_number);
        } else {
            Session::flash('danger', 'Unable to create dispatch');
        }
        return redirect()->route('dispatch.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dispatch = Dispatch::find($id);
        $dispatch->date = $request->input('date');
        $dispatch->reference_number = $request->input('reference_number');
        $dispatch->subject = $request->input('subject');
        $dispatch->remarks = $request->input('remarks');
        $dispatch->receiver = $request->input('receiver');
        $dispatch->branch = $request->input('branch');
        $dispatch->updated_by = Auth::user()->id;
        try {
            $status = $dispatch->update();

        } catch (\Exception $e) {
        }
        if ($status) {
            Session::flash('success', 'Dispatch Updated Successfully');
        } else {
            Session::flash('danger', 'Dispatch Update Failed');
        }
        return redirect()->route('dispatch.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
