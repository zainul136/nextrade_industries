<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ScanIn\UpdateRequest;
use App\Http\Controllers\Controller;
use App\Models\CGTGrade;
use App\Models\Color;
use App\Models\NTGrade;
use App\Models\ProductType;
use App\Models\ScanInInventory;
use App\Models\ScanInLog;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DataTables;

class ScanInController extends Controller
{

    public function __construct()
    {
        $this->middleware('check_scan_in_permission');
    }
    public function index()
    {

        // $referenceNumber = Str::random(30);
        $suppliers = Supplier::orderby('id', 'desc')->get();
        $warehouses = Warehouse::orderby('id', 'desc')->get();
        return view('admin.modules.scan-in.index', compact('suppliers', 'warehouses'));
    }

    public function getSkewValues(Request $request)
    {
        $skew_no = $request->skew_no;
        $parts = explode('.', $skew_no);
        $data = [];
        if ($parts[0]) {
            $data['unit'] = ucfirst($parts[0]);
        }
        if ($parts[1]) {
            $cgtGrade = $parts[1];
            $data['cgtGrade'] = CGTGrade::where('slug', $cgtGrade)->first(['id', 'grade_name', 'price', 'pnl']);
        }
        if ($parts[2]) {
            $ntGrade = $parts[2];
            $data['ntGrade'] = NTGrade::where('slug', $ntGrade)->first(['id', 'grade_name']);
        }
        if ($parts[3]) {
            $productType = $parts[3];
            $data['productType'] = ProductType::where('slug', $productType)->first(['id', 'product_type']);
        }
        if ($parts[4]) {
            $color = $parts[4];
            $data['color'] = Color::where('slug', $color)->first(['id', 'name']);
        }
        if ($parts[5]) {
            $data['rolls'] = $parts[5];
        }
        if ($parts[6]) {
            $data['w_or_y'] = $parts[6];
        }
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function addScanInInventory(Request $request)
    {

        $required_field = '';
        // check validation
        if (empty($request->reference_number)) {

            $required_field .= '<p style="margin:0px!important;">Refrence number is required</p>';
        }

        if (empty($request->supplier_id)) {

            $required_field .= '<p style="margin:0px!important;">Supplier is required</p>';
        }

        if (empty($request->warehouse_id)) {

            $required_field .= '<p style="margin:0px!important;">Warehouse is required</p>';
        }

        if (!empty($required_field)) {

            $output = ['success' => false, 'msg' => $required_field];
        } else {

            // check all skew numbers its all unique or not
            $inserted_skew_numbers = '';

            foreach ($request->skew_no as $key => $v) {

                if (empty($v)) {

                    $msg = '<p style="margin:0px!important;">Skew Number is required</p>';
                    return response()->json(['success' => false, 'msg' => $msg]);
                } else {

                    $ref_exist = ScanInInventory::where('reference_number', $request->reference_number)->first();
                    if ($ref_exist) {
                        $msg = '<p style="margin:0px!important;">Reference Number Already Exist!</p>';
                        return response()->json(['success' => false, 'msg' => $msg]);
                    }
                    // check data is null or not on skew number
                    $checkSkewNumberIsValid = $this->checkSkewNumberIsValid($v);
                    if ($checkSkewNumberIsValid != false) {

                        $inserted_skew_numbers .= '<p style="margin:0px!important;">' . $checkSkewNumberIsValid . '</p>';
                    } else {

                        $is_skew_unique = $this->uniqueSkewNumber($v);

                        if ($is_skew_unique != true) {

                            $inserted_skew_numbers .= '<p style="margin:0px!important;">Skew Number: ' . $v . ' is already exist</p>';
                        }
                    }
                }
            }

            if (!empty($inserted_skew_numbers)) {

                $output = ['success' => false, 'msg' => $inserted_skew_numbers];
            } else {

                $scan_in_refer_data = [
                    'reference_number' => $request->reference_number,
                    'supplier_id' => $request->supplier_id,
                    'warehouse_id' => $request->warehouse_id,
                    'nexpac_bill' => $request->nexpac_bill
                ];
                // insert row into inventory
                $scan_in_inventory = ScanInInventory::create($scan_in_refer_data);
                $scan_in_inventory_id = $scan_in_inventory->id;

                // make array for logs and insert it
                $skew_numbers_arr = [];
                foreach ($request->skew_no as $key => $v) {
                    $unit = ucfirst($request->unit[$key]);
                    $skew_numbers_arr[] = [
                        'scan_in_inventory_id' => $scan_in_inventory_id,
                        'unit' => $unit,
                        'skew_number' => ucfirst($v),
                        'cgt' => $request->cgt[$key],
                        'nt' => $request->nt[$key],
                        'product_type' => $request->product_type[$key],
                        'color' => $request->color[$key],
                        'rolls' => $request->rolls[$key],
                        'weight' => $unit == 'W' ? $request->w_or_y[$key] : null,
                        'yards' => $unit != 'W' ? $request->w_or_y[$key] : null,
                        'cgt_price' => $request->cgt_price[$key] ? $request->cgt_price[$key] : null,
                        'cgt_pnl' => $request->cgt_pnl[$key] ? $request->cgt_pnl[$key] : null,
                    ];
                }


                if (!empty($skew_numbers_arr)) {

                    ScanInLog::insert($skew_numbers_arr);

                    $output = ['success' => true, 'msg' => 'Scan In Successfully'];
                } else {
                    $output = ['success' => false, 'msg' => 'Skew Number is required'];
                }
            }
        }

        return response()->json($output);
    }

    protected function checkSkewNumberIsValid($skew_no)
    {

        $parts = explode('.', $skew_no);

        $total_parts = count($parts);
        $msg = 'Skew Number: ' . $skew_no . ' is incorrect';
        if ($total_parts != 8) {
            return $msg;
        } else {

            $data = [];
            if (empty($parts[0])) {
                // $data['unit'] = $parts[0];
                return $msg;
            }
            if ($parts[1]) {
                $cgtGrade = $parts[1];
                $data['cgtGrade'] = CGTGrade::where('slug', $cgtGrade)->first(['id', 'grade_name']);

                if (empty($data['cgtGrade'])) {

                    return $msg;
                }
            }
            if ($parts[2]) {
                $ntGrade = $parts[2];
                $data['ntGrade'] = NTGrade::where('slug', $ntGrade)->first(['id', 'grade_name']);

                if (empty($data['ntGrade'])) {

                    return $msg;
                }
            }
            if ($parts[3]) {
                $productType = $parts[3];
                $data['productType'] = ProductType::where('slug', $productType)->first(['id', 'product_type']);

                if (empty($data['productType'])) {

                    return $msg;
                }
            }
            if ($parts[4]) {
                $color = $parts[4];
                $data['color'] = Color::where('slug', $color)->first(['id', 'name']);

                if (empty($data['color'])) {

                    return $msg;
                }
            }

            if (empty($parts[1]) || empty($parts[2]) || empty($parts[3]) || empty($parts[4])) {

                return $msg;
            }

            if (empty($parts[5])) {
                // $data['rolls'] = $parts[5];
                return $msg;
            }
            if (empty($parts[6])) {
                // $data['w_or_y'] = $parts[6];
                return $msg;
            }
            if (empty($parts[7])) {
                // $data['w_or_y'] = $parts[6];
                return $msg;
            }
        }

        return false;
    }

    protected function uniqueSkewNumber($skew_number)
    {
        $count =  ScanInLog::where('skew_number', ucfirst($skew_number))->count();
        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function scanInLogs(Request $request)
    {

        if ($request->ajax()) {

            $year = $request->year;
            $warehouse_id = $request->warehouse_id;
            $supplier_id = $request->supplier_id;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $reference_number = $request->reference_number;
            $cgt = $request->cgt;
            $nt = $request->nt;
            $color = $request->color;
            $scan_in_logs = ScanInLog::with('getScanInInventory', 'getScanInInventory.warehouse', 'getScanInInventory.supplier', 'sIProductName', 'sICGT', 'sINT', 'sIColor', 'getScanOutLogs.scanOutInventory')
                ->when(!empty($year), function ($query) use ($year) {

                    $query->whereHas('getScanInInventory', function ($query) use ($year) {

                        $query->whereYear('created_at', $year);
                    });
                })
                ->when(!empty($reference_number), function ($query) use ($reference_number) {

                    $query->whereHas('getScanInInventory', function ($query) use ($reference_number) {

                        $query->where('reference_number', $reference_number);
                    });
                })
                ->when(!empty($cgt), function ($query) use ($cgt) {

                    $query->where('cgt', $cgt);
                })
                ->when(!empty($nt), function ($query) use ($nt) {

                    $query->where('nt', $nt);
                })
                ->when(!empty($color), function ($query) use ($color) {

                    $query->where('color', $color);
                })
                ->when(!empty($warehouse_id), function ($query) use ($warehouse_id) {

                    $query->whereHas('getScanInInventory', function ($query) use ($warehouse_id) {

                        $query->where('warehouse_id', $warehouse_id);
                    });
                })
                ->when(!empty($supplier_id), function ($query) use ($supplier_id) {

                    $query->whereHas('getScanInInventory.supplier', function ($query) use ($supplier_id) {

                        $query->where('supplier_id', $supplier_id);
                    });
                })
                ->when(!empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {

                    $query->whereHas('getScanInInventory', function ($query) use ($from_date, $to_date) {

                        $query->whereDate('created_at', '>=', $from_date)
                            ->whereDate('created_at', '<=', $to_date);
                    });
                })
                ->orderBy('scan_in_logs.id', 'desc')->get();

            return Datatables::of($scan_in_logs)
                ->addIndexColumn()
                ->addColumn('scan_in_inv_date', function ($data) {
                    return  changeDateFormatToUS($data->getScanInInventory->created_at);
                })
                ->addColumn('scan_out_status', function ($data) {

                    if ($data->is_scan_out == 1) {

                        $html = '<span class="badge rounded-pill bg-success"> <i class="fa fa-check"></i> </span>';
                    } else {

                        $html = '<span class="badge rounded-pill bg-danger"> <i class="fa fa-times"></i> </span>';
                    }

                    return $html;
                })
                ->rawColumns(['scan_in_inv_date', 'scan_out_status'])
                ->make(true);
        }

        $warehouses = Warehouse::orderBy('id', 'desc')->get();
        $suppliers = Supplier::orderBy('id', 'desc')->get();
        $reference_number = ScanInInventory::orderBy('id', 'desc')->get();
        $cgt = CGTGrade::orderBy('id', 'desc')->get();
        $nt = NTGrade::orderBy('id', 'desc')->get();
        $color = Color::orderBy('id', 'desc')->get();
        return view('admin.modules.scan-in.logs', compact('warehouses', 'suppliers', 'reference_number', 'cgt', 'nt', 'color'));
    }

    public function scanInInventory(Request $request)
    {
        if ($request->ajax()) {

            $inventory = ScanInInventory::with('getScanLogs')->with('warehouse')->with('supplier')->orderBy('id', 'desc')->get();

            return Datatables::of($inventory)
                ->addIndexColumn()
                ->addColumn('scanin_date', function ($data) {

                    if ($data->created_at) {

                        $html = changeDateFormatToUS($data->created_at);
                    } else {

                        $html = '-';
                    }

                    return $html;
                })
                ->addColumn('action', function ($data) {
                    $btn = '';
                    $has_scan_out = false; // Initialize flag for checking if any ScanLog records have is_scan_out = 1
                    foreach ($data->getScanLogs as $log) {
                        if ($log->is_scan_out) {
                            $has_scan_out = true;
                            break;
                        }
                    }

                    if (!$has_scan_out) { // Show edit and delete buttons only if there are no ScanLogs with is_scan_out = 1
                        $btn .= '<span class="action-icon-spacing">';

                        $btn .= '<a href="javascript:void(0);" class="delete" data-id="' . $data->id . '" title="Delete"><i class="fa fa-trash text-danger"></i></a>';
                    }
                    $btn .= '<a href="' . route('admin:ScanInInventory.edit', [$data->id]) . '" title="Edit">
                    <i class="fa fa-edit text-primary"></i>
                    </a>';
                    $btn .= '<a href="' . route('admin:ScanInInventory.inventoryHistory', $data->id) . '" title="Show"> <i class="fa fa-eye"></i></a>';
                    $btn .= '</span>';

                    return $btn;
                })
                ->rawColumns(['scanin_date', 'action'])
                ->make(true);
        }

        return view('admin.modules.scan-in.inventory_list');
    }

    public function destroy(Request $request)
    {
        $scan_in_inventory_id = $request->scan_in_inventory_id;
        ScanInLog::where('scan_in_inventory_id', $scan_in_inventory_id)->delete();
        $result = ScanInInventory::where('id', $scan_in_inventory_id)->delete();
        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Something went wrong! try again'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Record deleted successfully'], 200);
    }

    public function editInventory($id)
    {
        $scan_in_history = ScanInInventory::find($id);
        $suppliers = Supplier::orderby('id', 'desc')->get();
        $warehouses = Warehouse::orderby('id', 'desc')->get();

        $scanin_skew_number = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
            ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
            ->join('n_t_grades', 'n_t_grades.id', '=', 'scan_in_logs.nt')
            ->join('product_types', 'product_types.id', '=', 'scan_in_logs.product_type')
            ->join('suppliers', 'suppliers.id', '=', 'scan_in_inventories.supplier_id')
            ->join('colors', 'colors.id', '=', 'scan_in_logs.color')->where('scan_in_inventories.id', $id)->select('scan_in_logs.id as skew_id', 'scan_in_logs.skew_number', 'suppliers.name as supplier_name', 'product_types.product_type', 'c_g_t_grades.grade_name as cgt_grade', 'n_t_grades.grade_name as nt_grade', 'scan_in_logs.rolls', 'colors.name as color_name', 'scan_in_logs.weight as weight', 'scan_in_logs.yards as yards','scan_in_logs.is_scan_out as is_scan_out')->get();
        $total_records = ScanInLog::where('scan_in_inventory_id', $id)->count('id');
        return view('admin.modules.scan-in.edit', compact('scan_in_history', 'total_records', 'scanin_skew_number', 'suppliers', 'warehouses'));
    }

    public function updateInventory(UpdateRequest $request)
    {
        $id = $request->id;
        $inventoryDetails = $request->validated();
        $result = ScanInInventory::where('id', $id)->update($inventoryDetails);

        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        return redirect()->route('admin:scanInInventory')->with('success', 'The Record updated successfully.');
    }
    public function inventoryHistory($id)
    {
        $scan_in_history = ScanInInventory::where('id', $id)->with('supplier')->with('warehouse')->orderBy('id', 'desc')->first();
        $scanin_skew_number = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
            ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
            ->join('n_t_grades', 'n_t_grades.id', '=', 'scan_in_logs.nt')
            ->join('product_types', 'product_types.id', '=', 'scan_in_logs.product_type')
            ->join('suppliers', 'suppliers.id', '=', 'scan_in_inventories.supplier_id')
            ->join('colors', 'colors.id', '=', 'scan_in_logs.color')->where('scan_in_inventories.id', $id)->select('scan_in_logs.id as skew_id', 'scan_in_logs.is_scan_out as is_scan_out', 'scan_in_logs.skew_number', 'suppliers.name as supplier_name', 'product_types.product_type', 'c_g_t_grades.grade_name as cgt_grade', 'n_t_grades.grade_name as nt_grade', 'scan_in_logs.rolls', 'colors.name as color_name', 'scan_in_logs.weight as weight', 'scan_in_logs.yards as yards')->get();
        $total_records = ScanInLog::where('scan_in_inventory_id', $id)->count('id');
        return view('admin.modules.scan-in.inventory_history', compact('scan_in_history', 'total_records', 'scanin_skew_number'));
    }

    public function skewNumberDelete(Request $request)
    {
        $scan_in_id = $request->scan_in_id;
        $scan_in_inventory_id = $request->scan_in_inventory_id;
        $total_records = $request->total_records;
        $result = ScanInLog::where(['scan_in_inventory_id' => $scan_in_inventory_id, 'id' => $scan_in_id])->first();
        if ($result) {
            $result->delete();
            if ($total_records == 1) {
                $order = ScanInInventory::where('id', $scan_in_inventory_id)->delete();
                return response()->json(['status' => true, 'record_delete' => true, 'message' => 'Skew Number and Record deleted successfully'], 200);
            } else {
                return response()->json(['status' => true, 'record_delete' => false, 'message' => 'Skew Number deleted successfully'], 200);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong! try again'], 404);
        }
    }

    public function deleteSelected(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []); // Get selected_ids from request

        if (empty($selectedIds)) {
            return response()->json([
                'status' => false,
                'message' => 'No records selected for deletion.',
            ]);
        }

        // Perform the deletion (customize as per your database and model).
        ScanInLog::whereIn('id', $selectedIds)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Selected records deleted successfully.',
        ]);
    }
}
