<?php

namespace App\Http\Controllers;

use App\AuthorizedPerson;
use App\CorporateBorrower;
use App\CorporateGuarantor;
use App\CorporateGuarantorBorrower;
use App\CorporatePropertyOwner;
use App\Department;
use App\District;
use App\Http\Requests\CorporateCreateRequest;
use App\Http\Requests\CorporateGuarantorCreateRequest;
use App\Http\Requests\PersonalCreateRequest;
use App\Http\Requests\PersonalGuarantorCreateRequest;
use App\LocalBodies;
use App\Ministry;
use App\PersonalBorrower;
use App\PersonalGuarantor;
use App\PersonalPropertyOwner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CorporateGuarantorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function corporate_guarantor_index($bid)
    {
        $personal_borrower = PersonalBorrower::all();
        $personal_guarantor = PersonalGuarantor::all();
        $personal_property_owner = PersonalPropertyOwner::all();
        $corporate_borrower = CorporateBorrower::all();
        $corporate_guarantor = CorporateGuarantor::all();
        $corporate_property_owner = CorporatePropertyOwner::all();
        $authorized_person=AuthorizedPerson::all();
//list of available guarantors for borrower
        $personal_guarantor_list = CorporateGuarantorBorrower::where([
            ['borrower_id', $bid],
            ['guarantor_type', 'personal']
        ])->get();
        $corporate_guarantor_list = CorporateGuarantorBorrower::where([
            ['borrower_id', $bid],
            ['guarantor_type', 'corporate']
        ])->get();
        $district = [];
        $localbody = [];
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }
//        $lo = LocalBodies::select('id', 'name')->get();
//        $localbody = [];
        $local = LocalBodies::select('id', 'name')->get()->sortBy('name');
        foreach ($local as $d) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$d->id] = $d->name;
        }
        return view('corporate_guarantor.index', compact('bid','authorized_person', 'personal_guarantor_list', 'corporate_guarantor_list', 'localbody', 'district', 'personal_borrower', 'personal_guarantor', 'personal_property_owner', 'corporate_borrower', 'corporate_guarantor', 'corporate_property_owner'));
    }

    public function personal_borrower_guarantor_select($bid, $id)
    {
        $borrower = PersonalBorrower::find($id);
//            checking whether it exist or not
        $check = PersonalGuarantor::where([
            ['english_name', $borrower->english_name],
            ['grandfather_name', $borrower->grandfather_name],
            ['father_name', $borrower->father_name,],
            ['citizenship_number', $borrower->citizenship_number]
        ])->first();
//            if doesnot exist
        if (!$check) {
            $data = PersonalGuarantor::create([
                'english_name' => $borrower->english_name,
                'nepali_name' => $borrower->nepali_name,
                'client_id' => $borrower->client_id,
                'gender' => $borrower->gender,
                'grandfather_name' => $borrower->grandfather_name,
                'grandfather_relation' => $borrower->grandfather_relation,
                'father_name' => $borrower->father_name,
                'father_relation' => $borrower->father_relation,
                'spouse_name' => $borrower->spouse_name,
                'phone' => $borrower->phone,
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
            $gid = $data->id;
            $data1 = CorporateGuarantorBorrower::create([
                'borrower_id' => $bid,
                'personal_guarantor_id' => $gid,
                'guarantor_type' => 'personal',
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
            if ($data && $data1) {
                Session::flash('success', 'Guarantor added successfully.');
            } else {
                Session::flash('danger', 'Personal Guarantor Creation Failed, Please Try Again.');
            }
            return redirect()->route('corporate_guarantor.index', compact('bid'));

        } else {

//                if name already exist in guarantor
            $check1 = CorporateGuarantorBorrower::where([
                ['borrower_id', $bid],
                ['personal_guarantor_id', $check->id],
                ['guarantor_type', 'personal'],
            ])->first();

//            if doesnot exist
            if (!$check1) {

                $data2 = CorporateGuarantorBorrower::create([
                    'borrower_id' => $bid,
                    'personal_guarantor_id' => $check->id,
                    'guarantor_type' => 'personal',
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                ]);
                if ($data2) {
                    Session::flash('success', 'Guarantor added successfully.');
                } else {
                    Session::flash('danger', 'Personal Guarantor Creation Failed, Please Try Again.');
                }

                return redirect()->route('corporate_guarantor.index', compact('bid'));


            } else {
                //if exist
                Session::flash('warning', 'Guarantor Already Exist.');
                return redirect()->route('corporate_guarantor.index', compact('bid'));
            }

        }
    }

    public function authorized_person_guarantor_select($bid, $id)
    {
        $authorized_person = AuthorizedPerson::find($id);
//            checking whether it exist or not
        $check = PersonalGuarantor::where([
            ['english_name', $authorized_person->english_name],
            ['grandfather_name', $authorized_person->grandfather_name],
            ['father_name', $authorized_person->father_name,],
            ['citizenship_number', $authorized_person->citizenship_number]
        ])->first();
//            if doesnot exist
        if (!$check) {
            $data = PersonalGuarantor::create([
                'english_name' => $authorized_person->english_name,
                'nepali_name' => $authorized_person->nepali_name,
                'client_id' => $authorized_person->client_id,
                'gender' => $authorized_person->gender,
                'grandfather_name' => $authorized_person->grandfather_name,
                'grandfather_relation' => $authorized_person->grandfather_relation,
                'father_name' => $authorized_person->father_name,
                'father_relation' => $authorized_person->father_relation,
                'spouse_name' => $authorized_person->spouse_name,
                'phone' => $authorized_person->phone,
                'spouse_relation' => $authorized_person->spouse_relation,
                'district_id' => $authorized_person->district_id,
                'local_bodies_id' => $authorized_person->local_bodies_id,
                'wardno' => $authorized_person->wardno,
                'dob_year' => $authorized_person->dob_year,
                'dob_month' => $authorized_person->dob_month,
                'dob_day' => $authorized_person->dob_day,
                'citizenship_number' => $authorized_person->citizenship_number,
                'issued_year' => $authorized_person->issued_year,
                'issued_month' => $authorized_person->issued_month,
                'issued_day' => $authorized_person->issued_day,
                'issued_district' => $authorized_person->issued_district,
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
//                getting inserted id
            $gid = $data->id;
            $data1 = CorporateGuarantorBorrower::create([
                'borrower_id' => $bid,
                'personal_guarantor_id' => $gid,
                'guarantor_type' => 'personal',
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
            if ($data && $data1) {
                Session::flash('success', 'Guarantor added successfully.');
            } else {
                Session::flash('danger', 'Personal Guarantor Creation Failed, Please Try Again.');
            }
            return redirect()->route('corporate_guarantor.index', compact('bid'));

        } else {

//                if name already exist in guarantor
            $check1 = CorporateGuarantorBorrower::where([
                ['borrower_id', $bid],
                ['personal_guarantor_id', $check->id],
                ['guarantor_type', 'personal'],
            ])->first();

//            if doesnot exist
            if (!$check1) {

                $data2 = CorporateGuarantorBorrower::create([
                    'borrower_id' => $bid,
                    'personal_guarantor_id' => $check->id,
                    'guarantor_type' => 'personal',
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                ]);
                if ($data2) {
                    Session::flash('success', 'Guarantor added successfully.');
                } else {
                    Session::flash('danger', 'Personal Guarantor Creation Failed, Please Try Again.');
                }

                return redirect()->route('corporate_guarantor.index', compact('bid'));


            } else {
                //if exist
                Session::flash('warning', 'Guarantor Already Exist.');
                return redirect()->route('corporate_guarantor.index', compact('bid'));
            }

        }
    }



    public function personal_guarantor_select($bid, $id)
    {
//              checking whether it exist or not

        $check = CorporateGuarantorBorrower::where([
            ['borrower_id', $bid],
            ['personal_guarantor_id', $id],
            ['guarantor_type', 'personal'],
        ])->first();

        //            if doesnot exist
        if (!$check) {
            $data = CorporateGuarantorBorrower::create([
                'borrower_id' => $bid,
                'personal_guarantor_id' => $id,
                'guarantor_type' => 'personal',
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
            if ($data) {
                Session::flash('success', 'Guarantor added successfully.');
            } else {
                Session::flash('danger', 'Personal Guarantor Creation Failed, Please Try Again.');
            }
            return redirect()->route('corporate_guarantor.index', compact('bid'));
        } else {
            //if exist
            Session::flash('warning', 'Guarantor Already Exist.');
            return redirect()->route('corporate_guarantor.index', compact('bid'));
        }
    }


    public function personal_property_owner_select($bid, $id)
    {
        $borrower = PersonalPropertyOwner::find($id);
//            checking whether it exist or not
        $check = PersonalGuarantor::where([
            ['english_name', $borrower->english_name],
            ['grandfather_name', $borrower->grandfather_name],
            ['father_name', $borrower->father_name,],
            ['citizenship_number', $borrower->citizenship_number]
        ])->first();
//            if doesnot exist
        if (!$check) {
            $data = PersonalGuarantor::create([
                'english_name' => $borrower->english_name,
                'nepali_name' => $borrower->nepali_name,
                'client_id' => $borrower->client_id,
                'gender' => $borrower->gender,
                'grandfather_name' => $borrower->grandfather_name,
                'grandfather_relation' => $borrower->grandfather_relation,
                'father_name' => $borrower->father_name,
                'father_relation' => $borrower->father_relation,
                'spouse_name' => $borrower->spouse_name,
                'phone' => $borrower->phone,
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
            $gid = $data->id;
            $data1 = CorporateGuarantorBorrower::create([
                'borrower_id' => $bid,
                'personal_guarantor_id' => $gid,
                'guarantor_type' => 'personal',
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
            if ($data && $data1) {
                Session::flash('success', 'Guarantor added successfully.');
            } else {
                Session::flash('danger', 'Personal Guarantor Creation Failed, Please Try Again.');
            }
            return redirect()->route('corporate_guarantor.index', compact('bid'));
        } else {
//                if name already exist in guarantor
            $check1 = CorporateGuarantorBorrower::where([
                ['borrower_id', $bid],
                ['personal_guarantor_id', $check->id],
                ['guarantor_type', 'personal'],
            ])->first();

//            if doesnot exist
            if (!$check1) {

                $data2 = CorporateGuarantorBorrower::create([
                    'borrower_id' => $bid,
                    'personal_guarantor_id' => $check->id,
                    'guarantor_type' => 'personal',
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                ]);
                if ($data2) {
                    Session::flash('success', 'Guarantor added successfully.');
                } else {
                    Session::flash('danger', 'Personal Guarantor Creation Failed, Please Try Again.');
                }

                return redirect()->route('corporate_guarantor.index', compact('bid'));


            } else {
                //if exist
                Session::flash('warning', 'Personal Guarantor Already Exist.');
                return redirect()->route('corporate_guarantor.index', compact('bid'));
            }

        }

    }


    public function corporate_borrower_guarantor_select($bid, $id)
    {

        if ($bid == $id) {
            Session::flash('warning', 'Borrower Cant Be Own Guarantor, Please Try Again.');
            return redirect()->route('corporate_guarantor.index', compact('bid'));
        } else {
            $borrower = CorporateBorrower::find($id);
//            checking whether it exist or not
            $check = CorporateGuarantor::where([
                ['english_name', $borrower->english_name],
                ['registration_number', $borrower->registration_number]
            ])->first();
//            if doesnot exist
            if (!$check) {
                $data = CorporateGuarantor::create([
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
                $gid = $data->id;
                $data1 = CorporateGuarantorBorrower::create([
                    'borrower_id' => $bid,
                    'corporate_guarantor_id' => $gid,
                    'guarantor_type' => 'corporate',
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                ]);
                if ($data && $data1) {
                    Session::flash('success', 'Guarantor added successfully.');
                } else {
                    Session::flash('danger', 'Guarantor Creation Failed, Please Try Again.');
                }
                return redirect()->route('corporate_guarantor.index', compact('bid'));
            } else {
//                if name already exist in guarantor
                $check1 = CorporateGuarantorBorrower::where([
                    ['borrower_id', $bid],
                    ['corporate_guarantor_id', $check->id],
                    ['guarantor_type', 'corporate'],
                ])->first();

//            if doesnot exist
                if (!$check1) {

                    $data2 = CorporateGuarantorBorrower::create([
                        'borrower_id' => $bid,
                        'corporate_guarantor_id' => $check->id,
                        'guarantor_type' => 'corporate',
                        'status' => '1',
                        'created_by' => Auth::user()->id,
                    ]);
                    if ($data2) {
                        Session::flash('success', 'Guarantor added successfully.');
                    } else {
                        Session::flash('danger', 'Guarantor Creation Failed, Please Try Again.');
                    }

                    return redirect()->route('corporate_guarantor.index', compact('bid'));


                } else {
                    //if exist
                    Session::flash('warning', 'Guarantor Already Exist.');
                    return redirect()->route('corporate_guarantor.index', compact('bid'));
                }

            }

        }
    }

    public function corporate_guarantor_select($bid, $id)
    {
        $borrower_check = CorporateBorrower::find($bid);
        $guarantor_check = CorporateGuarantor::find($id);
//            checking whether it exist or not
        if ($guarantor_check->registration_number == $borrower_check->registration_number) {

            Session::flash('warning', 'Borrower cant be Own Guarantor');
            return redirect()->route('corporate_guarantor.index', compact('bid'));
        } else {
            $check = CorporateGuarantorBorrower::where([
                ['borrower_id', $bid],
                ['corporate_guarantor_id', $id],
                ['guarantor_type', 'corporate'],
            ])->first();
//            if doesnot exist
            if (!$check) {
                $data = CorporateGuarantorBorrower::create([
                    'borrower_id' => $bid,
                    'corporate_guarantor_id' => $id,
                    'guarantor_type' => 'corporate',
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                ]);
                if ($data) {
                    Session::flash('success', 'Guarantor added successfully.');
                } else {
                    Session::flash('danger', 'Guarantor Creation Failed, Please Try Again.');
                }
                return redirect()->route('corporate_guarantor.index', compact('bid'));
            } else {
                //if exist
                Session::flash('warning', 'Guarantor Already Exist.');
                return redirect()->route('corporate_guarantor.index', compact('bid'));
            }
        }
    }

    public function corporate_property_owner_select($bid, $id)
    {
        $borrower_check = CorporateBorrower::find($bid);
        $property_owner_check = CorporateGuarantor::find($id);

        if ($property_owner_check->registration_number == $borrower_check->registration_number) {
            Session::flash('warning', 'Borrower cant be Own Guarantor');
            return redirect()->route('corporate_guarantor.index', compact('bid'));
        } else {

            $property_owner = CorporatePropertyOwner::find($id);
//            checking whether it exist or not
            $check = CorporateGuarantor::where([
                ['english_name', $property_owner->english_name],
                ['registration_number', $property_owner->registration_number]
            ])->first();
//            if doesnot exist
            if (!$check) {
                $data = CorporateGuarantor::create([
                    'english_name' => $property_owner->english_name,
                    'nepali_name' => $property_owner->nepali_name,
                    'client_id' => $property_owner->client_id,
                    'district_id' => $property_owner->district_id,
                    'local_bodies_id' => $property_owner->local_bodies_id,
                    'wardno' => $property_owner->wardno,
                    'phone' => $property_owner->phone,
                    'reg_year' => $property_owner->reg_year,
                    'reg_month' => $property_owner->reg_month,
                    'reg_day' => $property_owner->reg_day,
                    'ministry_id' => $property_owner->ministry_id,
                    'department_id' => $property_owner->department_id,
                    'registration_number' => $property_owner->registration_number,
                    'authorized_person_id' => $property_owner->authorized_person_id,
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                ]);
//                getting inserted id
                $gid = $data->id;
                $data1 = CorporateGuarantorBorrower::create([
                    'borrower_id' => $bid,
                    'corporate_guarantor_id' => $gid,
                    'guarantor_type' => 'corporate',
                    'status' => '1',
                    'created_by' => Auth::user()->id,
                ]);
                if ($data && $data1) {
                    Session::flash('success', 'Guarantor added successfully.');
                } else {
                    Session::flash('danger', 'Guarantor Creation Failed, Please Try Again.');
                }
                return redirect()->route('corporate_guarantor.index', compact('bid'));
            } else {
//                if name already exist in guarantor
                $check1 = CorporateGuarantorBorrower::where([
                    ['borrower_id', $bid],
                    ['corporate_guarantor_id', $check->id],
                    ['guarantor_type', 'corporate'],
                ])->first();

//            if doesnot exist
                if (!$check1) {

                    $data2 = CorporateGuarantorBorrower::create([
                        'borrower_id' => $bid,
                        'corporate_guarantor_id' => $check->id,
                        'guarantor_type' => 'corporate',
                        'status' => '1',
                        'created_by' => Auth::user()->id,
                    ]);
                    if ($data2) {
                        Session::flash('success', 'Guarantor added successfully.');
                    } else {
                        Session::flash('danger', 'Guarantor Creation Failed, Please Try Again.');
                    }

                    return redirect()->route('corporate_guarantor.index', compact('bid'));


                } else {
                    //if exist
                    Session::flash('warning', 'Guarantor Already Exist.');
                    return redirect()->route('corporate_guarantor.index', compact('bid'));
                }

            }

        }
    }

    public function guarantor_destroy($bid, $gid)
    {
        $guarantor = CorporateGuarantorBorrower::find($gid);
        $status = null;
        try {
            $status = $guarantor->delete();
        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($status) {
            Session::flash('success', 'Data Deleted Successfully');
        }
        return redirect()->route('corporate_guarantor.index', compact('bid'));
    }

    public function guarantor_edit($bid, $gid)
    {
        $guarantor = CorporateGuarantorBorrower::find($gid);
        if ($guarantor->status == 1) {
            $guarantor->status = '0';
            $status = null;
            try {
                $status = $guarantor->update();

            } catch (\Exception $e) {

            }
        } else {
            $guarantor->status = '1';
            $status = null;
            try {
                $status = $guarantor->update();

            } catch (\Exception $e) {

            }
        }

        if ($status) {
            Session::flash('success', 'Status Changed Successfully');
        } else {

            Session::flash('danger', 'Failed Change Status, Please Try again.');
        }
        return redirect()->route('corporate_guarantor.index', compact('bid'));

    }

    public function proceed($bid)
    {
        Session::flash('success', 'Please Choose property owner and property.');
        return redirect()->route('corporate_property.index', compact('bid'));
    }

    public function personal_guarantor_create($bid)
    {
//district
        $district = [];
        $localbody = [];
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }
//        local body
        $local = LocalBodies::select('id', 'name')->get()->sortBy('name');
        foreach ($local as $d) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$d->id] = $d->name;
        }
        return view('corporate_guarantor.personal_create', compact('district', 'localbody', 'bid'));
    }

    public function personal_guarantor_store(PersonalGuarantorCreateRequest $request, $bid)
    {
// stores personal borrower
        $data = PersonalGuarantor::create([
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
            'dob_year' => $request->input('dob_year'),
            'phone' => $request->input('phone'),
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
        $data2 = CorporateGuarantorBorrower::create([
            'borrower_id' => $bid,
            'personal_guarantor_id' => $data->id,
            'guarantor_type' => 'personal',
            'status' => '1',
            'created_by' => Auth::user()->id,
        ]);

        if ($data && $data2) {
            Session::flash('success', 'Guarantor Created Successfully');
        } else {
            Session::flash('danger', 'Guarantor Creation Failed, Please Try Again.');
            return redirect()->route('personal_guarantor.personal_create', compact('bid'));
        }
        return redirect()->route('corporate_guarantor.index', compact('bid'));

    }


    public
    function corporate_guarantor_create($bid)
    {

        $district = [];
        $corporate_borrower = CorporateBorrower::all();
        $dis = District::select('id', 'name')->get();
        foreach ($dis as $d) {
            $district[''] = 'lhNnf';
            $district[$d->id] = $d->name;
        }
        $localbody = [];
        $local = LocalBodies::select('id', 'name')->get()->sortBy('name');
        foreach ($local as $d) {
            $localbody[''] = ':yflgo lgsfo';
            $localbody[$d->id] = $d->name;
        }
        $ministry = [];
        $department = [];
        $min = Ministry::select('id', 'name')->get();
        foreach ($min as $m) {
            $ministry[$m->id] = $m->name;
        }
        $dep = Department::select('id', 'name')->get();
        foreach ($dep as $d) {
            $department[$d->id] = $d->name;
        }
        return view('corporate_guarantor.corporate_create', compact('corporate_borrower', 'district', 'localbody', 'ministry', 'department', 'bid'));
    }


    public function corporate_guarantor_store(CorporateGuarantorCreateRequest $request, $bid)
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

                $data1 = CorporateGuarantor::create([
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
                    'authorized_person_id' => $authorized_person_id,
                ]);
                if ($data1) {
                    $data2 = CorporateGuarantorBorrower::create([
                        'borrower_id' => $bid,
                        'corporate_guarantor_id' => $data1->id,
                        'guarantor_type' => 'corporate',
                        'status' => '1',
                        'created_by' => Auth::user()->id,
                    ]);
                    if ($data2) {
                        Session::flash('success', 'Guarantor Created successfully.');
                        return redirect()->route('corporate_guarantor.index', compact('bid'));
                    } else {
                        Session::flash('danger', 'Error in creating Borrower, Please Try again.');
                        return redirect()->route('corporate_guarantor.corporate_create');

                    }

                } else {
                    Session::flash('danger', 'Error in creating Guarantor, Please Try again.');
                    return redirect()->route('corporate_guarantor.corporate_create');
                }

            } else {
                Session::flash('danger', 'Error in creating authorized person, Please Try again.');
                return redirect()->route('corporate_guarantor.corporate_create');
            }
        } else {

            $data2 = CorporateGuarantor::create([
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
            $data3 = CorporateGuarantorBorrower::create([
                'borrower_id' => $bid,
                'corporate_guarantor_id' => $data2->id,
                'guarantor_type' => 'corporate',
                'status' => '1',
                'created_by' => Auth::user()->id,
            ]);
            if ($data3) {
                Session::flash('success', 'Guarantor Created successfully.');
                return redirect()->route('corporate_guarantor.index', compact('bid'));
            } else {
                Session::flash('danger', 'Error in creating Borrower, Please Try again.');
                return redirect()->route('corporate_guarantor.corporate_create');

            }
        } else {
            Session::flash('danger', 'Error in creating Borrower, Please Try again.');
            return redirect()->route('corporate_guarantor.index');
        }

    }
}