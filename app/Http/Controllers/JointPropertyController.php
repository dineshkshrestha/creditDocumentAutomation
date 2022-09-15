<?php

namespace App\Http\Controllers;

use App\AuthorizedPerson;
use App\CorporateBorrower;
use App\CorporateGuarantor;
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
use App\JointHirePurchase;
use App\JointLandBorrower;
use App\JointShareBorrower;
use App\LocalBodies;
use App\Ministry;
use App\PersonalBorrower;
use App\PersonalGuarantor;
use App\JointGuarantorBorrower;
use App\PersonalHirePurchase;
use App\PersonalLand;
use App\PersonalLandBorrower;
use App\PersonalPropertyOwner;
use App\PersonalShare;
use App\PersonalShareBorrower;
use App\RegisteredCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class JointPropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index($bid)
    {
        $joint_property_owner = PersonalPropertyOwner::all();
        $personal_guarantor_list = JointGuarantorBorrower::where([
            ['borrower_id', $bid],
        ])->get();
        $vehicle = JointHirePurchase::where([
            ['borrower_id', $bid]
        ])->get();
        $pland = [];
        $pshare = [];
        $pland_id = JointLandBorrower::where([
            ['borrower_id', $bid],
        ])->get();
        foreach ($pland_id as $l) {
            $pland[] = PersonalLand::find($l->personal_land_id);
        }
        $pshare_id = JointShareBorrower::where([
            ['borrower_id', $bid]])->get();

        foreach ($pshare_id as $s) {
            $pshare[] = PersonalShare::find($s->personal_share_id);
        }

        return view('joint_property.index', compact('pland', 'pshare', 'bid', 'vehicle', 'personal_guarantor_list', 'joint_property_owner'));
    }

    public function copy_index($bid)
    {
        $personal_borrower = PersonalBorrower::all();
        $personal_guarantor = PersonalGuarantor::all();
        return view('joint_property.copy_index', compact('bid', 'personal_borrower', 'personal_guarantor'));
    }


    public function joint_property_create($bid)
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

        return view('joint_property.personal_create', compact('district', 'localbody', 'bid'));
    }

    public function joint_property_store(PersonalPropertyOwnerCreateRequest $request, $bid)
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
            Session::flash('success', 'Property Owner Created Successfully, Now Create property');
        } else {
            Session::flash('danger', 'Property owner Creation Failed, Please Try Again.');
            return redirect()->route('joint_property.personal_create', compact('bid'));
        }

        return redirect()->route('joint_property.choose', compact('bid', 'poid'));

    }

    public function joint_borrower_property_select($bid, $id)
    {
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
                'district_id' => $borrower->district_id,
                'local_bodies_id' => $borrower->local_bodies_id,
                'wardno' => $borrower->wardno,
                'phone' => $borrower->phone,
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
                return redirect()->route('joint_property.choose', compact('bid', 'poid'));
            } else {
                Session::flash('danger', 'Property owner Creation Failed, Please Try Again.');
                return redirect()->route('joint_property.index', compact('bid'));
            }

        } else {
//                if name already exist in Property owner
            $poid = $check->id;
            Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
            return redirect()->route('joint_property.choose', compact('bid', 'poid'));
        }


    }

    public function joint_guarantor_property_select($bid, $id)
    {

        $borrower = PersonalGuarantor::find($id);
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
                'district_id' => $borrower->district_id,
                'local_bodies_id' => $borrower->local_bodies_id,
                'wardno' => $borrower->wardno,
                'phone' => $borrower->phone,
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
                return redirect()->route('joint_property.choose', compact('bid', 'poid'));
            } else {
                Session::flash('danger', 'Property owner Creation Failed, Please Try Again.');
                return redirect()->route('joint_property.index', compact('bid'));
            }

        } else {
//                if name already exist in Property owner
            $poid = $check->id;
            Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
            return redirect()->route('joint_property.choose', compact('bid', 'poid'));
        }

    }

    public function joint_property_select($bid, $id)
    {
        $status = 'personal';
        $poid = $id;
        Session::flash('success', 'Property owner Selected Successfully. Now choose or create new Property');
        return redirect()->route('joint_property.choose', compact('bid', 'poid'));
    }


    public function choose_property($bid, $poid)
    {
        $pland = [];
        $pshare = [];
//          land property assigned for the borrrower
        $pland_id = JointLandBorrower::where([
            ['borrower_id', $bid],

        ])->get();
        foreach ($pland_id as $l) {
            $pland[] = PersonalLand::find($l->personal_land_id);
        }
        //        share Properties assigned for the borrower
        $pshare_id = JointShareBorrower::where([
            ['borrower_id', $bid]])->get();
        foreach ($pshare_id as $s) {
            $pshare[] = PersonalShare::find($s->personal_share_id);
        }


//all the properties of the property owner
        $property_owner = PersonalPropertyOwner::find($poid);
        $land_property = PersonalLand::where([
            ['property_owner_id', $poid],
        ])->get();
        $share_property = PersonalShare::where([
            ['property_owner_id', $poid]
        ])->get();


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

        $isin = [];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }
        return view('joint_property.choose', compact('bid', 'isin', 'property_owner', 'share_property', 'land_property', 'district', 'localbody', 'poid', 'pland', 'pshare'));
    }


    public function proceed($bid)
    {
        Session::flash('success', 'Please Add Banking Facilities.');
        return redirect()->route('joint_facilities.index', compact('bid'));
    }


    public function land_store(Request $request)
    {
        
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
                        'created_by' => Auth::user()->id
                    ]);
                    $result1 = JointLandBorrower::create($property);
                }
            }

        }

        if ($result1) {
            Session::flash('success', 'Property Added and assigned Successfully');
        } else {
            Session::flash('danger', 'Failed to Add and assign Property');
        }
        return redirect()->route('joint_property.choose', compact('bid', 'poid'));
    }


    public function land_edit($bid, $pid)
    {
        $land = PersonalLand::find($pid);

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



        return view('joint_property.edit_land', compact('bid', 'pid', 'land', 'localbody', 'district'));

    }

    public function land_update(LandUpdateRequest $request)
    {

        $bid = $request->input('bid');
        $pid = $request->input('id');

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


        if ($result) {
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property');
            return redirect()->route('joint_property.land_edit', compact('bid', 'poid'));
        }
        return redirect()->route('joint_property.choose', compact('bid', 'poid'));
    }


    public function land_assign($bid, $pid)
    {

        $result = false;


        $land = PersonalLand::find($pid);
        $poid = $land->property_owner_id;

        $check = JointLandBorrower::where([
            ['personal_land_id', $pid],
            ['borrower_id', $bid],
            ['status', '1']
        ])->first();

        if (!$check) {
            $result = JointLandBorrower::create([
                'borrower_id' => $bid,
                'status' => '1',
                'personal_land_id' => $land->id,
                'created_by' => Auth::user()->id
            ]);
        }


        if ($result) {
            Session::flash('success', 'Property assigned Successfully');
        } else {
            Session::flash('warning', 'Error! Property already assigned or the property may be inactive. Please Check and Try again');
        }
        return redirect()->route('joint_property.choose', compact('bid', 'poid'));
    }


    public function land_destroy($bid, $pid)
    {

        $result = false;
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


        if ($result) {
            Session::flash('success', 'Land Removed Successfully');
        }


        return redirect()->route('joint_property.choose', compact('bid', 'poid'));


    }


    public function land_borrower_edit($bid, $pid)
    {
        $land = PersonalLand::find($pid);
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



        return view('joint_property.edit_borrower_land', compact('bid', 'pid', 'land', 'localbody', 'district'));

    }


    public function land_borrower_destroy($bid, $pid)
    {

        $result = false;
        $land = PersonalLand::find($pid);
        $poid = $land->property_owner_id;
        $pland = JointLandBorrower::where([['personal_land_id', $pid], ['borrower_id', $bid]])->get();
        foreach ($pland as $p) {
            try {
                $result = $p->delete();

            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }
        }


        if ($result) {
            Session::flash('success', 'Land Removed Successfully');
        } else {

            Session::flash('danger', 'Failed to Remove land.');
        }


        return redirect()->route('joint_property.choose', compact('bid', 'poid'));
    }

    public function land_borrower_update(LandUpdateRequest $request)
    {


        $result = false;
        $bid = $request->input('bid');
        $pid = $request->input('id');

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


        if ($result) {
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property');
            return redirect()->route('joint_property.land_edit', compact('bid', 'poid'));
        }
        return redirect()->route('joint_property.choose', compact('bid', 'poid'));
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
                $result = JointHirePurchase::create($data);

            }
        }

        if ($result) {
            Session::flash('success', 'Vehicle Added and Successfully');
        } else {
            Session::flash('danger', 'Failed to Add Vehicle');
        }
        return redirect()->route('joint_property.index', compact('bid'));
    }


    public function vehicle_edit($bid, $id)
    {
        $vehicle = JointHirePurchase::find($id);
        return view('joint_property.edit_vehicle', compact('bid', 'vehicle'));
    }


    public function vehicle_update(VehicleUpdateRequest $request, $id)
    {
        $bid = $request->input('bid');
        $id = $request->input('id');
        $vehicle = JointHirePurchase::find($id);
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
        return redirect()->route('joint_property.index', compact('bid'));
    }


    public function vehicle_destroy($bid, $id)
    {
        $vehicle = JointHirePurchase::find($id);
        $result=null;
        try {
            $result = $vehicle->delete();

        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($result) {
            Session::flash('success', 'Vehicle Deleted and Successfully');
        } else {
            Session::flash('danger', 'Failed to Delete Vehicle');
        }
        return redirect()->route('joint_property.index', compact('bid'));

    }

    public function land_front_edit($bid, $pid)
    {

        $land = PersonalLand::find($pid);

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



        return view('joint_property.edit_landfront', compact('bid', 'pid', 'land', 'localbody', 'district'));

    }


    public function land_front_destroy($bid, $pid)
    {
        $result = false;
        $pland = JointLandBorrower::where([['personal_land_id', $pid], ['borrower_id', $bid]])->get();
        foreach ($pland as $p) {
            try {
                $result = $p->delete();

            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }
        }

        if ($result) {
            Session::flash('success', 'Land Removed Successfully');
        }
        return redirect()->route('joint_property.index', compact('bid'));
    }

    public function land_front_update(LandUpdateRequest $request)
    {
        $result = false;
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
        try {
            $result = null;
            try {
                $result = $land->update();

            } catch (\Exception $e) {

            }

        } catch (\Exception $e) {
        }

        if ($result) {
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property, Please Try again.');
        }
        return redirect()->route('joint_property.index', compact('bid'));
    }


    public function share_store(Request $request)
    {
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
                        'created_by' => Auth::user()->id
                    ]);
                    $result1 = JointShareBorrower::create($property);
                }
            }
        }


        if ($result1) {
            Session::flash('success', 'Property Added and assigned Successfully');
        } else {
            Session::flash('danger', 'Failed to Add and assign Property');
        }


        return redirect()->route('joint_property.choose', compact('bid', 'poid'));
    }

    public function share_edit($bid, $pid)
    {

        $share = PersonalShare::find($pid);
        $isin = [];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }
        return view('joint_property.edit_share', compact('bid', 'isin', 'pid', 'share'));

    }

    public function share_update(ShareUpdateRequest $request)
    {
        $result = false;
        $bid = $request->input('bid');
        $pid = $request->input('id');
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

        if ($result) {
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property');
            return redirect()->route('joint_property.share_edit', compact('bid', 'poid'));
        }
        return redirect()->route('joint_property.choose', compact('bid', 'poid'));
    }


    public function share_assign($bid, $pid)
    {

        $result = false;

        $share = PersonalShare::find($pid);
        $poid = $share->property_owner_id;

        $check = JointShareBorrower::where([
            ['personal_share_id', $pid],
            ['borrower_id', $bid],
            ['status', '1']
        ])->first();

        if (!$check) {
            $result = JointShareBorrower::create([
                'borrower_id' => $bid,
                'status' => '1',
                'personal_share_id' => $share->id,
                'created_by' => Auth::user()->id
            ]);
        }
        if ($result) {
            Session::flash('success', 'Property assigned Successfully');
        } else {
            Session::flash('warning', 'Error! Property already assigned or the property may be inactive. Please Check and Try again.');
        }
        return redirect()->route('joint_property.choose', compact('bid', 'poid'));
    }


    public
    function share_destroy($bid, $pid)
    {
        $result = false;
        $share = PersonalShare::find($pid);
        $poid = $share->property_owner_id;
        $pshare = PersonalShareBorrower::where([['personal_share_id', $pid]])->get();
        $cshare = CorporateShareBorrower::where([['personal_share_id', $pid]])->get();
        $jshare = JointShareBorrower::where([['personal_share_id', $pid]])->get();
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
        foreach ($jshare as $c) {
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
        if ($result) {
            Session::flash('success', 'Share Removed Successfully');
        }
        return redirect()->route('joint_property.choose', compact('bid', 'poid'));


    }


    public
    function share_borrower_edit($bid, $pid)
    {
            $share = PersonalShare::find($pid);
        $isin = [];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }

        return view('joint_property.edit_borrower_share', compact('bid', 'isin','pid', 'share'));

    }


    public
    function share_borrower_destroy($bid, $pid)
    {

        $result = false;

            $share = PersonalShare::find($pid);
            $poid = $share->property_owner_id;
            $pshare = JointShareBorrower::where([['personal_share_id', $pid], ['borrower_id', $bid]])->get();
            foreach ($pshare as $p) {
                try {
                    $result = $p->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }



        if ($result) {
            Session::flash('success', 'Share Removed Successfully');
        }
        return redirect()->route('joint_property.choose', compact('bid', 'poid'));


    }

    public
    function share_borrower_update(ShareUpdateRequest $request)
    {

        $result = false;
        $bid = $request->input('bid');
        $pid = $request->input('id');


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

        if ($result) {
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property');
            return redirect()->route('joint_property.share_edit', compact('bid', 'poid'));
        }
        return redirect()->route('joint_property.choose', compact('bid', 'poid'));
    }


    public function share_front_edit($bid, $pid)
    {
            $share = PersonalShare::find($pid);
        $isin=[];
        $registered_company = RegisteredCompany::select('id', 'isin')->get();
        foreach ($registered_company as $rc) {
            $isin[$rc->id] = $rc->isin;
        }
        return view('joint_property.edit_sharefront', compact('bid','isin', 'pid', 'share'));
    }
    public function share_front_update(ShareUpdateRequest $request)
    {

        $result = false;
        $bid = $request->input('bid');
        $pid = $request->input('id');
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
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property, Please Try again');
        }
        return redirect()->route('joint_property.index', compact('bid'));
    }

    public function share_front_destroy($bid, $pid)
    {
        $result = false;
            $pshare =JointShareBorrower::where([['personal_share_id', $pid], ['borrower_id', $bid]])->get();
            foreach ($pshare as $p) {
                try {
                    $result = $p->delete();

                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }


        if ($result) {
            Session::flash('success', 'Share Removed Successfully');
        } else {

            Session::flash('danger', 'Failed to Remove Share.');
        }
        return redirect()->route('joint_property.index', compact('bid'));
    }
}






