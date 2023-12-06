<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateRequest;
use App\Models\CGTGrade;
use App\Models\Color;
use App\Models\Customer;
use App\Models\NTGrade;
use App\Models\OrderFiles;
use App\Models\OrderQueue;
use App\Models\OrderStatus;
use App\Models\OrderStatusRequirement;
use App\Models\ProductType;
use App\Models\ScanInLog;
use App\Models\ScanOutInventory;
use App\Models\ScanOutLog;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use PDF;

class OrderController extends Controller
{

    public function __construct()
    {
        $route = request()->segment(3);
        if ($route == "active-orders") {
            $customer_id =  request()->segment(4);
            if ($customer_id) {
                $this->middleware('check_order_permission')->except('index');
            } else {
                $this->middleware('check_order_permission');
            }
        } elseif ($route == "edit-order") {
            $customer_id =  request()->segment(5);
            if ($customer_id) {
                $this->middleware('check_order_permission')->except('index', 'editOrder');
            } else {
                $this->middleware('check_order_permission');
            }
        } elseif ($route == 'update-order') {
            $customer_id = request()->segment(5);
            if ($customer_id) {
                $this->middleware('check_order_permission')->except('index', 'updateOrder');
            } else {
                $this->middleware('check_order_permission');
            }
        } elseif ($route == 'delete-skew-number') {
            $this->middleware('check_order_permission')->except('index', 'skewNumberDelete');
        } else {
            $this->middleware('check_order_permission');
        }
    }

    const PENDING = 'pending';
    const PRELOAD = 'preload';
    const SHIPPING_IN_PROCESS = 'shipping_in_process';
    const SHIPPED = 'shipped';
    const POST_LOADING_DOCUMENTATION = 'post_loading_documentation';
    const END_STAGE = 'end_stage';
    const CLOSED = 'closed';
    const CANCELLED = 'cancelled';
    const SCANOUT = 1;
    const NOT_SCANOUT = 0;

    public function index(Request $request, $customer_id = null)
    {
        // if ($request->ajax()) {
        if ($request->ajax()) {
            $orders = ScanOutInventory::with('getCustomers')->with('getWareHouse')->where('is_order_pending', 0)->orderBy('id', 'desc');

            if ($customer_id !== null && $customer_id !== "null") {
                $orders->where('customer_id', $customer_id);
            }
            $orders = $orders->get();
            return Datatables::of($orders)
                ->addIndexColumn()
                ->addColumn('order_status', function ($data) {
                    if ($data->status) {
                        $status_color = $data->status == 'pending' ? 'bg-secondary' : ($data->status == 'preload' ? 'bg-orange' : ($data->status == 'shipping_in_process' ? 'bg-dark-orange' : ($data->status == 'shipped' ? 'bg-blue' : ($data->status == 'post_loading_documentation' ? 'bg-primary' : ($data->status == 'end_stage' ? 'bg-light-green' : ($data->status == 'closed' ? 'bg-success' : ($data->status == 'cancelled' ? 'bg-danger' : 'bg-secondary')))))));

                        $status = $data->status == 'pending' ? 'Pending' : ($data->status == 'preload' ? 'PreLoaded' : ($data->status == 'shipping_in_process' ? 'Shipping In Process' : ($data->status == 'shipped' ? 'Shipped' : ($data->status == 'post_loading_documentation' ? 'Post Loading Documentation' : ($data->status == 'end_stage' ? 'End Stage' : ($data->status == 'closed' ? 'Closed' : ($data->status == 'cancelled' ? 'Cancelled' : '-')))))));
                        $html = '<span class="badge rounded-pill ' . $status_color . '">' . $status . '</span>';
                    } else {

                        $html = '-';
                    }

                    return $html;
                })
                ->addColumn('order_date', function ($data) {

                    if ($data->created_at) {

                        $html = changeDateFormatToUS($data->created_at);
                    } else {

                        $html = '-';
                    }

                    return $html;
                })
                ->addColumn('action', function ($data)  use ($customer_id) {
                    $btn = '';
                    $btn .= '<span class="action-icon-spacing">';
                    $btn .= '<a href="' . route('admin:orders.edit', [$data->id, $customer_id]) . '" title="Edit">
                        <i class="fa fa-edit text-primary"></i>
                    </a>';
                    if ($customer_id == null || $customer_id == "null") {
                        $btn .= '<a href="javascript:void(0);" class="delete" data-id="' . $data->id . '" title="Delete"><i class="fa fa-trash text-danger"></i></a>';
                        $btn .= '<a href="' . route('admin:orderHistory', $data->id) . '" title="show"> <i class="fa fa-eye"></i></a>';
                    }
                    $btn .= '</span>';

                    return $btn;
                })
                ->addColumn('order_pending_status', function ($data) {

                    if ($data->pending_order_complete == 1) {

                        $html = '<span class="badge rounded-pill bg-success"> <i class="fa fa-check"></i> </span>';
                    } else {

                        $html = '<span class="badge rounded-pill bg-danger"> <i class="fa fa-times"></i> </span>';
                    }

                    return $html;
                })
                ->rawColumns(['order_status', 'order_pending_status', 'order_date', 'action'])
                ->make(true);
        }
        return view('admin.modules.orders.order_list', compact('customer_id'));
    }

    //================= Pending order Section ==========================
    public function pendingOrders(Request $request)
    {
        if ($request->ajax()) {

            $orders = ScanOutInventory::where(['is_order_pending' => 1, 'pending_order_complete' => 0])->with('getCustomers')->with('getWareHouse')->orderBy('id', 'desc')->get();
            return Datatables::of($orders)
                ->addIndexColumn()
                ->addColumn('order_status', function ($data) {

                    if ($data->status) {
                        $status_color = $data->status == 'pending' ? 'bg-secondary' : ($data->status == 'preload' ? 'bg-orange' : ($data->status == 'shipping_in_process' ? 'bg-dark-orange' : ($data->status == 'shipped' ? 'bg-blue' : ($data->status == 'post_loading_documentation' ? 'bg-primary' : ($data->status == 'end_stage' ? 'bg-light-green' : ($data->status == 'closed' ? 'bg-success' : ($data->status == 'cancelled' ? 'bg-danger' : 'bg-secondary')))))));

                        $status = $data->status == 'pending' ? 'Pending' : ($data->status == 'preload' ? 'PreLoaded' : ($data->status == 'shipping_in_process' ? 'Shipping In Process' : ($data->status == 'shipped' ? 'Shipped' : ($data->status == 'post_loading_documentation' ? 'Post Loading Documentation' : ($data->status == 'end_stage' ? 'End Stage' : ($data->status == 'closed' ? 'Closed' : ($data->status == 'cancelled' ? 'Cancelled' : '-')))))));
                        $html = '<span class="badge rounded-pill ' . $status_color . '">' . $status . '</span>';
                    } else {

                        $html = '-';
                    }

                    return $html;
                })
                ->addColumn('order_date', function ($data) {

                    if ($data->created_at) {

                        $html = changeDateFormatToUS($data->created_at);
                    } else {

                        $html = '-';
                    }

                    return $html;
                })
                ->addColumn('action', function ($data) {

                    $btn = '';

                    $btn .= '<span class="action-icon-spacing">';
                    $btn .= '<a href="' . route('admin:orders.edit', [$data->id]) . '" title="Edit">
                        <i class="fa fa-edit text-primary"></i>
                    </a>';
                    $btn .= '<a href="' . route('admin:pendingOrder.queue', [$data->id]) . '" title="Order Queue">
                    <i class="fa fa-file text-primary"></i>
                    </a>';
                    $btn .= '<a href="javascript:void(0);" class="delete" data-id="' . $data->id . '" title="Delete"><i class="fa fa-trash text-danger"></i></a>';
                    $btn .= '<a href="' . route('admin:orderHistory', $data->id) . '" title="show"> <i class="fa fa-eye"></i></a>';
                    $btn .= '</span>';

                    return $btn;
                })
                ->rawColumns(['order_status', 'order_date', 'action'])
                ->make(true);
        }

        return view('admin.modules.orders.pending_order.index');
    }


    public function pendingOrderQueue($id)
    {
        $order_history = ScanOutInventory::where('id', $id)->with('getCustomers')->with('getWareHouse')->with('allOrderStatuses.getUser')->with('OrderFiles')->with('getScanOutLogs.scanInLog')->orderBy('id', 'desc')->first();
        $color_data = $this->getDataByColor($order_history->id);
        $queue_data_exist = OrderQueue::where('order_id', $id)->first();
        return view('admin.modules.orders.pending_order.pending_order_queue', compact('order_history', 'color_data', 'queue_data_exist'));
    }

    protected function getDataByColor($order_id)
    {
        $color_grade_group_data = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
            ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
            ->join('n_t_grades as nt_grades', 'scan_in_logs.nt', '=', 'nt_grades.id')
            ->where('scan_in_logs.is_scan_out', 0);

        $color_grade_group_data->groupBy('colors.id')
            ->select('colors.id as color_id', 'colors.name as color_name', DB::raw('SUM(scan_in_logs.weight) as total_weight'))
            ->havingRaw('total_weight > 0'); // Exclude colors with a total weight of 0

        // Execute the query and return the results    

        $nt_grade_group_data = $order_queue_data = [];
        $color_grade_group_data =  $color_grade_group_data->get();
        foreach ($color_grade_group_data as $color_key => $color) {
            $nt_group_result = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
                ->join('n_t_grades as nt_grades', 'scan_in_logs.nt', '=', 'nt_grades.id')
                ->where('scan_in_logs.is_scan_out', 0)
                ->where('scan_in_logs.color', $color->color_id)
                ->where('scan_in_logs.weight', '>', 0); // Add condition to filter out records with weight equal to 0

            // Group by CGT and NT, and select the relevant fields
            $nt_group_result->groupBy('nt_grades.id')
                ->select('nt_grades.id as nt_id', 'nt_grades.grade_name as nt_grade_name', DB::raw('SUM(scan_in_logs.weight) as total_weight'));

            // Execute the query and return the results
            $nt_group_result = $nt_group_result->get();
            $nt_grade_group_data[] = $nt_group_result;

            foreach ($nt_group_result as $nt_key => $nt) {
                $sum_of_order_column = $this->getSumOforderColumn($color->color_id, $nt->nt_id);
                $Order_column_value = $this->getOrderColumnValue($order_id, $color->color_id, $nt->nt_id);
                $order_queue_data[$color->color_id . '-' . $nt->nt_id] = ['sum_of_order_column' => $sum_of_order_column, 'Order_column_value' => $Order_column_value];
            }
        }
        $data = [
            'color_grade_group_data' => $color_grade_group_data,
            'nt_grade_group_data' => $nt_grade_group_data,
            'order_queue_data' => $order_queue_data
        ];

        return $data;
    }

    public function queueDataSubmit(Request $request, $order_id)
    {
        foreach ($request->color_id as $key => $color) {
            $nt = $request->nt_id[$key];
            $val = $request->order_column[$key];

            $data = [
                'order_id' => $order_id,
                'color_id' => $color,
                'nt_id' => $nt,
                'order_column' => isset($val) && !empty($val) ? $val : 0,
            ];

            OrderQueue::create($data);
        }
        return redirect()->back()->with('success', 'Data Submitted successfully');
    }

    public function getOrderColumnValue($order_id, $color_id, $nt_id)
    {
        return $data = OrderQueue::where(['order_id' => $order_id, 'color_id' => $color_id, 'nt_id' => $nt_id])->first();
    }

    public function getSumOforderColumn($color_id, $nt_id)
    {
        return $data = OrderQueue::where(['color_id' => $color_id, 'nt_id' => $nt_id])->sum('order_column');
    }

    public function generatePDF($id)
    {
        $order_data = ScanOutInventory::where('id', $id)->with('getCustomers')->with('getWareHouse')->with('allOrderStatuses.getUser')->with('OrderFiles')->with('getScanOutLogs.scanInLog')->orderBy('id', 'desc')->first();
        $color_data = $this->getDataByColor($order_data->id);
        $queue_data_exist = OrderQueue::where('order_id', $id)->first();

        $data = [
            'order_data' => $order_data,
            'color_data' => $color_data,
            'queue_data_exist' => $queue_data_exist,
            'title' => 'Pending Order Queue'
        ];
        $path = public_path('storage/images/orderFiles/');

        if (!File::exists($path)) {
            File::makeDirectory($path);
        } else {
        }
        $file_name = $order_data->release_number . '.pdf';
        $pdf = PDF::loadView('admin.modules.orders.pending_order.pending_order_pdf', $data)->save('' . $path . '/' . $file_name);

        // Create a new OrderFiles object for the PDF
        $orderFile = new OrderFiles();
        $orderFile->scan_out_inventory_id = $order_data->id;
        $orderFile->file_name = $file_name;
        $orderFile->save();

        // return $pdf->stream($order_data->release_number . '.pdf');
        return $pdf->download($file_name);
    }
    //================= End Pending order Section ==========================

    protected function OrderScanOut($scan_out_inventory_id)
    {
        if ($scan_out_inventory_id) {
            $scan_in_ids = ScanOutLog::where('scan_out_inventory_id', $scan_out_inventory_id)->pluck('scan_in_id');
            if ($scan_in_ids) {
                ScanInLog::whereIn('id', $scan_in_ids)->update(['is_scan_out' => self::SCANOUT]);
            }
        }
    }

    protected function OrderScanIn($scan_out_inventory_id)
    {
        if ($scan_out_inventory_id) {
            $scan_in_ids = ScanOutLog::where('scan_out_inventory_id', $scan_out_inventory_id)->pluck('scan_in_id');
            if ($scan_in_ids) {
                ScanInLog::whereIn('id', $scan_in_ids)->update(['is_scan_out' => self::NOT_SCANOUT]);
            }
        }
    }

    public function orderHistory($id)
    {
        $order_history = ScanOutInventory::where('id', $id)->with('getCustomers')->with('getWareHouse')->with('allOrderStatuses.getUser')->with('OrderFiles')->with('getScanOutLogs.scanInLog')->orderBy('id', 'desc')->first();
        $order_skew_number = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
            ->join('n_t_grades', 'n_t_grades.id', '=', 'scan_in_logs.nt')
            ->join('product_types', 'product_types.id', '=', 'scan_in_logs.product_type')
            ->join('customers', 'customers.id', '=', 'scan_out_inventories.customer_id')
            ->join('colors', 'colors.id', '=', 'scan_in_logs.color')->where('scan_out_inventories.id', $id)->select('scan_in_logs.id as skew_id', 'scan_in_logs.skew_number', 'customers.name as customer_name', 'product_types.product_type', 'c_g_t_grades.grade_name as cgt_grade', 'n_t_grades.grade_name as nt_grade', 'colors.name as color_name', 'scan_in_logs.weight as weight', 'scan_in_logs.yards as yards')->get();
        $total_records = ScanOutLog::where('scan_out_inventory_id', $id)->count('id');
        $order_status = [
            ['status' => self::PENDING],
            ['status' => self::PRELOAD],
            ['status' => self::SHIPPING_IN_PROCESS],
            ['status' => self::SHIPPED],
            ['status' => self::POST_LOADING_DOCUMENTATION],
            ['status' => self::END_STAGE],
            ['status' => self::CLOSED],
            ['status' => self::CANCELLED],
        ];
        $order_status_requirements = [];
        $order_status_requirements['preload'] = OrderStatusRequirement::where(['order_id' => $id, 'order_status' => self::PRELOAD])->first();
        $order_status_requirements['shipping_in_process'] = OrderStatusRequirement::where(['order_id' => $id, 'order_status' => self::SHIPPING_IN_PROCESS])->first();
        $order_status_requirements['shipped'] = OrderStatusRequirement::where(['order_id' => $id, 'order_status' => self::SHIPPED])->first();
        $order_status_requirements['post_loading_documentation'] = OrderStatusRequirement::where(['order_id' => $id, 'order_status' => self::POST_LOADING_DOCUMENTATION])->first();
        $order_status_requirements['end_stage'] = OrderStatusRequirement::where(['order_id' => $id, 'order_status' => self::END_STAGE])->first();
        $order_status_requirements['closed'] = OrderStatusRequirement::where(['order_id' => $id, 'order_status' => self::CLOSED])->first();
        $order_status_recent_record = OrderStatus::where('scan_out_inventory_id', $id)->orderBy('id', 'desc')->first();

        $nt_grades_prices = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
            ->join('n_t_grades', 'n_t_grades.id', '=', 'scan_in_logs.nt')
            ->join('product_types', 'product_types.id', '=', 'scan_in_logs.product_type')
            ->join('customers', 'customers.id', '=', 'scan_out_inventories.customer_id')
            ->join('colors', 'colors.id', '=', 'scan_in_logs.color')
            ->where('scan_out_inventories.id', $id)
            ->groupBy('scan_in_logs.nt')
            ->select('scan_in_logs.nt as nt_id', 'n_t_grades.grade_name as nt_grade', 'scan_out_logs.price', 'scan_out_logs.third_party_price')->get();

        return view('admin.modules.orders.order_history', compact('order_history', 'total_records', 'order_skew_number', 'order_status', 'order_status_recent_record', 'nt_grades_prices', 'order_status_requirements'));
    }

    public function updateOrderScanStatus(Request $request)
    {
        $scan_out_inventory_id = $request->scan_out_inventory_id;
        $orderStatus = $request->order_status;
        if ($orderStatus != 0) {
            if ($request->previous_status == $orderStatus) {
                return back()->with('error', 'No Changed occured!');
            }
            if ($orderStatus == self::PRELOAD) {
                $found = OrderStatusRequirement::where(['order_id' => $scan_out_inventory_id, 'order_status' => self::PRELOAD])->first();
                if (!$found) {
                    return back()->with('error', 'First Fill all the Requirements For this Status!');
                }
            } elseif ($orderStatus == self::SHIPPING_IN_PROCESS) {
                $found = OrderStatusRequirement::where(['order_id' => $scan_out_inventory_id, 'order_status' => self::SHIPPING_IN_PROCESS])->first();
                if (!$found) {
                    return back()->with('error', 'First Fill all the Requirements For this Status!');
                }
            } elseif ($orderStatus == self::SHIPPED) {
                $found = OrderStatusRequirement::where(['order_id' => $scan_out_inventory_id, 'order_status' => self::SHIPPED])->first();
                if (!$found) {
                    return back()->with('error', 'First Fill all the Requirements For this Status!');
                }
                $is_scanned = ScanOutLog::where('scan_out_inventory_id', $scan_out_inventory_id)->count();
                if ($is_scanned == 0) {
                    return back()->with('error', 'Atleast One Skew should be scanned out!');
                }
            } elseif ($orderStatus == self::POST_LOADING_DOCUMENTATION) {
                $found = OrderStatusRequirement::where(['order_id' => $scan_out_inventory_id, 'order_status' => self::POST_LOADING_DOCUMENTATION])->first();
                if (!$found) {
                    return back()->with('error', 'First Fill all the Requirements For this Status!');
                }
            } elseif ($orderStatus == self::END_STAGE) {
                $found = OrderStatusRequirement::where(['order_id' => $scan_out_inventory_id, 'order_status' => self::END_STAGE])->first();
                if (!$found) {
                    return back()->with('error', 'First Fill all the Requirements For this Status!');
                }
            } elseif ($orderStatus == self::CLOSED) {
                $found = OrderStatusRequirement::where(['order_id' => $scan_out_inventory_id, 'order_status' => self::CLOSED])->first();
                if (!$found) {
                    return back()->with('error', 'First Fill all the Requirements For this Status!');
                }
            }
            $data = $this->addOrderStatus($request);
            ScanOutInventory::where('id', $scan_out_inventory_id)->update(['status' => $orderStatus]);
            $scanOutInventory = ScanOutInventory::find($scan_out_inventory_id);
            $order = OrderStatus::create($data);
            $last_id = $order->id;
            $order->save($data);
            if ($orderStatus == self::SHIPPED) {
                if ($scanOutInventory->is_order_pending == 1) {
                    ScanOutInventory::where('id', $scan_out_inventory_id)->update(['is_order_pending' => 0, 'pending_order_complete' => 1]);
                }
                $this->OrderScanOut($scan_out_inventory_id);
            }
            if ($orderStatus == self::PENDING  || $orderStatus ==  self::PRELOAD || $orderStatus ==  self::SHIPPING_IN_PROCESS) {
                $this->OrderScanIn($scan_out_inventory_id);
            }
            return back()->with('success', 'Status Updated Successfully!');
        } else {
            return back()->with('error', 'Please Select Status!');
        }
    }

    protected function addOrderStatus($request)
    {
        $scan_out_inventory_id = $request->scan_out_inventory_id;
        $orderStatus = $request->order_status;
        $deposit_received = $request->deposit_received;
        $rate_received = $request->rate_received;
        $rate_approved = $request->rate_approved;
        $rate_quote = $request->rate_quote;
        $acid_received = $request->acid_received;
        $acid_number = $request->acid_number;
        $booking_completed = $request->booking_completed;
        $erd = $request->erd;
        $sailing_date = $request->sailing_date;
        $arrival_date = $request->arrival_date;
        $truker_name = $request->truker_name;
        $trucker_quote = $request->trucker_quote;
        $load_date = $request->load_date;
        $item_shipped = $request->item_shipped;
        $pre_shipped = $request->pre_shipped;
        $preliminary_doc = $request->preliminary_doc;
        $release_notes = $request->release_notes;
        $shipment_loaded = $request->shipment_loaded;
        $final_shipping_doc = $request->final_shipping_doc;
        $nextpac_report = $request->nextpac_report;
        $ktc_report = $request->ktc_report;
        $cus_paperwork_completed = $request->cus_paperwork_completed;
        $nextrade_invoicing = $request->nextrade_invoicing;
        $obselete_report = $request->obselete_report;
        $final_payment_received = $request->final_payment_received;
        $final_bl_draft = $request->final_bl_draft;
        $release_requested = $request->release_requested;
        $bl_requested = $request->bl_requested;
        $final_doc_to_bank = $request->final_doc_to_bank;
        $final_doc_to_customer = $request->final_doc_to_customer;
        $final_doc_to_cargoX = $request->final_doc_to_cargoX;
        $ff_invoice = $request->ff_invoice;
        $ff_paid = $request->ff_paid;
        $ff_date_paid = $request->ff_date_paid;
        $trucker_invoice = $request->trucker_invoice;
        $trucker_paid = $request->trucker_paid;
        $trucker_date = $request->trucker_date;
        $user_id = $request->user_id;
        return $data = [
            'scan_out_inventory_id' => $scan_out_inventory_id,
            'previous_status' => $request->previous_status,
            'changed_to' => $orderStatus,
            'deposit_received' => (isset($deposit_received) &&  $deposit_received == 'on') ? 1 : 0,
            'rate_received' => (isset($rate_received) && $rate_received == 'on') ? 1 : 0,
            'rate_approved' => (isset($rate_approved) && $rate_approved == 'on') ? 1 : 0,
            'rate_quote' => (isset($rate_quote) && $rate_quote == 'on') ? 1 : 0,
            'acid_received' => (isset($acid_received) && $acid_received == 'on') ? 1 : 0,
            'acid_number' => (isset($acid_number) && $acid_number == 'on') ? 1 : 0,
            'booking_completed' => (isset($booking_completed) && $booking_completed == 'on') ? 1 : 0,
            'erd' => (isset($erd) && $erd == 'on') ? 1 : 0,
            'sailing_date' => (isset($sailing_date) && $sailing_date == 'on') ? 1 : 0,
            'arrival_date' => (isset($arrival_date) && $arrival_date == 'on') ? 1 : 0,
            'truker_name' => (isset($truker_name) && $truker_name == 'on') ? 1 : 0,
            'trucker_quote' => (isset($trucker_quote) && $trucker_quote == 'on') ? 1 : 0,
            'load_date' => (isset($load_date) && $load_date == 'on') ? 1 : 0,
            'item_shipped' => (isset($item_shipped) && $item_shipped == 'on') ? 1 : 0,
            'pre_shipped' => (isset($pre_shipped) && $pre_shipped == 'on') ? 1 : 0,
            'preliminary_doc' => (isset($preliminary_doc) && $preliminary_doc == 'on') ? 1 : 0,
            'release_notes' => (isset($release_notes) && $release_notes == 'on') ? 1 : 0,
            'shipment_loaded' => (isset($shipment_loaded) && $shipment_loaded == 'on') ? 1 : 0,
            'final_shipping_doc' => (isset($final_shipping_doc) && $final_shipping_doc == 'on') ? 1 : 0,
            'nextpac_report' => (isset($nextpac_report) && $nextpac_report == 'on') ? 1 : 0,
            'ktc_report' => (isset($ktc_report) && $ktc_report == 'on') ? 1 : 0,
            'cus_paperwork_completed' => (isset($cus_paperwork_completed) && $cus_paperwork_completed == 'on') ? 1 : 0,
            'nextrade_invoicing' => (isset($nextrade_invoicing) && $nextrade_invoicing == 'on') ? 1 : 0,
            'obselete_report' => (isset($obselete_report) && $obselete_report == 'on') ? 1 : 0,
            'final_payment_received' => (isset($final_payment_received) && $final_payment_received == 'on') ? 1 : 0,
            'final_bl_draft' => (isset($final_bl_draft) && $final_bl_draft == 'on') ? 1 : 0,
            'release_requested' => (isset($release_requested) && $release_requested == 'on') ? 1 : 0,
            'bl_requested' => (isset($bl_requested) && $bl_requested == 'on') ? 1 : 0,
            'final_doc_to_bank' => (isset($final_doc_to_bank) && $final_doc_to_bank == 'on') ? 1 : 0,
            'final_doc_to_customer' => (isset($final_doc_to_customer) && $final_doc_to_customer == 'on') ? 1 : 0,
            'final_doc_to_cargoX' => (isset($final_doc_to_cargoX) && $final_doc_to_cargoX == 'on') ? 1 : 0,
            'ff_invoice' => (isset($ff_invoice) && $ff_invoice == 'on') ? 1 : 0,
            'ff_paid' => (isset($ff_paid) && $ff_paid == 'on') ? 1 : 0,
            'ff_date_paid' => (isset($ff_date_paid) && $ff_date_paid == 'on') ? 1 : 0,
            'trucker_invoice' => (isset($trucker_invoice) && $trucker_invoice == 'on') ? 1 : 0,
            'trucker_paid' => (isset($trucker_paid) && $trucker_paid == 'on') ? 1 : 0,
            'trucker_date' => (isset($trucker_date) && $trucker_date == 'on') ? 1 : 0,
            'user_id' => $user_id
        ];
    }

    public function updateOrderDocuments(Request $request)
    {
        if ($request->hasFile('images')) {
            $this->saveOrderFiles($request);
        }
        return back()->with('success', 'Documents Uploaded Successfully!');
    }
    protected function saveOrderFiles($request)
    {
        $order = new OrderFiles();
        $order->scan_out_inventory_id = $request->scan_out_inventory_id;
        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                // dd($image->getClientOriginalName());
                $file_name = $image->getClientOriginalName();
                $image->move(public_path('storage/images/orderFiles/'), $file_name);
                // $file_name = url('/') . '/' . 'storage/images/orderFiles/' . $file_name;
                // Create a new OrderFiles object for each image
                $order = new OrderFiles();
                $order->scan_out_inventory_id = $request->scan_out_inventory_id;
                $order->file_name = $file_name;
                $order->save();
            }
        }
    }

    public function newScanOut(Request $request)
    {
        $scan_out_inventory_id = $request->scan_out_inventory_id;
        // check all skew numbers its all scan in or not & it's  scan out or not
        $scan_in_skew_numbers = '';
        $scan_out_skew_numbers = '';
        foreach ($request->skew_no as $key => $v) {

            if (empty($v)) {

                $msg = '<p style="margin:0px!important;">Skew Number is required</p>';
                return response()->json(['success' => false, 'msg' => $msg]);
            } else {
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


    public function editOrder($id, $customer_id = NUll)
    {
        $order_history = ScanOutInventory::find($id);
        $customers = Customer::orderby('id', 'desc')->get();
        $warehouses = Warehouse::orderby('id', 'desc')->get();
        $order_skew_number = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
            ->join('n_t_grades', 'n_t_grades.id', '=', 'scan_in_logs.nt')
            ->join('product_types', 'product_types.id', '=', 'scan_in_logs.product_type')
            ->join('customers', 'customers.id', '=', 'scan_out_inventories.customer_id')
            ->join('colors', 'colors.id', '=', 'scan_in_logs.color')->where('scan_out_inventories.id', $id)->select('scan_in_logs.id as skew_id', 'scan_in_logs.skew_number', 'customers.name as customer_name', 'product_types.product_type', 'c_g_t_grades.grade_name as cgt_grade', 'n_t_grades.grade_name as nt_grade', 'colors.name as color_name', 'scan_in_logs.weight as weight', 'scan_in_logs.yards as yards')->get();
        $total_records = ScanOutLog::where('scan_out_inventory_id', $id)->count('id');

        $nt_grades_prices = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
            ->join('n_t_grades', 'n_t_grades.id', '=', 'scan_in_logs.nt')
            ->join('product_types', 'product_types.id', '=', 'scan_in_logs.product_type')
            ->join('customers', 'customers.id', '=', 'scan_out_inventories.customer_id')
            ->join('colors', 'colors.id', '=', 'scan_in_logs.color')
            ->where('scan_out_inventories.id', $id)
            ->groupBy('scan_in_logs.nt')
            ->select('scan_in_logs.nt as nt_id', 'n_t_grades.grade_name as nt_grade', 'scan_in_logs.cgt as cgt_id', 'c_g_t_grades.grade_name as cgt_grade', 'scan_out_logs.price', 'scan_out_logs.third_party_price')->get();
        return view('admin.modules.orders.edit', compact('order_history', 'total_records', 'order_skew_number', 'customers', 'warehouses', 'nt_grades_prices', 'customer_id'));
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


    public function updateOrder(UpdateRequest $request)
    {
        $id = $request->id;
        $customer_id = $request->customer_order;
        $orderDetails = $request->validated();

        $result = ScanOutInventory::where('id', $id)->update($orderDetails);
        $data = ScanOutInventory::where('id', $id)->first();
        // update nt wise price and third party prices
        if (isset($request->cgt_id) && !empty($request->cgt_id)) {
            foreach ($request->cgt_id as $key => $v) {

                $new_price = $request->cgt_price[$key];
                ScanOutLog::join('scan_in_logs', 'scan_out_logs.scan_in_id', '=', 'scan_in_logs.id')
                    ->where([
                        'scan_out_logs.scan_out_inventory_id' => $id,
                        'scan_in_logs.cgt' => $v
                    ])->update([
                        'scan_out_logs.price' => $new_price,
                    ]);
            }
        }
        if (isset($request->nt_id) && !empty($request->nt_id)) {
            foreach ($request->nt_id as $key => $v) {
                $new_third_party_price = $request->third_party_price[$key];
                ScanOutLog::join('scan_in_logs', 'scan_out_logs.scan_in_id', '=', 'scan_in_logs.id')
                    ->where([
                        'scan_out_logs.scan_out_inventory_id' => $id,
                        'scan_in_logs.nt' => $v
                    ])->update([
                        'scan_out_logs.third_party_price' => $new_third_party_price
                    ]);
            }
        }


        if (!$result) {
            return back()->with('error', 'Something went wrong, try again!');
        }
        if ($data->is_order_pending == 1) {
            return redirect()->route('admin:pendingOrders')->with('success', 'The Order updated successfully.');
        } else {
            return redirect()->route('admin:orders', [$customer_id ?? ''])->with('success', 'The Order updated successfully.');
        }
    }


    public function skewNumberDelete(Request $request)
    {
        $scan_in_id = $request->scan_in_id;
        $scan_out_inventory_id = $request->scan_out_inventory_id;
        $total_records = $request->total_records;
        $result = ScanOutLog::where(['scan_out_inventory_id' => $scan_out_inventory_id, 'scan_in_id' => $scan_in_id])->first();
        if ($result) {
            ScanInLog::where('id', $scan_in_id)->update(['is_scan_out' => 0]);
            $data = ScanOutInventory::where('id', $scan_out_inventory_id)->first();
            $result->delete();
            if ($total_records == 1) {
                $order = ScanOutInventory::where('id', $scan_out_inventory_id)->delete();
                return response()->json(['status' => true, 'order_delete' => true, 'message' => 'Skew Number and Order deleted successfully'], 200);
            } else {
                return response()->json(['status' => true, 'order_delete' => false, 'message' => 'Skew Number deleted successfully'], 200);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong! try again'], 404);
        }
    }

    public function destroy(Request $request)
    {
        $scan_out_inventory_id = $request->scan_out_inventory_id;
        $scan_in_ids = ScanOutLog::where('scan_out_inventory_id', $scan_out_inventory_id)->pluck('scan_in_id');
        ScanInLog::whereIn('id', $scan_in_ids)->update(['is_scan_out' => 0]);
        ScanOutLog::where('scan_out_inventory_id', $scan_out_inventory_id)->delete();
        $result = ScanOutInventory::where('id', $scan_out_inventory_id)->delete();
        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Something went wrong! try again'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Order deleted successfully'], 200);
    }

    //Orders Statuses Submissions

    public function preloadStatusSubmission(Request $request)
    {
        if ($request->order_current_status == 'closed') {
            return response()->json(['status' => false, 'errors' => "Order Status can't change after Closed"], 422);
        }
        if ($request->order_current_status == 'cancelled') {
            return response()->json(['status' => false, 'errors' => "Order Status can't change after Cancelled"], 422);
        }
        $data = [
            'order_id' => $request->order_id,
            'order_status' => $request->order_status,
            'deposit_received' => $request->deposit_received,
            'deposit_amount' => $request->deposit_amount
        ];

        $query = OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->first();

        if (!empty($query->deposit_received) && !empty($query->deposit_amount)) {
            $validator = Validator::make($request->all(), [
                'deposit_received' => 'required',
                'deposit_amount' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                // Validation failed
                $errors = $validator->errors()->toArray();
                return response()->json(['status' => false, 'errors' => $errors], 422);
            }
        }

        // Check if all fields are filled
        if ($request->filled('deposit_received', 'deposit_amount')) {
            // Perform additional checks if required
            if ($query) {
                OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->update($data);

                // Perform operations when all fields are filled
                $data2 = new OrderStatus();
                $data2->deposit_received = 'on';
                $data2->deposit_amount = 'on';
                $data2->scan_out_inventory_id = $request->order_id;
                $data2->order_status = 'preload';
                $previous_status_record = OrderStatus::where('scan_out_inventory_id', $request->order_id)->latest()->first();
                $data2->previous_status = $previous_status_record->changed_to ?? 'pending';
                $data2->user_id = auth()->user()->id;
                $result = $this->addOrderStatus($data2);
                ScanOutInventory::where('id', $request->order_id)->update(['status' => 'preload']);
                if ($previous_status_record->changed_to != 'preload') {
                    $order = OrderStatus::create($result);
                    $last_id = $order->id;
                }
                return response()->json(['status' => true, 'message' => 'Status Updated Successfully'], 200);
            } else {
                $order_req = OrderStatusRequirement::create($data);
                // Perform operations when all fields are filled
                $data2 = new OrderStatus();
                $data2->deposit_received = 'on';
                $data2->deposit_amount = 'on';
                $data2->scan_out_inventory_id = $request->order_id;
                $data2->order_status = 'preload';
                $previous_status_record = OrderStatus::where('scan_out_inventory_id', $request->order_id)->latest()->first();
                $data2->previous_status = $previous_status_record->changed_to ?? 'pending';
                $data2->user_id = auth()->user()->id;
                $result = $this->addOrderStatus($data2);
                ScanOutInventory::where('id', $request->order_id)->update(['status' => 'preload']);
                $order = OrderStatus::create($result);
                $last_id = $order->id;
                return response()->json(['status' => true, 'message' => 'Status Updated Successfully'], 200);
            }
        } else {
            // Fields are not filled completely
            if ($request->filled('deposit_received') || $request->filled('deposit_amount')) {
                if ($query) {
                    OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->update($data);
                } else {
                    $order_req = OrderStatusRequirement::create($data);
                }
                return response()->json(['status' => true, 'message' => 'Data submitted Successfully'], 200);
            } else {
                return response()->json(['status' => false, 'errors' => 'Anyone Field should be filled!'], 422);
            }
        }
    }

    public function shippingInProcessStatusSubmission(Request $request)
    {
        if ($request->order_current_status == 'closed') {
            return response()->json(['status' => false, 'errors' => "Order Status can't change after Closed"], 422);
        }
        if ($request->order_current_status == 'cancelled') {
            return response()->json(['status' => false, 'errors' => "Order Status can't change after Cancelled"], 422);
        }
        $data = [
            'order_id' => $request->order_id,
            'order_status' => $request->order_status,
            'freight_forwarder' => $request->freight_forwarder,
            'best_rate_received' => $request->best_rate_received,
            'shipping_line' => $request->shipping_line,
            'acid_received' => $request->acid_received,
            'acid_number' => $request->acid_number,
            'booking_completed' => $request->booking_completed,
            'erd' => $request->erd,
            'sailing_date' => $request->sailing_date,
            'truker_name' => $request->truker_name,
            'trucker_quote' => $request->trucker_quote,
            'load_date' => $request->load_date,
            'release_notes' => $request->release_notes,
            'pre_shipping_docs' => $request->pre_shipping_docs,
        ];

        $query = OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->first();

        if (!empty($query->freight_forwarder) && !empty($query->freight_forwarder) && !empty($query->shipping_line) && !empty($query->acid_received) && !empty($query->acid_number) && !empty($query->booking_completed) && !empty($query->erd) && !empty($query->sailing_date) && !empty($query->truker_name) && !empty($query->trucker_quote) && !empty($query->load_date) && !empty($query->release_notes) && !empty($query->pre_shipping_docs)) {
            $validator = Validator::make($request->all(), [
                'freight_forwarder' => 'required',
                'best_rate_received' => 'required',
                'shipping_line' => 'required',
                'acid_received' => 'required',
                'acid_number' => 'required',
                'booking_completed' => 'required',
                'erd' => 'required',
                'sailing_date' => 'required',
                'truker_name' => 'required',
                'trucker_quote' => 'required',
                'load_date' => 'required',
                'release_notes' => 'required',
                'pre_shipping_docs' => 'required',
            ]);

            if ($validator->fails()) {
                // Validation failed
                $errors = $validator->errors()->toArray();
                return response()->json(['status' => false, 'errors' => $errors], 422);
            }
        }

        // Check if all fields are filled
        if ($request->filled('order_id', 'order_status', 'freight_forwarder', 'best_rate_received', 'shipping_line', 'acid_received', 'acid_number', 'booking_completed', 'erd', 'sailing_date', 'truker_name', 'trucker_quote', 'load_date', 'release_notes', 'pre_shipping_docs')) {
            // Perform additional checks if required
            if ($query) {
                OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->update($data);

                // Perform operations when all fields are filled
                $data2 = new OrderStatus();
                $data2->rate_received = 'on';
                $data2->rate_approved = 'on';
                $data2->rate_quote = 'on';
                $data2->acid_received = 'on';
                $data2->acid_number = 'on';
                $data2->booking_completed = 'on';
                $data2->erd = 'on';
                $data2->sailing_date = 'on';
                $data2->arrival_date = 'on';
                $data2->truker_name = 'on';
                $data2->trucker_quote = 'on';
                $data2->load_date = 'on';
                $data2->scan_out_inventory_id = $request->order_id;
                $data2->order_status = 'shipping_in_process';
                $previous_status_record = OrderStatus::where('scan_out_inventory_id', $request->order_id)->latest()->first();
                $data2->previous_status = $previous_status_record->changed_to ?? 'pending';
                $data2->user_id = auth()->user()->id;
                $result = $this->addOrderStatus($data2);
                if ($previous_status_record->changed_to != 'shipping_in_process') {
                    $order = OrderStatus::create($result);
                    $last_id = $order->id;
                    $order->save($result);
                }
                ScanOutInventory::where('id', $request->order_id)->update(['status' => 'shipping_in_process']);
                return response()->json(['status' => true, 'message' => 'Status Updated Successfully'], 200);
            } else {
                $order_req = OrderStatusRequirement::create($data);
                // Perform operations when all fields are filled
                $data2 = new OrderStatus();
                $data2->rate_received = 'on';
                $data2->rate_approved = 'on';
                $data2->rate_quote = 'on';
                $data2->acid_received = 'on';
                $data2->acid_number = 'on';
                $data2->booking_completed = 'on';
                $data2->erd = 'on';
                $data2->sailing_date = 'on';
                $data2->arrival_date = 'on';
                $data2->truker_name = 'on';
                $data2->trucker_quote = 'on';
                $data2->load_date = 'on';
                $data2->scan_out_inventory_id = $request->order_id;
                $data2->order_status = 'shipping_in_process';
                $previous_status_record = OrderStatus::where('scan_out_inventory_id', $request->order_id)->latest()->first();
                $data2->previous_status = $previous_status_record->changed_to ?? 'pending';
                $data2->user_id = auth()->user()->id;
                $result = $this->addOrderStatus($data2);
                ScanOutInventory::where('id', $request->order_id)->update(['status' => 'shipping_in_process']);
                $order = OrderStatus::create($result);
                $last_id = $order->id;
                $order->save($result);
                return response()->json(['status' => true, 'message' => 'Status Updated Successfully'], 200);
            }
        } else {
            // Fields are not filled completely
            if ($request->filled('freight_forwarder') || $request->filled('best_rate_received') || $request->filled('shipping_line') || $request->filled('acid_received') || $request->filled('acid_number') || $request->filled('booking_completed') || $request->filled('erd') || $request->filled('sailing_date') || $request->filled('truker_name') || $request->filled('trucker_quote') || $request->filled('load_date') || $request->filled('release_notes') || $request->filled('pre_shipping_docs')) {

                if ($query) {
                    OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->update($data);
                } else {
                    $order_req = OrderStatusRequirement::create($data);
                }
                return response()->json(['status' => true, 'message' => 'Data submitted Successfully'], 200);
            } else {
                return response()->json(['status' => false, 'errors' => 'Anyone Field should be filled!'], 422);
            }
        }
    }

    public function shippedStatusSubmission(Request $request)
    {
        if ($request->order_current_status == 'closed') {
            return response()->json(['status' => false, 'errors' => "Order Status can't change after Closed"], 422);
        }
        if ($request->order_current_status == 'cancelled') {
            return response()->json(['status' => false, 'errors' => "Order Status can't change after Cancelled"], 422);
        }
        $data = [
            'order_id' => $request->order_id,
            'order_status' => $request->order_status,
            'item_shipped_scanned_out' => $request->item_shipped_scanned_out
        ];

        $query = OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->first();

        if (!empty($query->item_shipped_scanned_out)) {
            $validator = Validator::make($request->all(), [
                'item_shipped_scanned_out' => 'required',
            ]);

            if ($validator->fails()) {
                // Validation failed
                $errors = $validator->errors()->toArray();
                return response()->json(['status' => false, 'errors' => $errors['item_shipped_scanned_out'][0]], 422);
            }
        }

        // Check if all fields are filled
        if ($request->filled('item_shipped_scanned_out')) {
            // Perform additional checks if required
            if ($query) {
                $preload = OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => self::PRELOAD])->first();
                if ($preload) {
                    if (!empty($preload->deposit_received) && !empty($preload->deposit_amount)) {
                        $shipping_in_process = OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => self::SHIPPING_IN_PROCESS])->first();
                        if ($shipping_in_process) {
                            if (!empty($shipping_in_process->freight_forwarder) && !empty($shipping_in_process->freight_forwarder) && !empty($shipping_in_process->shipping_line) && !empty($shipping_in_process->acid_received) && !empty($shipping_in_process->acid_number) && !empty($shipping_in_process->booking_completed) && !empty($shipping_in_process->erd) && !empty($shipping_in_process->sailing_date) && !empty($shipping_in_process->truker_name) && !empty($shipping_in_process->trucker_quote) && !empty($shipping_in_process->load_date) && !empty($shipping_in_process->release_notes) && !empty($shipping_in_process->pre_shipping_docs)) {

                                OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->update($data);

                                // Perform operations when all fields are filled
                                $data2 = new OrderStatus();
                                $data2->item_shipped = 'on';
                                $data2->pre_shipped = 'on';
                                $data2->preliminary_doc = 'on';
                                $data2->release_notes = 'on';
                                $data2->scan_out_inventory_id = $request->order_id;
                                $data2->order_status = 'shipped';
                                $previous_status_record = OrderStatus::where('scan_out_inventory_id', $request->order_id)->latest()->first();
                                $data2->previous_status = $previous_status_record->changed_to ?? 'pending';
                                $data2->user_id = auth()->user()->id;
                                $result = $this->addOrderStatus($data2);
                                $is_scanned = ScanOutLog::where('scan_out_inventory_id', $request->order_id)->count();
                                if ($is_scanned > 0) {
                                    ScanOutInventory::where('id', $request->order_id)->update(['status' => 'shipped']);
                                    if ($previous_status_record->changed_to != 'shipped') {
                                        $order = OrderStatus::create($result);
                                        $last_id = $order->id;
                                        $order->save($result);
                                    }
                                    $scanOutInventory =  ScanOutInventory::find($request->order_id);
                                    if ($scanOutInventory->is_order_pending == 1) {
                                        ScanOutInventory::where('id', $scanOutInventory->id)->update(['is_order_pending' => 0, 'pending_order_complete' => 1]);
                                    }
                                    $this->OrderScanOut($scanOutInventory->id);
                                    return response()->json(['status' => true, 'message' => 'Status Updated Successfully'], 200);
                                } else {
                                    return response()->json(['status' => false, 'errors' => 'Atleast One Skew should be scanned out!'], 422);
                                }
                            } else {
                                return response()->json(['status' => false, 'errors' => 'First fill all the requirements for Shipping in Process'], 422);
                            }
                        } else {
                            return response()->json(['status' => false, 'errors' => 'Shipping in Process Status Should be filled'], 422);
                        }
                    } else {
                        return response()->json(['status' => false, 'errors' => 'First fill all the requirements for Preload!'], 422);
                    }
                } else {
                    return response()->json(['status' => false, 'errors' => 'Preload Status Should be filled'], 422);
                }
            } else {
                $preload = OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => self::PRELOAD])->first();
                if ($preload) {
                    if (!empty($preload->deposit_received) && !empty($preload->deposit_amount)) {
                        $shipping_in_process = OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => self::SHIPPING_IN_PROCESS])->first();
                        if ($shipping_in_process) {
                            if (!empty($shipping_in_process->freight_forwarder) && !empty($shipping_in_process->freight_forwarder) && !empty($shipping_in_process->shipping_line) && !empty($shipping_in_process->acid_received) && !empty($shipping_in_process->acid_number) && !empty($shipping_in_process->booking_completed) && !empty($shipping_in_process->erd) && !empty($shipping_in_process->sailing_date) && !empty($shipping_in_process->truker_name) && !empty($shipping_in_process->trucker_quote) && !empty($shipping_in_process->load_date) && !empty($shipping_in_process->release_notes) && !empty($shipping_in_process->pre_shipping_docs)) {

                                $order_req = OrderStatusRequirement::create($data);
                                // Perform operations when all fields are filled
                                $data2 = new OrderStatus();
                                $data2->item_shipped = 'on';
                                $data2->pre_shipped = 'on';
                                $data2->preliminary_doc = 'on';
                                $data2->release_notes = 'on';
                                $data2->scan_out_inventory_id = $request->order_id;
                                $data2->order_status = 'shipped';
                                $previous_status_record = OrderStatus::where('scan_out_inventory_id', $request->order_id)->latest()->first();
                                $data2->previous_status = $previous_status_record->changed_to ?? 'pending';
                                $data2->user_id = auth()->user()->id;
                                $result = $this->addOrderStatus($data2);

                                $is_scanned = ScanOutLog::where('scan_out_inventory_id', $request->order_id)->count();
                                if ($is_scanned > 0) {
                                    ScanOutInventory::where('id', $request->order_id)->update(['status' => 'shipped']);
                                    $order = OrderStatus::create($result);
                                    $last_id = $order->id;
                                    $order->save($result);
                                    $scanOutInventory =  ScanOutInventory::find($request->order_id);
                                    if ($scanOutInventory->is_order_pending == 1) {
                                        ScanOutInventory::where('id', $scanOutInventory->id)->update(['is_order_pending' => 0, 'pending_order_complete' => 1]);
                                    }
                                    $this->OrderScanOut($scanOutInventory->id);
                                    return response()->json(['status' => true, 'message' => 'Status Updated Successfully'], 200);
                                } else {
                                    return response()->json(['status' => false, 'errors' => 'Atleast One Skew should be scanned out!'], 422);
                                }
                            } else {
                                return response()->json(['status' => false, 'errors' => 'First fill all the requirements for Shipping in Process'], 422);
                            }
                        } else {
                            return response()->json(['status' => false, 'errors' => 'Shipping in Process Status Should be filled'], 422);
                        }
                    } else {
                        return response()->json(['status' => false, 'errors' => 'First fill all the requirements for Preload!'], 422);
                    }
                } else {
                    return response()->json(['status' => false, 'errors' => 'Preload Status Should be filled'], 422);
                }
            }
        } else {
            return response()->json(['status' => false, 'errors' => 'Field should be filled!'], 422);
        }
    }

    public function postLoadingDocumentationStatusSubmission(Request $request)
    {
        if ($request->order_current_status == 'closed') {
            return response()->json(['status' => false, 'errors' => "Order Status can't change after Closed"], 422);
        }
        if ($request->order_current_status == 'cancelled') {
            return response()->json(['status' => false, 'errors' => "Order Status can't change after Cancelled"], 422);
        }
        $data = [
            'order_id' => $request->order_id,
            'order_status' => $request->order_status,
            'final_doc_submitted_to_ff' => $request->final_doc_submitted_to_ff,
            'nexpac_report_sent' => $request->nexpac_report_sent,
            'ktc_report_sent' => $request->ktc_report_sent,
            'customer_email_all_paper_work' => $request->customer_email_all_paper_work,
            'nextrade_invoicing' => $request->nextrade_invoicing,
            'obelete_report_updated' => $request->obelete_report_updated

        ];

        $query = OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->first();

        if (!empty($query->final_doc_submitted_to_ff) && !empty($query->nexpac_report_sent) && !empty($query->ktc_report_sent) && !empty($query->customer_email_all_paper_work) && !empty($query->nextrade_invoicing) && !empty($query->obelete_report_updated)) {
            $validator = Validator::make($request->all(), [
                'final_doc_submitted_to_ff'  => 'required',
                'nexpac_report_sent'  => 'required',
                'ktc_report_sent'  => 'required',
                'customer_email_all_paper_work'  => 'required',
                'nextrade_invoicing'  => 'required',
                'obelete_report_updated'  => 'required'
            ]);

            if ($validator->fails()) {
                // Validation failed
                $errors = $validator->errors()->toArray();
                return response()->json(['status' => false, 'errors' => $errors], 422);
            }
        }

        // Check if all fields are filled
        if ($request->filled('order_id', 'order_status', 'final_doc_submitted_to_ff', 'nexpac_report_sent', 'ktc_report_sent', 'customer_email_all_paper_work', 'nextrade_invoicing', 'obelete_report_updated')) {
            // Perform additional checks if required
            if ($query) {
                OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->update($data);

                // Perform operations when all fields are filled
                $data2 = new OrderStatus();
                $data2->shipment_loaded = 'on';
                $data2->final_shipping_doc = 'on';
                $data2->nextpac_report = 'on';
                $data2->ktc_report = 'on';
                $data2->customer_email_all_paper_work = 'on';
                $data2->cus_paperwork_completed = 'on';
                $data2->nextrade_invoicing = 'on';
                $data2->obselete_report = 'on';
                $data2->scan_out_inventory_id = $request->order_id;
                $data2->order_status = 'post_loading_documentation';
                $previous_status_record = OrderStatus::where('scan_out_inventory_id', $request->order_id)->latest()->first();
                $data2->previous_status = $previous_status_record->changed_to ?? 'pending';
                $data2->user_id = auth()->user()->id;
                $result = $this->addOrderStatus($data2);
                ScanOutInventory::where('id', $request->order_id)->update(['status' => 'post_loading_documentation']);
                if ($previous_status_record->changed_to != 'post_loading_documentation') {
                    $order = OrderStatus::create($result);
                    $last_id = $order->id;
                    $order->save($result);
                }

                return response()->json(['status' => true, 'message' => 'Status Updated Successfully'], 200);
            } else {
                $order_req = OrderStatusRequirement::create($data);
                // Perform operations when all fields are filled
                $data2 = new OrderStatus();
                $data2->shipment_loaded = 'on';
                $data2->final_shipping_doc = 'on';
                $data2->nextpac_report = 'on';
                $data2->ktc_report = 'on';
                $data2->customer_email_all_paper_work = 'on';
                $data2->cus_paperwork_completed = 'on';
                $data2->nextrade_invoicing = 'on';
                $data2->obselete_report = 'on';
                $data2->scan_out_inventory_id = $request->order_id;
                $data2->order_status = 'post_loading_documentation';
                $previous_status_record = OrderStatus::where('scan_out_inventory_id', $request->order_id)->latest()->first();
                $data2->previous_status = $previous_status_record->changed_to ?? 'pending';
                $data2->user_id = auth()->user()->id;
                $result = $this->addOrderStatus($data2);
                ScanOutInventory::where('id', $request->order_id)->update(['status' => 'post_loading_documentation']);
                $order = OrderStatus::create($result);
                $last_id = $order->id;
                $order->save($result);
                return response()->json(['status' => true, 'message' => 'Status Updated Successfully'], 200);
            }
        } else {
            // Fields are not filled completely
            if ($request->filled('final_doc_submitted_to_ff') || $request->filled('nexpac_report_sent') || $request->filled('ktc_report_sent') || $request->filled('customer_email_all_paper_work') || $request->filled('nextrade_invoicing') || $request->filled('obelete_report_updated')) {

                if ($query) {
                    OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->update($data);
                } else {
                    $order_req = OrderStatusRequirement::create($data);
                }
                return response()->json(['status' => true, 'message' => 'Data submitted Successfully'], 200);
            } else {
                return response()->json(['status' => false, 'errors' => 'Anyone Field should be filled!'], 422);
            }
        }
    }

    public function endStageStatusSubmission(Request $request)
    {
        if ($request->order_current_status == 'closed') {
            return response()->json(['status' => false, 'errors' => "Order Status can't change after Closed"], 422);
        }
        if ($request->order_current_status == 'cancelled') {
            return response()->json(['status' => false, 'errors' => "Order Status can't change after Cancelled"], 422);
        }
        $data = [
            'order_id' => $request->order_id,
            'order_status' => $request->order_status,
            'final_bl_draft_to_customer'  => $request->final_bl_draft_to_customer,
            'release_requested'  => $request->release_requested,
            'bl_received'  => $request->bl_received,
            'final_document_to_bank'  => $request->final_document_to_bank,
            'final_document_to_customer'  => $request->final_document_to_customer,
            'final_document_to_cargox'  => $request->final_document_to_cargox,
            'final_payment'  => $request->final_payment,
        ];

        $query = OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->first();

        if (!empty($query->final_bl_draft_to_customer) && !empty($query->release_requested) && !empty($query->bl_received) && !empty($query->final_document_to_bank) && !empty($query->final_document_to_customer) && !empty($query->final_document_to_cargox) && !empty($query->final_payment)) {
            $validator = Validator::make($request->all(), [
                'final_bl_draft_to_customer'  => 'required',
                'release_requested'  => 'required',
                'bl_received'  => 'required',
                'final_document_to_bank'  => 'required',
                'final_document_to_customer'  => 'required',
                'final_document_to_cargox'  => 'required',
                'final_payment' => 'required'
            ]);

            if ($validator->fails()) {
                // Validation failed
                $errors = $validator->errors()->toArray();
                return response()->json(['status' => false, 'errors' => $errors], 422);
            }
        }

        // Check if all fields are filled
        if ($request->filled('order_id', 'order_status', 'final_bl_draft_to_customer', 'release_requested', 'bl_received', 'final_document_to_bank', 'final_document_to_customer', 'final_document_to_cargox', 'final_payment')) {
            // Perform additional checks if required
            if ($query) {
                OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->update($data);

                // Perform operations when all fields are filled
                $data2 = new OrderStatus();
                $data2->final_payment_received = 'on';
                $data2->final_bl_draft = 'on';
                $data2->release_requested = 'on';
                $data2->bl_requested = 'on';
                $data2->final_doc_to_bank = 'on';
                $data2->final_doc_to_customer = 'on';
                $data2->final_doc_to_cargoX = 'on';
                $data2->scan_out_inventory_id = $request->order_id;
                $data2->order_status = 'end_stage';
                $previous_status_record = OrderStatus::where('scan_out_inventory_id', $request->order_id)->latest()->first();
                $data2->previous_status = $previous_status_record->changed_to ?? 'pending';
                $data2->user_id = auth()->user()->id;
                $result = $this->addOrderStatus($data2);
                ScanOutInventory::where('id', $request->order_id)->update(['status' => 'end_stage']);
                if ($previous_status_record->changed_to != 'end_stage') {
                    $order = OrderStatus::create($result);
                    $last_id = $order->id;
                    $order->save($result);
                }
                return response()->json(['status' => true, 'message' => 'Status Updated Successfully'], 200);
            } else {
                $order_req = OrderStatusRequirement::create($data);
                // Perform operations when all fields are filled
                $data2 = new OrderStatus();
                $data2->final_payment_received = 'on';
                $data2->final_bl_draft = 'on';
                $data2->release_requested = 'on';
                $data2->bl_requested = 'on';
                $data2->final_doc_to_bank = 'on';
                $data2->final_doc_to_customer = 'on';
                $data2->final_doc_to_cargoX = 'on';
                $data2->scan_out_inventory_id = $request->order_id;
                $data2->order_status = 'end_stage';
                $previous_status_record = OrderStatus::where('scan_out_inventory_id', $request->order_id)->latest()->first();
                $data2->previous_status = $previous_status_record->changed_to ?? 'pending';
                $data2->user_id = auth()->user()->id;
                $result = $this->addOrderStatus($data2);
                ScanOutInventory::where('id', $request->order_id)->update(['status' => 'end_stage']);
                $order = OrderStatus::create($result);
                $last_id = $order->id;
                $order->save($result);
                return response()->json(['status' => true, 'message' => 'Status Updated Successfully'], 200);
            }
        } else {
            // Fields are not filled completely
            if ($request->filled('final_bl_draft_to_customer') || $request->filled('release_requested') || $request->filled('bl_received') || $request->filled('final_document_to_bank') || $request->filled('final_document_to_customer') || $request->filled('final_document_to_cargox') || $request->filled('final_payment')) {
                if ($query) {
                    OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->update($data);
                } else {
                    $order_req = OrderStatusRequirement::create($data);
                }
                return response()->json(['status' => true, 'message' => 'Data submitted Successfully'], 200);
            } else {
                return response()->json(['status' => false, 'errors' => 'Anyone Field should be filled!'], 422);
            }
        }
    }


    public function closedStatusSubmission(Request $request)
    {
        if ($request->order_current_status == 'closed') {
            return response()->json(['status' => false, 'errors' => "Order Status can't change after Closed"], 422);
        }
        if ($request->order_current_status == 'cancelled') {
            return response()->json(['status' => false, 'errors' => "Order Status can't change after Cancelled"], 422);
        }
        $data = [
            'order_id' => $request->order_id,
            'order_status' => $request->order_status,
            'ff_invoivce' => $request->ff_invoivce,
            'ff_paid' => $request->ff_paid,
            'ff_date_paid' => $request->ff_date_paid,
            'ff_invoice' => $request->ff_invoice,
            'trucker_paid' => $request->trucker_paid,
            'trucker_date' => $request->trucker_date,
            'final_payment_closed' => $request->final_payment_closed
        ];

        $query = OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->first();

        if (!empty($query->ff_invoivce) && !empty($query->ff_paid) && !empty($query->ff_date_paid) && !empty($query->ff_invoice) && !empty($query->trucker_paid) && !empty($query->trucker_date) && !empty($query->final_payment_closed)) {
            $validator = Validator::make($request->all(), [
                'ff_invoivce'  => 'required',
                'ff_paid'  => 'required',
                'ff_date_paid'  => 'required',
                'ff_invoice'  => 'required',
                'trucker_paid'  => 'required',
                'trucker_date'  => 'required',
                'final_payment_closed' => 'required'
            ]);

            if ($validator->fails()) {
                // Validation failed
                $errors = $validator->errors()->toArray();
                return response()->json(['status' => false, 'errors' => $errors], 422);
            }
        }

        // Check if all fields are filled
        if ($request->filled('order_id', 'order_status', 'ff_invoivce', 'ff_paid', 'ff_date_paid', 'ff_invoice', 'trucker_paid', 'trucker_date', 'final_payment_closed')) {
            // Perform additional checks if required
            if ($query) {
                OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->update($data);

                // Perform operations when all fields are filled
                $data2 = new OrderStatus();
                $data2->ff_invoice = 'on';
                $data2->ff_paid = 'on';
                $data2->ff_date_paid = 'on';
                $data2->trucker_invoice = 'on';
                $data2->trucker_paid = 'on';
                $data2->trucker_date = 'on';
                $data2->scan_out_inventory_id = $request->order_id;
                $data2->order_status = 'closed';
                $previous_status_record = OrderStatus::where('scan_out_inventory_id', $request->order_id)->latest()->first();
                $data2->previous_status = $previous_status_record->changed_to ?? 'pending';
                $data2->user_id = auth()->user()->id;
                $result = $this->addOrderStatus($data2);
                ScanOutInventory::where('id', $request->order_id)->update(['status' => 'closed']);
                if ($previous_status_record->changed_to != 'closed') {
                    $order = OrderStatus::create($result);
                    $last_id = $order->id;
                    $order->save($result);
                }
                return response()->json(['status' => true, 'message' => 'Status Updated Successfully'], 200);
            } else {
                $order_req = OrderStatusRequirement::create($data);
                // Perform operations when all fields are filled
                $data2 = new OrderStatus();
                $data2->ff_invoice = 'on';
                $data2->ff_paid = 'on';
                $data2->ff_date_paid = 'on';
                $data2->trucker_invoice = 'on';
                $data2->trucker_paid = 'on';
                $data2->trucker_date = 'on';
                $data2->scan_out_inventory_id = $request->order_id;
                $data2->order_status = 'closed';
                $previous_status_record = OrderStatus::where('scan_out_inventory_id', $request->order_id)->latest()->first();
                $data2->previous_status = $previous_status_record->changed_to ?? 'pending';
                $data2->user_id = auth()->user()->id;
                $result = $this->addOrderStatus($data2);
                ScanOutInventory::where('id', $request->order_id)->update(['status' => 'closed']);
                $order = OrderStatus::create($result);
                $last_id = $order->id;
                $order->save($result);
                return response()->json(['status' => true, 'message' => 'Status Updated Successfully'], 200);
            }
        } else {
            if ($request->filled('ff_invoivce') || $request->filled('ff_paid') || $request->filled('ff_date_paid') || $request->filled('ff_invoice') || $request->filled('trucker_paid') || $request->filled('trucker_date') || $request->filled('final_payment_closed')) {
                // Fields are not filled completely
                if ($query) {
                    OrderStatusRequirement::where(['order_id' => $request->order_id, 'order_status' => $request->order_status])->update($data);
                } else {
                    $order_req = OrderStatusRequirement::create($data);
                }
                return response()->json(['status' => true, 'message' => 'Data submitted Successfully'], 200);
            } else {
                return response()->json(['status' => false, 'errors' => 'Anyone Field should be filled!'], 422);
            }
        }
    }

    public function deleteDocument(Request $request)
    {
        $result = '';
        $id = $request->document_id;
        $document = OrderFiles::find($id);
        if ($document) {
            $result = $document->delete();
        }
        if (!isset($result)) {
            return response()->json(['status' => false, 'message' => 'Something went wrong! try again'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Document deleted successfully'], 200);
    }
}
