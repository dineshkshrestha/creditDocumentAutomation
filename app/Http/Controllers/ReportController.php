<?php

namespace App\Http\Controllers;

use App\CorporateLoan;
use App\JointLoan;
use App\PersonalLoan;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function create($type)
    {
        if ($type == 'borrower') {
        $papproved = PersonalLoan::whereNotNull('approvedby')->Where('document_status', 'Approved')->get();
        $pdownloaded = PersonalLoan::whereNotNull('approvedby')->Where('document_status', 'Downloaded')->get();
        $capproved = CorporateLoan::whereNotNull('approvedby')->Where('document_status', 'Approved')->get();
        $cdownloaded = CorporateLoan::whereNotNull('approvedby')->Where('document_status', 'Downloaded')->get();
        $japproved = JointLoan::whereNotNull('approvedby')->Where('document_status', 'Approved')->get();
        $jdownloaded = JointLoan::whereNotNull('approvedby')->Where('document_status', 'Downloaded')->get();

        return view('report.borrowerGuarantor', compact('papproved', 'pdownloaded', 'capproved', 'cdownloaded', 'japproved', 'jdownloaded'));

    }
        if ($type == 'approved') {
            $papproved = PersonalLoan::whereNotNull('approvedby')->Where('document_status', 'Approved')->get();
            $pdownloaded = PersonalLoan::whereNotNull('approvedby')->Where('document_status', 'Downloaded')->get();
            $capproved = CorporateLoan::whereNotNull('approvedby')->Where('document_status', 'Approved')->get();
            $cdownloaded = CorporateLoan::whereNotNull('approvedby')->Where('document_status', 'Downloaded')->get();
            $japproved = JointLoan::whereNotNull('approvedby')->Where('document_status', 'Approved')->get();
            $jdownloaded = JointLoan::whereNotNull('approvedby')->Where('document_status', 'Downloaded')->get();

            return view('report.approved', compact('papproved', 'pdownloaded', 'capproved', 'cdownloaded', 'japproved', 'jdownloaded'));

        }

        if ($type == 'rejected') {
            $prejected = PersonalLoan::whereNotNull('rejected_by')->Where('document_status', 'Rejected')->get();
            $crejected = CorporateLoan::whereNotNull('rejected_by')->Where('document_status', 'Rejected')->get();
            $jrejected = JointLoan::whereNotNull('rejected_by')->Where('document_status', 'Rejected')->get();

            return view('report.rejected', compact('prejected', 'crejected', 'jrejected'));
        }
        if ($type == 'submitted') {
            $psubmitted = PersonalLoan::whereNotNull('submitted_by')->Where('document_status', 'Submitted')->get();
            $csubmitted = CorporateLoan::whereNotNull('submitted_by')->Where('document_status', 'Submitted')->get();
            $jsubmitted = JointLoan::whereNotNull('submitted_by')->Where('document_status', 'Submitted')->get();
            return view('report.submitted', compact('psubmitted', 'csubmitted', 'jsubmitted'));
        }


    }
}
