<?php

namespace App\Http\Controllers;

use App\CorporateBorrower;
use App\CorporateLoan;
use App\JointBorrower;
use App\JointLoan;
use App\PersonalBorrower;
use App\PersonalLoan;
use Illuminate\Http\Request;

class ReviewController extends Controller
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
    public function personal_index()
    {
        $p = PersonalLoan::all();
        $borrower = [];
        foreach ($p as $pl) {
            $borrower[] = PersonalBorrower::find($pl->borrower_id);
        }
        return view('shortcut.personal_review_index', compact('borrower'));
    }


    public function corporate_index()
    {
        $c = CorporateLoan::all();
        $borrower = [];
        foreach ($c as $cl) {
            $borrower[] = CorporateBorrower::find($cl->borrower_id);
        }
        return view('shortcut.corporate_review_index', compact('borrower'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function joint_index()
    {
        $j = JointLoan::where([
            ['document_status', 'downloaded']
        ])->get();
        $borrower = [];
        foreach ($j as $jl) {
            $borrower[] = JointBorrower::find($jl->borrower_id);
        }
        return view('shortcut.joint_review_index', compact('borrower'));
    }


    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
