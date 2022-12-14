<?php

namespace App\Http\Controllers;

use App\AuthorizedPerson;
use App\Branch;
use App\CorporateBorrower;
use App\CorporateGuarantor;
use App\CorporateLand;
use App\CorporatePropertyOwner;
use App\CorporateShare;
use App\Department;
use App\District;
use App\Facility;
use App\Http\Requests\CorporateGuarantorUpdateRequest;
use App\Http\Requests\CorporatePropertyOwnerUpdateRequest;
use App\Http\Requests\CorporateUpdateRequest;
use App\Http\Requests\LandUpdateRequest;
use App\Http\Requests\PersonalFacilityUpdateRequest;
use App\Http\Requests\PersonalGuarantorUpdateRequest;
use App\Http\Requests\PersonalLoanUpdateRequest;
use App\Http\Requests\PersonalPropertyOwnerUpdateRequest;
use App\Http\Requests\PersonalUpdateRequest;
use App\Http\Requests\ShareUpdateRequest;
use App\Http\Requests\VehicleUpdateRequest;
use App\JointLand;
use App\JointPropertyOwner;
use App\LocalBodies;
use App\Ministry;
use App\PersonalBorrower;
use App\PersonalFacilities;
use App\PersonalGuarantor;
use App\PersonalGuarantorBorrower;
use App\PersonalHirePurchase;
use App\PersonalLand;
use App\PersonalLandBorrower;
use App\PersonalLoan;
use App\PersonalPropertyOwner;
use App\PersonalShare;
use App\PersonalShareBorrower;
use App\RegisteredCompany;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PersonalReviewController extends Controller
{
    public function __construct()

    {
        $this->middleware('auth');
    }

    public function index($bid)
    {
        $borrower = PersonalBorrower::find($bid);

        //year for current age
        $year = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($year) {
            if ($year->offerletter_year < '2076') {
                $cyear = 2076;
            } else {
                $cyear = $year->offerletter_year;
            }
        } else {
            $cyear = 2076;
        }
//personal guarantor and corporate guarantor
        $personal_guarantor = [];
        $corporate_guarantor = [];
        $corporatelandowner = [];
        $corporateshareowner = [];

        $p_guarantor = PersonalGuarantorBorrower::where([
            ['borrower_id', $bid],
            ['guarantor_type', 'personal'],
            ['status', '1']
        ])->get();
        foreach ($p_guarantor as $p) {
            $personal_guarantor[] = PersonalGuarantor::find($p->personal_guarantor_id);
        }
        $c_guarantor = PersonalGuarantorBorrower::where([
            ['borrower_id', $bid],
            ['guarantor_type', 'corporate'],
            ['status', '1']
        ])->get();
        foreach ($c_guarantor as $c) {
            $corporate_guarantor[] = CorporateGuarantor::find($c->corporate_guarantor_id);
        }


//        facilities
        $facilities = PersonalFacilities::where([
            ['borrower_id', $bid]
        ])->get();
//loan
        $shareowner = [];
        $landowner = [];

        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();

        if ($loan) {
            $loan->document_status = 'Pending';
            $loan->document_remarks='waiting for approval';
            $status = null;
            try {
                $status = $loan->update();

            } catch (\Exception $e) {

            }
        }

        $hirepurchase = PersonalHirePurchase::where([['borrower_id', $bid]])->get();

        foreach ($borrower->personalborrowerpersonalland as $land) {
            $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
        }
        $personal_land_owner = array_unique($landowner);

        foreach ($borrower->personalborrowerpersonalshare as $share) {
            $shareowner[] = PersonalPropertyOwner::find($share->property_owner_id);
        }
        $personal_share_owner = array_unique($shareowner);

        foreach ($borrower->personalborrowercorporateland as $land) {
            $corporatelandowner[] = CorporatePropertyOwner::find($land->property_owner_id);
        }
        $corporate_land_owner = array_unique($corporatelandowner);

        foreach ($borrower->personalborrowercorporateshare as $share) {
            $corporateshareowner[] = CorporatePropertyOwner::find($share->property_owner_id);
        }
        $corporate_share_owner = array_unique($corporateshareowner);
        $jointp = [];
        $joint = $borrower->personal_joint_land()->first();
        if ($joint) {
            $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
        }
        $joint_property_owner = [];
        if($jointp) {
            $joint_property_owner[] = PersonalPropertyOwner::find($joint->joint1);
            $joint_property_owner[] = PersonalPropertyOwner::find($joint->joint2);
            $joint_property_owner [] = PersonalPropertyOwner::find($joint->joint3);

        }
        return view('personal_review.index', compact('jointp', 'joint_property_owner', 'corporate_share_owner', 'corporate_land_owner', 'cyear', 'personal_share_owner', 'bid', 'borrower', 'corporate_guarantor', 'personal_guarantor', 'facilities', 'loan', 'personal_land_owner', 'hirepurchase'));
    }

    public function borrower_edit($id)
    {
        $borrower = PersonalBorrower::find($id);
        $district = [];
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }

        $localbody = [];
        $local=LocalBodies::select('id','name','body_type')->where([['district_id',$borrower->district_id]])->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }

        $bid = $id;

        return view('personal_review.edit', compact('borrower', 'district', 'localbody', 'id', 'bid'));

    }


    public function borrower_update(PersonalUpdateRequest $request, $id)
    {
        $borrower = PersonalBorrower::find($id);
        $borrower->english_name = $request->input('english_name');
        $borrower->nepali_name = $request->input('nepali_name');
        $borrower->client_id = $request->input('client_id');
        $borrower->gender = $request->input('gender');
        $borrower->grandfather_name = $request->input('grandfather_name');
        $borrower->grandfather_relation = $request->input('grandfather_relation');
        $borrower->father_name = $request->input('father_name');
        $borrower->father_relation = $request->input('father_relation');
        $borrower->spouse_name = $request->input('spouse_name');
        $borrower->spouse_relation = $request->input('spouse_relation');
        $borrower->district_id = $request->input('district_id');
        $borrower->local_bodies_id = $request->input('local_bodies_id');
        $borrower->wardno = $request->input('wardno');
        $borrower->phone = $request->input('phone');
        $borrower->dob_year = $request->input('dob_year');
        $borrower->dob_month = $request->input('dob_month');
        $borrower->dob_day = $request->input('dob_day');
        $borrower->citizenship_number = $request->input('citizenship_number');
        $borrower->issued_year = $request->input('issued_year');
        $borrower->issued_month = $request->input('issued_month');
        $borrower->issued_day = $request->input('issued_day');
        $borrower->issued_district = $request->input('issued_district');

        $borrower->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $borrower->update();

        } catch (\Exception $e) {

        }
        if ($status) {
            Session::flash('success', 'Personal Borrower Updated Successfully');
        } else {
            Session::flash('danger', 'Personal Borrower Update Failed');
        }
        return redirect()->route('personal_review.index', compact('id'));
    }


    public function personal_guarantor_edit($bid, $id)
    {
        $guarantor = PersonalGuarantor::find($id);
        $district = [];
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }

        $localbody = [];
        $local=LocalBodies::select('id','name','body_type')->where([['district_id',$guarantor->district_id]])->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }



        return view('personal_review.personal_guarantor_edit', compact('guarantor', 'district', 'localbody', 'bid'));

    }


    public function personal_guarantor_update(PersonalGuarantorUpdateRequest $request, $id)
    {

        $bid = $request->input('bid');
        $guarantor = PersonalGuarantor::find($id);
        $guarantor->english_name = $request->input('english_name');
        $guarantor->nepali_name = $request->input('nepali_name');
        $guarantor->client_id = $request->input('client_id');
        $guarantor->gender = $request->input('gender');
        $guarantor->grandfather_name = $request->input('grandfather_name');
        $guarantor->grandfather_relation = $request->input('grandfather_relation');
        $guarantor->father_name = $request->input('father_name');
        $guarantor->father_relation = $request->input('father_relation');
        $guarantor->spouse_name = $request->input('spouse_name');
        $guarantor->spouse_relation = $request->input('spouse_relation');
        $guarantor->district_id = $request->input('district_id');
        $guarantor->local_bodies_id = $request->input('local_bodies_id');
        $guarantor->wardno = $request->input('wardno');
        $guarantor->phone = $request->input('phone');
        $guarantor->dob_year = $request->input('dob_year');
        $guarantor->dob_month = $request->input('dob_month');
        $guarantor->dob_day = $request->input('dob_day');
        $guarantor->citizenship_number = $request->input('citizenship_number');
        $guarantor->issued_year = $request->input('issued_year');
        $guarantor->issued_month = $request->input('issued_month');
        $guarantor->issued_day = $request->input('issued_day');
        $guarantor->issued_district = $request->input('issued_district');
        $guarantor->status = $request->input('status');
        $guarantor->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $guarantor->update();

        } catch (\Exception $e) {

        }
        if ($status) {
            Session::flash('success', 'Personal Guarantor Updated Successfully');
        } else {
            Session::flash('danger', 'Personal Guarantor Update Failed');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }


    public function personal_guarantor_destroy($bid, $id)
    {
        $personal_guarantor = PersonalGuarantorBorrower::where([
            ['borrower_id', $bid],
            ['personal_guarantor_id', $id],
            ['guarantor_type', 'personal']
        ])->first();

$status=null;
        try {
            $status = $personal_guarantor->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Guarantor Removed from the borrower Successfully');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }


    public function corporate_guarantor_edit($bid, $id)
    {
        $ministry = [];
        $department = [];
        $district = [];

        $guarantor = CorporateGuarantor::find($id);
        $authorized_person = AuthorizedPerson::find($guarantor->authorized_person_id);

        $min = Ministry::select('id', 'name')->get();
        foreach ($min as $m) {
            $ministry[$m->id] = $m->name;
        }
        $dep = Department::select('id', 'name')->get();
        foreach ($dep as $d) {
            $department[$d->id] = $d->name;
        }
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }

        $localbody = [];
        $local = LocalBodies::select('id', 'name','body_type')->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }

        return view('personal_review.corporate_guarantor_edit', compact('guarantor', 'bid', 'authorized_person', 'department', 'ministry', 'localbody', 'district'));


    }

    public function corporate_guarantor_update(CorporateGuarantorUpdateRequest $request, $id)
    {
        $bid = $request->input('bid');
        $guarantor = CorporateGuarantor::find($id);
        $guarantor->english_name = $request->input('english_name');
        $guarantor->nepali_name = $request->input('nepali_name');
        $guarantor->client_id = $request->input('client_id');
        $guarantor->reg_year = $request->input('reg_year');
        $guarantor->reg_month = $request->input('reg_month');
        $guarantor->reg_day = $request->input('reg_day');
        $guarantor->phone = $request->input('phone');
        $guarantor->ministry_id = $request->input('ministry_id');
        $guarantor->department_id = $request->input('department_id');
        $guarantor->registration_number = $request->input('registration_number');
        $guarantor->district_id = $request->input('district_id');
        $guarantor->local_bodies_id = $request->input('local_bodies_id');
        $guarantor->wardno = $request->input('wardno');
        $guarantor->status = $request->input('status');
        $guarantor->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $guarantor->update();

        } catch (\Exception $e) {

        }

        $authorized_person = AuthorizedPerson::find($guarantor->authorized_person_id);
        $authorized_person->english_name = $request->input('a_english_name');
        $authorized_person->nepali_name = $request->input('a_nepali_name');
        $authorized_person->gender = $request->input('a_gender');
        $authorized_person->grandfather_name = $request->input('a_grandfather_name');
        $authorized_person->grandfather_relation = $request->input('a_grandfather_relation');
        $authorized_person->father_name = $request->input('a_father_name');
        $authorized_person->father_relation = $request->input('a_father_relation');
        $authorized_person->spouse_name = $request->input('a_spouse_name');
        $authorized_person->spouse_relation = $request->input('a_spouse_relation');
        $authorized_person->district_id = $request->input('a_district_id');
        $authorized_person->local_bodies_id = $request->input('a_local_bodies_id');
        $authorized_person->wardno = $request->input('a_wardno');
        $authorized_person->dob_year = $request->input('a_dob_year');
        $authorized_person->dob_month = $request->input('a_dob_month');
        $authorized_person->dob_day = $request->input('a_dob_day');
        $authorized_person->citizenship_number = $request->input('a_citizenship_number');
        $authorized_person->issued_year = $request->input('a_issued_year');
        $authorized_person->issued_month = $request->input('a_issued_month');
        $authorized_person->issued_day = $request->input('a_issued_day');
        $authorized_person->issued_district = $request->input('a_issued_district');
        $authorized_person->status = $request->input('status');
        $authorized_person->post = $request->input('a_post');
        $authorized_person->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status1 = $authorized_person->update();

        } catch (\Exception $e) {

        }

        if ($status && $status1) {
            Session::flash('success', 'Corporate Guarantor Updated Successfully');
        } else {

            Session::flash('danger', 'Corporate Guarantor Update Failed');
        }
        return redirect()->route('personal_review.index', compact('bid'));


    }

    public function corporate_guarantor_destroy($bid, $id)
    {
        $corporate_guarantor = PersonalGuarantorBorrower::where([
            ['borrower_id', $bid],
            ['corporate_guarantor_id', $id],
            ['guarantor_type', 'corporate']
        ])->first();


        $status = null;
        try {
            $status = $corporate_guarantor->delete();
        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Guarantor Removed from the borrower Successfully');
        }
        return redirect()->route('personal_review.index', compact('bid'));

    }


    public function personal_property_owner_edit($bid, $id)
    {
        $property_owner = PersonalPropertyOwner::find($id);
        $district = [];
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }

        $localbody = [];
        $local=LocalBodies::select('id','name','body_type')->where([['district_id',$property_owner->district_id]])->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }



        return view('personal_review.personal_property_owner_edit', compact('property_owner', 'district', 'localbody', 'bid'));

    }


    public function personal_property_owner_update(PersonalPropertyOwnerUpdateRequest $request, $id)
    {

        $bid = $request->input('bid');
        $property_owner = PersonalPropertyOwner::find($id);
        $property_owner->english_name = $request->input('english_name');
        $property_owner->nepali_name = $request->input('nepali_name');
        $property_owner->client_id = $request->input('client_id');
        $property_owner->gender = $request->input('gender');
        $property_owner->grandfather_name = $request->input('grandfather_name');
        $property_owner->grandfather_relation = $request->input('grandfather_relation');
        $property_owner->father_name = $request->input('father_name');
        $property_owner->phone = $request->input('phone');
        $property_owner->father_relation = $request->input('father_relation');
        $property_owner->spouse_name = $request->input('spouse_name');
        $property_owner->spouse_relation = $request->input('spouse_relation');
        $property_owner->district_id = $request->input('district_id');
        $property_owner->local_bodies_id = $request->input('local_bodies_id');
        $property_owner->wardno = $request->input('wardno');
        $property_owner->dob_year = $request->input('dob_year');
        $property_owner->dob_month = $request->input('dob_month');
        $property_owner->dob_day = $request->input('dob_day');
        $property_owner->citizenship_number = $request->input('citizenship_number');
        $property_owner->issued_year = $request->input('issued_year');
        $property_owner->issued_month = $request->input('issued_month');
        $property_owner->issued_day = $request->input('issued_day');
        $property_owner->issued_district = $request->input('issued_district');
        $property_owner->status = $request->input('status');
        $property_owner->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $property_owner->update();

        } catch (\Exception $e) {

        }
        if ($status) {
            Session::flash('success', 'Personal Property Owner Updated Successfully');
        } else {
            Session::flash('danger', 'Personal Property Owner Update Failed');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }


    public function personal_property_owner_destroy($bid, $id)
    {
        $status = null;
        $status1 = null;

        $personal_land = PersonalLand::where([
            ['property_owner_id', $id]])->get();
        foreach ($personal_land as $po) {
            $land = PersonalLandBorrower::where([['borrower_id', $bid],
                ['personal_land_id', $po->id],
                ['property_type', 'personal']])->first();
            if ($land) {
                try {
                    $status = $land->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }

        }
        $personal_share = PersonalShare::where([
            ['property_owner_id', $id]])->get();
        foreach ($personal_share as $po) {
            $share = PersonalShareBorrower::where([['borrower_id', $bid],
                ['personal_share_id', $po->id],
                ['property_type', 'personal']])->first();
            if ($share) {
                try {
                    $status1 = $share->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }

        }

        if ($status || $status1) {
            Session::flash('success', 'Property Owner along with property, removed from the borrower Successfully');
        } else {

            Session::flash('danger', 'Failed to remove properties and property owner');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }

    public function corporate_property_owner_edit($bid, $id)
    {
        $property_owner = CorporatePropertyOwner::find($id);
        $authorized_person = AuthorizedPerson::find($property_owner->authorized_person_id);

        $min = Ministry::select('id', 'name')->get();
        foreach ($min as $m) {
            $ministry[$m->id] = $m->name;
        }
        $dep = Department::select('id', 'name')->get();
        foreach ($dep as $d) {
            $department[$d->id] = $d->name;
        }
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }

        $localbody = [];
        $local = LocalBodies::select('id', 'name','body_type')->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }

        return view('personal_review.corporate_property_owner_edit', compact('bid', 'property_owner', 'authorized_person', 'department', 'ministry', 'localbody', 'district'));
    }

    public function corporate_property_owner_update(CorporatePropertyOwnerUpdateRequest $request, $id)
    {

        $bid = $request->input('bid');
        $property_owner = CorporatePropertyOwner::find($id);

        $property_owner->english_name = $request->input('english_name');
        $property_owner->nepali_name = $request->input('nepali_name');
        $property_owner->client_id = $request->input('client_id');
        $property_owner->reg_year = $request->input('reg_year');
        $property_owner->reg_month = $request->input('reg_month');
        $property_owner->reg_day = $request->input('reg_day');
        $property_owner->phone = $request->input('phone');
        $property_owner->ministry_id = $request->input('ministry_id');
        $property_owner->department_id = $request->input('department_id');
        $property_owner->registration_number = $request->input('registration_number');
        $property_owner->district_id = $request->input('district_id');
        $property_owner->local_bodies_id = $request->input('local_bodies_id');
        $property_owner->wardno = $request->input('wardno');
        $property_owner->status = $request->input('status');
        $property_owner->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $property_owner->update();

        } catch (\Exception $e) {

        }

        $authorized_person = AuthorizedPerson::find($property_owner->authorized_person_id);
        $authorized_person->english_name = $request->input('a_english_name');
        $authorized_person->nepali_name = $request->input('a_nepali_name');
        $authorized_person->gender = $request->input('a_gender');
        $authorized_person->grandfather_name = $request->input('a_grandfather_name');
        $authorized_person->grandfather_relation = $request->input('a_grandfather_relation');
        $authorized_person->father_name = $request->input('a_father_name');
        $authorized_person->father_relation = $request->input('a_father_relation');
        $authorized_person->spouse_name = $request->input('a_spouse_name');
        $authorized_person->spouse_relation = $request->input('a_spouse_relation');
        $authorized_person->district_id = $request->input('a_district_id');
        $authorized_person->local_bodies_id = $request->input('a_local_bodies_id');
        $authorized_person->wardno = $request->input('a_wardno');
        $authorized_person->dob_year = $request->input('a_dob_year');
        $authorized_person->dob_month = $request->input('a_dob_month');
        $authorized_person->dob_day = $request->input('a_dob_day');
        $authorized_person->citizenship_number = $request->input('a_citizenship_number');
        $authorized_person->issued_year = $request->input('a_issued_year');
        $authorized_person->issued_month = $request->input('a_issued_month');
        $authorized_person->issued_day = $request->input('a_issued_day');
        $authorized_person->issued_district = $request->input('a_issued_district');
        $authorized_person->status = $request->input('status');
        $authorized_person->post = $request->input('a_post');
        $authorized_person->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status1 = $authorized_person->update();

        } catch (\Exception $e) {

        }

        if ($status && $status1) {
            Session::flash('success', 'Personal Property Owner Updated Successfully');
        } else {
            Session::flash('danger', 'Personal Property Owner Update Failed');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }


    public function corporate_property_owner_destroy($bid, $id)
    {
        $status = null;
        $status1 = null;

        $corporate_land = CorporateLand::where([
            ['property_owner_id', $id]])->get();
        foreach ($corporate_land as $po) {
            $land = PersonalLandBorrower::where([['borrower_id', $bid],
                ['corporate_land_id', $po->id],
                ['property_type', 'corporate']])->first();
            if ($land) {
                try {
                    $status = $land->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
        }
        $personal_share = CorporateShare::where([
            ['property_owner_id', $id]])->get();
        foreach ($personal_share as $po) {
            $share = PersonalShareBorrower::where([['borrower_id', $bid],
                ['corporate_share_id', $po->id],
                ['property_type', 'corporate']])->first();
            if ($share) {
                try {
                    $status1 = $share->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }

        }

        if ($status || $status1) {
            Session::flash('success', 'Property Owner along with property, removed from the borrower Successfully');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }


    public function facilities_edit($bid, $id)
    {
        $facilities = [];
        $fac = Facility::select('id', 'name')->get();
        foreach ($fac as $f) {
            $facilities[$f->id] = $f->name;
        }

        $facility = PersonalFacilities::find($id);
        return view('personal_review.facilities_edit', compact('facility', 'facilities', 'bid'));

    }

    public function facilities_update(PersonalFacilityUpdateRequest $request, $id)
    {
        function Dinesh_money_format($money)
        {
            $len = strlen($money);
            $m = '';
            $money = strrev($money);
            for ($i = 0; $i < $len; $i++) {
                if (($i == 3 || ($i > 3 && ($i - 1) % 2 == 0)) && $i != $len) {
                    $m .= ',';
                }
                $m .= $money[$i];
            }
            return strrev($m);
        }

        $amt = Dinesh_money_format($request->input('amount'));
        $bid = $request->input('bid');
        $id = $request->input('id');
        $facility = PersonalFacilities::find($id);
        $facility->facility_id = $request->input('facility');
        $facility->amount = $amt;
        $facility->rate = $request->input('rate');
        $facility->remarks = $request->input('remarks');
        $facility->tenure = $request->input('tenure');
        $facility->tyear = $request->input('tyear');
        $facility->tmonth = $request->input('tmonth');
        $facility->within = $request->input('within');
        $facility->tday = $request->input('tday');
        $status = null;
        try {
            $result = $facility->update();

        } catch (\Exception $e) {

        }

        if ($result) {
            Session::flash('success', 'Facility Updated Successfully.');

        } else {
            Session::flash('warning', 'Updating Facility Failed. Please Try again.');
            return redirect()->route('personal_facilities.edit', compact('bid', 'id'));

        }


        return redirect()->route('personal_review.index', compact('bid'));


    }


    public function facilities_destroy($bid, $id)
    {
        $facility = PersonalFacilities::find($id);
        $status = null;
        try {
            $status = $facility->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Facility Deleted Successfully');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }


    public function loan_edit($bid, $id)
    {
        $facility = [];
        $fac = Facility::select('id', 'name')->get();
        foreach ($fac as $f) {
            $facility[$f->id] = $f->name;
        }

        $loan = PersonalLoan::find($id);
        $branch = [];
        $branches = Branch::select('id', 'location')->get();
        foreach ($branches as $b) {
            $branch[''] = '????????????';
            $branch[$b->id] = $b->location;
        }

        $a = $loan->loan_amount;
        $b = str_replace(',', '', $a);
        if (is_numeric($b)) {
            $amount = $b;
        }


        return view('personal_review.loan_edit', compact('loan', 'facility', 'bid', 'branch', 'amount'));

    }

    public function loan_update(PersonalLoanUpdateRequest $request, $id)
    {
        $bid = $request->get('bid');
        if ($request->input('words') == 's[kof C)f /sd /fVg\'xf]nf .') {
            Session::flash('danger', 'Please Convert Loan Amount by clicking "Convert to Word".');
            return redirect()->route('personal_loan.index', compact('bid'));
        }
        function Dinesh_money_format($money)
        {
            $len = strlen($money);
            $m = '';
            $money = strrev($money);
            for ($i = 0; $i < $len; $i++) {
                if (($i == 3 || ($i > 3 && ($i - 1) % 2 == 0)) && $i != $len) {
                    $m .= ',';
                }
                $m .= $money[$i];
            }
            return strrev($m);
        }

        $amount = Dinesh_money_format($request->input('amount'));


        $loan = PersonalLoan::find($id);
        $loan->loan_amount = $amount;
        $loan->loan_amount_words = $request->input('words');
        $loan->offerletter_day = $request->input('offerletter_day');
        $loan->offerletter_month = $request->input('offerletter_month');
        $loan->offerletter_year = $request->input('offerletter_year');
        $loan->branch_id = $request->input('branch_id');
        $loan->updated_by = Auth::user()->id;

        $status = null;
        try {
            $status = $loan->update();

        } catch (\Exception $e) {

        }
        if ($status) {
            Session::flash('success', 'Loan Details Updated Successfully');
        } else {

            Session::flash('danger', 'Unable to update loan details');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }


    public function hirepurchase_edit($bid, $id)
    {
        $vehicle = PersonalHirePurchase::find($id);
        return view('personal_review.edit_vehicle', compact('bid', 'vehicle'));
    }

    public function hirepurchase_update(VehicleUpdateRequest $request)
    {
        $bid = $request->input('bid');
        $id = $request->input('id');
        $vehicle = PersonalHirePurchase::find($id);
        $vehicle->model_number = $request->input('model_number');
        $vehicle->registration_number = $request->input('registration_number');
        $vehicle->engine_number = $request->input('engine_number');
        $vehicle->chassis_number = $request->input('chassis_number');
        $vehicle->updated_by = Auth::user()->id;
        $result = null;
        try {
            $result = $vehicle->update();

        } catch (\Exception $e) {

        }
        if ($result) {
            Session::flash('success', 'Vehicle Updated and Successfully');
        } else {
            Session::flash('danger', 'Failed to Update Vehicle');
        }
        return redirect()->route('personal_review.index', compact('bid'));


    }

    public function hirepurchase_destroy($bid, $id)
    {
        $vehicle = PersonalHirePurchase::find($id);
        $result = null;
        try {
            $result = $vehicle->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($result) {
            Session::flash('success', 'Vehicle Deleted and Successfully');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }

    public function personal_land_edit($bid, $id)
    {
        $land = PersonalLand::find($id);
        $district = [];
        $dis = District::select('id', 'name')->get();

        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }

        $localbody = [];
        $local=LocalBodies::select('id','name','body_type')->where([['district_id',$land->district_id]])->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }


        return view('personal_review.edit_land', compact('bid', 'land', 'localbody', 'district'));

    }


    public function personal_land_update(LandUpdateRequest $request, $id)
    {
        $bid = $request->input('bid');
        $pid = $request->input('id');
        $land = PersonalLand::find($pid);
        $land->district_id = $request->input('district_id');
        $land->local_bodies_id = $request->input('local_body_id');
        $land->wardno = $request->input('wardno');
        $land->sheet_no = $request->input('sheet_no');
        $land->kitta_no = $request->input('kitta_no');
        $land->area = $request->input('area');
        $land->remarks = $request->input('remarks');
        $land->malpot = $request->input('malpot');
        $land->status = $request->input('stat');
        $land->updated_by = Auth::user()->id;
        $result = null;
        try {
            $result = $land->update();

        } catch (\Exception $e) {

        }

        if ($result) {
            Session::flash('success', 'Land Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Land');
            return redirect()->route('personal_review.land_edit', compact('bid', 'id'));
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }

    public function personal_land_destroy($bid, $id)
    {
        $land = PersonalLandBorrower::where([['personal_land_id', $id], ['borrower_id', $bid]])->first();
        $result = null;
        try {
            $result = $land->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }

        if ($result) {
            Session::flash('success', 'Land Removed Successfully');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }

    public function personal_share_edit($bid, $id)
    {
        $share = PersonalShare::find($id);

        $isin = [];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }
        return view('personal_review.edit_share', compact('bid', 'isin', 'share'));
    }

    public function personal_share_update(ShareUpdateRequest $request, $id)
    {
        $bid = $request->input('bid');
        $pid = $request->input('id');
//dd($request);
        $share = PersonalShare::find($pid);
        $share->client_id = $request->input('client_id');
        $share->isin = $request->input('isin');
        $share->share_type = $request->input('share_type');
        $share->dpid = $request->input('dpid');
        $share->kitta = $request->input('kitta');
        $share->status = $request->input('stat');
        $share->updated_by = Auth::user()->id;
        $result = null;
        try {
            $result = $share->update();

        } catch (\Exception $e) {

        }
        if ($result) {
            Session::flash('success', 'Share Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Share');
            return redirect()->route('personal_reviewy.share_edit', compact('bid'));
        }
        return redirect()->route('personal_review.index', compact('bid'));

    }

    public function personal_share_destroy($bid, $id)
    {
        $land = PersonalShareBorrower::where([['personal_share_id', $id], ['borrower_id', $bid]])->first();
        $result = null;
        try {
            $result = $land->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }

        if ($result) {
            Session::flash('success', 'Share Removed Successfully');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }

    public function corporate_land_edit($bid, $id)
    {
        $land = CorporateLand::find($id);
        $district = [];
        $dis = District::select('id', 'name')->get();

        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }

        $localbody = [];
        $local=LocalBodies::select('id','name','body_type')->where([['district_id',$land->district_id]])->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }



        return view('personal_review.edit_corporate_land', compact('bid', 'land', 'localbody', 'district'));

    }


    public function corporate_land_update(LandUpdateRequest $request, $id)
    {
        $bid = $request->input('bid');
        $pid = $request->input('id');
        $land = CorporateLand::find($pid);
        $land->district_id = $request->input('district_id');
        $land->local_bodies_id = $request->input('local_body_id');
        $land->wardno = $request->input('wardno');
        $land->sheet_no = $request->input('sheet_no');
        $land->kitta_no = $request->input('kitta_no');
        $land->area = $request->input('area');
        $land->remarks = $request->input('remarks');
        $land->malpot = $request->input('malpot');
        $land->status = $request->input('stat');
        $land->updated_by = Auth::user()->id;
        $result = null;
        try {
            $result = $land->update();

        } catch (\Exception $e) {

        }

        if ($result) {
            Session::flash('success', 'Land Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Land');
            return redirect()->route('personal_review.land_edit', compact('bid', 'id'));
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }

    public function corporate_land_destroy($bid, $id)
    {
        $land = PersonalLandBorrower::where([['corporate_land_id', $id], ['borrower_id', $bid]])->first();
        $status = null;
        try {
            $result = $land->delete();
        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }

        if ($result) {
            Session::flash('success', 'Land Removed Successfully');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }

    public function corporate_share_edit($bid, $id)
    {
        $share = CorporateShare::find($id);

        $isin = [];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }
        return view('personal_review.edit_corporate_share', compact('bid', 'isin', 'share'));
    }

    public function corporate_share_update(ShareUpdateRequest $request, $id)
    {
        $bid = $request->input('bid');
        $pid = $request->input('id');
//dd($request);
        $share = CorporateShare::find($pid);
        $share->client_id = $request->input('client_id');
        $share->isin = $request->input('isin');
        $share->share_type = $request->input('share_type');
        $share->dpid = $request->input('dpid');
        $share->kitta = $request->input('kitta');
        $share->status = $request->input('stat');
        $share->updated_by = Auth::user()->id;
        $result = null;
        try {
            $result = $share->update();

        } catch (\Exception $e) {

        }
        if ($result) {
            Session::flash('success', 'Share Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Share');
            return redirect()->route('personal_reviewy.share_edit', compact('bid'));
        }
        return redirect()->route('personal_review.index', compact('bid'));

    }

    public function Corporate_share_destroy($bid, $id)
    {
        $land = PersonalShareBorrower::where([['corporate_share_id', $id], ['borrower_id', $bid]])->first();
        $result = null;
        try {
            $result = $land->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }

        if ($result) {
            Session::flash('success', 'Share Removed Successfully');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }

    public function joint_property_owner_destroy($bid)
    {
        $result=null;
        $borrower = PersonalBorrower::find($bid);

        $joint = $borrower->personal_joint_land()->first();
       
        if ($joint) {
            $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
        }
        if ($jointp) {
            foreach ($jointp as $j) {
                $result = null;
                try {
                    $result = $j->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
        }else{
            $result=1;
        }
        if ($result) {
            try {
                $joint->delete();

            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }
            Session::flash('success', 'Joint Owner and Related Property Removed Successfully');
        }
        return redirect()->route('personal_review.index', compact('bid'));

    }


    public function joint_land_edit($bid, $id)
    {
        $land = JointLand::find($id);
        $district = [];
        $dis = District::select('id', 'name')->get();

        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }

        $localbody = [];
        $local = LocalBodies::select('id', 'name','body_type')->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }


        return view('personal_review.edit_joint_land', compact('bid', 'land', 'localbody', 'district'));

    }


    public function joint_land_update(LandUpdateRequest $request, $id)
    {
        $bid = $request->input('bid');
        $pid = $request->input('id');
        $land = JointLand::find($pid);
        $land->district_id = $request->input('district_id');
        $land->local_bodies_id = $request->input('local_body_id');
        $land->wardno = $request->input('wardno');
        $land->sheet_no = $request->input('sheet_no');
        $land->kitta_no = $request->input('kitta_no');
        $land->area = $request->input('area');
        $land->remarks = $request->input('remarks');
        $land->malpot = $request->input('malpot');
        $land->status = $request->input('stat');
        $land->updated_by = Auth::user()->id;
        $result = null;
        try {
            $result = $land->update();
        } catch (\Exception $e) {

        }
        if ($result) {
            Session::flash('success', 'Land Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Land');
            return redirect()->route('personal_review.edit_joint_land', compact('bid', 'id'));
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }

    public function joint_land_destroy($bid, $id)
    {
        $land = JointLand::find($id);
        $result = null;
        try {
            $result = $land->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }

        if ($result) {
            Session::flash('success', 'Land Removed Successfully');
        }
        return redirect()->route('personal_review.index', compact('bid'));
    }


    public function proceed($bid)
    {
        $document = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        $document->document_status = 'Submitted';
        $document->document_remarks = 'Waiting for approval';
        $document->submitted_at=Carbon::now();
        $document->submitted_by=Auth::user()->id;
        $document->updated_by = Auth::user()->id;
        $status = null;
        try {
            $status = $document->update();

        } catch (\Exception $e) {

        }
        if ($status) {
            Session::flash('success', 'Document Submitted Successfully, Your Document will be approved soon.');
        } else {
            Session::flash('danger', 'Failed to submit data, please try again.');
            return redirect()->route('personal_review.index', compact('bid'));
        }
        return redirect()->route('home');
    }
}
