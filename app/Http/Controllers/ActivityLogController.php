<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function view_log($id)
    {

        $activity=Activity::where('causer_id',$id)->orderBy('created_at', 'desc')->get();
        return view('activity.index',compact('activity'));
    }

    public function view_all_log()
    {
        $activity=Activity::all();
        return view('activity.index',compact('activity'));
    }
}
