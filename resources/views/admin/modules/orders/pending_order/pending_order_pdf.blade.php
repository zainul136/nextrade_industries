<!DOCTYPE html>
<title>{{ $title }}</title>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">

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
    <div class="container mt-4 mb-4">

        <div class="col-md-12 mt-5 ">
            {{-- <div class="text-left">
                <img src="{{ asset('assets/images/logo/logo2.png') }}" style="width: 175px;">
            </div>
            <div class="text-center">
                <img src="{{ asset('assets/images/logo/Nextlogo.png') }}" style="width: 215px;">
            </div> --}}
        </div>
        <div class="row mt-3 mb-5">
            <div class="col-sm-12 text-center m-0">
                <h4>{{ $title }}</h4>
            </div>

        </div>


        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin" style=" font-size: 14px;">Release # :
                            {{ $order_data->release_number ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin" style=" font-size: 14px;">warehouse :
                            {{ $order_data->getWareHouse->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin" style=" font-size: 14px;">Customer :
                            {{ $order_data->getCustomers->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-sm-12" style="font-size: 14px;">
                        <p class="text-left font-weight-bold">4525 Macro, San Antonio, Texas 78218</p>
                        <p class="text-left font-weight-bold">1250 Franklin Blvd, Cambridge, Ontario, N1R 8B7, Canada
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">

                <div class="row">
                    <div class="col-sm-12">

                        <div class="table-responsive">
                            <br>
                            <table id="nextrade_billing_logs_group" class="table table-borderless mb-0">
                                <thead>
                                    <tr>
                                        <th>Color</th>
                                        <th>NT Grade</th>
                                        <th>Weight</th>
                                    </tr>
                                </thead>
                                <tbody class="text-dark">

                                    @php
                                        $order_column_total = 0;
                                        $available_weight_total = 0;
                                        $sum_of_existing_order_column = 0;
                                        $i = 0;
                                    @endphp
                                    @foreach ($color_data['color_grade_group_data'] as $color_key => $color)
                                        <tr>
                                            <th colspan="2">{{ $color->color_name }}</th>
                                        </tr>

                                        @foreach ($color_data['nt_grade_group_data'][$color_key] as $nt_key => $nt)
                                            @php
                                                $Order_column_value = $color_data['order_queue_data'][$color->color_id . '-' . $nt->nt_id]['Order_column_value'];
                                                $sum_of_order_column = $color_data['order_queue_data'][$color->color_id . '-' . $nt->nt_id]['sum_of_order_column'];
                                                $available_weight = ($nt->total_weight ?? 0) - ($order_queue_data->order_column ?? 0);
                                                $order_column_total += $Order_column_value->order_column ?? 0;
                                                $available_weight_total += $available_weight ?? 0;
                                                $sum_of_existing_order_column += $sum_of_order_column ?? 0;
                                            @endphp
                                            <tr>
                                                <td></td>
                                                <th colspan="1">{{ $nt->nt_grade_name }}</th>
                                                <th>

                                                    <p>{{ $Order_column_value->order_column ?? '' }}</p>

                                                </th>
                                            </tr>
                                            @if ($loop->last)
                                                <tr>
                                                    <td colspan="5">
                                                        <hr />
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endforeach

                                    <tr>
                                        <th colspan="2">Grand Total </th>
                                        <th class="order_column_total">{{ $order_column_total }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
