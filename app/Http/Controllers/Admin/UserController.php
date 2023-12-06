<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_user_permission');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $users = User::with('userRole')->where('del_status', 0)->where('role', '!=', 1)->orderBy('id', 'desc')->get();

            return Datatables::of($users)
                ->addIndexColumn()
                ->addColumn('user_status', function ($data) {

                    if ($data->status == 1) {

                        $html = '<span class="badge rounded-pill bg-success"> Active </span>';
                    } else {

                        $html = '<span class="badge rounded-pill bg-danger"> Deactive </span>';
                    }

                    return $html;
                })
                ->addColumn('action', function ($data) {

                    $btn = '';

                    $btn .= '<span class="action-icon-spacing">';

                    $btn .= '<a href="' . route('admin:user.edit', $data->id) . '" title="Edit">
                                        <i class="fa fa-edit text-primary"></i>
                                    </a>';

                    $btn .= '<a href="javascript:void(0);" class="delete" data-id="' . $data->id . '" title="Delete">
                                     <i class="fa fa-trash text-danger"></i>
                                 </a>';

                    $btn .= '</span>';

                    return $btn;
                })
                ->rawColumns(['user_status', 'action'])
                ->make(true);
        }

        return view('admin.modules.users.users_list');
    }

    public function create()
    {
        $roles = Role::where('id', '!=', 1)->get();
        return view('admin.modules.users.user_form', compact('roles'));
    }

    public function store(Request $request)
    {

        $messages = array(
            'full_name.required' => __('Full Name field is required.'),
            'contact.required' => __('Contact field is required.'),
            'address.required' => __('Address field is required.'),
            'email.required' => __('Email field is required.'),
            'email.email' => __('Email address must be valid.'),
            // 'password.required' => __('Password field is required.'),
            'role.required' => __('Role field is required.'),
        );
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'contact' => 'required',
            'address' => 'required',
            'email' => 'required|email',
            // 'password' => 'required',
            'role' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $data = [
            'full_name' => $request->full_name,
            'contact' => $request->contact,
            'email' => $request->email,
            'address' => $request->address,
            'role' => $request->role,
        ];
        $update_id = $request->id;
        if (isset($update_id) && !empty($update_id) && $update_id != 0) {
            $user = User::where('id', $update_id)->first();
            $user->update($data);
            return redirect()->route('admin:users')->with('success', 'User Updated Successfully');
        } else {
            $data['password'] = Hash::make($request->password);
            $user = User::create($data);
            $last_id = $user->id;
            $user->save($data);

            if (isset($last_id) && !empty($last_id)) {
                return redirect()->route('admin:users')->with('success', 'User Added Successfully');
            } else {
                return back()->with('error', 'Something Went wrong');
            }
        }
    }

    public function change_password(Request $request)
    {
        $id = $request->id;
        $messages = array(
            'current_password.required' => __('Current Password field is required.'),
            'new_password.required' => __('New Password field is required.'),
            'confirm_password.required' => __('Confirm Password field is required.'),
        );
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }
        $user = new User();
        $result = $user->updatePassword($id, $request->current_password, $request->new_password, $request->confirm_password);
        if ($result['success'] == true) {
            // Password updated successfully
            return redirect()->back()->with('success', $result['message']);
        } else {
            // Current password was incorrect or new password/confirm password did not match
            return redirect()->back()->withErrors([
                'errors' => __($result['message']),
            ])->withInput();
        }
    }


    public function admin_change_password(Request $request)
    {
        $id = $request->id;
        $messages = array(
            'new_password.required' => __('New Password field is required.'),
            'confirm_password.required' => __('Confirm Password field is required.'),
        );
        $validator = Validator::make($request->all(), [
            'new_password' => 'required',
            'confirm_password' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }
        $user = new User();
        $result = $user->adminUpdatePassword($id, $request->new_password, $request->confirm_password);
        if ($result['success'] == true) {
            // Password updated successfully
            return redirect()->back()->with('success', $result['message']);
        } else {
            // Current password was incorrect or new password/confirm password did not match
            return redirect()->back()->withErrors([
                'errors' => __($result['message']),
            ])->withInput();
        }
    }


    public function edit($id)
    {
        $roles = Role::where('id', '!=', 1)->get();
        $user_data = User::find($id);
        return view('admin.modules.users.user_form', compact('user_data', 'roles'));
    }

    public function destroy(Request $request)
    {
        $id = $request->user_id;
        $user = User::where('id', $id)->update(['del_status' => 1]);
        return response()->json(['status' => true]);
    }
}
