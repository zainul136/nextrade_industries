<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CGTGrade;
use App\Models\Color;
use App\Models\Customer;
use App\Models\NTGrade;
use App\Models\OrderStatus;
use App\Models\ProductType;
use App\Models\ScanOutInventory;
use App\Models\ScanInLog;
use App\Models\ScanOutLog;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DataTables;

class ScanOutController extends Controller
{

    public function __construct()
    {
        $this->middleware('check_scan_out_permission');
    }

    public function index()
    {
        // $releaseNumber = Str::random(30);
        $customers = Customer::orderby('id', 'desc')->get();
        $warehouses = Warehouse::orderby('id', 'desc')->get();
        return view('admin.modules.scan-out.index', compact('customers', 'warehouses'));
    }

    public function getCustomers(Request $request)
    {
        $sel_customer_id = Customer::where('id', $request->customer_id)->value('id');
        $customers = Customer::orderby('id', 'desc')->get();
        return response()->json(['status' => true, 'sel_customer_id' => $sel_customer_id, 'customers' => $customers]);
    }

    public function addScanOutInventory(Request $request)
    {

        $required_field = '';
        // check validation
        if (empty($request->release_number)) {

            $required_field .= '<p style="margin:0px!important;">Release number is required</p>';
        }

        if (empty($request->customer_id)) {

            $required_field .= '<p style="margin:0px!important;">Customer is required</p>';
        }

        if (empty($request->warehouse_id)) {

            $required_field .= '<p style="margin:0px!important;">Warehouse is required</p>';
        }

        // if ($request->is_order_pending == 'no') {

        //     if (empty($request->container)) {

        //         $required_field .= '<p style="margin:0px!important;">Container is required</p>';
        //     }
        //     if (empty($request->tear_factor)) {

        //         $required_field .= '<p style="margin:0px!important;">Tear factor is required</p>';
        //     }
        //     if (empty($request->seal)) {

        //         $required_field .= '<p style="margin:0px!important;">Seal is required</p>';
        //     }

        //     if (empty($request->pallet_weight)) {

        //         $required_field .= '<p style="margin:0px!important;">Pallet Tear is required</p>';
        //     }

        //     if (empty($request->scale_discrepancy)) {

        //         $required_field .= '<p style="margin:0px!important;">Scale Tickets Weight is required</p>';
        //     }
        // }

        if (!empty($required_field)) {

            $output = ['success' => false, 'msg' => $required_field];
        } else {

            if ($request->is_order_pending == 'yes') {
                $rlz_exist = ScanOutInventory::where('release_number', $request->release_number)->first();
                if ($rlz_exist) {
                    $msg = '<p style="margin:0px!important;">Release Number Already Exist!</p>';
                    return response()->json(['success' => false, 'msg' => $msg]);
                }
                $scan_out_rls_data = [
                    'release_number' => $request->release_number,
                    'customer_id' => $request->customer_id,
                    'warehouse_id' => $request->warehouse_id,
                    'is_order_pending' => $request->is_order_pending != 'yes' ? 0 : 1,
                ];
                // insert row into inventory
                $scan_out_inventory = ScanOutInventory::create($scan_out_rls_data);
                $order_status = OrderStatus::create(['scan_out_inventory_id' => $scan_out_inventory->id, 'user_id' => auth()->user()->id]);
                if ($scan_out_inventory->id) {
                    $output = ['success' => true, 'msg' => 'Pending Order created Successfully!'];
                } else {
                    $output = ['success' => false, 'msg' => 'Pending Order not created!'];
                }
            } else {
                // check all skew numbers its all scan in or not & it's  scan out or not
                $scan_in_skew_numbers = '';
                $scan_out_skew_numbers = '';

                foreach ($request->skew_no as $key => $v) {

                    if (empty($v)) {

                        $msg = '<p style="margin:0px!important;">Skew Number is required</p>';
                        return response()->json(['success' => false, 'msg' => $msg]);
                    } else {
                        $rlz_exist = ScanOutInventory::where('release_number', $request->release_number)->first();
                        if ($rlz_exist) {
                            $msg = '<p style="margin:0px!important;">Release Number Already Exist!</p>';
                            return response()->json(['success' => false, 'msg' => $msg]);
                        }
                        // check data is null or not on skew number
                        $checkSkewNumberIsValid = $this->checkSkewNumberIsValid($v);
                        if ($checkSkewNumberIsValid != false) {

                            $scan_out_skew_numbers .= '<p style="margin:0px!important;">' . $checkSkewNumberIsValid . '</p>';
                        } else {

                            // check scan in
                            $is_skew_unique = $this->checkScanInSkewNumber($v);

                            if ($is_skew_unique != true) {

                                $scan_in_skew_numbers .= '<p style="margin:0px!important;">Skew Number: ' . $v . ' is not scan in yet</p>';
                            }

                            // check scan out
                            $is_skew_unique = $this->checkScanOutSkewNumber($v);

                            if ($is_skew_unique == true) {

                                $scan_out_skew_numbers .= '<p style="margin:0px!important;">Skew Number: ' . $v . ' is already scan out</p>';
                            }
                        }
                    }
                }

                if (!empty($scan_in_skew_numbers) || !empty($scan_out_skew_numbers)) {

                    if (!empty($scan_in_skew_numbers)) {

                        $output = ['success' => false, 'msg' => $scan_in_skew_numbers];
                    }

                    if (!empty($scan_out_skew_numbers)) {

                        $output = ['success' => false, 'msg' => $scan_out_skew_numbers];
                    }
                } else {

                    $scan_out_rls_data = [
                        'release_number' => $request->release_number,
                        'customer_id' => $request->customer_id,
                        'warehouse_id' => $request->warehouse_id,
                        'tear_factor' => $request->tear_factor,
                        'container' => $request->container,
                        'seal' => $request->seal,
                        'pallet_weight' => $request->pallet_weight,
                        'tear_factor_weight' => $request->tear_factor_weight,
                        'scale_discrepancy' => $request->scale_discrepancy,
                        'is_order_pending' => $request->is_order_pending != 'yes' ? 0 : 1,
                        'pallet_on_container' => $request->pallet_on_container,
                    ];
                    // insert row into inventory
                    $scan_out_inventory = ScanOutInventory::create($scan_out_rls_data);
                    $order_status = OrderStatus::create(['scan_out_inventory_id' => $scan_out_inventory->id, 'user_id' => auth()->user()->id]);
                    $scan_out_inventory_id = $scan_out_inventory->id;
                    // make array for logs and insert it
                    $scan_out_details_arr = [];
                    foreach ($request->skew_no as $key => $v) {

                        $scan_in_log = ScanInLog::where('skew_number', $v)->first();

                        $scan_out_details_arr[] = [
                            'scan_out_inventory_id' => $scan_out_inventory_id,
                            'scan_in_id' => $scan_in_log->id,
                        ];
                    }


                    if (!empty($scan_out_details_arr)) {

                        ScanOutLog::insert($scan_out_details_arr);

                        $output = ['success' => true, 'msg' => 'Scan Out Successfully'];
                    } else {
                        $output = ['success' => false, 'msg' => 'Skew Number is required'];
                    }
                }
            }
        }

        return response()->json($output);
    }

    public function getSkewValues(Request $request)
    {
        $skew_no = $request->skew_no;
        $parts = explode('.', $skew_no);
        $data = [];
        if ($parts[0]) {
            $data['unit'] =  ucfirst($parts[0]);
        }
        if ($parts[1]) {
            $cgtGrade = $parts[1];
            $data['cgtGrade'] = CGTGrade::where('slug', $cgtGrade)->first(['id', 'grade_name', 'price']);
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

    protected function checkScanInSkewNumber($skew_number)
    {
        $count =  ScanInLog::where('skew_number', $skew_number)->count();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    protected function checkScanOutSkewNumber($skew_number)
    {
        $count = ScanOutLog::Join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')->where('scan_in_logs.skew_number', $skew_number)->count();

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function scanOutLogs(Request $request)
    {

        if ($request->ajax()) {

            $year = $request->year;
            $warehouse_id = $request->warehouse_id;
            $customer_id = $request->customer_id;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $release_number = $request->release_number;
            $cgt = $request->cgt;
            $nt = $request->nt;
            $color = $request->color;

            $scan_out_logs = ScanOutLog::with('scanOutInventory', 'scanOutInventory.getWareHouse', 'scanOutInventory.getCustomers', 'scanInLog', 'scanInLog.sIProductName', 'scanInLog.sICGT', 'scanInLog.sINT', 'scanInLog.sIColor')
                ->when(!empty($year), function ($query) use ($year) {

                    $query->whereHas('scanOutInventory', function ($query) use ($year) {

                        $query->whereYear('created_at', $year);
                    });
                })
                ->when(!empty($warehouse_id), function ($query) use ($warehouse_id) {

                    $query->whereHas('scanOutInventory', function ($query) use ($warehouse_id) {

                        $query->where('warehouse_id', $warehouse_id);
                    });
                })
                ->when(!empty($customer_id), function ($query) use ($customer_id) {

                    $query->whereHas('scanOutInventory.getCustomers', function ($query) use ($customer_id) {

                        $query->where('customer_id', $customer_id);
                    });
                })
                ->when(!empty($release_number), function ($query) use ($release_number) {

                    $query->whereHas('scanOutInventory', function ($query) use ($release_number) {

                        $query->where('release_number', $release_number);
                    });
                })
                ->when(!empty($cgt), function ($query) use ($cgt) {

                    $query->whereHas('scanInLog', function ($query) use ($cgt) {

                        $query->where('cgt', $cgt);
                    });
                })
                ->when(!empty($nt), function ($query) use ($nt) {

                    $query->whereHas('scanInLog', function ($query) use ($nt) {

                        $query->where('nt', $nt);
                    });
                })
                ->when(!empty($color), function ($query) use ($color) {

                    $query->whereHas('scanInLog', function ($query) use ($color) {

                        $query->where('color', $color);
                    });
                })
                ->when(!empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {

                    $query->whereHas('scanOutInventory', function ($query) use ($from_date, $to_date) {

                        $query->whereDate('created_at', '>=', $from_date)
                            ->whereDate('created_at', '<=', $to_date);
                    });
                })
                ->orderBy('scan_out_logs.id', 'desc')->get();

            return Datatables::of($scan_out_logs)
                ->addIndexColumn()
                ->addColumn('scan_out_inv_date', function ($data) {
                    return changeDateFormatToUS($data->scanOutInventory->created_at);
                })
                ->addColumn('scan_out_status', function ($data) {

                    if ($data->is_scan_out == 1) {

                        $html = '<span class="badge rounded-pill bg-success"> <i class="fa fa-check"></i> </span>';
                    } else {

                        $html = '<span class="badge rounded-pill bg-danger"> <i class="fa fa-times"></i> </span>';
                    }

                    return $html;
                })
                ->rawColumns(['scan_out_inv_date', 'scan_out_status'])
                ->make(true);
        }

        $warehouses = Warehouse::orderBy('id', 'desc')->get();
        $customers = Customer::orderby('id', 'desc')->get(['id', 'name']);
        $release_number = ScanOutInventory::orderBy('id', 'desc')->get();
        $cgt = CGTGrade::orderBy('id', 'desc')->get();
        $nt = NTGrade::orderBy('id', 'desc')->get();
        $color = Color::orderBy('id', 'desc')->get();
        return view('admin.modules.scan-out.scanOutLogs', compact('warehouses', 'customers', 'release_number', 'cgt', 'nt', 'color'));
    }

    // public function getLogsByfilters(Request $request)
    // {
    //     $scanOutLogs = ScanOutInventory::with('getScanOutLogs.getCustomer')->with('getScanOutLogs.sOProductName')->with('getScanOutLogs.sOCGT')->with('getScanOutLogs.sONT')
    //     ->with('getScanOutLogs.sOColor')->orderBy('id', 'desc');
    //     $year = $request->year;
    //     $warehouse_id = $request->warehouse;
    //     $from = $request->from;
    //     $to = $request->to;
    //     if (isset($year) && !empty($year)) {
    //         $scanOutLogs->whereYear('created_at', $year);
    //     } else {
    //         $year = '';
    //     }
    //     if (isset($warehouse_id) && !empty($warehouse_id)) {
    //         $scanOutLogs->where('warehouse_id', $warehouse_id);
    //     } else {
    //         $warehouse_id = '';
    //     }
    //     if (isset($from) && !empty($from)) {
    //         if (isset($to) && !empty($to)) {
    //             $scanOutLogs->whereBetween('created_at', [$from, $to]);
    //         } else {
    //             $to = '';
    //         }
    //     } else {
    //         $from = '';
    //     }
    //     $scanOutLogs = $scanOutLogs->get();
    //     return response()->json(['status' => true, 'data' => $scanOutLogs]);
    // }

    public function getappendCustomers()
    {
        $customers = Customer::orderby('id', 'desc')->get(['id', 'name']);
        return response()->json(['status' => true, 'data' => $customers]);
    }
}
