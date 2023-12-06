<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CGTGrade;
use App\Models\Color;
use App\Models\Customer;
use App\Models\NTGrade;
use App\Models\ProductType;
use App\Models\ScanInInventory;
use App\Models\ScanInLog;
use App\Models\ScanOutInventory;
use App\Models\ScanOutLog;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\TextUI\XmlConfiguration\Group;

class InventoryController extends Controller
{

    public function __construct()
    {
        // $this->middleware('check_report_permission');
    }

    public function index()
    {

        $warehouses = Warehouse::orderBy('id', 'desc')->get();
        return view('admin.modules.inventory.inventory_summary', compact('warehouses'));
    }

    public function getInventoryByFilter(Request $request)
    {
        $warehouse_id = $request->warehouse_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $year = $request->year;
        $inv_type = $request->inv_type;
        $currentDate = Carbon::now()->format('Y-m-d');
        $cgt_grades = CGTGrade::orderBy('id', 'asc')->get();
        foreach ($cgt_grades as $key => $v) {
            $inventory_summary[$key] = ScanInInventory::Join('scan_in_logs', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                // ->where('scan_in_logs.is_scan_out', 0)
                ->where('scan_in_logs.cgt', $v->id)
                // when warehouse_id != all
                ->when(!empty($warehouse_id), function ($query) use ($warehouse_id) {

                    $query->where('scan_in_inventories.warehouse_id', $warehouse_id);
                })
                // when year != all
                ->when(!empty($year) && $year != 'current' && empty($from_date) && empty($to_date), function ($query) use ($year) {
                    $query->whereYear('scan_in_inventories.created_at', $year);
                })
                // when date range is not null
                ->when(empty($year) && !empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                    $query->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                        ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
                })
                ->when(!empty($year) && $year == 'current' && empty($from_date) && empty($to_date), function ($query) use ($currentDate) {
                    $query->where('scan_in_logs.is_scan_out', 0);
                })
                ->when($inv_type == 'weight', function ($query) {

                    $query->where('scan_in_logs.unit', 'W');
                })
                ->when($inv_type == 'yards', function ($query) {

                    $query->where('scan_in_logs.unit', '!=', 'W');
                })
                ->select(DB::raw("SUM(scan_in_logs.weight) as total_weight"), DB::raw("SUM(scan_in_logs.yards) as total_yards"), DB::raw("SUM(scan_in_logs.rolls) as total_rolls"), DB::raw("COUNT(scan_in_logs.id) as pallet_count"))
                ->first();
        }
        $inventory_summary_html = view('admin.modules.inventory.inventory_summary_data', compact('cgt_grades', 'inventory_summary', 'inv_type'))->render();
        return response()->json(['status' => true, 'data' => $inventory_summary_html]);
    }

    public function cgtSummary()
    {

        $warehouses = Warehouse::orderBy('id', 'desc')->get();
        $product_type = ProductType::orderBy('id', 'desc')->get();
        $cgt = CGTGrade::orderBy('id', 'desc')->get();
        $nt = NTGrade::orderBy('id', 'desc')->get();
        $total_scan_in_weight = ScanInLog::join('c_g_t_grades', 'scan_in_logs.cgt', '=', 'c_g_t_grades.id')
            ->where('scan_in_logs.is_scan_out', 0)
            ->sum('scan_in_logs.weight');
        return view('admin.modules.inventory.cgt_summary', compact('warehouses', 'total_scan_in_weight', 'cgt', 'nt', 'product_type'));
    }

    public function ntSummary()
    {
        $warehouses = Warehouse::orderBy('id', 'desc')->get();
        $cgt = CGTGrade::orderBy('id', 'desc')->get();
        $product_type = ProductType::orderBy('id', 'desc')->get();
        $nt = NTGrade::orderBy('id', 'desc')->get();
        $total_scan_in_weight = ScanInLog::join('n_t_grades', 'scan_in_logs.nt', '=', 'n_t_grades.id')
            ->where('scan_in_logs.is_scan_out', 0)
            ->sum('scan_in_logs.weight');
        return view('admin.modules.inventory.nt_summary', compact('warehouses', 'total_scan_in_weight', 'cgt', 'nt', 'product_type'));
    }

    public function color_summary()
    {

        $warehouses = Warehouse::orderBy('id', 'desc')->get();
        $colors = Color::orderBy('id', 'desc')->get();
        $nt = NTGrade::orderBy('id', 'desc')->get();
        $cgt_grades = CGTGrade::orderBy('id', 'desc')->get();
        $total_scan_in_weight = ScanInLog::where('is_scan_out', 0)->sum('weight');

        return view('admin.modules.inventory.color_summary', compact('warehouses', 'total_scan_in_weight', 'colors', 'nt', 'cgt_grades'));
    }

    public function getNtByColorFilter(Request $request)
    {

        $data = [];
        $data['color_slug'] = !empty($request->color_id) ? 'Color ' . Color::where('id', $request->color_id)->value('slug') : 'Color All';
        $data['nt_slug'] = !empty($request->nt_id) ? 'NT Grade ' . NTGrade::where('id', $request->nt_id)->value('slug') : 'NT Grade All';

        $data['total_weight_color_wise'] = $this->colorSummaryFilter('color', $request);

        $data['total_weight_nt_wise'] = $this->colorSummaryFilter('nt', $request);

        $data['colors_weight_html'] = $this->getCgtReportData($request, 'color');

        // dd($data['colors_weight_html']);
        $colors_weight_html = '';

        $total_grand_weight = $total_grand_yards = 0;
        foreach ($data['colors_weight_html']['color_grade_group_data'] as $color_key => $color) {
            $colors_weight_html .= '<tr>
                <th colspan="3">' . $color->color_name . '</th>
                <th>' . ($color->total_weight ?? 0) . '</th>
                <th>' . ($color->total_yards ?? 0) . '</th>
            </tr>';

            foreach ($data['colors_weight_html']['nt_grade_group_data3'][$color_key] as $nt_key => $nt) {
                $colors_weight_html .= ' <tr>
                <td></td>
                                                                <th colspan="2">' . $nt->nt_grade_name . '</th>
                                                                <th>' . ($nt->total_weight ?? 0) . '</th>
                                                                <th>' . ($nt->total_yards ?? 0) . '</th>
                                                            </tr>';
                foreach ($data['colors_weight_html']['cgt_grade_group_data3'][$color_key][$nt_key] as $cgt_key => $cgt) {
                    $total_grand_weight += $cgt->total_weight;
                    $total_grand_yards += $cgt->total_yards;
                    $colors_weight_html .= '<tr>
                                            <td></td>
                                            <td></td>
                                            <td>' . $cgt->cgt_grade_name . '</td>
                                            <th>' . ($cgt->total_weight ?? 0) . '</th>
                                            <th>' . ($cgt->total_yards ?? 0) . '</th>
                                        </tr>';
                }
            }

            $colors_weight_html .= ' <tr>
                                    <td colspan="5">
                                        <hr />
                                    </td>
                                </tr>';
        }
        $colors_weight_html .= ' <tr>
                                <th colspan="3">Grand Total </th>
                                <th>' . ($total_grand_weight ?? 0) . '</th>
                                <th>' . ($total_grand_yards ?? 0) . '</th>
                            </tr>';
        $data['colors_weight_html'] = $colors_weight_html;
        return response()->json(['status' => true, 'result' => $data]);
    }

    protected function colorSummaryFilter($type, $request)
    {

        $color_id = $request->color_id;
        $nt_id = $request->nt_id;
        $cgt_id = $request->cgt_id;
        $warehouse_id = $request->warehouse_id;
        $year = $request->year;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $currentDate = Carbon::now()->format('Y-m-d');
        return ScanInInventory::Join('scan_in_logs', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
            // when warehouse_id != all
            ->when(!empty($warehouse_id), function ($query) use ($warehouse_id) {

                $query->where('scan_in_inventories.warehouse_id', $warehouse_id);
            })
            // when color_id != all
            ->when(!empty($color_id), function ($query) use ($color_id) {

                $query->where('scan_in_logs.color', $color_id);
            })
            // when nt_id != all
            ->when(!empty($nt_id) && $type == 'nt', function ($query) use ($nt_id) {
                $query->where('scan_in_logs.nt', $nt_id);
            })
            // when cgt_id != all
            ->when(!empty($cgt_id), function ($query) use ($cgt_id) {
                $query->where('scan_in_logs.cgt', $cgt_id);
            })
            // when year != all
            ->when(!empty($year) && $year != 'current' && empty($from_date) && empty($to_date), function ($query) use ($year) {
                $query->whereYear('scan_in_inventories.created_at', $year);
            })
            ->when(!empty($year) && $year == 'current' && empty($from_date) && empty($to_date), function ($query) {
                $query->where('scan_in_logs.is_scan_out', 0);
            })
            // when date range is not null
            ->when(empty($year) && !empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                $query->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                    ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
            })->sum('scan_in_logs.weight');
    }

    protected function getColorsWeightNTwise($request)
    {

        $color_id = $request->color_id;
        $nt_id = $request->nt_id;
        $cgt_id = $request->cgt_id;
        $warehouse_id = $request->warehouse_id;
        $year = $request->year;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        return ScanInInventory::Join('scan_in_logs', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
            ->Join('colors', 'scan_in_logs.color', '=', 'colors.id')
            ->Join('n_t_grades', 'scan_in_logs.nt', '=', 'n_t_grades.id')
            ->Join('c_g_t_grades', 'scan_in_logs.cgt', '=', 'c_g_t_grades.id')
            ->where('scan_in_logs.is_scan_out', 0)
            // when warehouse_id != all
            ->when(!empty($warehouse_id), function ($query) use ($warehouse_id) {

                $query->where('scan_in_inventories.warehouse_id', $warehouse_id);
            })
            // when color_id != all
            ->when(!empty($color_id), function ($query) use ($color_id) {

                $query->where('scan_in_logs.color', $color_id);
            })
            // when nt_id != all
            ->when(!empty($nt_id), function ($query) use ($nt_id) {
                $query->where('scan_in_logs.nt', $nt_id);
            })
            // when cgt_id != all
            ->when(!empty($cgt_id), function ($query) use ($cgt_id) {
                $query->where('scan_in_logs.cgt', $cgt_id);
            })
            // when year != all
            ->when(!empty($year) && $year != 'current' && empty($from_date) && empty($to_date), function ($query) use ($year) {
                $query->whereYear('scan_in_inventories.created_at', $year);
            })
            // when date range is not null
            ->when(empty($year) && !empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                $query->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                    ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
            })
            ->groupBy('colors.id', 'n_t_grades.id', 'c_g_t_grades.id')->orderBy('colors.id')
            ->select(DB::raw("SUM(scan_in_logs.weight) as total_weight"), 'colors.name as color_name', 'n_t_grades.grade_name as nt_grade_name', 'c_g_t_grades.grade_name as cgt_grade_name')
            ->get();
    }

    public function getCgtSummaryByFilterr(Request $request)
    {
        $data = [];

        $data['cgt_slug'] = !empty($request->cgt_id) ? 'CGT Grade ' . CGTGrade::where('id', $request->cgt_id)->value('slug') : 'CGT Grade All';
        $data['nt_slug'] = !empty($request->nt_id) ? 'NT Grade ' . NTGrade::where('id', $request->nt_id)->value('slug') : 'NT Grade All';

        $data['total_weight_cgt_wise'] = $this->cgtSummaryFilter('cgt', $request);

        $data['total_weight_nt_wise'] = $this->cgtSummaryFilter('nt', $request);

        $data['colors_weight_html'] = $this->getCgtReportData($request, 'cgt');
        $colors_weight_html = '';

        $total_grand_weight = $total_grand_yards = 0;
        foreach ($data['colors_weight_html']['cgt_grade_group_data'] as $cgt_key => $cgt) {
            $colors_weight_html .=  '<tr>
                                        <th colspan="3">' . $cgt->cgt_grade_name . '</th>
                                        <th class="font-weight-bold">' . ($cgt->total_weight ?? 0) . '</th>
                                        <th class="font-weight-bold">' . ($cgt->total_yards ?? 0) . '</th>
                                    </tr>';
            foreach ($data['colors_weight_html']['nt_grade_group_data'][$cgt_key] as $nt_key => $nt) {
                $colors_weight_html .= ' <tr>
                <td></td>
                                                                <th colspan="2">' . $nt->nt_grade . '</th>
                                                                <th>' . ($nt->total_weight ?? 0) . '</th>
                                                                <th>' . ($nt->total_yards ?? 0) . '</th>
                                                            </tr>';
                foreach ($data['colors_weight_html']['color_group_data'][$cgt_key][$nt_key] as $color_key => $color) {
                    $total_grand_weight += $color->total_weight;
                    $total_grand_yards += $color->total_yards;
                    $colors_weight_html .= '<tr>
                                            <td></td>
                                            <td></td>
                                            <td>' . $color->color_name . '</td>
                                            <td>' . ($color->total_weight ?? 0) . '</td>
                                            <td>' . ($color->total_yards ?? 0) . '</td>
                                        </tr>';
                }
            }

            $colors_weight_html .= ' <tr>
                                    <td colspan="5">
                                        <hr />
                                    </td>
                                </tr>';
        }
        $colors_weight_html .= ' <tr>
                                <th colspan="3">Grand Total </th>
                                <th class="font-weight-bold">' . $total_grand_weight . '</th>
                                <th class="font-weight-bold">' . $total_grand_yards . '</th>
                            </tr>';
        $data['colors_weight_html'] = $colors_weight_html;
        return response()->json(['status' => true, 'result' => $data]);
    }

    public function getNtSummaryByFilter(Request $request)
    {

        $data = [];

        $data['cgt_slug'] = !empty($request->cgt_id) ? 'CGT Grade ' . CGTGrade::where('id', $request->cgt_id)->value('slug') : 'CGT Grade All';
        $data['nt_slug'] = !empty($request->nt_id) ? 'NT Grade ' . NTGrade::where('id', $request->nt_id)->value('slug') : 'NT Grade All';

        $data['total_weight_nt_wise'] = $this->ntSummaryFilter('nt', $request);
        $data['total_weight_cgt_wise'] = $this->ntSummaryFilter('cgt', $request);

        $data['colors_weight_html'] = $this->getCgtReportData($request, 'nt');
        $colors_weight_html = '';
        $total_grand_weight = $total_grand_yards = 0;
        foreach ($data['colors_weight_html']['nt_grade_group_data2'] as $nt_key => $nt) {
            $colors_weight_html .=  '<tr>
                                        <th colspan="3">' . $nt->nt_grade_name . '</th>
                                        <th>' . ($nt->total_weight ?? 0) . '</th>
                                        <th>' . ($nt->total_yards ?? 0) . '</th>
                                    </tr>';
            foreach ($data['colors_weight_html']['cgt_grade_group_data2'][$nt_key] as $cgt_key => $cgt) {
                $colors_weight_html .= ' <tr>
                <td></td>
                                                                <th colspan="2">' . $cgt->cgt_grade_name . '</th>
                                                                <th>' . ($cgt->total_weight ?? 0) . '</th>
                                                                <th>' . ($cgt->total_yards ?? 0) . '</th>
                                                            </tr>';
                foreach ($data['colors_weight_html']['color_group_data2'][$nt_key][$cgt_key] as $color_key => $color) {
                    $total_grand_weight += $color->total_weight;
                    $total_grand_yards += $color->total_yards;
                    $colors_weight_html .= '<tr>
                                            <td></td>
                                            <td></td>
                                            <td>' . $color->color_name . '</td>
                                            <td>' . ($color->total_weight ?? 0) . '</td>
                                            <td>' . ($color->total_yards ?? 0) . '</td>
                                        </tr>';
                }
            }

            $colors_weight_html .= ' <tr>
                                    <td colspan="5">
                                        <hr />
                                    </td>
                                </tr>';
        }
        $colors_weight_html .= ' <tr>
                                <th colspan="3">Grand Total </th>
                                <th>' . $total_grand_weight . '</th>
                                <th>' . $total_grand_yards . '</th>
                            </tr>';
        $data['colors_weight_html'] = $colors_weight_html;
        return response()->json(['status' => true, 'result' => $data]);
    }

    protected function cgtAndNtSummaryColorWise($request, $type)
    {
        $cgt_id = $request->cgt_id;
        $nt_id = $request->nt_id;
        $warehouse_id = $request->warehouse_id;
        $product_type_id = $request->product_type_id;
        $year = $request->year;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $query = ScanInInventory::join('scan_in_logs', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
            ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
            ->join('c_g_t_grades as cgt_grades', 'scan_in_logs.cgt', '=', 'cgt_grades.id')
            ->join('n_t_grades as nt_grades', 'scan_in_logs.nt', '=', 'nt_grades.id')
            ->where('scan_in_logs.is_scan_out', 0);

        // Add filters based on request parameters
        if (!empty($warehouse_id)) {
            $query->where('scan_in_inventories.warehouse_id', $warehouse_id);
        }
        if (!empty($product_type_id)) {
            $query->where('scan_in_logs.product_type', $product_type_id);
        }
        if (!empty($cgt_id)) {
            $query->where('scan_in_logs.cgt', $cgt_id);
        }
        if (!empty($nt_id)) {
            $query->where('scan_in_logs.nt', $nt_id);
        }
        if (!empty($year) && empty($from_date) && empty($to_date)) {
            $query->whereYear('scan_in_inventories.created_at', $year);
        }
        if (empty($year) && !empty($from_date) && !empty($to_date)) {
            $query->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
        }

        // Group by CGT and NT, and select the relevant fields
        $query->groupBy('cgt_grades.id', 'nt_grades.id', 'colors.id')
            ->select('cgt_grades.grade_name as cgt', 'nt_grades.grade_name as nt', 'colors.name as color_name', DB::raw("SUM(scan_in_logs.weight) as total_weight"));

        // Order the results by CGT, NT, color, and total weight
        if ($type == 'cgt') {
            $query->orderBy('cgt_grades.id', 'asc');
        } elseif ($type == 'nt') {
            $query->orderBy('nt_grades.id', 'asc');
        }
        // Execute the query and return the results
        return $query->get();
    }


    protected function ntSummaryFilter($type, $request)
    {
        $cgt_id = $request->cgt_id;
        $nt_id = $request->nt_id;
        $warehouse_id = $request->warehouse_id;
        $product_type_id = $request->product_type_id;
        $year = $request->year;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        return ScanInInventory::Join('scan_in_logs', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
            // ->where('scan_in_logs.is_scan_out', 0)
            // when warehouse_id != all
            ->when(!empty($warehouse_id), function ($query) use ($warehouse_id) {

                $query->where('scan_in_inventories.warehouse_id', $warehouse_id);
            })
            // when cgt_id != all
            ->when(!empty($cgt_id) && $type == 'cgt', function ($query) use ($cgt_id) {

                $query->where('scan_in_logs.cgt', $cgt_id);
            })
            ->when(!empty($nt_id), function ($query) use ($nt_id) {
                $query->where('scan_in_logs.nt', $nt_id);
            })
            // when Product type != all
            ->when(!empty($product_type_id), function ($query) use ($product_type_id) {

                $query->where('scan_in_logs.product_type', $product_type_id);
            })
            // when year != all
            ->when(!empty($year) && $year != 'current' && empty($from_date) && empty($to_date), function ($query) use ($year) {
                $query->whereYear('scan_in_inventories.created_at', $year);
            })
            ->when(!empty($year) && $year == 'current' && empty($from_date) && empty($to_date), function ($query) use ($year) {
                $query->where('scan_in_logs.is_scan_out', 0);
            })
            // when date range is not null
            ->when(empty($year) && !empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                $query->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                    ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
            })->sum('scan_in_logs.weight');
    }

    protected function cgtSummaryFilter($type, $request)
    {

        $cgt_id = $request->cgt_id;
        $nt_id = $request->nt_id;
        $warehouse_id = $request->warehouse_id;
        $product_type_id = $request->product_type_id;
        $year = $request->year;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        return ScanInInventory::Join('scan_in_logs', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
            // ->where('scan_in_logs.is_scan_out', 0)
            // when warehouse_id != all
            ->when(!empty($warehouse_id), function ($query) use ($warehouse_id) {

                $query->where('scan_in_inventories.warehouse_id', $warehouse_id);
            })
            // when cgt_id != all
            ->when(!empty($cgt_id), function ($query) use ($cgt_id) {

                $query->where('scan_in_logs.cgt', $cgt_id);
            })
            ->when(!empty($nt_id) && $type == 'nt', function ($query) use ($nt_id) {
                $query->where('scan_in_logs.nt', $nt_id);
            })
            // when Product type != all
            ->when(!empty($product_type_id), function ($query) use ($product_type_id) {

                $query->where('scan_in_logs.product_type', $product_type_id);
            })
            // when year != all
            ->when(!empty($year) && $year != 'current' && empty($from_date) && empty($to_date), function ($query) use ($year) {
                $query->whereYear('scan_in_inventories.created_at', $year);
            })
            // when year != all
            ->when(!empty($year) && $year == 'current' && empty($from_date) && empty($to_date), function ($query) {
                $query->where('scan_in_logs.is_scan_out', 0);
            })
            // when date range is not null
            ->when(empty($year) && !empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                $query->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                    ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
            })->sum('scan_in_logs.weight');
    }

    public function cgtComulativeSummary()
    {
        $warehouses = Warehouse::orderBy('id', 'desc')->get();

        return view('admin.modules.inventory.cgt_comulative_summary', compact('warehouses'));
    }

    public function getComulativeCgtByFilter(Request $request)
    {

        $warehouse_id = $request->warehouse_id;
        $year = $request->year;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $warehouse = Warehouse::find($warehouse_id);

        $warehouse_name = !empty($warehouse) ? $warehouse->name : 'All';

        $cgt_comulative = ScanInInventory::Join('scan_in_logs', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')

            // ->where('scan_in_logs.is_scan_out', 0)
            // when warehouse_id != all
            ->when($warehouse_id != '', function ($query) use ($warehouse_id) {
                $query->where('scan_in_inventories.warehouse_id', $warehouse_id);
            })
            // when year != all
            ->when($year != '' && empty($from_date) && empty($to_date), function ($query) use ($year) {
                $query->whereYear('scan_in_inventories.created_at', $year);
            })
            // when date range is not null
            ->when($year == '' && !empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                $query->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                    ->whereDate('scan_in_inventories.created_at', '<=', $to_date)
                    ->groupBy('new_date')
                    ->distinct('new_date');
            })
            ->when(empty($from_date) && empty($to_date), function ($query) use ($from_date, $to_date) {
                $query->groupBy('year', 'month');
            })
            ->select(DB::raw("DATE_FORMAT(scan_in_inventories.created_at, '%d-%m-%Y') new_date"), DB::raw("DATE_FORMAT(scan_in_inventories.created_at, '%m-%Y') month_year"),  DB::raw('YEAR(scan_in_inventories.created_at) year, MONTH(scan_in_inventories.created_at) month'), DB::raw("SUM(scan_in_logs.weight) as total_weight"))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        $cgt_grades = CGTGrade::orderBy('id', 'asc')->get();
        $cgt_comulative_weight = [];
        foreach ($cgt_comulative as $cgt_comulative_key => $v) {

            $commulative_year = $v->year;
            $commulative_month = $v->month;
            $commulative_date = date('Y-m-d', strtotime($v->new_date));

            $cgt_grade_wise_weight_arr = [];
            foreach ($cgt_grades as $key => $cgt) {
                $result = ScanInInventory::join('scan_in_logs', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                    ->where(['scan_in_logs.cgt' => $cgt->id])
                    ->when($warehouse_id != '', function ($query) use ($warehouse_id) {
                        $query->where('scan_in_inventories.warehouse_id', $warehouse_id);
                    })
                    ->when($year == '' && !empty($from_date) && !empty($to_date), function ($query) use ($commulative_date) {
                        $query->whereDate('scan_in_inventories.created_at', '=', $commulative_date);
                    })
                    ->when(empty($from_date) && empty($to_date), function ($query) use ($commulative_year, $commulative_month) {
                        $query->whereYear('scan_in_inventories.created_at', $commulative_year)
                            ->whereMonth('scan_in_inventories.created_at', $commulative_month);
                    })
                    ->select(DB::raw("SUM(scan_in_logs.weight) as cgt_weight"), 'scan_in_logs.cgt')
                    ->first();

                $cgt_grade_wise_weight_arr[] = [
                    'cgt_grade_name' => $cgt->grade_name,
                    'cgt_weight' => isset($result->cgt_weight) ? $result->cgt_weight : 0,
                ];
            }

            $cgt_comulative_weight[] = $cgt_grade_wise_weight_arr;
        }
        $page_title = 'CGT Cumulative Summary';

        return view('admin.modules.inventory.print_cgt_comulative_summary', compact('page_title', 'cgt_grades', 'cgt_comulative', 'cgt_comulative_weight', 'from_date', 'to_date', 'warehouse_name', 'year'));
    }

    public function ntComulativeSummary()
    {

        $warehouses = Warehouse::orderBy('id', 'desc')->get();

        return view('admin.modules.inventory.nt_comulative_summary', compact('warehouses'));
    }

    public function getComulativeNtByFilter(Request $request)
    {
        $warehouse_id = $request->warehouse_id;
        $year = $request->year;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $warehouse = Warehouse::find($warehouse_id);

        $warehouse_name = !empty($warehouse) ? $warehouse->name : 'All';

        $nt_comulative = ScanInInventory::Join('scan_in_logs', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
            // ->where('scan_in_logs.is_scan_out', 0)

            // when warehouse_id != all
            ->when($warehouse_id != '', function ($query) use ($warehouse_id) {
                $query->where('scan_in_inventories.warehouse_id', $warehouse_id);
            })
            // when year != all
            ->when($year != '' && empty($from_date) && empty($to_date), function ($query) use ($year) {
                $query->whereYear('scan_in_inventories.created_at', $year);
            })
            // when date range is not null
            ->when($year == '' && !empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                $query->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                    ->whereDate('scan_in_inventories.created_at', '<=', $to_date)
                    ->groupBy('new_date')
                    ->distinct('new_date');
            })
            ->when(empty($from_date) && empty($to_date), function ($query) use ($from_date, $to_date) {
                $query->groupBy('year', 'month');
            })
            ->select(DB::raw("DATE_FORMAT(scan_in_inventories.created_at, '%d-%m-%Y') new_date"), DB::raw("DATE_FORMAT(scan_in_inventories.created_at, '%m-%Y') month_year"),  DB::raw('YEAR(scan_in_inventories.created_at) year, MONTH(scan_in_inventories.created_at) month'), DB::raw("SUM(scan_in_logs.weight) as total_weight"))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $nt_grades = NTGrade::orderBy('id', 'asc')->get();
        $nt_comulative_weight = [];

        foreach ($nt_comulative as $nt_comulative_key => $v) {

            $commulative_year = $v->year;
            $commulative_month = $v->month;
            $commulative_date = date('Y-m-d', strtotime($v->new_date));

            $nt_grade_wise_weight_arr = [];
            foreach ($nt_grades as $key => $nt) {

                $result = ScanInInventory::Join('scan_in_logs', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                    ->where(['scan_in_logs.nt' => $nt->id])
                    ->when($warehouse_id != '', function ($query) use ($warehouse_id) {
                        $query->where('scan_in_inventories.warehouse_id', $warehouse_id);
                    })
                    ->when($year == '' && !empty($from_date) && !empty($to_date), function ($query) use ($commulative_date) {
                        $query->whereDate('scan_in_inventories.created_at', '=', $commulative_date);
                    })
                    ->when(empty($from_date) && empty($to_date), function ($query) use ($commulative_year, $commulative_month) {
                        $query->whereYear('scan_in_inventories.created_at', $commulative_year)
                            ->whereMonth('scan_in_inventories.created_at', $commulative_month);
                    })
                    ->select(DB::raw("SUM(scan_in_logs.weight) as nt_weight"))
                    ->first();

                $nt_grade_wise_weight_arr[] = [
                    'nt_grade_name' => $nt->grade_name,
                    'nt_weight' => isset($result->nt_weight) ? $result->nt_weight : 0,
                ];
            }

            $nt_comulative_weight[] = $nt_grade_wise_weight_arr;
        }

        $page_title = 'NT Cumulative Summary';

        return view('admin.modules.inventory.print_nt_comulative_summary', compact('page_title', 'nt_grades', 'nt_comulative', 'nt_comulative_weight', 'from_date', 'to_date', 'warehouse_name', 'year'));
    }

    public function customerSummaryReport()
    {
        return view('admin.modules.inventory.customer_report');
    }

    public function getCustomerReportByRlsNo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'release_number' => 'required',
        ], ['release_number' => 'Release number is required']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $release_number = $request->release_number;

        $order_history = ScanOutInventory::where('release_number', $release_number)->Join('customers', 'customers.id', '=', 'scan_out_inventories.customer_id')->Join('warehouses', 'warehouses.id', '=', 'scan_out_inventories.warehouse_id')
            ->Join('scan_out_logs', 'scan_out_logs.scan_out_inventory_id', '=', 'scan_out_inventories.id')->Join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')->select(
                'scan_out_inventories.id',
                'scan_out_inventories.release_number',
                'customers.name as customer',
                'warehouses.name as warehouse',
                'scan_out_inventories.container',
                'scan_out_inventories.tear_factor',
                'scan_out_inventories.seal',
                'scan_out_inventories.pallet_weight',
                'scan_out_inventories.tear_factor_weight',
                'scan_out_inventories.pallet_on_container',
                'scan_out_inventories.scale_discrepancy',
                'scan_out_inventories.created_at',
                DB::raw('SUM(scan_in_logs.weight) as weight_sum'),
                DB::raw('COUNT(scan_out_logs.id) as pallet'),
                DB::raw('SUM(scan_in_logs.rolls) as roll_sum')
            )->first();

        if (!isset($order_history->id) || empty($order_history->id)) {

            return back()->with('release_number_error', ['msg' => 'The Release number data is not available', 'release_number' => $release_number]);
        }
        $order_status_history =  ScanOutInventory::where('release_number', $release_number)->with('allOrderStatuses.getUser')->orderBy('id', 'desc')->first();
        $order_skew_number = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
            ->join('n_t_grades', 'n_t_grades.id', '=', 'scan_in_logs.nt')
            ->join('product_types', 'product_types.id', '=', 'scan_in_logs.product_type')
            ->join('colors', 'colors.id', '=', 'scan_in_logs.color')->where('scan_out_inventories.id', $order_history->id)->select('scan_in_logs.skew_number', 'product_types.product_type', 'c_g_t_grades.grade_name as cgt_grade', 'n_t_grades.grade_name as nt_grade', 'colors.name as color_name', 'scan_in_logs.rolls', 'scan_in_logs.weight as weight', 'scan_in_logs.yards as yards')->get();
        return view('admin.modules.inventory.customer_report_details', compact('order_history', 'order_skew_number', 'order_status_history'));
    }

    public function nexpacReport()
    {
        return view('admin.modules.inventory.nexpac_report');
    }
    public function getNexpacReportByRlsNo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'release_number' => 'required',
        ], ['release_number' => 'Release number is required']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $release_number = $request->release_number;

        $order_history = ScanOutInventory::where('release_number', $release_number)->Join('customers', 'customers.id', '=', 'scan_out_inventories.customer_id')->Join('warehouses', 'warehouses.id', '=', 'scan_out_inventories.warehouse_id')
            ->Join('scan_out_logs', 'scan_out_logs.scan_out_inventory_id', '=', 'scan_out_inventories.id')->Join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')->select(
                'scan_out_inventories.id',
                'scan_out_inventories.release_number',
                'customers.name as customer',
                'warehouses.name as warehouse',
                'scan_out_inventories.container',
                'scan_out_inventories.tear_factor',
                'scan_out_inventories.seal',
                'scan_out_inventories.pallet_weight',
                'scan_out_inventories.tear_factor_weight',
                'scan_out_inventories.pallet_on_container',
                'scan_out_inventories.scale_discrepancy',
                'scan_out_inventories.created_at',
                DB::raw('SUM(scan_in_logs.weight) as weight_sum'),
                DB::raw('SUM(scan_in_logs.yards) as yards_sum'),
                DB::raw('COUNT(scan_out_logs.id) as pallet'),
                DB::raw('SUM(scan_in_logs.rolls) as roll_sum')
            )->first();

        if (!isset($order_history->id) || empty($order_history->id)) {

            return back()->with('release_number_error', ['msg' => 'The Release number data is not available', 'release_number' => $release_number]);
        }

        $order_skew_number = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
            ->join('n_t_grades', 'n_t_grades.id', '=', 'scan_in_logs.nt')
            ->join('product_types', 'product_types.id', '=', 'scan_in_logs.product_type')
            ->join('colors', 'colors.id', '=', 'scan_in_logs.color')->where('scan_out_inventories.id', $order_history->id)->select('scan_in_logs.skew_number', 'product_types.product_type', 'c_g_t_grades.grade_name as cgt_grade', 'n_t_grades.grade_name as nt_grade', 'colors.name as color_name', 'scan_in_logs.rolls', 'scan_in_logs.weight as weight', 'scan_in_logs.yards as yards')->get();
        $nexpac_billing = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
            ->where('scan_out_inventories.id', $order_history->id)->select('c_g_t_grades.grade_name as cgt_grade', 'c_g_t_grades.billing_code as billing_code', DB::raw('SUM(scan_in_logs.weight) as weight_sum'), 'scan_in_logs.weight', 'scan_in_logs.yards', DB::raw('SUM(scan_in_logs.yards) as yards_sum'), DB::raw('COUNT(scan_out_logs.id) as no_of_pallets'), DB::raw('SUM(scan_in_logs.rolls) as rolls_sum'))->groupBy('scan_in_logs.cgt')->get();
        $count_rows_with_weight = 0;
        $count_rows_with_yard = 0;
        foreach ($nexpac_billing as $key => $val) {
            if ($val->weight !== null) {
                $count_rows_with_weight++;
            }

            if ($val->yards !== null) {
                $count_rows_with_yard++;
            }
        }
        return view('admin.modules.inventory.nexpac_report_details', compact('order_history', 'order_skew_number', 'nexpac_billing', 'count_rows_with_weight', 'count_rows_with_yard'));
    }

    public function billingReport()
    {
        return view('admin.modules.inventory.billing_report');
    }

    public function pnl_report()
    {
        $customers = Customer::orderBy('id', 'desc')->get();
        return view('admin.modules.inventory.pnl_report', compact('customers'));
    }

    public function getPnlReportByRlsNo(Request $request)
    {
        $release_number = $request->release_number;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer_id = $request->customer_id;

        $orders_data = [];

        // Query to retrieve PNL data based on filters
        $query = ScanOutInventory::join('customers', 'customers.id', '=', 'scan_out_inventories.customer_id')
            ->join('warehouses', 'warehouses.id', '=', 'scan_out_inventories.warehouse_id')
            ->join('scan_out_logs', 'scan_out_logs.scan_out_inventory_id', '=', 'scan_out_inventories.id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->select(
                'scan_out_inventories.id',
                'scan_out_inventories.release_number',
                'customers.name as customer',
                'warehouses.name as warehouse',
                'scan_out_inventories.container',
                'scan_out_inventories.tear_factor',
                'scan_out_inventories.seal',
                'scan_out_inventories.pallet_weight',
                'scan_out_inventories.tear_factor_weight',
                'scan_out_inventories.scale_discrepancy',
                'scan_out_inventories.pallet_on_container',
                'scan_out_inventories.created_at',
                DB::raw('SUM(scan_in_logs.weight) as weight_sum'),
                DB::raw('SUM(scan_in_logs.yards) as yards_sum'),
                DB::raw('COUNT(scan_out_logs.id) as pallet'),
                DB::raw('SUM(scan_in_logs.rolls) as roll_sum')
            )
            ->groupBy('scan_out_inventories.release_number');
        // ->orderBy('scan_out_inventories.id');

        $query->when($release_number, function ($query) use ($release_number) {
            return $query->where('scan_out_inventories.release_number', $release_number);
        });

        $query->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
            return $query->whereBetween('scan_out_inventories.created_at', [$from_date, $to_date]);
        });

        $query->when($customer_id, function ($query) use ($customer_id) {
            return $query->where('customers.id', $customer_id);
        });
        $orders_data = $query->get();
        $nexpac_billing_total_prices = $third_party_total_prices =
            $nextrade_billing = $third_party_prices = $nextrade_billing_total_prices = [];

        foreach ($orders_data as $order) {
            $order_id = $order->id;
            $tear_factor_weight_for_bill = $order->tear_factor_weight ?? 3.5;
            $scale_ticket_wgt = $order->scale_discrepancy;
            $total_weight_preloaded_lbs = $order->weight_sum;
            $pallets_tare_wgt = $order->pallet_weight * $order->pallet;
            $total_weight_loaded_lbs = $total_weight_preloaded_lbs - $pallets_tare_wgt;
            $scale_discrepancy = $scale_ticket_wgt - $total_weight_loaded_lbs;

            $weight['count_rows_with_weight1'] = 0;
            $weight['count_rows_with_next_trade_weight'] = 0;

            //For Nexpac
            $nexpac_billing1[$order_id] = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
                ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
                ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
                ->where('scan_out_inventories.id', $order_id)
                ->select('c_g_t_grades.grade_name as cgt_grade', 'c_g_t_grades.billing_code as cgt_code', DB::raw('SUM(scan_in_logs.weight) as weight_sum'), 'scan_in_logs.weight as weight', 'scan_in_logs.yards', DB::raw('SUM(scan_in_logs.yards) as yards_sum'), DB::raw('COUNT(scan_out_logs.id) as no_of_pallets'), 'c_g_t_grades.price as cgt_price', DB::raw('SUM(scan_in_logs.rolls) as rolls_sum'))
                ->groupBy('scan_in_logs.cgt')
                ->get();

            // For $nextrade_billing
            $nextrade_billing[$order_id] = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
                ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
                ->join('c_g_t_grades', 'scan_in_logs.cgt', '=', 'c_g_t_grades.id')
                ->where('scan_out_inventories.id', $order_id)
                ->select(
                    'c_g_t_grades.grade_name as cgt_grade',
                    'scan_out_logs.price as cgt_price',
                    DB::raw('COUNT(scan_out_logs.id) as no_of_pallets'),
                    'scan_in_logs.weight as weight',
                    'scan_in_logs.yards',
                    DB::raw('SUM(scan_in_logs.rolls) as rolls_sum'),
                    DB::raw('SUM(scan_in_logs.weight) as weight_sum'),
                    DB::raw('SUM(scan_in_logs.yards) as yards_sum')
                )
                ->groupBy('scan_in_logs.cgt')
                ->get();

            // $weight['count_rows_with_next_trade_weight2'] = 0;

            // foreach ($third_party_prices as $key => $val) {
            //     if ($val->weight !== null) {
            //         $weight['count_rows_with_next_trade_weight2']++;
            //     }
            // }


            //For third party prices
            $third_party_prices[$order_id] = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
                ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
                ->join('n_t_grades', 'scan_in_logs.nt', '=', 'n_t_grades.id')
                ->where('scan_out_inventories.id', $order_id)
                ->select(
                    'n_t_grades.grade_name as nt_grade',
                    'scan_out_logs.third_party_price as third_party_price',
                    DB::raw('COUNT(scan_out_logs.id) as no_of_pallets'),
                    'scan_in_logs.weight as weight',
                    'scan_in_logs.yards',
                    DB::raw('SUM(scan_in_logs.rolls) as rolls_sum'),
                    DB::raw('SUM(scan_in_logs.weight) as weight_sum'),
                    DB::raw('SUM(scan_in_logs.yards) as yards_sum')
                )
                ->groupBy('scan_in_logs.nt')
                ->get();

            foreach ($nexpac_billing1[$order_id] as $weightVal) {

                if ($weightVal->weight !== null) {
                    if (!isset($weight[$order_id])) {
                        $weight[$order_id] = [
                            'count_rows_with_weight1' => 0,
                        ];
                    }
                    $weight[$order_id]['count_rows_with_weight1']++;
                }
            }
            foreach ($nexpac_billing1[$order_id] as $val) {
                if (!isset($nexpac_billing_total_prices[$order_id])) {
                    $nexpac_billing_total_prices[$order_id] = [
                        'total_nexpac_wgt_sum' => 0,
                        'total_nexpac_yards_sum' => 0,
                        'total_paid_sum_of_all_cgts' => 0,
                    ];
                }
                $pallets = $val->no_of_pallets * ($order->pallet_weight ?? 0);
                $rolls = $val->rolls_sum * $tear_factor_weight_for_bill;
                $sum_of_pallets_and_rolls = $pallets + $rolls;

                $remaining_weight = ($val->weight_sum ?? 0) - ($sum_of_pallets_and_rolls ?? 0);
                $remaining_yards = ($val->yards_sum ?? 0) - ($sum_of_pallets_and_rolls ?? 0);
                if ($weight[$order_id]['count_rows_with_weight1'] != 0) {
                    $second_part_wgt = ($order->pallet_on_container * $order->pallet_weight) / $weight[$order_id]['count_rows_with_weight1'];
                    $third_part_wgt = $scale_discrepancy / $weight[$order_id]['count_rows_with_weight1'];
                }

                $nexpac_billable_wgt = $remaining_weight + $second_part_wgt + $third_part_wgt;

                $wgt_total = $nexpac_billable_wgt * $val->cgt_price;
                $validate_wgt_total = isset($val->weight_sum) && ($val->weight_sum != 0 || $val->weight_sum != null) ? $wgt_total : 0;
                $nexpac_billing_total_prices[$order_id]['total_nexpac_wgt_sum'] += $validate_wgt_total;

                $yards_total = $val->yards_sum * $val->cgt_price;
                $validate_yards_total = isset($val->yards_sum) && ($val->yards_sum != 0 || $val->yards_sum != null) ? $yards_total : 0;
                $nexpac_billing_total_prices[$order_id]['total_nexpac_yards_sum'] += $validate_yards_total ?? 0;

                $total_paid_per_each_cgt = $validate_wgt_total + $validate_yards_total;
                $nexpac_billing_total_prices[$order_id]['total_paid_sum_of_all_cgts'] += $total_paid_per_each_cgt;
            }

            foreach ($nextrade_billing[$order_id] as $weightVal) {
                if ($weightVal->weight !== null) {
                    if (!isset($weight[$order_id])) {
                        $weight[$order_id] = [
                            'count_rows_with_next_trade_weight' => 0,
                        ];
                    }
                    if (!isset($weight[$order_id]['count_rows_with_next_trade_weight'])) {
                        $weight[$order_id]['count_rows_with_next_trade_weight'] = 0;
                    }
                    $weight[$order_id]['count_rows_with_next_trade_weight']++;
                }
            }

            foreach ($nextrade_billing[$order_id] as $val) {
                if (!isset($nextrade_billing_total_prices[$order_id])) {
                    $nextrade_billing_total_prices[$order_id] = [
                        'total_nextrade_wgt_sum' => 0,
                        'total_nextrade_yards_sum' => 0,
                        'total_paid_sum_of_all_nts' => 0,
                    ];
                }

                $pallets = $val->no_of_pallets * ($order->pallet_weight ?? 0);
                $rolls = $val->rolls_sum * $tear_factor_weight_for_bill;
                $sum_of_pallets_and_rolls = $pallets + $rolls;
                $remaining_weight = ($val->weight_sum ?? 0) - ($sum_of_pallets_and_rolls ?? 0);
                $remaining_yards = ($val->yards_sum ?? 0) - ($sum_of_pallets_and_rolls ?? 0);
                if ($weight[$order_id]['count_rows_with_next_trade_weight'] != 0) {
                    $second_part_wgt = ($order->pallet_on_container * $order->pallet_weight) / $weight[$order_id]['count_rows_with_next_trade_weight'];
                    $third_part_wgt = $scale_discrepancy / $weight[$order_id]['count_rows_with_next_trade_weight'];
                }
                $nextrade_billable_wgt = $remaining_weight + $second_part_wgt + $third_part_wgt;

                //Total wgt sum
                $wgt_total = $nextrade_billable_wgt * $val->cgt_price;
                $validate_wgt_total = isset($val->weight_sum) && ($val->weight_sum != 0 || $val->weight_sum != null) ? $wgt_total : 0;
                $nextrade_billing_total_prices[$order_id]['total_nextrade_wgt_sum'] += $validate_wgt_total;

                // Total yards sum
                $yards_total = $val->yards_sum * $val->cgt_price;
                $validate_yards_total = isset($val->yards_sum) && ($val->yards_sum != 0 || $val->yards_sum != null) ? $yards_total : 0;
                $nextrade_billing_total_prices[$order_id]['total_nextrade_yards_sum'] += $validate_yards_total;

                $total_paid_per_each_nt = $validate_wgt_total + $validate_yards_total;
                $nextrade_billing_total_prices[$order_id]['total_paid_sum_of_all_nts'] = $nextrade_billing_total_prices[$order_id]['total_nextrade_wgt_sum'] + $nextrade_billing_total_prices[$order_id]['total_nextrade_yards_sum'];
            }



            foreach ($third_party_prices[$order_id] as $weightVal) {
                if ($weightVal->weight !== null) {
                    if (!isset($weight[$order_id])) {
                        $weight[$order_id] = [
                            'count_rows_with_next_trade_weight2' => 0,
                        ];
                    }
                    if (!isset($weight[$order_id]['count_rows_with_next_trade_weight2'])) {
                        $weight[$order_id]['count_rows_with_next_trade_weight2'] = 0;
                    }
                    $weight[$order_id]['count_rows_with_next_trade_weight2']++;
                }
            }

            foreach ($third_party_prices[$order_id] as $val) {
                if (!isset($third_party_total_prices[$order_id])) {
                    $third_party_total_prices[$order_id] = [
                        'total_third_party_wgt_sum' => 0,
                        'total_third_party_yards_sum' => 0,
                        'total_sum_of_third_prices' => 0,
                    ];
                }
                $pallets = $val->no_of_pallets * ($order->pallet_weight ?? 0);
                $rolls = $val->rolls_sum * $tear_factor_weight_for_bill;
                $sum_of_pallets_and_rolls = $pallets + $rolls;
                $remaining_weight = ($val->weight_sum ?? 0) - ($sum_of_pallets_and_rolls ?? 0);
                $remaining_yards = ($val->yards_sum ?? 0) - ($sum_of_pallets_and_rolls ?? 0);

                if ($weight[$order_id]['count_rows_with_next_trade_weight2'] != 0) {
                    $second_part_wgt = ($order->pallet_on_container * $order->pallet_weight) / $weight[$order_id]['count_rows_with_next_trade_weight2'];
                    $third_part_wgt = $scale_discrepancy / $weight[$order_id]['count_rows_with_next_trade_weight2'];
                }

                $third_party_billable_wgt = $remaining_weight + $second_part_wgt + $third_part_wgt;

                $wgt_total = $third_party_billable_wgt * $val->third_party_price;
                $validate_wgt_total = isset($val->weight_sum) && ($val->weight_sum != 0 || $val->weight_sum != null) ? $wgt_total : 0;
                $third_party_total_prices[$order_id]['total_third_party_wgt_sum'] += $validate_wgt_total;

                $yards_total = $val->yards_sum * $val->third_party_price;
                $validate_yards_total = isset($val->yards_sum) && ($val->yards_sum != 0 || $val->yards_sum != null) ? $yards_total : 0;
                $third_party_total_prices[$order_id]['total_third_party_yards_sum'] += $validate_yards_total ?? 0;

                $total_paid_per_each_nt = $validate_wgt_total + $validate_yards_total;
                $third_party_total_prices[$order_id]['total_sum_of_third_prices'] += $total_paid_per_each_nt;
            }
        }
        $page_title = 'PNL Report';
        return view('admin.modules.inventory.pnl_report_details', compact('page_title', 'orders_data', 'nextrade_billing_total_prices', 'nexpac_billing_total_prices', 'third_party_total_prices','from_date','to_date'));
    }


    public function getBillingReportByRefNo(Request $request)
    {

        // $validator = Validator::make($request->all(), [
        //     'reference_number' => 'required',
        // ], ['reference_number' => 'Reference number is required']);

        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator->messages())->withInput();
        // }
        $data = [];
        $reference_number = $request->reference_number;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        if ($reference_number) {
            $scan_in_id = ScanInInventory::where('reference_number', $reference_number)->first();
            if (!isset($scan_in_id) || empty($scan_in_id)) {

                return back()->with('reference_number_error', ['msg' => 'The Reference number data is not available', 'reference_number' => $reference_number]);
            }


            $data = ScanInInventory::where('reference_number', $reference_number)->with(['supplier', 'warehouse'])->first();
        }
        $cgt_inventory = DB::table('scan_in_logs')
            ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
            ->join('product_types', 'product_types.id', '=', 'scan_in_logs.product_type')
            ->join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
            ->when($reference_number, function ($query) use ($reference_number) {
                $query->where('scan_in_inventories.reference_number', $reference_number);
            })
            ->when(!empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                $query->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                    ->whereDate('scan_in_inventories.created_at', '<=', $to_date)
                    ->groupBy('new_date', 'reference_number')
                    ->distinct('new_date');
            })
            // ->when(empty($from_date) && empty($to_date), function ($query) {
            //     $query->groupBy('year', 'month', 'day');
            // })
            ->select(
                'c_g_t_grades.grade_name as cgt_grade',
                DB::raw('sum(scan_in_logs.weight) as weight'),
                DB::raw('sum(scan_in_logs.yards) as yards'),
                DB::raw('sum(scan_in_logs.rolls) as rolls_sum'),
                DB::raw('COUNT(scan_in_logs.id) as pallet_count'),
                DB::raw('product_types.product_type as product_type'),
                DB::raw('scan_in_inventories.reference_number as reference_number'),
                DB::raw('product_types.slug as slug'),
                DB::raw("DATE_FORMAT(scan_in_logs.created_at, '%d-%m-%Y') new_date"),
                DB::raw("DATE_FORMAT(scan_in_logs.created_at, '%m-%Y') month_year"),
                DB::raw('YEAR(scan_in_logs.created_at) year, MONTH(scan_in_logs.created_at) month, DAY(scan_in_logs.created_at) day'),
                DB::raw("CASE WHEN c_g_t_grades.grade_name LIKE '%paper%' THEN COUNT(scan_in_logs.id) * 80 ELSE COUNT(scan_in_logs.id) * 55 END as pallet_tare")
            )
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->orderBy('day', 'asc')
            ->groupBy('c_g_t_grades.grade_name', 'c_g_t_grades.id', 'scan_in_logs.product_type')
            ->get();

        $page_title = 'Billing Report';
        return view('admin.modules.inventory.billing_report_details', compact('page_title', 'cgt_inventory', 'reference_number', 'data', 'to_date', 'from_date'));
    }


    public function internalReport()
    {
        return view('admin.modules.inventory.internal_report');
    }

    public function getInternalReportByRlsNo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'release_number' => 'required',
        ], ['release_number' => 'Release number is required']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $release_number = $request->release_number;
        $weight = [];
        $order_data = ScanOutInventory::where('release_number', $release_number)->Join('customers', 'customers.id', '=', 'scan_out_inventories.customer_id')->Join('warehouses', 'warehouses.id', '=', 'scan_out_inventories.warehouse_id')
            ->Join('scan_out_logs', 'scan_out_logs.scan_out_inventory_id', '=', 'scan_out_inventories.id')->Join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')->select(
                'scan_out_inventories.id',
                'scan_out_inventories.release_number',
                'customers.name as customer',
                'warehouses.name as warehouse',
                'scan_out_inventories.container',
                'scan_out_inventories.tear_factor',
                'scan_out_inventories.seal',
                'scan_out_inventories.pallet_weight',
                'scan_out_inventories.tear_factor_weight',
                'scan_out_inventories.scale_discrepancy',
                'scan_out_inventories.pallet_on_container',
                'scan_out_inventories.created_at',
                DB::raw('SUM(scan_in_logs.weight) as weight_sum'),
                DB::raw('SUM(scan_in_logs.yards) as yards_sum'),
                DB::raw('COUNT(scan_out_logs.id) as pallet'),
                DB::raw('SUM(scan_in_logs.rolls) as roll_sum')
            )->first();


        if (!isset($order_data->id) || empty($order_data->id)) {

            return back()->with('release_number_error', ['msg' => 'The Release number data is not available', 'release_number' => $release_number]);
        }

        $order_skew_number = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
            ->join('n_t_grades', 'n_t_grades.id', '=', 'scan_in_logs.nt')
            ->join('product_types', 'product_types.id', '=', 'scan_in_logs.product_type')
            ->join('colors', 'colors.id', '=', 'scan_in_logs.color')->where('scan_out_inventories.id', $order_data->id)->select('scan_in_logs.skew_number', 'product_types.product_type', 'c_g_t_grades.grade_name as cgt_grade', 'n_t_grades.grade_name as nt_grade', 'colors.name as color_name', 'scan_in_logs.rolls', 'scan_in_logs.weight as weight', 'scan_in_logs.yards as yards')->get();

        $nexpac_billing1 = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('c_g_t_grades', 'c_g_t_grades.id', '=', 'scan_in_logs.cgt')
            ->where('scan_out_inventories.id', $order_data->id)->select('c_g_t_grades.grade_name as cgt_grade', 'c_g_t_grades.billing_code as cgt_code', DB::raw('SUM(scan_in_logs.weight) as weight_sum'), 'scan_in_logs.weight', 'scan_in_logs.yards', DB::raw('SUM(scan_in_logs.yards) as yards_sum'), DB::raw('COUNT(scan_out_logs.id) as no_of_pallets'), 'c_g_t_grades.price as cgt_price', DB::raw('SUM(scan_in_logs.rolls) as rolls_sum'))->groupBy('scan_in_logs.cgt')->get();
        $nextrade_billing = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('c_g_t_grades', 'scan_in_logs.cgt', '=', 'c_g_t_grades.id')
            ->where('scan_out_inventories.id', $order_data->id)->select('c_g_t_grades.grade_name as cgt_grade', 'scan_out_logs.price as cgt_price', DB::raw('COUNT(scan_out_logs.id) as no_of_pallets'),  'scan_in_logs.weight', 'scan_in_logs.yards', DB::raw('SUM(scan_in_logs.rolls) as rolls_sum'), DB::raw('SUM(scan_in_logs.weight) as weight_sum'), DB::raw('SUM(scan_in_logs.yards) as yards_sum'))->groupBy('scan_in_logs.cgt')->get();
        $third_party_prices = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('n_t_grades', 'scan_in_logs.nt', '=', 'n_t_grades.id')
            ->where('scan_out_inventories.id', $order_data->id)->select('n_t_grades.grade_name as nt_grade', 'scan_out_logs.third_party_price as third_party_price', DB::raw('COUNT(scan_out_logs.id) as no_of_pallets'),  'scan_in_logs.weight', 'scan_in_logs.yards', DB::raw('SUM(scan_in_logs.rolls) as rolls_sum'), DB::raw('SUM(scan_in_logs.weight) as weight_sum'), DB::raw('SUM(scan_in_logs.yards) as yards_sum'))->groupBy('scan_in_logs.nt')->get();
        $profit2 = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('n_t_grades', 'scan_in_logs.nt', '=', 'n_t_grades.id')
            ->where('scan_out_inventories.id', $order_data->id)->select('n_t_grades.grade_name as nt_grade', 'scan_out_logs.price as nt_price', 'scan_out_logs.third_party_price as third_party_price')->groupBy('scan_in_logs.nt')->get();
        $weight['count_rows_with_weight1'] = 0;
        $weight['count_rows_with_next_trade_weight'] = 0;
        $weight['count_rows_with_next_trade_weight2'] = 0;
        foreach ($nexpac_billing1 as $key => $val) {
            if ($val->weight !== null) {
                $weight['count_rows_with_weight1']++;
            }
        }
        foreach ($third_party_prices as $key => $val) {
            if ($val->weight !== null) {
                $weight['count_rows_with_next_trade_weight2']++;
            }
        }
        foreach ($nextrade_billing as $key => $val) {
            if ($val->weight !== null) {
                $weight['count_rows_with_next_trade_weight']++;
            }
        }
        $scan_logs_group_data = $this->getInternalReportLogsGroupData($order_data->id);
        $page_title = 'Internal Report';
        return view('admin.modules.inventory.internal_report_details', compact('page_title', 'order_data', 'order_skew_number', 'nexpac_billing1', 'third_party_prices', 'nextrade_billing', 'scan_logs_group_data', 'weight', 'profit2'));
    }


    protected function getInternalReportLogsGroupData($scan_out_inventory_id)
    {

        $nt_grade_group_data = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
            ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
            ->join('n_t_grades', 'scan_in_logs.nt', '=', 'n_t_grades.id')
            ->join('c_g_t_grades', 'scan_in_logs.cgt', '=', 'c_g_t_grades.id')
            ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
            ->where('scan_out_inventories.id', $scan_out_inventory_id)
            ->select('n_t_grades.id as nt_id', 'n_t_grades.grade_name as nt_grade', DB::raw('SUM(scan_in_logs.weight) as total_weight'))
            ->groupBy('n_t_grades.id')
            ->get();

        $cgt_grade_group_data = [];
        $color_group_data = [];

        foreach ($nt_grade_group_data as $nt_key => $nt) {

            $cgt_grade_group_result = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
                ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
                ->join('n_t_grades', 'scan_in_logs.nt', '=', 'n_t_grades.id')
                ->join('c_g_t_grades', 'scan_in_logs.cgt', '=', 'c_g_t_grades.id')
                ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
                ->where('scan_out_inventories.id', $scan_out_inventory_id)
                ->where('scan_in_logs.nt', $nt->nt_id)
                ->select('n_t_grades.id as nt_id', 'c_g_t_grades.id as cgt_id', 'c_g_t_grades.grade_name as cgt_grade', DB::raw('SUM(scan_in_logs.weight) as total_weight'))
                ->groupBy('c_g_t_grades.id')
                ->get();

            foreach ($cgt_grade_group_result as $cgt_key => $cgt) {

                $color_group_data[$nt_key][$cgt_key] = ScanOutLog::join('scan_out_inventories', 'scan_out_inventories.id', '=', 'scan_out_logs.scan_out_inventory_id')
                    ->join('scan_in_logs', 'scan_in_logs.id', '=', 'scan_out_logs.scan_in_id')
                    ->join('n_t_grades', 'scan_in_logs.nt', '=', 'n_t_grades.id')
                    ->join('c_g_t_grades', 'scan_in_logs.cgt', '=', 'c_g_t_grades.id')
                    ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
                    ->where('scan_out_inventories.id', $scan_out_inventory_id)
                    ->where('scan_in_logs.nt', $nt->nt_id)
                    ->where('scan_in_logs.cgt', $cgt->cgt_id)
                    ->select('colors.id as color_id', 'colors.name as color_name', DB::raw('SUM(scan_in_logs.weight) as total_weight'))
                    ->groupBy('colors.id')
                    ->get();
            }

            $cgt_grade_group_data[] = $cgt_grade_group_result;
        }

        $data = [
            'nt_grade_group_data' => $nt_grade_group_data,
            'cgt_grade_group_data' => $cgt_grade_group_data,
            'color_group_data' => $color_group_data
        ];

        return $data;
    }


    protected function getCgtReportData($request, $type)
    {

        $cgt_id = $request->cgt_id;
        $nt_id = $request->nt_id;
        $color_id = $request->color_id;
        $warehouse_id = $request->warehouse_id;
        $product_type_id = $request->product_type_id;
        $year = $request->year;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $currentDate = Carbon::now()->format('Y-m-d');

        if ($type == 'cgt') {
            $cgt_grade_group_data = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
                ->join('c_g_t_grades as cgt_grades', 'scan_in_logs.cgt', '=', 'cgt_grades.id')
                ->join('n_t_grades as nt_grades', 'scan_in_logs.nt', '=', 'nt_grades.id');
            // ->where('scan_in_logs.is_scan_out', 0);
            // Add filters based on request parameters
            if (!empty($warehouse_id)) {
                $cgt_grade_group_data->where('scan_in_inventories.warehouse_id', $warehouse_id);
            }
            if (!empty($product_type_id)) {
                $cgt_grade_group_data->where('scan_in_logs.product_type', $product_type_id);
            }
            if (!empty($cgt_id)) {
                $cgt_grade_group_data->where('scan_in_logs.cgt', $cgt_id);
            }
            if (!empty($year) && $year != 'current' && empty($from_date) && empty($to_date)) {
                $cgt_grade_group_data->whereYear('scan_in_inventories.created_at', $year);
            }
            if (empty($year) && !empty($from_date) && !empty($to_date)) {
                $cgt_grade_group_data->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                    ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
            }
            if (!empty($year) && $year == 'current' && empty($from_date) && empty($to_date)) {
                $cgt_grade_group_data->where('scan_in_logs.is_scan_out', 0);
            }
            // Group by CGT and NT, and select the relevant fields
            $cgt_grade_group_data->groupBy('cgt_grades.id')
                ->select('cgt_grades.id as cgt_id', 'cgt_grades.grade_name as cgt_grade_name', DB::raw('SUM(scan_in_logs.weight) as total_weight'), DB::raw('SUM(scan_in_logs.yards) as total_yards'));
            // Execute thquery and return the results

            $nt_grade_group_data = [];
            $color_group_data = [];
            $cgt_grade_group_data =  $cgt_grade_group_data->get();
            foreach ($cgt_grade_group_data as $cgt_key => $cgt) {

                $nt_grade_group_result = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                    ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
                    ->join('c_g_t_grades as cgt_grades', 'scan_in_logs.cgt', '=', 'cgt_grades.id')
                    ->join('n_t_grades as nt_grades', 'scan_in_logs.nt', '=', 'nt_grades.id')
                    // ->where('scan_in_logs.is_scan_out', 0)
                    ->where('scan_in_logs.cgt', $cgt->cgt_id);
                // Add filters based on request parameters
                if (!empty($warehouse_id)) {
                    $nt_grade_group_result->where('scan_in_inventories.warehouse_id', $warehouse_id);
                }
                if (!empty($product_type_id)) {
                    $nt_grade_group_result->where('scan_in_logs.product_type', $product_type_id);
                }
                if (!empty($nt_id)) {
                    $nt_grade_group_result->where('scan_in_logs.nt', $nt_id);
                }
                if (!empty($year) && $year != 'current' && empty($from_date) && empty($to_date)) {
                    $nt_grade_group_result->whereYear('scan_in_inventories.created_at', $year);
                }
                if (empty($year) && !empty($from_date) && !empty($to_date)) {
                    $nt_grade_group_result->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                        ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
                }
                if (!empty($year) && $year == 'current' && empty($from_date) && empty($to_date)) {
                    $nt_grade_group_result->where('scan_in_logs.is_scan_out', 0);
                }
                // Group by CGT and NT, and select the relevant fields
                $nt_grade_group_result->groupBy('nt_grades.id')
                    ->select('nt_grades.id as nt_id', 'cgt_grades.id as cgt_id', 'nt_grades.grade_name as nt_grade', DB::raw('SUM(scan_in_logs.weight) as total_weight'), DB::raw('SUM(scan_in_logs.yards) as total_yards'));
                // Execute thquery and return the results
                $nt_grade_group_result = $nt_grade_group_result->get();
                foreach ($nt_grade_group_result as $nt_key => $nt) {

                    $color_group_data[$cgt_key][$nt_key] = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                        ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
                        ->join('c_g_t_grades as cgt_grades', 'scan_in_logs.cgt', '=', 'cgt_grades.id')
                        ->join('n_t_grades as nt_grades', 'scan_in_logs.nt', '=', 'nt_grades.id')
                        // ->where('scan_in_logs.is_scan_out', 0)
                        ->where('scan_in_logs.cgt', $cgt->cgt_id)
                        ->where('scan_in_logs.nt', $nt->nt_id)
                        ->when(!empty($year) && $year == 'current' && empty($from_date) && empty($to_date), function ($query) use ($currentDate) {
                            $query->where('scan_in_logs.is_scan_out', 0);
                        })
                        ->when(!empty($year) && $year != 'current' && empty($from_date) && empty($to_date), function ($query) use ($year) {
                            $query->whereYear('scan_in_inventories.created_at', $year);
                        })
                        ->when(empty($year) && !empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                            $query->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                                ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
                        })
                        // Add filters based on request parameters
                        ->groupBy('colors.id')
                        ->select('cgt_grades.id as cgt_id', 'nt_grades.id as nt_id', 'colors.id as color_id', 'colors.name as color_name', DB::raw('SUM(scan_in_logs.weight) as total_weight'), DB::raw('SUM(scan_in_logs.yards) as total_yards'))->get();
                    // Execute thquery and return the results
                }
                $nt_grade_group_data[] = $nt_grade_group_result;
            }
            $data = [
                'cgt_grade_group_data' => $cgt_grade_group_data,
                'nt_grade_group_data' => $nt_grade_group_data,
                'color_group_data' => $color_group_data
            ];
        }

        if ($type == 'nt') {
            $nt_grade_group_data2 = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
                ->join('c_g_t_grades as cgt_grades', 'scan_in_logs.cgt', '=', 'cgt_grades.id')
                ->join('n_t_grades as nt_grades', 'scan_in_logs.nt', '=', 'nt_grades.id');
            // ->where('scan_in_logs.is_scan_out', 0);
            // Add filters based on request parameters
            if (!empty($warehouse_id)) {
                $nt_grade_group_data2->where('scan_in_inventories.warehouse_id', $warehouse_id);
            }
            if (!empty($product_type_id)) {
                $nt_grade_group_data2->where('scan_in_logs.product_type', $product_type_id);
            }
            if (!empty($nt_id)) {
                $nt_grade_group_data2->where('scan_in_logs.nt', $nt_id);
            }
            if (!empty($year) && $year != 'current' && empty($from_date) && empty($to_date)) {
                $nt_grade_group_data2->whereYear('scan_in_inventories.created_at', $year);
            }
            if (empty($year) && !empty($from_date) && !empty($to_date)) {
                $nt_grade_group_data2->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                    ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
            }
            if (!empty($year) && $year == 'current' && empty($from_date) && empty($to_date)) {
                $nt_grade_group_data2->where('scan_in_logs.is_scan_out', 0);
            }
            // Group by CGT and NT, and select the relevant fields
            $nt_grade_group_data2->groupBy('nt_grades.id')
                ->select('nt_grades.id as nt_id', 'nt_grades.grade_name as nt_grade_name', DB::raw('SUM(scan_in_logs.weight) as total_weight'), DB::raw('SUM(scan_in_logs.yards) as total_yards'));
            // Execute thquery and return the results

            $cgt_grade_group_data2 = [];
            $color_group_data2 = [];
            $nt_grade_group_data2 =  $nt_grade_group_data2->get();
            foreach ($nt_grade_group_data2 as $nt_key => $nt) {

                $cgt_grade_group_result2 = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                    ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
                    ->join('c_g_t_grades as cgt_grades', 'scan_in_logs.cgt', '=', 'cgt_grades.id')
                    ->join('n_t_grades as nt_grades', 'scan_in_logs.nt', '=', 'nt_grades.id')
                    // ->where('scan_in_logs.is_scan_out', 0)
                    ->where('scan_in_logs.nt', $nt->nt_id);
                // Add filters based on request parameters
                if (!empty($warehouse_id)) {
                    $cgt_grade_group_result2->where('scan_in_inventories.warehouse_id', $warehouse_id);
                }
                if (!empty($product_type_id)) {
                    $cgt_grade_group_result2->where('scan_in_logs.product_type', $product_type_id);
                }
                if (!empty($cgt_id)) {
                    $cgt_grade_group_result2->where('scan_in_logs.cgt', $cgt_id);
                }
                if (!empty($year) && $year != 'current' && empty($from_date) && empty($to_date)) {
                    $cgt_grade_group_result2->whereYear('scan_in_inventories.created_at', $year);
                }
                if (empty($year) && !empty($from_date) && !empty($to_date)) {
                    $cgt_grade_group_result2->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                        ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
                }
                if (!empty($year) && $year == 'current' && empty($from_date) && empty($to_date)) {
                    $cgt_grade_group_result2->where('scan_in_logs.is_scan_out', 0);
                }
                // Group by CGT and NT, and select the relevant fields
                $cgt_grade_group_result2->groupBy('cgt_grades.id')
                    ->select('cgt_grades.id as cgt_id', 'nt_grades.id as nt_id', 'cgt_grades.grade_name as cgt_grade_name', DB::raw('SUM(scan_in_logs.weight) as total_weight', DB::raw('SUM(scan_in_logs.yards) as total_yards')));
                // Execute thquery and return the results
                $cgt_grade_group_result2 = $cgt_grade_group_result2->get();
                foreach ($cgt_grade_group_result2 as $cgt_key => $cgt) {
                    $color_group_data2[$nt_key][$cgt_key] = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                        ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
                        ->join('c_g_t_grades as cgt_grades', 'scan_in_logs.cgt', '=', 'cgt_grades.id')
                        ->join('n_t_grades as nt_grades', 'scan_in_logs.nt', '=', 'nt_grades.id')
                        // ->where('scan_in_logs.is_scan_out', 0)
                        ->where('scan_in_logs.nt', $nt->nt_id)
                        ->where('scan_in_logs.cgt', $cgt->cgt_id)
                        ->when(!empty($year) && $year == 'current' && empty($from_date) && empty($to_date), function ($query) use ($currentDate) {
                            $query->where('scan_in_logs.is_scan_out', 0);
                        })
                        ->when(!empty($year) && $year != 'current' && empty($from_date) && empty($to_date), function ($query) use ($year) {
                            $query->whereYear('scan_in_inventories.created_at', $year);
                        })
                        ->when(empty($year) && !empty($from_date) && !empty($to_date), function ($query) use ($from_date, $to_date) {
                            $query->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                                ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
                        })
                        // Add filters based on request parameters
                        ->groupBy('colors.id')
                        ->select('colors.id as color_id', 'colors.name as color_name', DB::raw('SUM(scan_in_logs.weight) as total_weight'), DB::raw('SUM(scan_in_logs.yards) as total_yards'))->get();
                    // Execute thquery and return the results
                }
                $cgt_grade_group_data2[] = $cgt_grade_group_result2;
            }
            $data = [
                'nt_grade_group_data2' => $nt_grade_group_data2,
                'cgt_grade_group_data2' => $cgt_grade_group_data2,
                'color_group_data2' => $color_group_data2
            ];
        }

        if ($type == 'color') {
            $color_grade_group_data = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
                ->join('c_g_t_grades as cgt_grades', 'scan_in_logs.cgt', '=', 'cgt_grades.id')
                ->join('n_t_grades as nt_grades', 'scan_in_logs.nt', '=', 'nt_grades.id');
            // ->where('scan_in_logs.is_scan_out', 0);
            // Add filters based on request parameters
            if (!empty($warehouse_id)) {
                $color_grade_group_data->where('scan_in_inventories.warehouse_id', $warehouse_id);
            }
            if (!empty($product_type_id)) {
                $color_grade_group_data->where('scan_in_logs.product_type', $product_type_id);
            }
            if (!empty($color_id)) {
                $color_grade_group_data->where('scan_in_logs.color', $color_id);
            }
            if (!empty($year) && $year != 'current' && empty($from_date) && empty($to_date)) {
                $color_grade_group_data->whereYear('scan_in_inventories.created_at', $year);
            }
            if (empty($year) && !empty($from_date) && !empty($to_date)) {
                $color_grade_group_data->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                    ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
            }
            if (!empty($year) && $year == 'current' && empty($from_date) && empty($to_date)) {
                $color_grade_group_data->where('scan_in_logs.is_scan_out', 0);
            }
            // Group by CGT and NT, and select the relevant fields
            $color_grade_group_data->groupBy('colors.id')
                ->select('colors.id as color_id', 'colors.name as color_name', DB::raw('SUM(scan_in_logs.weight) as total_weight'), DB::raw('SUM(scan_in_logs.yards) as total_yards'));
            // Execute thquery and return the results

            $nt_grade_group_data3 = [];
            $cgt_all_data = [];
            $color_grade_group_data =  $color_grade_group_data->get();
            foreach ($color_grade_group_data as $color_key => $color) {

                $nt_group_result3 = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                    ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
                    ->join('c_g_t_grades as cgt_grades', 'scan_in_logs.cgt', '=', 'cgt_grades.id')
                    ->join('n_t_grades as nt_grades', 'scan_in_logs.nt', '=', 'nt_grades.id')
                    // ->where('scan_in_logs.is_scan_out', 0)
                    ->where('scan_in_logs.color', $color->color_id);
                // Add filters based on request parameters
                if (!empty($warehouse_id)) {
                    $nt_group_result3->where('scan_in_inventories.warehouse_id', $warehouse_id);
                }
                if (!empty($product_type_id)) {
                    $nt_group_result3->where('scan_in_logs.product_type', $product_type_id);
                }
                if (!empty($nt_id)) {
                    $nt_group_result3->where('scan_in_logs.nt', $nt_id);
                }
                if (!empty($year) && $year != 'current' && empty($from_date) && empty($to_date)) {
                    $nt_group_result3->whereYear('scan_in_inventories.created_at', $year);
                }
                if (empty($year) && !empty($from_date) && !empty($to_date)) {
                    $nt_group_result3->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                        ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
                }
                if (!empty($year) && $year == 'current' && empty($from_date) && empty($to_date)) {
                    $nt_group_result3->where('scan_in_logs.is_scan_out', 0);
                }
                // Group by CGT and NT, and select the relevant fields
                $nt_group_result3->groupBy('nt_grades.id')
                    ->select('cgt_grades.id as cgt_id', 'nt_grades.id as nt_id', 'nt_grades.grade_name as nt_grade_name', DB::raw('SUM(scan_in_logs.weight) as total_weight'), DB::raw('SUM(scan_in_logs.yards) as total_yards'));
                // Execute thquery and return the results
                $nt_group_result3 = $nt_group_result3->get();
                foreach ($nt_group_result3 as $nt_key => $nt) {
                    $cgt_grade_group_data3 = ScanInLog::join('scan_in_inventories', 'scan_in_inventories.id', '=', 'scan_in_logs.scan_in_inventory_id')
                        ->join('colors', 'scan_in_logs.color', '=', 'colors.id')
                        ->join('c_g_t_grades as cgt_grades', 'scan_in_logs.cgt', '=', 'cgt_grades.id')
                        ->join('n_t_grades as nt_grades', 'scan_in_logs.nt', '=', 'nt_grades.id')
                        // ->where('scan_in_logs.is_scan_out', 0)
                        ->where('scan_in_logs.color', $color->color_id)
                        ->where('scan_in_logs.nt', $nt->nt_id);
                    // Add filters based on request parameters
                    // Add filters based on request parameters
                    if (!empty($warehouse_id)) {
                        $cgt_grade_group_data3->where('scan_in_inventories.warehouse_id', $warehouse_id);
                    }
                    if (!empty($product_type_id)) {
                        $cgt_grade_group_data3->where('scan_in_logs.product_type', $product_type_id);
                    }
                    if (!empty($cgt_id)) {
                        $cgt_grade_group_data3->where('scan_in_logs.cgt', $cgt_id);
                    }
                    if (!empty($year) && $year != 'current' && empty($from_date) && empty($to_date)) {
                        $cgt_grade_group_data3->whereYear('scan_in_inventories.created_at', $year);
                    }
                    if (empty($year) && !empty($from_date) && !empty($to_date)) {
                        $cgt_grade_group_data3->whereDate('scan_in_inventories.created_at', '>=', $from_date)
                            ->whereDate('scan_in_inventories.created_at', '<=', $to_date);
                    };
                    if (!empty($year) && $year == 'current' && empty($from_date) && empty($to_date)) {
                        $cgt_grade_group_data3->where('scan_in_logs.is_scan_out', 0);
                    }
                    $cgt_grade_group_data3->groupBy('cgt_grades.id')
                        ->select('cgt_grades.id as cgt_id', 'cgt_grades.grade_name as cgt_grade_name', DB::raw('SUM(scan_in_logs.weight) as total_weight'), DB::raw('SUM(scan_in_logs.yards) as total_yards'));
                    $cgt_all_data[$color_key][$nt_key] = $cgt_grade_group_data3->get();
                }
                $nt_grade_group_data3[] = $nt_group_result3;
            }
            $data = [
                'color_grade_group_data' => $color_grade_group_data,
                'nt_grade_group_data3' => $nt_grade_group_data3,
                'cgt_grade_group_data3' => $cgt_all_data
            ];
        }

        return $data;
    }
}
