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
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    function importData(Request $request)
    {
        $this->validate($request, [
            'import_inventory' => 'required|file|mimes:xls,xlsx'
        ]);
        $the_file = $request->file('import_inventory');
        try {
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            // if ($row_limit > 1000) {
            //     $row_limit = 1000;
            // }
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range(6, $row_limit);
            // $column_range = range('F', $column_limit);
            // $startcount = 2
            $data = [];
            $existingSkewNumbers = []; // Array to store existing Skew numbers
            $errorMessages = []; // Array to store error messages

            $grouped_data = array_reduce($row_range, function ($result, $item) use ($sheet) {
                $skew = $sheet->getCell('E' . $item)->getValue();
                $rolls = intval($sheet->getCell('F' . $item)->getValue());
                $weight = intval($sheet->getCell('G' . $item)->getValue());
                $product = $sheet->getCell('H' . $item)->getValue();
                $cgtGrade = $sheet->getCell('I' . $item)->getValue();
                $cgtGradeName = $sheet->getCell('J' . $item)->getValue();
                $ntGrade = $sheet->getCell('M' . $item)->getValue();
                $ntGradeName = ($sheet->getCell('N' . $item)->getValue());
                $color_slug = $sheet->getCell('O' . $item)->getValue();
                $colorName = $sheet->getCell('P' . $item)->getValue();
                $referenceNumber = $sheet->getCell('C' . $item)->getValue();
                $ref_date = $sheet->getCell('D' . $item)->getFormattedValue();
                $releaseNumber = $sheet->getCell('T' . $item)->getValue();
                $ship_date = $sheet->getCell('S' . $item)->getFormattedValue();
                $customer_name = $sheet->getCell('Q' . $item)->getValue();
                $is_scan_out_val = ($sheet->getCell('W' . $item)->getValue());

                $result_key = "{$referenceNumber}";
                if (!isset($result[$result_key])) {
                    $result[$result_key] = array(
                        'referenceNumber' => $referenceNumber,
                        'ref_date' => (isset($ref_date) && !empty($ref_date) ? date('Y-m-d', strtotime($ref_date)) : ''),
                        'rows' => array()
                    );
                }
                $result[$result_key]['rows'][] = array(
                    'skew' => $skew,
                    'rolls' => $rolls,
                    'weight' => $weight,
                    'product' => $product,
                    'cgtGrade' => $cgtGrade,
                    'cgtGradeName' => $cgtGradeName,
                    'ntGrade' => $ntGrade,
                    'ntGradeName' => $ntGradeName,
                    'color_slug' => $color_slug,
                    'colorName' => $colorName,
                    'customer_name' => $customer_name,
                    'is_scan_out_val' => $is_scan_out_val,
                    'releaseNumber' => $releaseNumber,
                    'ref_date' => (isset($ref_date) && !empty($ref_date) ? date('Y-m-d', strtotime($ref_date)) : ''),
                    'ship_date' => (isset($ship_date) && !empty($ship_date) ? date('Y-m-d', strtotime($ship_date)) : '')
                );
                return $result;
            }, array());

            $data = array_values($grouped_data);
            foreach ($data as $key => $val) {
                $reference_number = $val['referenceNumber'];
                $exist = ScanInInventory::where('reference_number', $reference_number)->first();
                if (!$exist) {
                    $supplier_exist = Supplier::where('name', 'CGT')->value('id');
                    if (!$supplier_exist) {
                        $supplier = Supplier::create(['name' => 'CGT']);
                        $supplier_exist = $supplier->id;
                    }
                    $warehouse_exist = Warehouse::where('name', 'USA')->value('id');
                    if (!$warehouse_exist) {
                        $warehouse = Warehouse::create(['name' => 'USA']);
                        $warehouse_exist = $warehouse->id;
                    }
                    $scanInInventory = new ScanInInventory();
                    $scanInInventory->reference_number = $reference_number;
                    $scanInInventory->warehouse_id = $warehouse_exist;
                    $scanInInventory->supplier_id = $supplier_exist;
                    $scanInInventory->created_at = date('Y-m-d', strtotime($val['ref_date']));
                    $scanInInventory->save();
                    $scan_in_inventory_id = $scanInInventory->id;

                    if (isset($val['rows']) && !empty($val['rows'])) {
                        foreach ($val['rows'] as $key1 => $val2) {

                            $val2_skew = $val2['skew'];
                            $last_character = substr($val2_skew, -1); // Extract the last character

                            if (ctype_alpha($last_character)) {
                                $random_number = ord(strtoupper($last_character)) - ord('A') + 1;
                            } else {
                                $random_number = 1; // Set a default value if the last character is not an alphabet
                            }

                            // Determine product based on ntGrade
                            $product = ($val2['ntGrade'] == 'R') ? 'P' : (($val2['ntGrade'] == 'Z') ? 'F' : (($val2['ntGrade'] == 'O') ? 'O' : (($val2['ntGrade'] == 'H') ? 'H' : 'L')));
                            // Determine is_scan_out based on is_scan_out_val
                            $is_scan_out = ($val2['is_scan_out_val'] == 'N') ? 0 : 1;


                            // Create new Skew number based on the given format
                            $newSkewNumber = 'W.' . $val2['cgtGrade'] . 'U' . '.' . $val2['ntGrade'] . 'U' . '.' . $product . '.' . $val2['color_slug'] . '.' . $val2['rolls'] . '.' . $val2['weight'] . '.' . $random_number;

                            // Check if Skew number already exists in the sheet
                            while (in_array($newSkewNumber, $existingSkewNumbers)) {
                                $newSkewNumber = 'W.' . $val2['cgtGrade'] . 'U' . '.' . $val2['ntGrade'] . 'U' . '.' . $product . '.' . $val2['color_slug'] . '.' . $val2['rolls'] . '.' . $val2['weight'] . '.' . $random_number;
                            }

                            // Store the new Skew number in the array
                            $existingSkewNumbers[] = $newSkewNumber;
                            if ($product) {
                                $product_type = ProductType::where('slug', $product)->value('id');
                                if (!$product_type) {
                                    $product_name = ($val2['ntGrade'] == 'R') ? 'Paper' : (($val2['ntGrade'] == 'Z') ? 'Fabrics' : (($val2['ntGrade'] == 'O') ? 'Obsolete' : (($val2['ntGrade'] == 'H') ? 'Headliner' : 'Leather')));
                                    $result = ProductType::create(['product_type' => $product_name, 'slug' => $product]);
                                    $product_type = $result->id;
                                }
                            } else {
                                $product_type = null;
                            }
                            if ($val2['cgtGrade']) {
                                $cgt = CGTGrade::where('slug', $val2['cgtGrade'] . 'U')->value('id');
                                if (!$cgt) {
                                    $result = CGTGrade::create(['grade_name' => $val2['cgtGradeName'], 'slug' => $val2['cgtGrade'] . 'U']);
                                    $cgt = $result->id;
                                }
                            } else {
                                $cgt = null;
                            }
                            if ($val2['ntGrade']) {
                                $nt = NTGrade::where('slug', $val2['ntGrade'] . 'U')->value('id');
                                if (!$nt) {
                                    $result = NTGrade::create(['grade_name' => $val2['ntGradeName'], 'slug' => $val2['ntGrade'] . 'U']);
                                    $nt = $result->id;
                                }
                            } else {
                                $nt = null;
                            }
                            if ($val2['color_slug']) {
                                $color = Color::where('slug', $val2['color_slug'])->value('id');
                                if (!$color) {
                                    $result = Color::create(['name' => $val2['colorName'], 'slug' => $val2['color_slug']]);
                                    $color = $result->id;
                                }
                            } else {
                                $color = null;
                            }


                            $scan_in = ScanInLog::create([
                                'scan_in_inventory_id' => $scan_in_inventory_id,
                                'unit' => 'W',
                                'skew_number' => $newSkewNumber,
                                'rolls' => $val2['rolls'],
                                'weight' => $val2['weight'],
                                'product_type' => $product_type,
                                'cgt' => $cgt,
                                'nt' => $nt,
                                'color' => $color,
                                'created_at' => date('Y-m-d', strtotime($val2['ref_date']))
                                // 'customer' => $sheet->getCell('Q' . $row)->getValue(),
                                // 'ship_date' => $sheet->getCell('S' . $row)->getValue(),
                                // 'release_number' => $sheet->getCell('T' . $row)->getValue(),
                            ]);

                            if ($is_scan_out == 1) {
                                $customer = null;
                                if (!empty($val2['customer_name'])) {
                                    $customer = Customer::where('name', $val2['customer_name'])->value('id');
                                    if (!$customer) {
                                        $result = Customer::create(['name' => $val2['customer_name']]);
                                        $customer = $result->id;
                                    }
                                }

                                $release_number = $val2['releaseNumber'];
                                $exist = ScanOutInventory::where('release_number', $release_number)->first();
                                if (!$exist) {
                                    $scanOutInventory = new ScanOutInventory();
                                    $scanOutInventory->release_number = $release_number;
                                    $scanOutInventory->customer_id = $customer;
                                    $scanOutInventory->created_at = date('Y-m-d', strtotime($val2['ship_date']));
                                    $scanOutInventory->status = 'closed';
                                    $scanOutInventory->warehouse_id = $warehouse_exist;
                                    $scanOutInventory->save();
                                    $scan_out_inventory_id = $scanOutInventory->id;
                                } else {
                                    // Get the existing ScanInInventory ID for the release number
                                    $scan_out_inventory_id = ScanOutInventory::where('release_number', $release_number)->value('id');
                                }

                                $scan_out_id = ScanOutLog::create([
                                    'scan_out_inventory_id' => $scan_out_inventory_id,
                                    'scan_in_id' => $scan_in->id,
                                    'created_at' => date('Y-m-d', strtotime($val2['ship_date']))
                                ]);
                                ScanInLog::where('id', $scan_in->id)->update(['is_scan_out' => 1]);
                            }
                        }
                    }
                } else {
                    // Get the existing ScanInInventory ID for the Reference number
                    // $scan_in_inventory_id = $exist->id;
                    // Check if the reference number is already in the existingReferenceNumbers array
                    $errorMessages[] =  $reference_number;
                }
            }
            if (!empty($errorMessages)) {
                return redirect()->back()->withErrors($errorMessages);
            }
            return redirect('admin/scan-in/scanInLogs')->with('success', 'Great! Data has been successfully Imported!.');
        } catch (Exception $e) {
            $error_code = $e->errorInfo[1];
            return back()->withErrors('There was a problem uploading the data!');
        }
    }
}
