<?php

namespace App\Http\Controllers;

use App\CorporateBorrower;
use App\CorporateLoan;
use App\JointBorrower;
use App\JointLoan;
use App\PersonalBorrower;
use App\PersonalLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {

        $path = storage_path('results/');
        $files = File::allFiles($path);


        return view('document.index', compact('files'));
    }

    public function download($filename)
    {
        return response()->download(storage_path('results/' . $filename));
    }

    public function delete($filename)
    {
        if (File::exists(storage_path('results/' . $filename))) {

            $result = unlink(storage_path('results/' . $filename));
            if ($result) {
                Session::flash('success', 'Document Deleted Successfully.');

            } else {
                Session::flash('warning', 'Unable to delete Document. Please Try again.');

            }
            return redirect()->route('document.index');
        }
        Session::flash('warning', 'Unable to find Document. Please Try again.');

        return redirect()->route('document.index');


    }


    public function make_document()
    {
        if(Auth::user()->user_type=='user') {
            $p = PersonalLoan::where([
                ['document_status', 'downloaded']
            ])->get();
            $ploan = [];
            foreach ($p as $pl) {
                $ploan[] = PersonalBorrower::find($pl->borrower_id);
            }
            $c = CorporateLoan::where([
                ['document_status', 'downloaded']
            ])->get();

            $cloan = [];
            foreach ($c as $cl) {
                $cloan[] = CorporateBorrower::find($cl->borrower_id);
            }
            $j = JointLoan::where([
                ['document_status', 'downloaded']
            ])->get();
            $jloan = [];
            foreach ($j as $jl) {
                $jloan[] = JointBorrower::find($jl->borrower_id);
            }
        }else{
            $p = PersonalLoan::all();
            $ploan = [];
            foreach ($p as $pl) {
                $ploan[] = PersonalBorrower::find($pl->borrower_id);
            }
            $c = CorporateLoan::all();

            $cloan = [];
            foreach ($c as $cl) {
                $cloan[] = CorporateBorrower::find($cl->borrower_id);
            }
            $j = JointLoan::all();
            $jloan = [];
            foreach ($j as $jl) {
                $jloan[] = JointBorrower::find($jl->borrower_id);
            }

        }

        return view('document.make_new_document', compact('ploan', 'cloan', 'jloan'));
    }

    public function personal_delete($bid)
    {
        $loan = PersonalLoan::where([['borrower_id', $bid]])->first();
$status=null;
        try {
            $status = $loan->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Deleted Successfully.');
        }


        return redirect()->route('document.make_document');
    }

    public function joint_delete($bid)
    {
        $loan = JointLoan::where([['borrower_id', $bid]])->first();
$status=null;
        try {
            $status = $loan->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Deleted Successfully.');
        }

        return redirect()->route('document.make_document');
    }

    public function corporate_delete($bid)
    {
        $loan = CorporateLoan::where([['borrower_id', $bid]])->first();
$status=null;
        try {
            $status = $loan->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Deleted Successfully.');
        }

        return redirect()->route('document.make_document');
    }


}