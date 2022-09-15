<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    public $maxAttempts = 3;
    public $decayMinutes = 2;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {

        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {

        $this->validate($request, ['username' => 'required', 'password' => 'required']);
        $requests = $request->all();

        $user = User::where('username', $requests['username'])->get()->first();
        if ($user) {
            if ($user->status == 1) {
                $username = $request->input('username');
                $password = $request->input('password');
                $status = Auth::attempt(
                    ['username' => $username, 'password' => $password]);
                if ($status == true) {
                    $user = Auth::user();
                    activity()
                        ->causedBy($user)
                        ->log($user->name . ' logged in.');
                    Session::flash('success', 'Welcome! You have been successfully logged in.');
                    return redirect()->intended(route('home'));
                } else {
                    Session::flash('danger', 'Wrong Username or Password, Please Try Again');
                    return view('auth.login');
                }
            } else {
                Session::flash('warning', 'Your Account Has Been Disabled, Please Contact Your System Admin.');
                return view('auth.login');
            }
        } else {
            Session::flash('danger', 'Wrong Username or Password, Please Try Again');
            return view('auth.login');
        }
    }


    function logout(Request $request)
    {
        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log($user->name . ' logged out.');

        $this->guard('user')->logout();
        $activeGuards = 0;
        foreach (config('auth.guards') as $guard => $guardConfig) {
            if ($guardConfig['driver'] === 'session') {
                $guardName = Auth::guard($guard)->getName();
                if ($request->session()->has($guardName) && $request->session()->get($guardName) === Auth::guard($guard)->user()->getAuthIdentifier()) {
                    $activeGuards++;
                }
            }
        }

        if ($activeGuards === 0) {
            $request->session()->flush();
            $request->session()->regenerate();
        }
        Session::flash('success', 'You are logged out Successfully');
        return redirect('/');
    }
}
