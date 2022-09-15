<?php

namespace App\Http\Controllers;

use App\District;
use App\Http\Requests\JointLandUpdateRequest;
use App\Http\Requests\LandUpdateRequest;
use App\Http\Requests\PersonalPropertyOwnerCreateRequest;
use App\JointLand;
use App\JointPropertyOwner;
use App\LocalBodies;
use App\PersonalBorrower;
use App\PersonalPropertyOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CorporateJointPropertyController extends Controller
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
    public function index($bid)
    {
        $jid = null;
        $joint1 = null;
        $joint2 = null;
        $joint3 = null;
        $land = null;
        $joint = JointPropertyOwner::where([
            ['corporate_borrower_id', $bid]
        ])->first();
        $property_owner = PersonalPropertyOwner::all();
//    `   district
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

        if ($joint) {
            $jid = $joint->id;
            $joint1 = $joint->joint1;
            $joint2 = $joint->joint2;
            $joint3 = $joint->joint3;
            $land = JointPropertyOwner::find($jid)->joint_land;
            if ($joint1 && $joint2 && $joint3) {
            } elseif ($joint1 && $joint2) {

            } elseif ($joint1 && $joint3) {

            } elseif ($joint3 && $joint2) {

            } else {
                Session::flash('warning', 'Please Add More joint property owners.');
                return view('corporate_joint_property.create_joint', compact('bid', 'district', 'localbody', 'property_owner', 'jid', 'joint'));
            }


        } else {
            Session::flash('warning', 'Please Add More joint property owners.');
            return view('corporate_joint_property.create_joint', compact('bid', 'district', 'localbody', 'property_owner', 'jid'));
        }


        return view('corporate_joint_property.index', compact('bid', 'district', 'localbody', 'jid', 'joint1', 'joint2', 'joint3', 'land', 'property_owner'));
    }


    public function newstore(PersonalPropertyOwnerCreateRequest $request)
    {

        $bid = $request->input('bid');
        $jid = $request->input('jid');

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
            if ($jid) {
                $j = JointPropertyOwner::find($jid);
                if ($j->joint1 == null) {
                    $j->joint1 = $data->id;
                    $results = null;
                    try {
                        $results = $j->update();

                    } catch (\Exception $e) {

                    }
                    if ($results) {
                        Session::flash('success', 'Property owner Added successfully');
                    } else {
                        Session::flash('success', 'Failed to Add property Owner, Please Try again.');
                    }


                } elseif ($j->joint2 == null) {
                    $j->joint2 = $data->id;
                    $results = null;
                    try {
                        $results = $j->update();

                    } catch (\Exception $e) {

                    }
                    if ($results) {
                        Session::flash('success', 'Property owner Added successfully');
                    } else {
                        Session::flash('success', 'Failed to Add property Owner, Please Try again.');
                    }

                } elseif ($j->joint3 == null) {
                    $j->joint3 = $data->id;
                    $results = null;
                    try {
                        $results = $j->update();

                    } catch (\Exception $e) {

                    }
                    if ($results) {
                        Session::flash('success', 'Property owner Added successfully');
                    } else {
                        Session::flash('success', 'Failed to Add property Owner, Please Try again.');
                    }

                } else {
                    Session::flash('danger', 'Joint Borrower Already Full, Please Delete Existing and Try again. You can create only 3 joint borrowers');
                }

            } else {
                $result = JointPropertyOwner::create([
                    'joint1' => $data->id,
                    'created_by' => Auth::user()->id,
                    'corporate_borrower_id' => $bid,
                ]);
                if ($result) {
                    Session::flash('success', 'Property owner created Successfully.');
                } else {
                    Session::flash('danger', 'Property owner Creation Failed, Please Try Again.');
                }
            }

        } else {
            Session::flash('danger', 'Property owner Creation Failed, Please Try Again.');
        }
        return redirect()->route('corporate_property_join.index', compact('bid'));
    }


    public function select($bid, $poid)
    {
        $jid=null;
        $alljoin = JointPropertyOwner::where([
            ['corporate_borrower_id', $bid]
        ])->first();
        if($alljoin) {
            $jid = $alljoin->id;
        }

            if ($jid) {
                $j = JointPropertyOwner::find($jid);

                if ($j->joint1 == $poid) {
                    Session::flash('danger', 'Joint Borrower Already Assinged.');
                } elseif ($j->joint2 == $poid) {
                    Session::flash('danger', 'Joint Borrower Already Assinged.');
                } elseif ($j->joint3 == $poid) {
                    Session::flash('danger', 'Joint Borrower Already Assinged.');
                } else {
                    if ($j->joint1 == null) {
                        $j->joint1 = $poid;
                        $results = null;
                        try {
                            $results = $j->update();

                        } catch (\Exception $e) {

                        }
                        if ($results) {
                            Session::flash('success', 'Property owner Added successfully');
                        } else {
                            Session::flash('success', 'Failed to Add property Owner, Please Try again.');
                        }

                    } elseif ($j->joint2 == null) {
                        $j->joint2 = $poid;
                        $results = null;
                        try {
                            $results = $j->update();

                        } catch (\Exception $e) {

                        }
                        if ($results) {
                            Session::flash('success', 'Property owner Added successfully');
                        } else {
                            Session::flash('success', 'Failed to Add property Owner, Please Try again.');
                        }


                    } elseif ($j->joint3 == null) {
                        $j->joint3 = $poid;
                        $results = null;
                        try {
                            $results = $j->update();

                        } catch (\Exception $e) {

                        }

                        if ($results) {
                            Session::flash('success', 'Property owner Added successfully');
                        } else {
                            Session::flash('success', 'Failed to adding property Owner, Please Try again.');
                        }

                    } else {
                        Session::flash('danger', 'Joint Borrower Already Full, Please Delete Existing and Try again. You can create only 3 joint borrowers');
                    }
                }

            } else {
                $result = JointPropertyOwner::create([
                    'joint1' => $poid,
                    'created_by' => Auth::user()->id,
                    'corporate_borrower_id' => $bid,
                ]);
                if ($result) {
                    Session::flash('success', 'Property owner Created successfully');
                } else {
                    Session::flash('danger', 'Failed to create property Owner, Please Try again.');
                }
            }


        return redirect()->route('corporate_property_join.index', compact('bid'));


    }


    public function remove($bid, $jid, $poid)
    {
        $j = JointPropertyOwner::find($jid);
        if ($j->joint1 == $poid) {
            $j->joint1 = null;
            $results = null;
            try {
                $results = $j->update();

            } catch (\Exception $e) {

            }

            if ($results) {
                Session::flash('success', 'Property owner Removed successfully');
            } else {
                Session::flash('success', 'Failed to Remove property Owner, Please Try again.');
            }


        } elseif ($j->joint2 == $poid) {
            $j->joint2 = null;
            $results = null;
            try {
                $results = $j->update();

            } catch (\Exception $e) {

            }


            if ($results) {
                Session::flash('success', 'Property owner Removed successfully');
            } else {
                Session::flash('success', 'Failed to Remove property Owner, Please Try again.');
            }


        } elseif ($j->joint3 == $poid) {
            $j->joint3 = null;
            $results = null;
            try {
                $results = $j->update();

            } catch (\Exception $e) {

            }


            if ($results) {
                Session::flash('success', 'Property owner Removed successfully');
            } else {
                Session::flash('success', 'Failed to Remove property Owner, Please Try again.');
            }

        } else {
            Session::flash('danger', 'Failed to remove property Owner, Please Try again.');
        }
        if ($j->joint1 == null && $j->joint2 == null && $j->joint3 == null) {

            $land = JointPropertyOwner::find($jid);

            $status = null;
            try {
                $status = $land->joint_land()->delete();
            } catch (\Exception $e) {
                Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
            }
            if ($status) {
                try {
                     $land->delete();
                } catch (\Exception $e) {
                    Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
                }
            }
        }
        return redirect()->route('corporate_property_join.owner_create', compact('bid'));

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function owner_create($bid)
    {
        $jid = null;
        $joint1 = null;
        $joint2 = null;
        $joint3 = null;
        $land = null;
        $joint = JointPropertyOwner::where([
            ['corporate_borrower_id', $bid]
        ])->first();
        $property_owner = PersonalPropertyOwner::all();
//    `   district
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

        if ($joint) {
            $jid = $joint->id;
        }
        return view('corporate_joint_property.create_joint', compact('bid', 'district', 'localbody', 'property_owner', 'jid', 'joint'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function land_store(Request $request)
    {
        $jid = $request->input('jid');
        $bid = $request->input('bid');
        $result = false;
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


            if ($district_id[$i] && $local_body_id[$i] && $wardno[$i] && $kitta_no[$i] && $area[$i] && $malpot[$i]) {
                $data = ([
                    'property_owner_id' => $request->input('poid'),
                    'joint_id' => $jid,
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
                $result = JointLand::create($data);
            }
        }


        if ($result) {
            Session::flash('success', 'Property Added  Successfully');
        } else {
            Session::flash('danger', 'Failed to Add  Property');
        }
        return redirect()->route('corporate_property_join.index', compact('bid'));
    }


    public function land_edit($bid, $pid)
    {
        $land = JointLand::find($pid);

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



        return view('corporate_joint_property.edit_land', compact('bid', 'land', 'localbody', 'district'));

    }

    public function land_update(JointLandUpdateRequest $request)
    {
        $bid = $request->input('bid');
        $pid = $request->input('pid');

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
            Session::flash('success', 'Property Edited Successfully');
        } else {
            Session::flash('danger', 'Failed to Edit Property');
            return redirect()->route('corporate_property_join.land_edit', compact('bid', 'pid'));
        }
        return redirect()->route('corporate_property_join.index', compact('bid'));
    }
    public function land_destroy($bid, $pid)
    {
        dd('sorry you cannot delete at this time');

        $land = JointLand::find($pid);
       $result=null;
        try {
            $result = $land->delete();
        } catch (\Exception $e) {
            Session::flash('danger', 'Failed to Delete. Please Delete all the data associated with it and Try again.');
        }
        if ($result) {
            Session::flash('success', 'Land Removed Successfully');
        }


        return redirect()->route('corporate_property_join.index', compact('bid'));


    }






}
