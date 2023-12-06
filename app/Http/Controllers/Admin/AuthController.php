<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleHasPermission;
use App\Models\RoleReportsPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.modules.auth.login');
    }

    public function login_attempt(Request $request)
    {

        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 1, 'del_status' => 0])) {
            $user = Auth::user();
            $RoleHasPermission = RoleHasPermission::with('roleName')->where('role_id', $user->role)->first();
            $RoleReportsPermission = RoleReportsPermission::with('roleName')->where('role_id', $user->role)->first();
            session(['RoleHasPermission' => $RoleHasPermission]);
            session(['RoleReportsPermission' => $RoleReportsPermission]);
            return redirect()->intended('/admin/dashboard');
        } else {
            return redirect()->back()->with('error', 'Invalid credentials!');
        }
    }

    public function forget_password()
    {
        return view('admin.modules.auth.forget_password');
    }

    public function passwordUpdate(Request $request)
    {
        $password = $request->password_;
        $email = $request->email;
        // Validate the token
        $tokenData = DB::table('password_resets')
            ->where('email', $request->email)->first();
        // Redirect the user back to the password reset request form if the token is invalid
        if (!$tokenData){
            return redirect()->intended('/admin/forget-password');
        }
        $user = User::where('email', $tokenData->email)->first();
        // Redirect the user back if the email is invalid
        if (!$user) return redirect()->back()->withErrors(['email1' => 'Email not found']);
        //Hash and update the new password
        $user->password = Hash::make($password);
        $user->update(); //or $user->save();

        //Delete the token
        DB::table('password_resets')->where('email', $user->email)
            ->delete();

        //Send Email Reset Success Email
        if ($user) {
            return redirect('/admin');
        } else {
            return redirect()->back()->withErrors(['email' => trans('A Network Error occurred. Please try again.')]);
        }
    }


    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect()->route('admin:login');
    }
}
