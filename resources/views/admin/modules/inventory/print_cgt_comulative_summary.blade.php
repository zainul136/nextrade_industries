<!DOCTYPE html>
<html>
<title> {{ $page_title }}</title>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <style type="text/css">
        .paragraph_margin {
            margin: 0px;
        }

        @media print {
            .hide_content {
                display: none !important;
            }
        }
    </style>
</head>

<body class="bg-white">
    <div class="container mt-4 mb-4">

        <div class="row">
            <div class="col-md-12 mt-4 ">
                <div class="text-left">
                    <img src="{{ asset('assets/images/logo/logo2.png') }}" style="width: 175px;">
                </div>
                <div class="text-center">
                    <img src="{{ asset('assets/images/logo/Nextlogo.png') }}" style="width: 215px;">
                </div>
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
                <p class="font-weight-bold paragraph_margin">Warehouse: {{ $warehouse_name }} </p>
            </div>
            <div class="col-sm-12">
                <p class="font-weight-bold paragraph_margin">Year: {{ $year ?? 'All' }} </p>
            </div>

            @if (!empty($from_date))
                <div class="col-sm-12">
                    <p class="font-weight-bold paragraph_margin">From: {{ changeDateFormatToUS($from_date) }} </p>
                </div>
            @endif
            @if (!empty($to_date))
                <div class="col-sm-12">
                    <p class="font-weight-bold paragraph_margin">To: {{ changeDateFormatToUS($to_date) }} </p>
                </div>
            @endif

        </div>
        <br />

        <div class="table-responsive">
            <table id="cgt_comulative_summary" class="table table-bordered mb-0">
                <thead>
                <tr>
                    <th>Date</th>

                    @foreach ($cgt_grades as $key => $cgt_grade)
                        <th>{{ $cgt_grade->grade_name }}</th>
                    @endforeach

                    <th>Total</th>
                </tr>
                </thead>
                <tbody class="text-dark">
                @forelse ($cgt_comulative as $key => $value)
                    <tr>
                        @if (!empty($from_date) && empty(!$to_date))
                            <td>{{ changeDateFormatToUS($value->new_date) }}</td>
                        @else
                            <td>{{ $value->month_year }}</td>
                        @endif

                        @php
                            $total_row_weight = 0;
                        @endphp
                        @isset($cgt_comulative_weight[$key])
                            @foreach ($cgt_comulative_weight[$key] as $key1 => $cgt)
                                <td>{{ $cgt['cgt_weight'] ?? 0 }}</td>
                                @php
                                    $total_row_weight += $cgt['cgt_weight'] ?? 0;
                                @endphp
                            @endforeach
                        @else
                            <td>{{ 0 }}</td>
                        @endisset
                        <td>{{ number_format($value->total_weight, 2, '.', '') ?? 0 }}</td>
                    </tr>
                @empty
                @endforelse

                <!-- Total row -->
                <tr>
                    <td>Total</td>
                    @foreach ($cgt_grades as $key => $cgt_grade)
                        @php
                            $total_col_weight = 0;
                        @endphp
                        @isset($cgt_comulative_weight[$key])
                            @foreach ($cgt_comulative_weight[$key] as $key1 => $cgt)
                                @php
                                    $total_col_weight += $cgt['cgt_weight'] ?? 0;
                                @endphp
                            @endforeach
                            <td>{{ $total_col_weight }}</td>
                        @else
                            <td>0</td>
                        @endisset
                    @endforeach
                    <!-- Calculate and display the total of the "Total" column here -->
                    <td>
                        @php
                            $total_total_weight = 0;
                        @endphp
                        @forelse ($cgt_comulative as $value)
                            @php
                                $total_total_weight += $value->total_weight;
                            @endphp
                        @empty
                        @endforelse
                        {{ number_format($total_total_weight, 2, '.', '') ?? 0 }}
                    </td>
                </tr>
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
