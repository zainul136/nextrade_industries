<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CGTGrade;
use App\Models\Customer;
use App\Models\NTGrade;
use App\Models\ProductType;
use App\Models\ScanInInventory;
use App\Models\ScanInLog;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index($date = null)
    {
        if ($date == null) {
            $date = 'current';
        }
        session(['date' => $date]);
        $currentDate = Carbon::now()->format('Y-m-d');
        $warehouses = Warehouse::orderBy('id', 'desc')->get();
        $data['cgt'] = ScanInLog::join('c_g_t_grades', 'scan_in_logs.cgt', '=', 'c_g_t_grades.id')
            ->leftJoin('scan_in_inventories', 'scan_in_logs.scan_in_inventory_id', '=', 'scan_in_inventories.id')

            // ->where('scan_in_logs.is_scan_out', 0)
            ->when(!empty($date) && $date != 'current' && $date != 'all', function ($query) use ($date) {
                $query->whereYear('scan_in_inventories.created_at', $date);
            })
            ->when(!empty($date) && $date == 'current' && $date != 'all', function ($query) {
                $query->where('scan_in_logs.is_scan_out', 0);
            })
            ->sum('scan_in_logs.weight');
        $data['yards'] = ScanInLog::join('c_g_t_grades', 'scan_in_logs.cgt', '=', 'c_g_t_grades.id')
            ->leftJoin('scan_in_inventories', 'scan_in_logs.scan_in_inventory_id', '=', 'scan_in_inventories.id')
            // ->where('scan_in_logs.is_scan_out', 0)
            ->when(!empty($date) && $date != 'current' && $date != 'all', function ($query) use ($date) {
                $query->whereYear('scan_in_inventories.created_at', $date);
            })
            ->when(!empty($date) && $date == 'current' && $date != 'all', function ($query) {
                $query->where('scan_in_logs.is_scan_out', 0);
            })
            ->sum('scan_in_logs.yards');
        $data['users'] = User::where('role', '!=', 1)->count();
        $data['suppliers'] = Supplier::count();
        $data['customers'] = Customer::count();
        $data['warehouses'] = Warehouse::count();
        $cgt_inventory = [];
        $cgt = CGTGrade::orderBy('id', 'asc')->get();
        foreach ($cgt as $key => $value) {
            $query = DB::table('scan_in_logs')
                ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
                ->select(
                    'c_g_t_grades.grade_name as cgt_grade',
                    DB::raw('sum(scan_in_logs.weight) as weight'),
                    DB::raw('sum(scan_in_logs.yards) as yards'),
                )
                ->where(['c_g_t_grades.id' => $value->id, 'scan_in_logs.cgt' => $value->id])
                ->when(!empty($date) && $date != 'current' && $date != 'all', function ($query) use ($date) {
                    $query->whereYear('scan_in_logs.created_at', $date);
                })
                ->when(!empty($date) && $date == 'current' && $date != 'all', function ($query) {
                    $query->where('scan_in_logs.is_scan_out', 0);
                })
                ->groupBy('c_g_t_grades.grade_name', 'c_g_t_grades.id')
                ->first();
            $cgt_inventory[$value->grade_name] = $query;
        }

        $nt_inventory = [];
        $nt = NTGrade::orderBy('id', 'asc')->get();
        foreach ($nt as $key => $value) {
            $query = DB::table('scan_in_logs')
                ->join('n_t_grades', 'n_t_grades.id', '=', 'scan_in_logs.nt')
                ->select(
                    'n_t_grades.grade_name as nt_grade',
                    DB::raw('sum(scan_in_logs.weight) as weight'),
                )
                ->where(['n_t_grades.id' => $value->id])
                ->when(!empty($date) && $date != 'current' && $date != 'all', function ($query) use ($date) {
                    $query->whereYear('scan_in_logs.created_at', $date);
                })
                ->when(!empty($date) && $date == 'current' && $date != 'all', function ($query) {
                    $query->where('scan_in_logs.is_scan_out', 0);
                })
                ->groupBy('n_t_grades.grade_name', 'n_t_grades.id')
                ->first();
            $nt_inventory[$value->grade_name] = $query;
        }

        $product_type_inventory = [];
        $product_type = ProductType::orderBy('id', 'asc')->get();
        foreach ($product_type as $key => $value) {
            $query = DB::table('scan_in_logs')
                ->join('product_types', 'product_types.id', '=', 'scan_in_logs.product_type')
                ->select(
                    'product_types.product_type as product_type',
                    DB::raw('sum(scan_in_logs.weight) as weight'),
                    DB::raw('sum(scan_in_logs.yards) as yards'),
                )
                ->where(['product_types.id' => $value->id])
                ->when(!empty($date) && $date != 'current' && $date != 'all', function ($query) use ($date) {
                    $query->whereYear('scan_in_logs.created_at', $date);
                })
                ->when(!empty($date) && $date == 'current' && $date != 'all', function ($query) use ($currentDate) {
                    $query->where('scan_in_logs.is_scan_out', 0);
                })
                ->groupBy('product_types.product_type', 'product_types.id')
                ->first();
            $product_type_inventory[$value->product_type] = $query;
        }
        return view('admin.modules.dashboard.index', compact('data', 'cgt_inventory', 'nt_inventory', 'warehouses', 'product_type_inventory'));
    }

    public function getNTInventoryByFilter(Request $request)
    {
        $warehouse_id = $request->warehouse_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $year = $request->year;
        $inv_type = $request->inv_type;
        $nt_grades = NTGrade::orderBy('id', 'asc')->get();
        $currentDate = Carbon::now()->format('Y-m-d');

        foreach ($nt_grades as $key => $v) {


            $inventory_summary[$key] = ScanInInventory::Join('scan_in_logs', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                ->where('scan_in_logs.nt', $v->id)
                ->when(!empty($warehouse_id), function ($query) use ($warehouse_id) {

                    $query->where('scan_in_inventories.warehouse_id', $warehouse_id);
                })
                ->when(!empty($year) && $year != 'current' && empty($from_date) && empty($to_date), function ($query) use ($year) {
                    $query->whereYear('scan_in_inventories.created_at', $year);
                })
                 ->when(empty($year) && !empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                    $query->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                        ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
                })
                ->when(!empty($year) && $year == 'current' && empty($from_date) && empty($to_date), function ($query) use ($currentDate) {
                    $query->where('scan_in_logs.is_scan_out', 0);
                })
                ->when($inv_type == 'weight', function ($query) use ($inv_type) {

                    $query->where('scan_in_logs.unit', 'W');
                })
                ->when($inv_type == 'yards', function ($query) use ($inv_type) {

                    $query->where('scan_in_logs.unit', '!=', 'W');
                })
                ->select(DB::raw("SUM(scan_in_logs.weight) as total_weight"), DB::raw("SUM(scan_in_logs.yards) as total_yards"), DB::raw("SUM(scan_in_logs.rolls) as total_rolls"), DB::raw("COUNT(scan_in_logs.id) as pallet_count"))
                ->first();
        }

        $nt_inventory_summary_html = view('admin.modules.dashboard.nt_inventory_summary', compact('nt_grades', 'inventory_summary', 'inv_type'))->render();

        return response()->json(['status' => true, 'data' => $nt_inventory_summary_html]);
    }

    public function editUserProfile($id)
    {
        $user_data = User::find($id);
        return view('admin.modules.users.edit_user_profile', compact('user_data'));
    }

    public function viewUserProfile($id)
    {
        $user_data = User::where('id', $id)->with('userRole')->first();
        return view('admin.modules.users.view_user_profile', compact('user_data'));
    }

    public function userProfile(Request $request)
    {
        $messages = array(
            'full_name.required' => __('Full Name field is required.'),
            'contact.required' => __('Contact field is required.'),
            'address.required' => __('Address field is required.'),
            'email.required' => __('Email field is required.'),
            'email.email' => __('Email address must be valid.'),
            // 'password.required' => __('Password field is required.'),
        );
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'contact' => 'required',
            'address' => 'required',
            'email' => 'required|email',
            // 'password' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $data = [
            'full_name' => $request->full_name,
            'contact' => $request->contact,
            'email' => $request->email,
            'address' => $request->address,
        ];
        $update_id = $request->id;
        if (isset($update_id) && !empty($update_id) && $update_id != 0) {
            $user = User::where('id', $update_id)->first();
            $user->password = Hash::make($request->password) ??  $user->password;
            if ($request->hasfile('profile_picture')) {
                $profile_picture = $request->profile_picture->getClientOriginalName();
                $request->profile_picture->move(public_path('storage/images/profilePicture/'), $profile_picture);
                // $profile_picture = url('/') . '/' . 'storage/images/profilePicture/' . $imageName;
                $user->profile_picture = $profile_picture;
            }
            $user->update($data);
            return redirect()->route('admin:edit-user-profile', $update_id)->with('success', 'Profile Updated Successfully');
        }
    }
}
