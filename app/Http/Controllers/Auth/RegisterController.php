<?php

namespace App\Http\Controllers\Auth;

use App\Branch;
use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\UserCreateRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Auth::user()->user_type == 'user') {
            Session::flash('danger', 'Sorry, You do not have permission to access this.');
            return view('home');
        } else {
            $user = User::all();
            return view('auth.index', compact('user'));
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->user_type == 'admin') {
            $branch = [];
            $branches = Branch::select('id', 'location')->get();
            foreach ($branches as $b) {
                $branch[''] = 'zfvf';
                $branch[$b->id] = $b->location;
            }

            return view('auth.register', compact('branch'));

        } else {
            Session::flash('danger', 'Sorry, You do not have permission to access this.');
            return view('home');
        }


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        if (Auth::user()->user_type == 'admin') {
            $data = [
                'name' => $request->input('name'),
                'username' => $request->input('username'),
                'post' => $request->input('post'),
                'email' => $request->input('email'),
                'branch' => $request->input('branch'),
                'user_type' => $request->input('usertype'),
                $pass = $request->input('password'),
                'password' => bcrypt($pass),
                'status' => $request->input('status'),
            ];

            if (!empty($request->file('image'))) {
                $image = $request->file('image');
                $path = base_path() . '/public/assets/backend/dist/img/';
                $name = uniqid() . '_' . $image->getClientOriginalName();
                if ($image->move($path, $name)) {

                    $data['image'] = $name;

                }

            } else {
                $data['image'] = 'default.png';
            }

            $status = User::create($data);


            if ($status) {
                Session::flash('success', 'User Created Successfully');

            } else {
                Session::flash('danger', 'Error in creating user, Please Try again.');
                return redirect()->route('userregister.create');
            }
            return redirect()->route('userregister.index');

        } else {
            Session::flash('danger', 'Sorry, You do not have permission to access this.');
            return view('home');
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        if (Auth::user()->user_type == 'admin') {
            $user = User::find($id);
            return view('auth.show', compact('user'));
        } else {
            Session::flash('danger', 'Sorry, You do not have permission to access this.');
            return view('home');
        }


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->user_type == 'admin') {
            $branch = [];
            $branches = Branch::select('id', 'location')->get();
            foreach ($branches as $b) {
                $branch[''] = 'zfvf';
                $branch[$b->id] = $b->location;
            }
            $user = User::find($id);
            return view('auth.edit', compact('branch', 'user'));
        } else {
            Session::flash('danger', 'Sorry, You do not have permission to access this.');
            return view('home');
        }
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
        if (Auth::user()->user_type == 'admin') {
            $user = User::find($id);
            $oldimage = $user->image;
            $user->name = $request->input('name');
            $user->username = $request->input('username');
            $user->user_type = $request->input('usertype');
            $user->email = $request->input('email');
            $user->post = $request->input('post');
            $user->branch = $request->input('branch');
            $user->status = $request->input('status');

            if (!empty($request->file('image'))) {
                $image = $request->file('image');
                $path = base_path() . '/public/assets/backend/dist/img/';
                $name = uniqid() . '_' . $image->getClientOriginalName();
                if ($image->move($path, $name)) {

                    $user->image = $name;
                    if (!empty($oldimage) && file_exists(public_path() . '/assets/backend/dist/img/' . $oldimage)) {
                        unlink(public_path() . '/assets/backend/dist/img/' . $oldimage);
                    }
                }

            }
            $status = $user->update();

            if ($status) {
                Session::flash('success', 'User Updated Successfully');

            } else {
                Session::flash('danger', 'Error in Updating user, Please Try again.');
                return redirect()->route('userregister.edit', compact('id'));
            }
            return redirect()->route('userregister.index');

        } else {
            Session::flash('danger', 'Sorry, You do not have permission to access this.');
            return view('home');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        dd('sorry you cannot perform this action.');
    }

    public function profile()
    {
        $user = User::find(Auth::user()->id);
        return view('auth.profile', compact('user'));
    }

    public function change_password($id)
    {
        if (Auth::user()->user_type == 'admin') {
            $user = User::find($id);
            return view('auth.change_password', compact('user'));
        } else {
            if (Auth::user()->user_type == 'checker' || Auth::user()->user_type == 'user') {
                if(Auth::user()->id==$id){
                    $user = User::find($id);
                    return view('auth.change_password', compact('user'));
                }else{
                    Session::flash('danger', 'Sorry you cannot access this');
                    return redirect()->route('home');
                }

            }else{
                Session::flash('danger', 'Sorry you cannot access this');
                return redirect()->route('home');
            }
        }
    }

    public function update_password(PasswordUpdateRequest $request, $id)
    {
        if (Auth::user()->user_type == 'admin') {
            if (!Hash::check($request->input('old_password'), Auth::user()->password)) {
                Session::flash('danger', 'Failed to change Password, Please Try Again.');
                return redirect()->route('auth.change_password', compact('id'));
            } else {
                $user = User::find($id);
                $user->password = bcrypt($request->input('new_password'));
                $status = $user->update();
                if ($status) {
                    Session::flash('success', 'Password Changed Successfully.');
                }
                return redirect()->route('home');
            }
        } else {
            if (Auth::user()->user_type == 'checker' || Auth::user()->user_type == 'user') {
                if(Auth::user()->id==$id){
                    if (!Hash::check($request->input('old_password'), Auth::user()->password)) {
                        Session::flash('danger', 'Failed to change Password, Please Try Again.');
                        return redirect()->route('auth.change_password', compact('id'));
                    } else {

                        $user = User::find($id);
                        $user->password = bcrypt($request->input('new_password'));
                        $status = $user->update();
                        if ($status) {
                            Session::flash('success', 'Password Changed Successfully.');
                        }
                        return redirect()->route('home');
                    }
                }else{
                    Session::flash('danger', 'Sorry you cannot access this');
                    return redirect()->route('home');
                }

            }else{
                Session::flash('danger', 'Sorry you cannot access this');
                return redirect()->route('home');
            }
        }
    }


}
