<!DOCTYPE html>
<title>Customer Report</title>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        .paragraph_margin,
        p {
            margin: 0px;
        }
    </style>
</head>

<body class="bg-white">
    @php
        
        $lbs_into_kg = 0.453592;
        
        $scale_ticket_wgt = $order_history->scale_discrepancy;
        
        $pallets_tare_wgt = $order_history->pallet_weight * $order_history->pallet;
        
        $tear_factor = $order_history->tear_factor ?? 0;
        $roll_tare_wgt = $order_history->roll_sum * $tear_factor;
        
        $total_weight_preloaded_lbs = $order_history->weight_sum;
        $total_weight_preloaded_kg = number_format($order_history->weight_sum * $lbs_into_kg, 2, '.', '');
        $total_weight_loaded_lbs = $total_weight_preloaded_lbs - $pallets_tare_wgt;
        $total_weight_loaded_kg = number_format($total_weight_loaded_lbs * $lbs_into_kg, 2, '.', '');
        
        $scale_discrepancy = $scale_ticket_wgt - $total_weight_loaded_lbs;
        
        $scale_ticket_wgt_lbs = $scale_ticket_wgt;
        $scale_ticket_wgt_kg = number_format($scale_ticket_wgt_lbs * $lbs_into_kg, 2, '.', '');
        
        $net_billable_wgt_lbs = $total_weight_loaded_lbs - $roll_tare_wgt - ($order_history->pallet_on_container * $order_history->pallet_weight ?? 0) + $scale_discrepancy;
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
                <h4>Customer Summary Report</h4>
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
                    <p class="text-right"><b>Phone #: (+1) 7322 086 932</b></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Release # :
                            {{ $order_history->release_number ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Container: {{ $order_history->container ?? '0' }}
                        </p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Scanout Date # :
                            {{ changeDateFormatToUS($order_history->created_at) ?? 'N/A' }}</p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Seal # : {{ $order_history->seal ?? '0' }}</p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Scanout Location :
                            {{ $order_history->warehouse ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Customer : {{ $order_history->customer ?? 'N/A' }}
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
                        <p> {{ $order_history->pallet ?? '0' }}</p>
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
                        <p>{{ $order_history->roll_sum ?? '0' }} </p>
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
                        <p class="font-weight-bold">Cust Tare Wgt</p>
                    </div>
                    <div class="col-sm-5">
                        <p> {{ $order_history->tear_factor ?? '0' }}</p>
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
                        <p> {{ $order_history->pallet_on_container ?? '0' }}
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <p class="font-weight-bold">Pallets on Container Wgt</p>
                    </div>
                    <div class="col-sm-5">
                        <p> {{ isset($order_history->pallet_on_container) && !empty($order_history->pallet_on_container) ? $order_history->pallet_on_container * $order_history->pallet_weight : '0' }}
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

        <br><br>

        {{-- Order History --}}
        <div class="table-responsive">
            <h4 class="card-title text-center mb-4">Packing List</h4>
            <table id="cgt_comulative_summary" class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Skew #</th>
                        <th>Product Type</th>
                        <th>Color</th>
                        <th>Rolls</th>
                        <th>Weight</th>
                        <th>Yards</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    @if (isset($order_skew_number) && !empty($order_skew_number))
                        @foreach ($order_skew_number as $key => $value)
                            <tr>
                                <td>
                                    {{ $key + 1 }}
                                </td>
                                <td>{{ $value->skew_number ?? 'N/A' }}</td>
                                <td>{{ $value->product_type ?? 'N/A' }}</td>
                                <td>{{ $value->color_name ?? 'N/A' }}</td>
                                <td>{{ $value->rolls ?? 'N/A' }}</td>
                                <td>{{ $value->weight ?? '-' }}</td>
                                <td>{{ $value->yards ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="9">No record found</th>
                        </tr>

                    @endif
                </tbody>
                </tbody>
            </table>
        </div>
        <br><br>

        <br><br>
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

        window.print();
    });
</script>
