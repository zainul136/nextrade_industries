<!DOCTYPE html>
<title>{{ $page_title }}</title>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <style>
        .paragraph_margin,
        p {
            margin: 0px;
        }

        @media print {
            .hide_content {
                display: none !important;
            }

            #nextrade_billing_logs_group tbody th,
            #nextrade_billing_logs_group tbody td {
                border: none;
                padding: 0px 0px 2px 10px;
            }
        }

        #nextrade_billing_logs_group tbody th,
        #nextrade_billing_logs_group tbody td {
            border: none;
            padding: 0px 0px 2px 10px;
        }
    </style>

</head>

<body class="bg-white">
    @php
        
        $lbs_into_kg = 0.453592;
        
        $scale_ticket_wgt = $order_data->scale_discrepancy;
        
        $pallets_tare_wgt = $order_data->pallet_weight * $order_data->pallet;
        
        $tear_factor_weight = $order_data->tear_factor ?? 3.5;
        $roll_tare_wgt = $order_data->roll_sum * $tear_factor_weight;
        
        $tear_factor_weight_for_bill = $order_data->tear_factor_weight ?? 3.5;
        
        $total_weight_preloaded_lbs = $order_data->weight_sum;
        $total_weight_preloaded_kg = number_format($order_data->weight_sum * $lbs_into_kg, 2, '.', '');
        $total_weight_loaded_lbs = $total_weight_preloaded_lbs - $pallets_tare_wgt;
        $total_weight_loaded_kg = number_format($total_weight_loaded_lbs * $lbs_into_kg, 2, '.', '');
        
        $scale_discrepancy = $scale_ticket_wgt - $total_weight_loaded_lbs;
        
        $scale_ticket_wgt_lbs = $scale_ticket_wgt;
        $scale_ticket_wgt_kg = number_format($scale_ticket_wgt_lbs * $lbs_into_kg, 2, '.', '');
        
        $net_billable_wgt_lbs = $total_weight_loaded_lbs - $roll_tare_wgt - ($order_data->pallet_on_container * $order_data->pallet_weight ?? 0) + $scale_discrepancy;
        $net_billable_wgt_kg = number_format($net_billable_wgt_lbs * $lbs_into_kg, 2, '.', '');
        
    @endphp
    <div class="container mt-4 mb-4">

        <div class="col-md-12 mt-5 ">
            <div class="text-left">
                <img src="{{ asset('assets/images/logo/logo2.png') }}" style="width: 175px;">
            </div>
            <div class="text-center">
                <img src="{{ asset('assets/images/logo/Nextlogo.png') }}" style="width: 215px;">
            </div>
        </div>
        <div class="row mt-3 mb-5">
            <div class="col-sm-12 text-center m-0">
                <h4>{{ $page_title }}</h4>
            </div>
            <div class="col-sm-12 text-center m-0">
                <a href="javascript:void(0)" class="btn btn-sm btn-primary float-right hide_content"
                    id="print_details">Print</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 offset-md-6" style=" font-size: 14px;">
                <div class="float-right">
                    <p class="text-right"><b>4525 Macro, San Antonio, Texas 78218</b></p>
                    <p class="text-right"><b>1250 Franklin Blvd, Cambridge, Ontario, N1R 8B7, Canada</b></p>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Release # :
                            {{ $order_data->release_number ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Container: {{ $order_data->container ?? '0' }}
                        </p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Scanout Date # :
                            {{ changeDateFormatToUS($order_data->created_at) ?? 'N/A' }}</p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Seal # : {{ $order_data->seal ?? '0' }}</p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Scanout Location :
                            {{ $order_data->warehouse ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Customer : {{ $order_data->customer ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Pallets</p>
                    </div>
                    <div class="col-sm-5">
                        <p> {{ $order_data->pallet ?? '0' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Pallets Tare Wgt</p>
                    </div>
                    <div class="col-sm-5">
                        <p> {{ $pallets_tare_wgt ?? '0' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Rolls</p>
                    </div>
                    <div class="col-sm-5">
                        <p>{{ $order_data->roll_sum ?? '0' }} </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Roll Tare Wgt</p>
                    </div>
                    <div class="col-sm-5">
                        <p>{{ $roll_tare_wgt ?? '0' }} </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Tare Factor</p>
                    </div>
                    <div class="col-sm-5">
                        <p> {{ $tear_factor_weight }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Scale Discrepancy</p>
                    </div>
                    <div class="col-sm-5">
                        <p> {{ $scale_discrepancy ?? '0' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Pallets on Container</p>
                    </div>
                    <div class="col-sm-5">
                        <p> {{ $order_data->pallet_on_container ?? '0' }}
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Pallets on Container Wgt</p>
                    </div>
                    <div class="col-sm-5">
                        <p> {{ isset($order_data->pallet_on_container) && !empty($order_data->pallet_on_container) ? $order_data->pallet_on_container * $order_data->pallet_weight : '0' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-5">
                    </div>
                    <div class="col-sm-3">
                        <p class="font-weight-bold"> LBS </p>
                    </div>
                    <div class="col-sm-3">
                        <p class="font-weight-bold">KGS </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Gross Weight Pre Load</p>
                    </div>
                    <div class="col-sm-3">
                        <p> {{ $total_weight_preloaded_lbs ?? '0' }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p> {{ $total_weight_preloaded_kg ?? '0' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Gross Weight Loaded</p>
                    </div>
                    <div class="col-sm-3">
                        <p> {{ $total_weight_loaded_lbs ?? '0' }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p> {{ $total_weight_loaded_kg ?? '0' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Scale Tickets Gross Loaded</p>
                    </div>
                    <div class="col-sm-3">
                        <p> {{ $scale_ticket_wgt_lbs ?? '0' }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p> {{ $scale_ticket_wgt_kg ?? '0' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Net Billable Weight</p>
                    </div>
                    <div class="col-sm-3">
                        <p> {{ $net_billable_wgt_lbs ?? '0' }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p> {{ $net_billable_wgt_kg ?? '0' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <br /><br />
        {{-- NEXPAC Billing --}}
        <div class="table-responsive">
            <h4 class="card-title text-center mb-4">NEXPAC BILLING</h4>
            <table id="nexpac_billing" class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Billing Description</th>
                        <th># of Pallets</th>
                        <th># of Rolls</th>
                        <th>Weight</th>
                        <th>Yards</th>
                        <th>Billable Weight</th>
                        <th>Billable yards</th>
                        <th>Code</th>
                        <th>Price Paid</th>
                        <th>Total</th>
                    </tr>
                </thead>
                @php
                    $total_wgt_sum = $total_yards_sum = 0;
                @endphp
                <tbody class="text-dark">
                    @if (isset($nexpac_billing1) && !empty($nexpac_billing1))
                        @php
                            $second_part_wgt = $third_part_wgt = $second_part_yards = $third_part_yards = 0;
                        @endphp
                        @foreach ($nexpac_billing1 as $key => $val)
                            <tr>
                                @php
                                    
                                    $pallets = $val->no_of_pallets * ($order_data->pallet_weight ?? 0);                  
                                    $rolls = $val->rolls_sum * $tear_factor_weight_for_bill;
                                    $sum_of_pallets_and_rolls = $pallets + $rolls;
                                    $remaining_weight = ($val->weight_sum ?? 0) - ($sum_of_pallets_and_rolls ?? 0);
                                    $remaining_yards = ($val->yards_sum ?? 0) - ($sum_of_pallets_and_rolls ?? 0);
                                    if ($weight['count_rows_with_weight1'] != 0) {
                                        $second_part_wgt = ($order_data->pallet_on_container * $order_data->pallet_weight) / $weight['count_rows_with_weight1'];
                                        $third_part_wgt = $scale_discrepancy / $weight['count_rows_with_weight1'];
                                    }
                                    $nexpac_billable_wgt = $remaining_weight + $second_part_wgt + $third_part_wgt;
                                    
                                    //Total wgt sum
                                    $wgt_total = $nexpac_billable_wgt * $val->cgt_price;
                                    $validate_wgt_total = isset($val->weight_sum) && ($val->weight_sum != 0 || $val->weight_sum != null) ? $wgt_total : '0';
                                    $total_wgt_sum += $validate_wgt_total;
                                    $yards_total = $val->yards_sum * $val->cgt_price;
                                    $validate_yards_total = isset($val->yards_sum) && ($val->yards_sum != 0 || $val->yards_sum != null) ? $yards_total : '0';
                                    $total_yards_sum += $validate_yards_total ?? 0;
                                    $total_paid_per_each_cgt = $validate_wgt_total + $validate_yards_total;
                                    $total_paid_sum_of_all_cgts = ($total_wgt_sum ?? 0) + ($total_yards_sum ?? 0);
                                    
                                @endphp
                                <td>{{ $val->cgt_grade ?? 'N/A' }}
                                </td>
                                <td>{{ $val->no_of_pallets ?? 'N/A' }}
                                </td>
                                <td>{{ $val->rolls_sum ?? 'N/A' }}
                                </td>
                                <td>{{ $val->weight_sum ?? '0' }}
                                </td>
                                <td>{{ $val->yards_sum ?? '0' }}
                                </td>
                                <td>{{ isset($val->weight_sum) && ($val->weight_sum != 0 || $val->weight_sum != null) ? number_format($nexpac_billable_wgt, 2, '.', '') : 0 }}
                                </td>
                                <td>{{ $val->yards_sum ?? '0' }}
                                </td>
                                <td>{{ $val->cgt_code ?? '-' }}</td>
                                <td>{{ '$' . $val->cgt_price ?? '-' }}</td>
                                <td>{{ isset($total_paid_per_each_cgt) && !empty($total_paid_per_each_cgt) ? '$' . number_format($total_paid_per_each_cgt, 2, '.', '') : 0 }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="8">No record found</th>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                    <tr>
                        <th colspan="9" class="text-right">Total price:</th>
                        <th colspan="10">{{ '$' . number_format($total_paid_sum_of_all_cgts, 2, '.', '') ?? 0 }}
                        </th>
                    </tr>
                    </tr>
                </tfoot>
            </table>
        </div>
        <br /><br />

        {{-- Nextrade BILLING --}}
        <div class="table-responsive">
            <h4 class="card-title text-center mb-4">Nextrade BILLING </h4>
            <table id="nextrade_billing" class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>CGT Grade</th>
                        <th># of Pallets</th>
                        <th># of Rolls</th>
                        <th>Gross Weight</th>
                        <th>Gross Yards</th>
                        <th>Billable Weight</th>
                        <th>Billable Yards</th>
                        <th>Price Billed</th>
                        <th>Total Paid</th>
                    </tr>
                </thead>
                @php
                    $total_wgt_sum = $total_yards_sum = 0;
                @endphp
                <tbody class="text-dark">
                    @if (isset($nextrade_billing) && !empty($nextrade_billing))
                        @php
                            $second_part_wgt = $third_part_wgt = $second_part_yards = $third_part_yards = 0;
                        @endphp
                        @foreach ($nextrade_billing as $key => $val)
                            <tr>
                                @php
                                    
                                    $pallets = $val->no_of_pallets * ($order_data->pallet_weight ?? 0);
                                    $rolls = $val->rolls_sum * $tear_factor_weight_for_bill;
                                    $sum_of_pallets_and_rolls = $pallets + $rolls;
                                    $remaining_weight = ($val->weight_sum ?? 0) - ($sum_of_pallets_and_rolls ?? 0);
                                    $remaining_yards = ($val->yards_sum ?? 0) - ($sum_of_pallets_and_rolls ?? 0);
                                    if ($weight['count_rows_with_next_trade_weight'] != 0) {
                                        $second_part_wgt = ($order_data->pallet_on_container * $order_data->pallet_weight) / $weight['count_rows_with_next_trade_weight'];
                                        $third_part_wgt = $scale_discrepancy / $weight['count_rows_with_next_trade_weight'];
                                    }
                                    $nextrade_billable_wgt = $remaining_weight + $second_part_wgt + $third_part_wgt;
                                    
                                    //Total wgt sum
                                    $wgt_total = $nextrade_billable_wgt * $val->cgt_price;
                                    $validate_wgt_total = isset($val->weight_sum) && ($val->weight_sum != 0 || $val->weight_sum != null) ? $wgt_total : '0';
                                    $total_wgt_sum += $validate_wgt_total;
                                    
                                    // Total yards sum
                                    $yards_total = $val->yards_sum * $val->cgt_price;
                                    $validate_yards_total = isset($val->yards_sum) && ($val->yards_sum != 0 || $val->yards_sum != null) ? $yards_total : '0';
                                    $total_yards_sum += $validate_yards_total ?? 0;
                                    
                                    $total_paid_per_each_nt = $validate_wgt_total + $validate_yards_total;
                                    $total_paid_sum_of_all_nts = ($total_wgt_sum ?? 0) + ($total_yards_sum ?? 0);
                                    
                                @endphp
                                <td>{{ $val->cgt_grade ?? 'N/A' }}
                                </td>
                                <td>{{ $val->no_of_pallets ?? 'N/A' }}
                                </td>
                                <td>{{ $val->rolls_sum ?? 'N/A' }}
                                </td>
                                <td>{{ $val->weight_sum ?? '0' }}</td>
                                <td>{{ $val->yards_sum ?? '0' }}</td>
                                <td>{{ isset($val->weight_sum) && ($val->weight_sum != 0 || $val->weight_sum != null) ? number_format($nextrade_billable_wgt, 2, '.', '') : 0 }}
                                </td>
                                <td>{{ $val->yards_sum ?? '0' }}</td>
                                <td>{{ '$' . $val->cgt_price ?? '0' }}</td>
                                <td>{{ isset($total_paid_per_each_nt) && !empty($total_paid_per_each_nt) ? '$' . number_format($total_paid_per_each_nt, 2, '.', '') : 0 }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="7">No record found</th>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="8" class="text-right">Total price:</th>
                        <th colspan="9">{{ '$' . number_format($total_paid_sum_of_all_nts, 2, '.', '') ?? 0 }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <br><br>
        {{-- Third Party Prices --}}
        @if (auth()->check() &&
                session('RoleHasPermission') !== null &&
                session('RoleHasPermission')->third_party_price_column == 1)
            <div class="table-responsive">
                <h4 class="card-title text-center mb-4">THIRD PARTY BILLING</h4>
                <table id="nextrade_billing2" class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>NT Grade</th>
                            <th># of Pallets</th>
                            <th># of Rolls</th>
                            <th>Gross Weight</th>
                            <th>Gross Yards</th>
                            <th>Billable Weight</th>
                            <th>Billable Yards</th>
                            <th>Third Party Price</th>
                            <th>Total Paid</th>
                        </tr>
                    </thead>
                    @php
                        $total_wgt_sum = $total_yards_sum = 0;
                    @endphp
                    <tbody class="text-dark">
                        @if (isset($third_party_prices) && !empty($third_party_prices))
                            @php
                                $second_part_wgt = $third_part_wgt = $second_part_yards = $third_part_yards = 0;
                            @endphp
                            @foreach ($third_party_prices as $key => $val)
                                <tr>
                                    @php
                                        
                                        $pallets = $val->no_of_pallets * ($order_data->pallet_weight ?? 0);
                                        $rolls = $val->rolls_sum * $tear_factor_weight_for_bill;
                                        $sum_of_pallets_and_rolls = $pallets + $rolls;
                                        $remaining_weight = ($val->weight_sum ?? 0) - ($sum_of_pallets_and_rolls ?? 0);
                                        $remaining_yards = ($val->yards_sum ?? 0) - ($sum_of_pallets_and_rolls ?? 0);
                                        if ($weight['count_rows_with_next_trade_weight2'] != 0) {
                                            $second_part_wgt = ($order_data->pallet_on_container * $order_data->pallet_weight) / $weight['count_rows_with_next_trade_weight2'];
                                            $third_part_wgt = $scale_discrepancy / $weight['count_rows_with_next_trade_weight2'];
                                        }
                                        $nextrade_billable_wgt = $remaining_weight + $second_part_wgt + $third_part_wgt;
                                        
                                        //Total wgt sum
                                        $wgt_total = $nextrade_billable_wgt * $val->third_party_price;
                                        $validate_wgt_total = isset($val->weight_sum) && ($val->weight_sum != 0 || $val->weight_sum != null) ? $wgt_total : '0';
                                        $total_wgt_sum += $validate_wgt_total;
                                        
                                        // Total yards sum
                                        $yards_total = $val->yards_sum * $val->third_party_price;
                                        $validate_yards_total = isset($val->yards_sum) && ($val->yards_sum != 0 || $val->yards_sum != null) ? $yards_total : '0';
                                        $total_yards_sum += $validate_yards_total;
                                        
                                        $total_paid_per_each_nt = $validate_wgt_total + $validate_yards_total;
                                        $total_sum_of_third_prices = ($total_wgt_sum ?? 0) + ($total_yards_sum ?? 0);
                                        
                                    @endphp
                                    <td>{{ $val->nt_grade ?? 'N/A' }}
                                    </td>
                                    <td>{{ $val->no_of_pallets ?? 'N/A' }}
                                    </td>
                                    <td>{{ $val->rolls_sum ?? 'N/A' }}
                                    </td>
                                    <td>{{ $val->weight_sum ?? '0' }}</td>
                                    <td>{{ $val->yards_sum ?? '0' }}</td>
                                    <td>{{ isset($val->weight_sum) && ($val->weight_sum != 0 || $val->weight_sum != null) ? number_format($nextrade_billable_wgt, 2, '.', '') : 0 }}
                                    </td>
                                    <td>{{ $val->yards_sum ?? '0' }}</td>
                                    <td>{{ '$' . $val->third_party_price ?? '0' }}</td>
                                    <td>{{ isset($total_paid_per_each_nt) && !empty($total_paid_per_each_nt) ? '$' . number_format($total_paid_per_each_nt, 2, '.', '') : 0 }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <th colspan="7">No record found</th>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8" class="text-right">Total price:</th>
                            <th colspan="9">{{ '$' . number_format($total_sum_of_third_prices, 2, '.', '') ?? 0 }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
        <br>
        @php
            $profit1 = 0;
        @endphp
        <div class="table-responsive">
            <h4 class="card-title text-center mb-4">NEXTRADE PROFIT </h4>
            <table id="nextrade_billing2" class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Nextrade Billing Total Price</th>
                        <th>Nexpac Billing Total Price</th>
                        <th>Profit1</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    @php
                        $total_paid_sum_of_all_nts = isset($total_paid_sum_of_all_nts) && !empty($total_paid_sum_of_all_nts) ? $total_paid_sum_of_all_nts : 0;
                        $total_paid_sum_of_all_cgts = isset($total_paid_sum_of_all_cgts) && !empty($total_paid_sum_of_all_cgts) ? $total_paid_sum_of_all_cgts : 0;
                        $profit1 = $total_paid_sum_of_all_nts - $total_paid_sum_of_all_cgts;
                    @endphp

                    <tr>
                        <td>{{ '$' . number_format($total_paid_sum_of_all_nts, 2, '.', '') }}</td>
                        <td>{{ '$' . number_format($total_paid_sum_of_all_cgts, 2, '.', '') }}</td>
                        <td>{{ '$' . number_format($profit1, 2, '.', '') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @if (auth()->check() &&
                session('RoleHasPermission') !== null &&
                session('RoleHasPermission')->third_party_price_column == 1)
            @if ($nextrade_billing && $third_party_prices)
                <div class="table-responsive mb-2 mt-4">
                    <h4 class="card-title text-center mb-4">THIRD PARTY PROFIT </h4>
                    <table id="nextrade_billing2" class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Third Party Total Paid</th>
                                <th>NEXTRADE Total Paid</th>
                                <th>Profit2</th>
                            </tr>
                        </thead>@php
                            $profit2 = 0;
                        @endphp
                        <tbody class="text-dark">
                            @php
                                $total_paid_sum_of_all_nts = isset($total_paid_sum_of_all_nts) && !empty($total_paid_sum_of_all_nts) ? $total_paid_sum_of_all_nts : 0;
                                $total_sum_of_third_prices = isset($total_sum_of_third_prices) && !empty($total_sum_of_third_prices) ? $total_sum_of_third_prices : 0;
                                $profit2 = isset($total_paid_sum_of_all_nts) && isset($total_sum_of_third_prices) ? $total_sum_of_third_prices - $total_paid_sum_of_all_nts : 0;
                            @endphp

                            <tr>
                                <td>{{ '$' . number_format($total_sum_of_third_prices, 2, '.', '') }}</td>
                                <td>{{ '$' . number_format($total_paid_sum_of_all_nts, 2, '.', '') }}</td>
                                <td>{{ '$' . number_format($profit2, 2, '.', '') }}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            @endif
        @endif
        <br /><br />

        <div class="row">

            <div class="col-sm-12">

                <div class="table-responsive">
                    <h4 class="card-title text-center mb-4">Nextrade Billing</h4>
                    <table id="nextrade_billing_logs_group" class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>NT Grade</th>
                                <th>CGT Grade</th>
                                <th>Color</th>
                                <th>Weight</th>
                            </tr>
                        </thead>
                        <tbody class="text-dark">

                            @php
                                $total_grand_weight = 0;
                                $i = 0;
                            @endphp
                            @foreach ($scan_logs_group_data['nt_grade_group_data'] as $nt_key => $nt)
                                <tr>
                                    <th colspan="3">{{ $nt->nt_grade }}</th>
                                    <th>{{ $nt->total_weight }}</th>
                                </tr>

                                @foreach ($scan_logs_group_data['cgt_grade_group_data'][$nt_key] as $cgt_key => $cgt)
                                    <tr>
                                        <td></td>
                                        <th colspan="2">{{ $cgt->cgt_grade }}</th>
                                        <th>{{ $cgt->total_weight }}</th>
                                    </tr>
                                    @foreach ($scan_logs_group_data['color_group_data'][$nt_key][$cgt_key] as $color_key => $color)
                                        @php
                                            $total_grand_weight += $color->total_weight;
                                        @endphp
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>{{ $color->color_name }}</td>
                                            <td>{{ $color->total_weight }}</td>
                                        </tr>
                                    @endforeach

                                    @if ($loop->last)
                                        <tr>
                                            <td colspan="4">
                                                <hr />
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach

                            <tr>
                                <th colspan="3">Grand Total </th>
                                <th>{{ $total_grand_weight }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        <br><br>

        {{-- Order History --}}
        <div class="table-responsive">
            <h4 class="card-title text-center mb-4">Order History</h4>
            <table id="order_data" class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>CGT Grade Billing</th>
                        <th>NEXPAC Billing</th>
                        <th>Nextrade Grade</th>
                        <th>Rolls</th>
                        <th>Weight</th>
                        <th>Yards </th>
                        <th>Color</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    @if (isset($order_skew_number) && !empty($order_skew_number))
                        @foreach ($order_skew_number as $key => $value)
                            <tr>
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td>{{ $value->cgt_grade ?? 'N/A' }}</td>
                                <td>{{ $value->cgt_grade ?? 'N/A' }}</td>
                                <td>{{ $value->nt_grade ?? 'N/A' }}</td>
                                <td>{{ $value->rolls ?? 'N/A' }}</td>
                                <td>{{ $value->weight ?? '-' }}</td>
                                <td>{{ $value->yards_sum ?? '-' }}</td>
                                <td>{{ $value->color_name ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="7">No record found</th>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
    integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
    integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"
    integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
</script>

</html>
<script>
    $('#print_details').click(function() {
        window.print()
    });
</script>
