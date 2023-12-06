<!DOCTYPE html>
<title>{{ $page_title }}</title>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                        <p class="font-weight-bold paragraph_margin">reference # :
                            {{ isset($data) && !empty($data) ? $reference_number ?? 'N/A' : 'No' }}
                        </p>
                    </div>
                    {{-- <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Date:
                            {{ changeDateFormatToUS($data->created_at) ?? 'N/A' }}
                        </p>
                    </div> --}}
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Supplier:
                            {{ isset($data->supplier->name) && !empty($data->supplier->name) ? $data->supplier->name : 'No' }}
                        </p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">Warehouse:
                            {{ isset($data->warehouse->name) && !empty($data->warehouse->name) ? $data->warehouse->name : 'No' }}
                        </p>
                    </div>
                    <div class="col-sm-12">
                        <p class="font-weight-bold paragraph_margin">From date to To date:
                            {{ isset($from_date) && !empty($from_date) ? date('m-d-Y', strtotime($from_date)) : '' }}
                            -
                            {{ isset($to_date) && !empty($to_date) ? date('m-d-Y', strtotime($to_date)) : '' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <br /><br />

        {{-- Billing Report --}}
        <div class="table-responsive">
            <table id="order_data" class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Scan In Date</th>
                        <th>Reference #</th>
                        <th>CGT Grade</th>
                        <th>product type</th>
                        <th>Rolls</th>
                        <th>Pallets</th>
                        <th>Roll Tare </th>
                        <th>Pallet Tare</th>
                        <th>Gross Weight</th>
                        <th>Yards </th>
                        <th>Net Weight</th>
                        <th>Net Yards</th>
                        <th>Billable</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    @if (isset($cgt_inventory) && !empty($cgt_inventory))
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($cgt_inventory as $key => $value)
                            @php
                                $roll_tare = ($value->rolls_sum ?? 0) * 3.5;
                            @endphp
                            <tr class="{{ isset($value->slug) && $value->slug == 'L' ? 'bg-light' : '' }}">
                                <td>
                                    {{ ++$i }}
                                </td>
                                @if (!empty($from_date) && empty(!$to_date))
                                    <td>{{ changeDateFormatToUS($value->new_date) }}</td>
                                @else
                                    <td>{{ $value->month_year }}</td>
                                @endif
                                <td>{{ $value->reference_number ?? 'N/A' }}</td>
                                <td>{{ $value->cgt_grade ?? 'N/A' }}</td>
                                <td>{{ $value->product_type ?? 'N/A' }}</td>
                                <td>{{ $value->rolls_sum ?? '0' }}</td>
                                <td>{{ $value->pallet_count ?? '0' }}</td>
                                <td>{{ $roll_tare ?? '0' }}</td>
                                <td>{{ $value->pallet_tare ?? '0' }}</td>
                                <td>{{ $value->weight ?? '-' }}</td>
                                <td>{{ $value->yards ?? '-' }}</td>
                                <td>{{ isset($value->weight) && !empty($value->weight)
                                    ? number_format(($value->weight ?? 0) - $roll_tare - ($value->pallet_tare ?? 0), 2, '.', '')
                                    : 0 }}
                                <td>{{ $value->yards ?? 0 }}</td>
                                <td class="text-center">
                                    @if (isset($value->slug) && $value->slug == 'L')
                                        <i class="fa fa-check text-success"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="5">No record found</th>
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
