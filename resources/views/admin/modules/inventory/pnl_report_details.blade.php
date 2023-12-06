<!DOCTYPE html>
<title>{{ $page_title }}</title>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

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

        .equal-width-table th,
        .equal-width-table td {
            text-align: center;
            vertical-align: middle;
            border: 1px solid #ccc;
        }

        .equal-width-table th:nth-child(3),
        .equal-width-table td:nth-child(3) {
            text-align: right;
        }

        .equal-width-table th, .equal-width-table td {
            width: 33.33%; /* Adjust the width as needed for equal distribution */
        }
    </style>

</head>

<body class="bg-white">
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
            <div class="col-sm-12">
                <p class="font-weight-bold paragraph_margin" style="font-size: 13px">Release No :
                    <span class="font-weight-light ">
                        {{ implode(', ', $orders_data->pluck('release_number')->toArray()) }}
                    </span>

                </p>
            </div>

            <div class="col-sm-12">
                <p class="font-weight-bold paragraph_margin" style="font-size: 13px">
                </p>
            </div>
            <div class="col-sm-12">
                <p class="font-weight-bold paragraph_margin" style="font-size: 13px">
                    From date and To date:
                    {{ isset($from_date) && !empty($from_date) ? date('m-d-Y', strtotime($from_date)) : '' }}
                    -
                    {{ isset($to_date) && !empty($to_date) ? date('m-d-Y', strtotime($to_date)) : '' }}
                </p>
            </div>
        </div>
        <br /><br />
        {{-- NEXPAC Billing --}}
        @if ($nextrade_billing_total_prices && $nexpac_billing_total_prices)
            <div class="table-responsive equal-width-table">
                <h4 class="card-title text-center mb-4">NEXTRADE PROFIT</h4>
{{--                {{ implode(', ', $orders_data->pluck('customer')->toArray()) }}--}}
                <table id="nexpac_billing" class="table table-bordered mb-0">
                    <thead>
                    <tr>
                        <th style="width: 25%;">Release Number</th>
                        <th style="width: 25%;">Customer name</th>
                        <th style="width: 25%;">Scan Out Date</th>
                        <th style="width: 25%;">Profit Per Release #</th>
                    </tr>
                    </thead>
                    <tbody class="text-dark">
                    @if (isset($orders_data) && !empty($orders_data))
                        @php
                            $total_profit = 0;
                        @endphp
                        @foreach ($orders_data as $key => $val)
                            <tr>
                                @php
                                    $total_paid_sum_of_all_nts = $nextrade_billing_total_prices[$val->id]['total_paid_sum_of_all_nts'] ?? 0;
                                    $total_paid_sum_of_all_cgts = $nexpac_billing_total_prices[$val->id]['total_paid_sum_of_all_cgts'] ?? 0;
                                    $profit1 = $total_paid_sum_of_all_nts - $total_paid_sum_of_all_cgts;
                                    $total_profit += $profit1;
                                @endphp
                                <td style="width: 25%;"><a href="{{ route('admin:orders.edit', $val->id) }}">{{ $val->release_number ?? 'N/A' }}</a></td>
                                <td style="width: 25%;">{{ $val->customer ?? 'N/A' }}</td> <!-- Display the customer name here -->
                                <td style="width: 25%;">{{ changeDateFormatToUS($val->created_at) ?? 'N/A' }}</td>
                                <td style="width: 25%;">{{ '$' . $profit1 ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="4">No record found</th>
                        </tr>
                    @endif
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Total price:</th>
                        <th colspan="4">{{ '$' . rtrim(number_format($total_profit, 2, '.', ''),'0.') }}</th>
                    </tr>
                    </tfoot>
                </table>

            </div>
        @endif

        <br /><br />

        @if (auth()->check() && session('RoleHasPermission') !== null && session('RoleHasPermission')->third_party_price_column == 1)
            @if ($nextrade_billing_total_prices && $third_party_total_prices)
                <div class="table-responsive equal-width-table mb-2 mt-4">
                    <h4 class="card-title text-center mb-4">THIRD PARTY PROFIT</h4>
                    <table id="nextrade_billing2" class="table table-bordered mb-0">
                        <thead>
                        <tr>
                            <th>Release Number</th>
                            <th>Scan Out Date</th>
                            <th>Profit2</th>
                        </tr>
                        </thead>
                        @php
                            $profit2 = 0;
                        @endphp
                        <tbody class="text-dark">
                        @if (isset($orders_data) && !empty($orders_data))
                            @php
                                $total_profit2 = 0;
                            @endphp
                            @foreach ($orders_data as $key => $val)
                                <tr>
                                    @php
                                        $total_paid_sum_of_all_nts = $nextrade_billing_total_prices[$val->id]['total_paid_sum_of_all_nts'];
                                        $total_sum_of_third_prices = $third_party_total_prices[$val->id]['total_sum_of_third_prices'];
                                        $profit2 = $total_sum_of_third_prices - $total_paid_sum_of_all_nts;
                                        $total_profit2 += $profit2;
                                    @endphp
                                    <td><a href="{{ route('admin:orders.edit', $val->id) }}">{{ $val->release_number ?? 'N/A' }}</a></td>
                                    <td>{{ changeDateFormatToUS($val->created_at) ?? 'N/A' }}</td>
                                    <td>{{ '$' . $profit2 ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <th colspan="3">No record found</th>
                            </tr>
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total price:</th>
                            <th colspan="3">{{ '$' . rtrim(number_format($total_profit, 2, '.', ''),'0.') }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        @endif

        <br /><br />

    </div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
</script>

</html>
<script>
    $('#print_details').click(function() {
        window.print()
    });
</script>
