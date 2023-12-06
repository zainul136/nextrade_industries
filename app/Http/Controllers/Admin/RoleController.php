<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleHasPermission;
use App\Models\RoleReportsPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('check_role_permission');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::orderBy('id', 'desc')->where('id', '!=', 1)->get();
            return Datatables::of($roles)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {

                    $btn = '';

                    $btn .= '<span class="action-icon-spacing">';
                    $btn .= '<a href="#" title="show" class="show_permission_details" data-id="' . $data->id . '"> <i class="fa fa-eye"></i></a>';
                    $btn .= '<a href="' . route('admin:role.edit', $data->id) . '" title="Edit">
                                          <i class="fa fa-edit text-primary"></i>
                                      </a>';

                    $btn .= '<a href="javascript:void(0);" class="delete" data-id="' . $data->id . '" title="Delete">
                                       <i class="fa fa-trash text-danger"></i>
                                   </a>';

                    $btn .= '</span>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.modules.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.modules.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = array(
            'name.required' => __('Role Name field is required.'),
            'name.unique' => __('Role already exists!')
        );
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }
        $data = [
            'name' => $request->name
        ];
        $result = Role::create($data);
        $this->addPermissionToModules($result->id, $request);
        if (isset($request->reports) && $request->reports === 'on') {
            $this->add_each_report_permission($result->id, $request);
        }
        if (!$result) {
            return back()->with('error', 'Something went wrong! try again.');
        }
        return redirect()->route('admin:roles')->with('success', 'Role created successfully.');
    }
    protected function addPermissionToModules($role_id, $request)
    {
        $role_has_permission_id = $request->role_has_permission_id;
        $data['role_id'] = $role_id;
        $data['users'] = $request->has('users') && $request->users === 'on' ? 1 : 0;
        $data['roles'] = $request->has('roles') && $request->roles === 'on' ? 1 : 0;
        $data['warehouses'] = $request->has('warehouses') && $request->warehouses === 'on' ? 1 : 0;
        $data['customers'] = $request->has('customers') && $request->customers === 'on' ? 1 : 0;
        $data['suppliers'] = $request->has('suppliers') && $request->suppliers === 'on' ? 1 : 0;
        $data['cgt_gardes'] = $request->has('cgt_gardes') && $request->cgt_gardes === 'on' ? 1 : 0;
        $data['nt_grades'] = $request->has('nt_grades') && $request->nt_grades === 'on' ? 1 : 0;
        $data['colors'] = $request->has('colors') && $request->colors === 'on' ? 1 : 0;
        $data['product_types'] = $request->has('product_types') && $request->product_types === 'on' ? 1 : 0;
        $data['scan_in'] = $request->has('scan_in') && $request->scan_in === 'on' ? 1 : 0;
        $data['scan_out'] = $request->has('scan_out') && $request->scan_out === 'on' ? 1 : 0;
        $data['inventory'] = $request->has('inventory') && $request->inventory === 'on' ? 1 : 0;
        $data['orders'] = $request->has('orders') && $request->orders === 'on' ? 1 : 0;
        $data['reports'] = $request->has('reports') && $request->reports === 'on' ? 1 : 0;
        $data['nt_grade_column'] = $request->has('nt_grade_column') && $request->nt_grade_column === 'on' ? 1 : 0;
        $data['nt_price_column'] = $request->has('nt_price_column') && $request->nt_price_column === 'on' ? 1 : 0;
        $data['third_party_price_column'] = $request->has('third_party_price_column') && $request->third_party_price_column === 'on' ? 1 : 0;
        if ($role_has_permission_id) {
            RoleHasPermission::where('id', $role_has_permission_id)->update($data);
        } else {
            RoleHasPermission::create($data);
        }
    }

    protected function add_each_report_permission($role_id, $request)
    {
        $role_report_permission_id = $request->role_report_permission_id;
        $data['role_id'] = $role_id;
        $data['inventory_report'] = $request->has('inventory_report') && $request->inventory_report === 'on' ? 1 : 0;
        $data['cgt_summary'] = $request->has('cgt_summary') && $request->cgt_summary === 'on' ? 1 : 0;
        $data['nt_summary'] = $request->has('nt_summary') && $request->nt_summary === 'on' ? 1 : 0;
        $data['color_summary'] = $request->has('color_summary') && $request->color_summary === 'on' ? 1 : 0;
        $data['commulative_cgt'] = $request->has('commulative_cgt') && $request->commulative_cgt === 'on' ? 1 : 0;
        $data['commulative_nt'] = $request->has('commulative_nt') && $request->commulative_nt === 'on' ? 1 : 0;
        $data['customer_summary'] = $request->has('customer_summary') && $request->customer_summary === 'on' ? 1 : 0;
        $data['nexpac_report'] = $request->has('nexpac_report') && $request->nexpac_report === 'on' ? 1 : 0;
        $data['internal_report'] = $request->has('internal_report') && $request->internal_report === 'on' ? 1 : 0;
        $data['billing_report'] = $request->has('billing_report') && $request->billing_report === 'on' ? 1 : 0;
        $data['pnl_report'] = $request->has('pnl_report') && $request->pnl_report === 'on' ? 1 : 0;
        if ($role_report_permission_id) {
            RoleReportsPermission::where('id', $role_report_permission_id)->update($data);
        } else {
            RoleReportsPermission::create($data);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $role_id = $request->role_id;
        $role_has_permission = RoleHasPermission::where('role_id', $role_id)->first();
        $role_report_permission = RoleReportsPermission::where('role_id', $role_id)->first();
        $view = view('admin.modules.roles.details', compact('role_has_permission', 'role_report_permission'))->render();
        return response()->json(['status' => true, 'data' => $view]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $role_has_permission = RoleHasPermission::where('role_id', $role->id)->first();
        $role_report_permission = RoleReportsPermission::where('role_id', $role->id)->first();
        return view('admin.modules.roles.edit', compact('role', 'role_has_permission', 'role_report_permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $messages = array(
            'name.required' => __('Role Name field is required.'),
            'name.unique' => __('Role already exists!')
        );
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $id,
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }
        $data = [
            'name' => $request->name
        ];
        $result = Role::where('id', $id)->update($data);
        $this->addPermissionToModules($id, $request);
        if (isset($request->reports) && $request->reports === 'on') {
            $this->add_each_report_permission($id, $request);
        }
        if (!$result) {
            return back()->with('error', 'Something went wrong! try again.');
        }
        return redirect()->route('admin:roles')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->role_id;
        $result = Role::find($id)->delete();
        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Something went wrong! try again'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Color deleted successfully'], 200);
    }
}
