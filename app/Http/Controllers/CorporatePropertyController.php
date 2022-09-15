<?php

namespace App\Http\Controllers;

use App\AuthorizedPerson;
use App\CorporateBorrower;
use App\CorporateGuarantor;
use App\CorporateGuarantorBorrower;
use App\CorporateLand;
use App\CorporateLandBorrower;
use App\CorporatePropertyOwner;
use App\CorporateShare;
use App\CorporateShareBorrower;
use App\Department;
use App\District;
use App\Http\Requests\CorporateCreateRequest;
use App\Http\Requests\CorporatePropertyOwnerCreateRequest;
use App\Http\Requests\LandUpdateRequest;
use App\Http\Requests\PersonalCreateRequest;
use App\Http\Requests\PersonalPropertyOwnerCreateRequest;
use App\Http\Requests\ShareUpdateRequest;
use App\Http\Requests\VehicleUpdateRequest;
use App\JointLandBorrower;
use App\LocalBodies;
use App\Ministry;
use App\PersonalBorrower;
use App\PersonalGuarantor;
use App\PersonalGuarantorBorrower;
use App\CorporateHirePurchase;
use App\PersonalLand;
use App\PersonalLandBorrower;
use App\PersonalPropertyOwner;
use App\PersonalShare;
use App\PersonalShareBorrower;
use App\RegisteredCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CorporatePropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index($bid)
    {
        $personal_property_owner = PersonalPropertyOwner::all();
        $corporate_property_owner = CorporatePropertyOwner::all();
        $personal_guarantor_list = CorporateGuarantorBorrower::where([
            ['borrower_id', $bid],
            ['guarantor_type', 'personal']
        ])->get();

        $corporate_guarantor_list = CorporateGuarantorBorrower::where([
            ['borrower_id', $bid],
            ['guarantor_type', 'corporate']
        ])->get();

        $vehicle = CorporateHirePurchase::where([
            ['borrower_id', $bid]
        ])->get();

        $pland = [];
        $cland = [];
        $pshare = [];
        $cshare = [];
//        land property assigned for the borrrower
        $pland_id = CorporateLandBorrower::where([
            ['borrower_id', $bid],
            ['property_type', 'personal']
        ])->get();
        foreach ($pland_id as $l) {
            $pland[] = PersonalLand::find($l->personal_land_id);
        }

        $cland_id = CorporateLandBorrower::where([
            ['borrower_id', $bid],
            ['property_type', 'corporate']
        ])->get();
        foreach ($cland_id as $l) {
            $cland[] = CorporateLand::find($l->corporate_land_id);
        }


        //        share Properties assigned for the borrower
        $pshare_id = CorporateShareBorrower::where([
            ['borrower_id', $bid],
            ['property_type', 'personal'],

        ])->get();
        foreach ($pshare_id as $s) {
            $pshare[] = PersonalShare::find($s->personal_share_id);
        }

        $cshare_id = CorporateShareBorrower::where([
            ['borrower_id', $bid],
            ['property_type', 'corporate']
        ])->get();
        foreach ($cshare_id as $s) {
            $cshare[] = CorporateShare::find($s->corporate_share_id);
        }

        $isin=[];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }
        return view('corporate_property.index', compact('cshare', 'isin','pshare', 'cland', 'pland', 'bid', 'vehicle', 'personal_guarantor_list', 'corporate_guarantor_list', 'personal_property_owner', 'corporate_property_owner'));
    }

    public function copy_index($bid)
    {
        $personal_borrower = PersonalBorrower::all();
        $personal_guarantor = PersonalGuarantor::all();
        $corporate_borrower = CorporateBorrower::all();
        $corporate_guarantor = CorporateGuarantor::all();

        return view('corporate_property.copy_index', compact('bid', 'personal_borrower', 'personal_guarantor', 'corporate_borrower', 'corporate_guarantor'));
    }


    public function personal_property_create($bid)
    {
//district
        $district = [];
        $dis = District::select('id', 'name')->get()->sortBy('name');
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }
//        local body


        $localbody = [];
        $local = LocalBodies::select('id', 'name','body_type')->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }

        $isin=[];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }

        return view('corporate_property.personal_create', compact('district','isin', 'localbody', 'bid'));
    }

    public function personal_property_store(PersonalPropertyOwnerCreateRequest $request, $bid)
    {
        //        stores personal property
        $data = PersonalPropertyOwner::create([
            'english_name' => $request->input('english_name'),
            'nepali_name' => $request->input('nepali_name'),
            'client_id' => $request->input('client_id'),
            'gender' => $request->input('gender'),
            'grandfather_name' => $request->input('grandfather_name'),
            'grandfather_relation' => $request->input('grandfather_relation'),
            'father_name' => $request->input('father_name'),
            'father_relation' => $request->input('father_relation'),
            'spouse_name' => $request->input('spouse_name'),
            'spouse_relation' => $request->input('spouse_relation'),
            'district_id' => $request->input('district_id'),
            'local_bodies_id' => $request->input('local_bodies_id'),
            'wardno' => $request->input('wardno'),
            'phone' => $request->input('phone'),
            'dob_year' => $request->input('dob_year'),
            'dob_month' => $request->input('dob_month'),
            'dob_day' => $request->input('dob_day'),
            'citizenship_number' => $request->input('citizenship_number'),
            'issued_year' => $request->input('issued_year'),
            'issued_month' => $request->input('issued_month'),
            'issued_day' => $request->input('issued_day'),
            'issued_district' => $request->input('issued_district'),
            'status' => '1',
            'created_by' => Auth::user()->id,
        ]);
//        getting id of inserted data
        if ($data) {
            $poid = $data->id;
            $status = 'personal';
            Session::flash('success', 'Property Owner Created Successfully, Now Create property');
        } else {
            Session::flash('danger', 'Property owner Creation Failed, Please Try Again.');
            return redirect()->route('corporate_property.personal_create', compact('bid'));
        }

        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));

    }

    public function corporate_property_create($bid)
    {

        $district = [];
        $dis = District::select('id', 'name')->get()->sortBy('name');
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

        $ministry = [];
        $min = Ministry::select('id', 'name')->get()->sortBy('name');
        foreach ($min as $m) {
            $ministry[$m->id] = $m->name;
        }
        $department = [];
        $dep = Department::select('id', 'name')->get()->sortBy('name');
        foreach ($dep as $d) {
            $department[$d->id] = $d->name;
        }
        $isin=[];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }
        return view('corporate_property.corporate_create', compact('district','isin', 'localbody', 'ministry', 'department', 'bid'));
    }

    public function corporate_property_store(CorporatePropertyOwnerCreateRequest $request, $bid)
    {
        $check = AuthorizedPerson::where([
            ['english_name', $request->input('a_english_name')],
            ['grandfather_name', $request->input('a_grandfather_name')],
            ['father_name', $request->input('a_father_name')],
            ['citizenship_number', $request->input('a_citizenship_number')]
        ])->first();


        //            if the authorized person is present in authorized list
        if (!$check) {

            $data = AuthorizedPerson::create([
                'english_name' => $request->input('a_english_name'),
                'nepali_name' => $request->input('a_nepali_name'),
                'gender' => $request->input('a_gender'),
                'grandfather_name' => $request->input('a_grandfather_name'),
                'grandfather_relation' => $request->input('a_grandfather_relation'),
                'father_name' => $request->input('a_father_name'),
                'father_relation' => $request->input('a_father_relation'),
                'spouse_name' => $request->input('a_spouse_name'),
                'spouse_relation' => $request->input('a_spouse_relation'),
                'district_id' => $request->input('a_district_id'),
                'local_bodies_id' => $request->input('a_local_bodies_id'),
                'wardno' => $request->input('a_wardno'),
                'dob_year' => $request->input('a_dob_year'),
                'dob_month' => $request->input('a_dob_month'),
                'dob_day' => $request->input('a_dob_day'),
                'citizenship_number' => $request->input('a_citizenship_number'),
                'issued_year' => $request->input('a_issued_year'),
                'issued_month' => $request->input('a_issued_month'),
                'issued_day' => $request->input('a_issued_day'),
                'issued_district' => $request->input('a_issued_district'),
                'post' => $request->input('a_post'),
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);


            if ($data) {
                $authorized_person_id = $data->id;
                $data1 = CorporatePropertyOwner::create([
                    'english_name' => $request->input('english_name'),
                    'nepali_name' => $request->input('nepali_name'),
                    'client_id' => $request->input('client_id'),
                    'district_id' => $request->input('district_id'),
                    'local_bodies_id' => $request->input('local_bodies_id'),
                    'wardno' => $request->input('wardno'),
                    'reg_year' => $request->input('reg_year'),
                    'reg_month' => $request->input('reg_month'),
                    'reg_day' => $request->input('reg_day'),
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                    'ministry_id' => $request->input('ministry_id'),
                    'department_id' => $request->input('department_id'),
                    'registration_number' => $request->input('registration_number'),
                    'authorized_person_id' => $authorized_person_id,
                ]);

                if ($data1) {

                    $poid = $data1->id;
                    $status = 'corporate';
                    Session::flash('success', 'Property Owner Created Successfully, Now Create Property.');

                }

            } else {
                Session::flash('danger', 'Error in creating Property_owner, Please Try again.');
                return redirect()->route('personal_guarantor.corporate_create', compact('bid'));
            }

        } else {

            $data2 = CorporatePropertyOwner::create([
                'english_name' => $request->input('english_name'),
                'nepali_name' => $request->input('nepali_name'),
                'client_id' => $request->input('client_id'),
                'district_id' => $request->input('district_id'),
                'local_bodies_id' => $request->input('local_bodies_id'),
                'wardno' => $request->input('wardno'),
                'phone' => $request->input('phone'),
                'reg_year' => $request->input('reg_year'),
                'reg_month' => $request->input('reg_month'),
                'reg_day' => $request->input('reg_day'),
                'status' => '1',
                'created_by' => Auth::user()->id,
                'ministry_id' => $request->input('ministry_id'),
                'department_id' => $request->input('department_id'),
                'registration_number' => $request->input('registration_number'),
                'authorized_person_id' => $check->id,
            ]);
        }
        if ($data2) {

            $poid = $data2->id;
            $status = 'corporate';
            Session::flash('success', 'Property Owner Created Successfully, Now Create Property.');
        } else {
            Session::flash('danger', 'Error in creating Property Owner, Please Try again.');
            return redirect()->route('personal_guarantor.corporate_create', compact('bid'));

        }
        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
    }

    public function personal_borrower_property_select($bid, $id)
    {
        $status = 'personal';
        $borrower = PersonalBorrower::find($id);
//            checking whether it exist or not
        $check = PersonalPropertyOwner::where([
            ['english_name', $borrower->english_name],
            ['grandfather_name', $borrower->grandfather_name],
            ['father_name', $borrower->father_name,],
            ['citizenship_number', $borrower->citizenship_number]
        ])->first();
//            if doesnot exist
        if (!$check) {
            $data = PersonalPropertyOwner::create([
                'english_name' => $borrower->english_name,
                'nepali_name' => $borrower->nepali_name,
                'client_id' => $borrower->client_id,
                'gender' => $borrower->gender,
                'grandfather_name' => $borrower->grandfather_name,
                'grandfather_relation' => $borrower->grandfather_relation,
                'father_name' => $borrower->father_name,
                'father_relation' => $borrower->father_relation,
                'spouse_name' => $borrower->spouse_name,
                'spouse_relation' => $borrower->spouse_relation,
                'phone' => $borrower->phone,
                'district_id' => $borrower->district_id,
                'local_bodies_id' => $borrower->local_bodies_id,
                'wardno' => $borrower->wardno,
                'dob_year' => $borrower->dob_year,
                'dob_month' => $borrower->dob_month,
                'dob_day' => $borrower->dob_day,
                'citizenship_number' => $borrower->citizenship_number,
                'issued_year' => $borrower->issued_year,
                'issued_month' => $borrower->issued_month,
                'issued_day' => $borrower->issued_day,
                'issued_district' => $borrower->issued_district,
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
//                getting inserted id
            $poid = $data->id;

            if ($data) {
                Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
                return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
            } else {
                Session::flash('danger', 'Property owner Creation Failed, Please Try Again.');
                return redirect()->route('corporate_property.index', compact('bid'));
            }

        } else {
//                if name already exist in Property owner
            $poid = $check->id;
            Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
            return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
        }


    }

    public function personal_guarantor_property_select($bid, $id)
    {

        $borrower = PersonalGuarantor::find($id);
        $status = 'personal';

        //            checking whether it exist or not
        $check = PersonalPropertyOwner::where([
            ['english_name', $borrower->english_name],
            ['grandfather_name', $borrower->grandfather_name],
            ['father_name', $borrower->father_name,],
            ['citizenship_number', $borrower->citizenship_number]
        ])->first();
//            if doesnot exist

        if (!$check) {
            $data = PersonalPropertyOwner::create([
                'english_name' => $borrower->english_name,
                'nepali_name' => $borrower->nepali_name,
                'client_id' => $borrower->client_id,
                'gender' => $borrower->gender,
                'grandfather_name' => $borrower->grandfather_name,
                'grandfather_relation' => $borrower->grandfather_relation,
                'father_name' => $borrower->father_name,
                'phone' => $borrower->phone,
                'father_relation' => $borrower->father_relation,
                'spouse_name' => $borrower->spouse_name,
                'spouse_relation' => $borrower->spouse_relation,
                'district_id' => $borrower->district_id,
                'local_bodies_id' => $borrower->local_bodies_id,
                'wardno' => $borrower->wardno,
                'dob_year' => $borrower->dob_year,
                'dob_month' => $borrower->dob_month,
                'dob_day' => $borrower->dob_day,
                'citizenship_number' => $borrower->citizenship_number,
                'issued_year' => $borrower->issued_year,
                'issued_month' => $borrower->issued_month,
                'issued_day' => $borrower->issued_day,
                'issued_district' => $borrower->issued_district,
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
//                getting inserted id
            $poid = $data->id;

            if ($data) {
                Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
                return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
            } else {
                Session::flash('danger', 'Property owner Creation Failed, Please Try Again.');
                return redirect()->route('corporate_property.index', compact('bid'));
            }

        } else {
//                if name already exist in Property owner
            $poid = $check->id;
            Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
            return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
        }

    }

    public function personal_property_select($bid, $id)
    {
        $status = 'personal';
        $poid = $id;
        Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
    }

    public function corporate_borrower_property_select($bid, $id)
    {
        $status = 'corporate';
        $borrower = CorporateBorrower::find($id);
//            checking whether it exist or not
        $check = CorporatePropertyOwner::where([
            ['english_name', $borrower->english_name],
            ['registration_number', $borrower->registration_number]
        ])->first();
//            if doesnot exist
        if (!$check) {
            $data = CorporatePropertyOwner::create([
                'english_name' => $borrower->english_name,
                'nepali_name' => $borrower->nepali_name,
                'client_id' => $borrower->client_id,
                'district_id' => $borrower->district_id,
                'local_bodies_id' => $borrower->local_bodies_id,
                'wardno' => $borrower->wardno,
                'phone' => $borrower->phone,
                'reg_year' => $borrower->reg_year,
                'reg_month' => $borrower->reg_month,
                'reg_day' => $borrower->reg_day,
                'ministry_id' => $borrower->ministry_id,
                'department_id' => $borrower->department_id,
                'registration_number' => $borrower->registration_number,
                'authorized_person_id' => $borrower->authorized_person_id,
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
//                getting inserted id
            $poid = $data->id;
            if ($data) {
                Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
                return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
            } else {
                Session::flash('danger', 'Property owner Creation Failed, Please Try Again.');
                return redirect()->route('corporate_property.index', compact('bid'));
            }
        } else {
//                if name already exist in Property owner
            $poid = $check->id;
            Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
            return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
        }


    }

    public function corporate_guarantor_property_select($bid, $id)
    {
        $status = 'corporate';
        $borrower = CorporateGuarantor::find($id);
//            checking whether it exist or not
        $check = CorporatePropertyOwner::where([
            ['english_name', $borrower->english_name],
            ['registration_number', $borrower->registration_number]
        ])->first();
//            if doesnot exist
        if (!$check) {
            $data = CorporatePropertyOwner::create([
                'english_name' => $borrower->english_name,
                'nepali_name' => $borrower->nepali_name,
                'client_id' => $borrower->client_id,
                'district_id' => $borrower->district_id,
                'local_bodies_id' => $borrower->local_bodies_id,
                'wardno' => $borrower->wardno,
                'phone' => $borrower->phone,
                'reg_year' => $borrower->reg_year,
                'reg_month' => $borrower->reg_month,
                'reg_day' => $borrower->reg_day,
                'ministry_id' => $borrower->ministry_id,
                'department_id' => $borrower->department_id,
                'registration_number' => $borrower->registration_number,
                'authorized_person_id' => $borrower->authorized_person_id,
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
//                getting inserted id
            $poid = $data->id;
            if ($data) {
                Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
                return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
            } else {
                Session::flash('danger', 'Property owner Creation Failed, Please Try Again.');
                return redirect()->route('corporate_property.index', compact('bid'));
            }
        } else {
//                if name already exist in Property owner
            $poid = $check->id;
            Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
            return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
        }


    }

    public function corporate_property_select($bid, $id)
    {
        $status = 'corporate';
        $poid = $id;
        Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
    }


    public function choose_property($bid, $poid, $status)
    {
        $pland = [];
        $cland = [];
        $pshare = [];
        $cshare = [];
//        land property assigned for the borrrower
        $pland_id = CorporateLandBorrower::where([
            ['borrower_id', $bid],
            ['property_type', 'personal']
        ])->get();
        foreach ($pland_id as $l) {
            $pland[] = PersonalLand::find($l->personal_land_id);
        }

        $cland_id = CorporateLandBorrower::where([
            ['borrower_id', $bid],
            ['property_type', 'corporate']
        ])->get();
        foreach ($cland_id as $l) {
            $cland[] = CorporateLand::find($l->corporate_land_id);
        }


        //        share Properties assigned for the borrower
        $pshare_id = CorporateShareBorrower::where([
            ['borrower_id', $bid],
            ['property_type', 'personal'],

        ])->get();
        foreach ($pshare_id as $s) {
            $pshare[] = PersonalShare::find($s->personal_share_id);
        }

        $cshare_id = CorporateShareBorrower::where([
            ['borrower_id', $bid],
            ['property_type', 'corporate']
        ])->get();
        foreach ($cshare_id as $s) {
            $cshare[] = CorporateShare::find($s->corporate_share_id);
        }

        //        all the properties of the property owner
        if ($status == 'corporate') {
            $property_owner = CorporatePropertyOwner::find($poid);
            $land_property = CorporateLand::where([
                ['property_owner_id', $poid],
            ])->get();

            $share_property = CorporateShare::where([
                ['property_owner_id', $poid]
            ])->get();
        }

//all the properties of the property owner
        if ($status == 'personal') {
            $property_owner = PersonalPropertyOwner::find($poid);
            $land_property = PersonalLand::where([
                ['property_owner_id', $poid],
            ])->get();
            $share_property = PersonalShare::where([
                ['property_owner_id', $poid]
            ])->get();
        }

        $district = [];
        $dis = District::select('id', 'name')->get()->sortBy('name');
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


//        $vehicle=CorporateHirePurchase::where([
//            ['borrower_id',$bid]
//        ])->get();
        $isin=[];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }
        return view('corporate_property.choose', compact('bid','isin', 'status', 'property_owner', 'land_property', 'share_property', 'district', 'localbody', 'poid', 'pland', 'cland', 'pshare', 'cshare'));
    }


    public function proceed($bid)
    {
        Session::flash('success', 'Please Add Banking Facilities.');
        return redirect()->route('corporate_facilities.index', compact('bid'));
    }


    public function land_store(Request $request)
    {
        $status = $request->input('status');
        $poid = $request->input('poid');
        $bid = $request->input('bid');
        $result1 = false;
        $count1 = count($request->input('wardno'));

        $count = $count1 - 1;

        for ($i = 0; $i < $count; $i++) {
            $district_id = $request->get('district_id');
            $local_body_id = $request->get('local_body_id');
            $wardno = $request->get('wardno');
            $sheet_no = $request->get('sheet_no');
            $kitta_no = $request->get('kitta_no');
            $area = $request->get('area');
            $remarks = $request->get('remarks');
            $malpot = $request->get('malpot');
//     for personal
            if ($request->input('status') == 'personal') {
                if ($district_id[$i] && $local_body_id[$i] && $wardno[$i] && $kitta_no[$i] && $area[$i] && $malpot[$i]) {

                    $data = ([
                        'property_owner_id' => $request->input('poid'),
                        'district_id' => $district_id[$i],
                        'local_bodies_id' => $local_body_id[$i],
                        'wardno' => $wardno[$i],
                        'sheet_no' => $sheet_no[$i],
                        'kitta_no' => $kitta_no[$i],
                        'area' => $area[$i],
                        'remarks' => $remarks[$i],
                        'malpot' => $malpot[$i],
                        'status' => '1',
                        'created_by' => Auth::user()->id
                    ]);
                    $result = PersonalLand::create($data);

                    if ($result) {
                        $property = ([
                            'borrower_id' => $request->input('bid'),
                            'status' => '1',
                            'personal_land_id' => $result->id,
                            'property_type' => 'personal',
                            'created_by' => Auth::user()->id
                        ]);
                        $result1 = CorporateLandBorrower::create($property);
                    }
                }
            }


//corporate


            if ($request->input('status') == 'corporate') {
                if ($district_id[$i] && $local_body_id[$i] && $wardno[$i] && $kitta_no[$i] && $area[$i] && $malpot[$i]) {


                    $data = ([
                        'property_owner_id' => $request->input('poid'),
                        'district_id' => $district_id[$i],
                        'local_bodies_id' => $local_body_id[$i],
                        'wardno' => $wardno[$i],
                        'sheet_no' => $sheet_no[$i],
                        'kitta_no' => $kitta_no[$i],
                        'area' => $area[$i],
                        'remarks' => $remarks[$i],
                        'malpot' => $malpot[$i],
                        'status' => '1',
                        'created_by' => Auth::user()->id
                    ]);
                    $result = CorporateLand::create($data);

                    if ($result) {
                        $property = ([
                            'borrower_id' => $request->input('bid'),
                            'status' => '1',
                            'corporate_land_id' => $result->id,
                            'property_type' => 'corporate',
                            'created_by' => Auth::user()->id
                        ]);
                        $result1 = CorporateLandBorrower::create($property);
                    }
                }

            }
        }

        if ($result1) {
            Session::flash('success', 'Property Added and assigned Successfully');
        } else {
            Session::flash('danger', 'Failed to Add and assign Property');
        }


        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
    }


    public function share_store(Request $request)
    {
        $status = $request->input('status');
        $poid = $request->input('poid');
        $bid = $request->input('bid');
        $result1 = false;
        $count1 = count($request->input('isin'));
        $count = $count1 - 1;


        for ($i = 0; $i < $count; $i++) {

            $client_id = $request->get('client_id');

            $dpid = $request->get('dpid');
            $kitta = $request->get('kitta');
            $isin = $request->get('isin');
            $share_type = $request->get('share_type');

//     for personal
            if ($request->input('status') == 'personal') {
                if ($dpid[$i] && $kitta[$i] && $isin[$i] && $share_type[$i]) {

                    $data = ([
                        'property_owner_id' => $request->input('poid'),
                        'client_id' => $client_id[$i],
                        'dpid' => $dpid[$i],
                        'isin' => $isin[$i],
                        'kitta' => $kitta[$i],
                        'share_type' => $share_type[$i],
                        'status' => '1',
                        'created_by' => Auth::user()->id
                    ]);
                    $result = PersonalShare::create($data);

                    if ($result) {
                        $property = ([
                            'borrower_id' => $request->input('bid'),
                            'status' => '1',
                            'personal_share_id' => $result->id,
                            'property_type' => 'personal',
                            'created_by' => Auth::user()->id
                        ]);
                        $result1 = CorporateShareBorrower::create($property);
                    }
                }
            }
//corporate


            if ($request->input('status') == 'corporate') {

                if ($dpid[$i] && $kitta[$i] && $isin[$i] && $share_type[$i]) {
                    $data = ([
                        'property_owner_id' => $request->input('poid'),
                        'client_id' => $client_id[$i],
                        'dpid' => $dpid[$i],
                        'isin' => $isin[$i],
                        'kitta' => $kitta[$i],
                        'share_type' => $share_type[$i],
                        'status' => '1',
                        'created_by' => Auth::user()->id
                    ]);

                    $result = CorporateShare::create($data);
                    if ($result) {
                        $property = ([
                            'borrower_id' => $request->input('bid'),
                            'status' => '1',
                            'corporate_share_id' => $result->id,
                            'property_type' => 'corporate',
                            'created_by' => Auth::user()->id
                        ]);
                        $result1 = CorporateShareBorrower::create($property);
                    }
                }


            }

        }


        if ($result1) {
            Session::flash('success', 'Property Added and assigned Successfully');
        } else {
            Session::flash('danger', 'Failed to Add and assign Property');
        }


        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
    }

//to do
//Check the validation if it is empty


    public function land_edit($bid, $pid, $status)
    {
        if ($status == 'personal') {
            $land = PersonalLand::find($pid);
        }
        if ($status == 'corporate') {
            $land = CorporateLand::find($pid);
        }

        $district = [];
        $dis = District::select('id', 'name')->get()->sortBy('name');

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



        return view('corporate_property.edit_land', compact('bid', 'status', 'pid', 'land', 'localbody', 'district'));

    }

    public function land_update(LandUpdateRequest $request)
    {

        $bid = $request->input('bid');
        $status = $request->input('status');
        $pid = $request->input('id');

        if ($status == 'personal') {
            $land = PersonalLand::find($pid);
            $poid = $land->property_owner_id;
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
        }

        if ($status == 'corporate') {
            $land = CorporateLand::find($pid);
            $poid = $land->property_owner_id;
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
        }


        if ($result) {
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property');
            return redirect()->route('corporate_property.land_edit', compact('bid', 'poid', 'status'));
        }
        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
    }


    public function land_assign($bid, $pid, $status)
    {

        $result = false;

        if ($status == 'personal') {
            $land = PersonalLand::find($pid);
            $poid = $land->property_owner_id;

            $check = CorporateLandBorrower::where([
                ['personal_land_id', $pid],
                ['borrower_id', $bid],
                ['property_type', 'personal'], ['status', '1']
            ])->first();

            if (!$check) {
                $result = CorporateLandBorrower::create([
                    'borrower_id' => $bid,
                    'status' => '1',
                    'personal_land_id' => $land->id,
                    'property_type' => 'personal',
                    'created_by' => Auth::user()->id
                ]);

            }

        }
        if ($status == 'corporate') {
            $land = CorporateLand::find($pid);
            $poid = $land->property_owner_id;

            $check = CorporateLandBorrower::where([
                ['corporate_land_id', $pid],
                ['borrower_id', $bid],
                ['property_type', 'corporate'], ['status', '1']
            ])->first();

            if (!$check) {
                $result = CorporateLandBorrower::create([
                    'borrower_id' => $bid,
                    'status' => '1',
                    'corporate_land_id' => $land->id,
                    'property_type' => 'corporate',
                    'created_by' => Auth::user()->id
                ]);
            }
        }
        if ($result) {
            Session::flash('success', 'Property assigned Successfully');
        } else {
            Session::flash('warning', 'Error! Property already assigned or the property may be inactive. Please Check and Try again');
        }
        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
    }


    public function land_destroy($bid, $pid, $status)
    {

        $result = false;
        if ($status == 'personal') {
            $land = PersonalLand::find($pid);
            $poid = $land->property_owner_id;
            $pland = PersonalLandBorrower::where([['personal_land_id', $pid]])->get();
            $cland = CorporateLandBorrower::where([['personal_land_id', $pid]])->get();
            $jland = JointLandBorrower::where([['personal_land_id', $pid]])->get();
            foreach ($pland as $p) {
                try {
                    $p->delete();
                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }

            }
            foreach ($cland as $c) {
                try {
                    $c->delete();
                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
            foreach ($jland as $j) {

                try {
                    $j->delete();
                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }


            try {
                $result = $land->delete();
            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }

        }
        if ($status == 'corporate') {
            $land = CorporateLand::find($pid);
            $poid = $land->property_owner_id;
            $pland = PersonalLandBorrower::where([['corporate_land_id', $pid]])->get();
            $cland = CorporateLandBorrower::where([['corporate_land_id', $pid]])->get();
            foreach ($pland as $p) {
                try {
                    $p->delete();
                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
            foreach ($cland as $c) {
                try {
                    $c->delete();
                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }

            try {
                $result = $land->delete();
            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }
        }

        if ($result) {
            Session::flash('success', 'Land Removed Successfully');
        }

        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));


    }

    public function share_edit($bid, $pid, $status)
    {
        if ($status == 'personal') {
            $share = PersonalShare::find($pid);
        }
        if ($status == 'corporate') {
            $share = CorporateShare::find($pid);
        }
        $isin=[];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }

        return view('corporate_property.edit_share', compact('bid','isin', 'status', 'pid', 'share'));

    }

    public function share_update(ShareUpdateRequest $request)
    {
        $result = false;
        $bid = $request->input('bid');
        $status = $request->input('status');
        $pid = $request->input('id');
//dd($request);
        if ($status == 'personal') {
            $share = PersonalShare::find($pid);
            $poid = $share->property_owner_id;

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
        }

        if ($status == 'corporate') {
            $share = CorporateShare::find($pid);
            $poid = $share->property_owner_id;

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
        }


        if ($result) {
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property');
            return redirect()->route('corporate_property.share_edit', compact('bid', 'poid', 'status'));
        }
        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
    }


    public function share_assign($bid, $pid, $status)
    {

        $result = false;

        if ($status == 'personal') {
            $share = PersonalShare::find($pid);
            $poid = $share->property_owner_id;

            $check = CorporateShareBorrower::where([
                ['personal_share_id', $pid],
                ['borrower_id', $bid],
                ['property_type', 'personal'], ['status', '1']
            ])->first();

            if (!$check) {
                $result = CorporateShareBorrower::create([
                    'borrower_id' => $bid,
                    'status' => '1',
                    'personal_share_id' => $share->id,
                    'property_type' => 'personal',
                    'created_by' => Auth::user()->id
                ]);
            }
        }
        if ($status == 'corporate') {
            $share = CorporateShare::find($pid);
            $poid = $share->property_owner_id;

            $check = CorporateShareBorrower::where([
                ['corporate_share_id', $pid],
                ['borrower_id', $bid],
                ['property_type', 'corporate'], ['status', '1']
            ])->first();

            if (!$check) {
                $result = CorporateShareBorrower::create([
                    'borrower_id' => $bid,
                    'status' => '1',
                    'corporate_share_id' => $share->id,
                    'property_type' => 'corporate',
                    'created_by' => Auth::user()->id
                ]);
            }
        }
        if ($result) {
            Session::flash('success', 'Property assigned Successfully');
        } else {
            Session::flash('warning', 'Error! Property already assigned or the property may be inactive. Please Check and Try again.');
        }
        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
    }


    public function share_destroy($bid, $pid, $status)
    {

        $result = false;
        if ($status == 'personal') {
            $share = PersonalShare::find($pid);
            $poid = $share->property_owner_id;
            $pshare = PersonalShareBorrower::where([['personal_share_id', $pid]])->get();
            $cshare = CorporateShareBorrower::where([['personal_share_id', $pid]])->get();
            foreach ($pshare as $p) {
                try {
                    $p->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
            foreach ($cshare as $c) {
                try {
                    $c->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
            try {
                $result = $share->delete();

            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }
        }
        if ($status == 'corporate') {
            $share = CorporateShare::find($pid);
            $poid = $share->property_owner_id;
            $pshare = PersonalShareBorrower::where([['corporate_share_id', $pid]])->get();
            $cshare = CorporateShareBorrower::where([['corporate_share_id', $pid]])->get();
            foreach ($pshare as $p) {
                try {
                    $p->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
            foreach ($cshare as $c) {
                try {
                    $c->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
            try {
                $result = $share->delete();

            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }

        }

        if ($result) {
            Session::flash('success', 'Share Removed Successfully');
        }


        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));


    }


    public function share_borrower_edit($bid, $pid, $stat, $status)
    {
        $stats = $stat;
        if ($stat == 'personal') {
            $share = PersonalShare::find($pid);
        }
        if ($stat == 'corporate') {
            $share = CorporateShare::find($pid);
        }
        $isin=[];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }


        return view('corporate_property.edit_borrower_share', compact('bid','isin', 'status', 'stats', 'pid', 'share'));

    }


    public function share_borrower_destroy($bid, $pid, $stat, $status)
    {

        $result = false;
        if ($stat == 'personal') {
            $share = PersonalShare::find($pid);
            $poid = $share->property_owner_id;
            $pshare = CorporateShareBorrower::where([['personal_share_id', $pid], ['borrower_id', $bid]])->get();
            foreach ($pshare as $p) {
                try {
                    $result = $p->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
        }
        if ($stat == 'corporate') {
            $share = CorporateShare::find($pid);
            $poid = $share->property_owner_id;
            $cshare = CorporateShareBorrower::where([['corporate_share_id', $pid], ['borrower_id', $bid]])->get();
            foreach ($cshare as $c) {
                try {
                    $result = $c->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }

        }

        if ($result) {
            Session::flash('success', 'Share Removed Successfully');
        }


        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));


    }

    public function land_borrower_edit($bid, $pid, $stat, $status)
    {

        $stats = $stat;
        if ($stat == 'personal') {
            $land = PersonalLand::find($pid);
        }
        if ($stat == 'corporate') {
            $land = CorporateLand::find($pid);
        }

        $district = [];
        $dis = District::select('id', 'name')->get()->sortBy('name');

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

        return view('corporate_property.edit_borrower_land', compact('bid', 'status', 'stats', 'pid', 'land', 'localbody', 'district'));

    }


    public function land_borrower_destroy($bid, $pid, $stat, $status)
    {

        $result = false;
        if ($stat == 'personal') {
            $land = PersonalLand::find($pid);
            $poid = $land->property_owner_id;
            $cland = CorporateLandBorrower::where([['personal_land_id', $pid], ['borrower_id', $bid]])->get();
            foreach ($cland as $c) {
                try {
                    $result = $c->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
        }
        if ($stat == 'corporate') {
            $land = CorporateLand::find($pid);
            $poid = $land->property_owner_id;
            $cland = CorporateLandBorrower::where([['corporate_land_id', $pid], ['borrower_id', $bid]])->get();
            foreach ($cland as $c) {
                try {
                    $result = $c->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
        }

        if ($result) {
            Session::flash('success', 'Land Removed Successfully');
        }


        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
    }

    public function land_borrower_update(LandUpdateRequest $request)
    {


        $result = false;
        $bid = $request->input('bid');
        $stats = $request->input('stats');
        $status = $request->input('status');
        $pid = $request->input('id');

        if ($stats == 'personal') {
            $land = PersonalLand::find($pid);
            $poid = $land->property_owner_id;
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
        }

        if ($stats == 'corporate') {
            $land = CorporateLand::find($pid);
            $poid = $land->property_owner_id;
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
        }


        if ($result) {
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property');
            return redirect()->route('corporate_property.land_edit', compact('bid', 'poid', 'status'));
        }
        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
    }

    public function share_borrower_update(ShareUpdateRequest $request)
    {

        $result = false;
        $bid = $request->input('bid');
        $status = $request->input('status');
        $stats = $request->input('stats');
        $pid = $request->input('id');
//dd($request);
        if ($stats == 'personal') {
            $share = PersonalShare::find($pid);
            $poid = $share->property_owner_id;

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
        }

        if ($stats == 'corporate') {
            $share = CorporateShare::find($pid);
            $poid = $share->property_owner_id;

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
        }


        if ($result) {
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property');
            return redirect()->route('corporate_property.share_edit', compact('bid', 'poid', 'status'));
        }
        return redirect()->route('corporate_property.choose', compact('bid', 'poid', 'status'));
    }


    public function vehicle_store(Request $request)
    {
        $result = false;
        $bid = $request->input('bid');

        $count1 = count($request->input('model_number'));

        $count = $count1 - 1;

        for ($i = 0; $i < $count; $i++) {
            $model_number = $request->get('model_number');
            $registration_number = $request->get('registration_number');
            $engine_number = $request->get('engine_number');
            $chassis_number = $request->get('chassis_number');

//     for personal

            if ($model_number[$i] && $registration_number[$i] && $engine_number[$i] && $chassis_number[$i]) {
                $data = ([
                    'borrower_id' => $bid,
                    'model_number' => $model_number[$i],
                    'registration_number' => $registration_number[$i],
                    'engine_number' => $engine_number[$i],
                    'chassis_number' => $chassis_number[$i],
                    'created_by' => Auth::user()->id
                ]);
                $result = CorporateHirePurchase::create($data);

            }
        }

        if ($result) {
            Session::flash('success', 'Vehicle Added and Successfully');
        } else {
            Session::flash('danger', 'Failed to Add Vehicle');
        }
        return redirect()->route('corporate_property.index', compact('bid'));
    }


    public function vehicle_edit($bid, $id)
    {
        $vehicle = CorporateHirePurchase::find($id);
        return view('corporate_property.edit_vehicle', compact('bid', 'vehicle'));
    }


    public function vehicle_update(VehicleUpdateRequest $request, $id)
    {
        $bid = $request->input('bid');
        $id = $request->input('id');
        $vehicle = CorporateHirePurchase::find($id);
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
        return redirect()->route('corporate_property.index', compact('bid'));
    }


    public function vehicle_destroy($bid,$id)
    {

        $vehicle = CorporateHirePurchase::find($id);
        try {
            $result = $vehicle->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($result) {
            Session::flash('success', 'Vehicle Deleted and Successfully');
        }
        return redirect()->route('corporate_property.index', compact('bid'));

    }


    public function share_front_edit($bid, $pid, $status)
    {
        if ($status == 'personal') {
            $share = PersonalShare::find($pid);
        }
        if ($status == 'corporate') {
            $share = CorporateShare::find($pid);
        }
        $isin=[];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }
        return view('corporate_property.edit_sharefront', compact('bid','isin', 'status', 'pid', 'share'));
    }


    public function share_front_destroy($bid, $pid, $status)
    {
        $result = false;
        if ($status == 'personal') {
            $pshare = CorporateShareBorrower::where([['personal_share_id', $pid], ['borrower_id', $bid]])->get();
            foreach ($pshare as $p) {
                try {
                    $result = $p->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
        }
        if ($status == 'corporate') {
            $share = CorporateShare::find($pid);
            $cshare = CorporateShareBorrower::where([['corporate_share_id', $pid], ['borrower_id', $bid]])->get();
            foreach ($cshare as $c) {
                try {
                    $result = $c->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }


        }

        if ($result) {
            Session::flash('success', 'Share Removed Successfully');
        }
        return redirect()->route('corporate_property.index', compact('bid'));


    }

    public function land_front_edit($bid, $pid, $status)
    {

        if ($status == 'personal') {
            $land = PersonalLand::find($pid);
        }
        if ($status == 'corporate') {
            $land = CorporateLand::find($pid);
        }

        $district = [];
        $dis = District::select('id', 'name')->get()->sortBy('name');

        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }

        $local = LocalBodies::select('id', 'name','body_type')->get()->sortBy('name');
        foreach ($local as $l) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$l->id] = $l->name.' '. $l->body_type;
        }



        return view('corporate_property.edit_landfront', compact('bid', 'status', 'pid', 'land', 'localbody', 'district'));

    }


    public function land_front_destroy($bid, $pid, $status)
    {
        $result = false;
        if ($status == 'personal') {
            $pland = CorporateLandBorrower::where([['personal_land_id', $pid], ['borrower_id', $bid]])->get();
            foreach ($pland as $p) {
                try {
                    $result = $p->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
        }
        if ($status == 'corporate') {
            $cland = CorporateLandBorrower::where([['corporate_land_id', $pid], ['borrower_id', $bid]])->get();
            foreach ($cland as $c) {
                try {
                    $result = $c->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
        }

        if ($result) {
            Session::flash('success', 'Land Removed Successfully');
        }
        return redirect()->route('corporate_property.index', compact('bid'));
    }

    public function land_front_update(LandUpdateRequest $request)
    {
        $result = false;
        $bid = $request->input('bid');
        $status = $request->input('status');
        $pid = $request->input('id');

        if ($status == 'personal') {
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
        }

        if ($status == 'corporate') {
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
        }


        if ($result) {
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property, Please Try again.');
        }
        return redirect()->route('corporate_property.index', compact('bid'));
    }

    public function share_front_update(ShareUpdateRequest $request)
    {

        $result = false;
        $bid = $request->input('bid');
        $status = $request->input('status');
        $pid = $request->input('id');
//dd($request);
        if ($status == 'personal') {
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
        }

        if ($status == 'corporate') {
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
        }


        if ($result) {
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property, Please Try again');
        }
        return redirect()->route('corporate_property.index', compact('bid'));
    }
}