@extends('admin.layout.app')
@section('content')
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link type="text/css" rel="stylesheet" href="{{ asset('assets/css/image-uploader.min.css') }}">
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a
                                href="{{ route('admin:pendingOrders') }}">Pending Orders</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pending Order Queue</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Order Details</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>RELEASE #</td>
                                        <td>{{ $order_history->release_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>CUSTOMER</td>
                                        <td>{{ $order_history->getCustomers->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>WAREHOUSE</td>
                                        <td>{{ $order_history->getWareHouse->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>CONTAINER #</td>
                                        <td>{{ $order_history->container ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>ROLLS COUNT</td>
                                        @php
                                            $total_rolls = 0;
                                            foreach ($order_history->getScanOutLogs as $key => $value) {
                                                $total_rolls += $value->scanInLog->rolls ?? 0;
                                            }
                                        @endphp
                                        <td>{{ $total_rolls ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>TARE FACTOR</td>
                                        <td>{{ $order_history->tear_factor ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>SEAL</td>
                                        <td>{{ $order_history->seal ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>PALLET TEAR</td>
                                        <td>{{ $order_history->pallet_weight ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>TARE FACTOR 2</td>
                                        <td>{{ $order_history->tear_factor_weight ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>SCALE TICKET WEIGHT</td>
                                        <td>{{ $order_history->scale_discrepancy ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>PALLET ON CONTAINER</td>
                                        <td>{{ $order_history->pallet_on_container ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <td>ORDER DATE</td>
                                        <td>{{ changeDateFormatToUS($order_history->created_at) ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>STATUS</td>
                                        <td><span
                                                class="status-btn  btn-sm text-center  {{ $order_history->status == 'closed' ? 'btn-success' : ($order_history->status == 'cancelled' ? 'btn-danger' : 'btn-secondary') }}
                                                get_order_id"
                                                data-id="{{ $order_history->id }}" data-bs-toggle="modal"
                                                data-bs-target="#exampleModalDefault">
                                                {{ $order_history->status == 'pending' ? 'Pending' : ($order_history->status == 'preload' ? 'PreLoaded' : ($order_history->status == 'shipping_in_process' ? 'Shipping In Process' : ($order_history->status == 'shipped' ? 'Shipped' : ($order_history->status == 'post_loading_documentation' ? 'Post Loading Documentation' : ($order_history->status == 'end_stage' ? 'End Stage' : ($order_history->status == 'closed' ? 'Closed' : ($order_history->status == 'cancelled' ? 'Cancelled' : '-'))))))) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-sm-12">

                                <div class="table-responsive">
                                    <h4 class="card-title mb-4" style="float:left">Order Queue Data</h4>
                                    @if (isset($queue_data_exist) && !empty($queue_data_exist))
                                        <a class="btn-sm btn-primary" style="float:right"
                                            href="{{ route('admin:orders.pendingOrder.makeQueuePDF', [$order_history ?? 0]) }}">Create
                                            PDF</a>
                                    @endif
                                    <form
                                        action="{{ route('admin:orders.pendingOrder.queueDataSubmit', [$order_history->id]) }}"
                                        method="POST">
                                        @csrf
                                        <table id="nextrade_billing_logs_group" class="table table-borderless mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Color</th>
                                                    <th>NT Grade</th>
                                                    <th>Weight</th>
                                                    <th>Order Column</th>
                                                    <th>Available Weight</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-dark">

                                                @php
                                                    $total_grand_weight = 0;
                                                    $order_column_total = 0;
                                                    $available_weight_total = 0;
                                                    $sum_of_existing_order_column = 0;
                                                    $i = 0;
                                                @endphp
                                                @foreach ($color_data['color_grade_group_data'] as $color_key => $color)
                                                    <tr>
                                                        <th colspan="2">{{ $color->color_name }}</th>
                                                        <th>{{ $color->total_weight }}</th>
                                                    </tr>

                                                    @foreach ($color_data['nt_grade_group_data'][$color_key] as $nt_key => $nt)
                                                        @php
                                                            $total_grand_weight += $nt->total_weight;
                                                            $Order_column_value = $color_data['order_queue_data'][$color->color_id . '-' . $nt->nt_id]['Order_column_value'];
                                                            $sum_of_order_column = $color_data['order_queue_data'][$color->color_id . '-' . $nt->nt_id]['sum_of_order_column'];
                                                            $available_weight = ($nt->total_weight ?? 0) - ($order_queue_data->order_column ?? 0);
                                                            $order_column_total += $Order_column_value->order_column ?? 0;
                                                            $available_weight_total += $available_weight ?? 0;
                                                            $sum_of_existing_order_column += $sum_of_order_column ?? 0;
                                                        @endphp
                                                        <tr>
                                                            <td></td>
                                                            <input type="hidden" name="color_id[]"
                                                                value="{{ $color->color_id ?? '' }}">
                                                            <input type="hidden" name="nt_id[]"
                                                                value="{{ $nt->nt_id }}">
                                                            <th colspan="1">{{ $nt->nt_grade_name }}</th>
                                                            <th>{{ $nt->total_weight }}</th>
                                                            <th>
                                                                @if (isset($queue_data_exist) && !empty($queue_data_exist))
                                                                    <p>{{ $Order_column_value->order_column ?? '' }}</p>
                                                                @else
                                                                    <input class="order_column"
                                                                        weight="{{ $nt->total_weight }}" type="number"
                                                                        order-column="{{ $sum_of_order_column ?? 0 }}"
                                                                        class="form-control form-control-sm"
                                                                        name="order_column[]" id="" >
                                                                @endif
                                                            </th>
                                                            <th>
                                                                <p class="available_weight">
                                                                    {{ ($nt->total_weight ?? 0) - ($sum_of_order_column ?? 0) }}
                                                                </p>
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
                                                    <th>{{ $total_grand_weight }}</th>
                                                    <th class="order_column_total">{{ $order_column_total }}</th>
                                                    <th class="total_available_weight">
                                                        {{ $total_grand_weight - $sum_of_existing_order_column ?? 0 }}
                                                    </th>

                                                </tr>
                                                {{-- <tr>
                                                    <th colspan="3">Order column Total </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3">Available weight Total </th>
                                                </tr> --}}
                                            </tbody>
                                        </table>
                                        @if (!isset($queue_data_exist) && empty($queue_data_exist))
                                            <button type="submit" class="btn btn-sm btn-primary" style="float:right">
                                                Submit</button>
                                        @endif
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        // $(document).ready(function() {
        //     var order_column_total = 0;
        //     var total_available_weight = parseFloat($('.total_available_weight').text());
        //     $('.order_column').on('keyup', function() {
        //         var order_columns = $('.order_column'); // Get all order_column inputs
        //         order_column_total = 0; // Reset order_column_total
        //         order_columns.each(function() {
        //             var order_column = parseFloat($(this).val());
        //             order_column_total += order_column || 0;
        //         });
        //         $('.order_column_total').text(order_column_total);
        //         var current_order_column = $(this).val();
        //         current_order_column = parseFloat(current_order_column);

        //         var remaining_available_weight = total_available_weight - order_column_total;
        //         if (remaining_available_weight < 0) {
        //             let str = current_order_column.toString();
        //             var remove_last_digit = str.substring(0, str.length - 1);
        //             $(this).val(remove_last_digit);
        //             order_columns.each(function() {
        //                 var order_column = parseFloat(remove_last_digit);
        //                 order_column_total += order_column || 0;
        //             });
        //             $('.order_column_total').text(order_column_total);
        //             return toastr.error('Total Available Weight is less than given weight!');
        //         }
        //         $('.total_available_weight').text(remaining_available_weight);

        //         var weight = $(this).attr('weight');
        //         weight = parseFloat(weight);
        //         var existing_order_column = $(this).attr('order-column');
        //         existing_order_column = parseFloat(existing_order_column);
        //         var existing_available_weight = $(this).closest('tr').find('.available_weight').text();
        //         existing_available_weight = parseFloat(existing_available_weight);
        //         var order_column_new_val = 0;
        //         var available_weight = 0;
        //         if (existing_order_column != 0) {
        //             if (current_order_column != 0) {
        //                 order_column_new_val = current_order_column;
        //             }
        //             var new_order_column = existing_order_column + (order_column_new_val || 0);
        //             available_weight = (weight || 0) - (new_order_column || 0);
        //         } else {
        //             available_weight = (weight || 0) - (current_order_column || 0);
        //         }
        //         if (available_weight < 0) {
        //             let str1 = current_order_column.toString();
        //             var remove_last_digit1 = str1.substring(0, str1.length - 1);
        //             $(this).val(remove_last_digit1);
        //             var order_column_total = 0; // Initialize the total to 0
        //             order_columns.each(function() {
        //                 var order_column = parseFloat($(this).val());
        //                 order_column_total += order_column || 0;
        //             });
        //             $('.order_column_total').text(order_column_total);
        //             return toastr.error('Available Weight is less than given weight!');
        //         }
        //         $(this).closest('tr').find('.available_weight').text(available_weight);
        //     });

        //     // Add event listener for total column value change
        //     $('.total_column').on('keyup', function() {
        //         var new_total_available_weight = parseFloat($(this).val());
        //         var total_order_column = 0;
        //         order_columns.each(function() {
        //             var order_column = parseFloat($(this).val());
        //             total_order_column += order_column || 0;
        //         });
        //         var remaining_available_weight = new_total_available_weight - total_order_column;
        //         $('.total_available_weight').text(remaining_available_weight);
        //     });
        // });
        // $(document).ready(function() {
        //     var order_column_total = 0;
        //     var total_available_weight = parseFloat($('.total_available_weight').text());

        //     $('.order_column').on('keyup', function() {
        //         var order_columns = $('.order_column');
        //         order_column_total = 0;
        //         order_columns.each(function() {
        //             var order_column = parseFloat($(this).val());
        //             order_column_total += order_column || 0;
        //         });
        //         $('.order_column_total').text(order_column_total);

        //         var current_order_column = parseFloat($(this).val());
        //         var weight = parseFloat($(this).attr('weight'));
        //         var existing_order_column = parseFloat($(this).attr('order-column'));
        //         var existing_available_weight = parseFloat($(this).closest('tr').find('.available_weight')
        //             .text());

        //         var order_column_new_val = 0;
        //         var available_weight = 0;

        //         if (existing_order_column != 0) {
        //             if (current_order_column != 0) {
        //                 order_column_new_val = current_order_column;
        //             }
        //             var new_order_column = existing_order_column + (order_column_new_val || 0);
        //             available_weight = weight - new_order_column;
        //         } else {
        //             available_weight = weight - current_order_column;
        //         }

        //         if (isNaN(available_weight)) {
        //             available_weight = existing_available_weight;
        //         }

        //         if (available_weight < 0) {
        //             let str1 = current_order_column.toString();
        //             var remove_last_digit1 = str1.substring(0, str1.length - 1);
        //             $(this).val(remove_last_digit1);
        //             var order_column_total = 0;
        //             order_columns.each(function() {
        //                 var order_column = parseFloat($(this).val());
        //                 order_column_total += order_column || 0;
        //             });
        //             $('.order_column_total').text(order_column_total);
        //             return toastr.error('Available Weight for this column has not enough weight!');
        //         }

        //         $(this).closest('tr').find('.available_weight').text(available_weight);

        //         // Calculate the remaining available weight
        //         var remaining_available_weight = total_available_weight - order_column_total;
        //         if (remaining_available_weight < 0) {
        //             let str = $(this).val().toString();
        //             var remove_last_digit = str.substring(0, str.length - 1);
        //             $(this).val(remove_last_digit);
        //             order_columns.each(function() {
        //                 var order_column = parseFloat(remove_last_digit);
        //                 order_column_total += order_column || 0;
        //             });
        //             $('.order_column_total').text(order_column_total);
        //             return toastr.error('Total Available Weight is less than given weight!');
        //         }
        //         $('.total_available_weight').text(remaining_available_weight);
        //     });

        //     // Add event listener for total column value change
        //     $('.total_column').on('keyup', function() {
        //         var new_total_available_weight = parseFloat($(this).val());
        //         var total_order_column = 0;
        //         $('.order_column').each(function() {
        //             var order_column = parseFloat($(this).val());
        //             total_order_column += order_column || 0;
        //         });
        //         var remaining_available_weight = new_total_available_weight - total_order_column;
        //         $('.total_available_weight').text(remaining_available_weight);
        //     });
        // });

        $(document).ready(function() {
            var order_column_total = 0;
            var total_available_weight = parseFloat($('.total_available_weight').text());

            $('.order_column').on('keyup', function() {
                var order_columns = $('.order_column');
                order_column_total = 0;
                order_columns.each(function() {
                    var order_column = parseFloat($(this).val());
                    order_column_total += order_column || 0;
                });
                $('.order_column_total').text(order_column_total);

                var current_order_column = parseFloat($(this).val());
                var weight = parseFloat($(this).attr('weight'));
                var existing_order_column = parseFloat($(this).attr('order-column'));
                var existing_available_weight = parseFloat($(this).closest('tr').find('.available_weight')
                    .text());

                var order_column_new_val = 0;
                var available_weight = 0;

                if (existing_order_column != 0) {
                    if (current_order_column != 0) {
                        order_column_new_val = current_order_column;
                    }
                    var new_order_column = existing_order_column + (order_column_new_val || 0);
                    available_weight = weight - new_order_column;
                } else {
                    available_weight = weight - current_order_column;
                }

                if (isNaN(available_weight)) {
                    available_weight = weight - existing_order_column;
                }

                if (available_weight < 0) {
                    let str1 = current_order_column.toString();
                    var remove_last_digit1 = str1.substring(0, str1.length - 1);
                    $(this).val(remove_last_digit1);
                    var order_column_total = 0;
                    order_columns.each(function() {
                        var order_column = parseFloat($(this).val());
                        order_column_total += order_column || 0;
                    });
                    $('.order_column_total').text(order_column_total);
                    return toastr.error('Available Weight for this column has not enough weight!');
                }

                $(this).closest('tr').find('.available_weight').text(available_weight);

                // Calculate the remaining available weight
                var remaining_available_weight = total_available_weight - order_column_total;
                if (remaining_available_weight < 0) {
                    let str = $(this).val().toString();
                    var remove_last_digit = str.substring(0, str.length - 1);
                    $(this).val(remove_last_digit);
                    order_columns.each(function() {
                        var order_column = parseFloat($(this).val());
                        order_column_total += order_column || 0;
                    });
                    $('.order_column_total').text(order_column_total);
                    return toastr.error('Total Available Weight is less than given weight!');
                }
                $('.total_available_weight').text(remaining_available_weight);
            });

            // Add event listener for total column value change
            $('.total_column').on('keyup', function() {
                var new_total_available_weight = parseFloat($(this).val());
                var total_order_column = 0;
                $('.order_column').each(function() {
                    var order_column = parseFloat($(this).val());
                    total_order_column += order_column || 0;
                });
                var remaining_available_weight = new_total_available_weight - total_order_column;
                $('.total_available_weight').text(remaining_available_weight);
            });
        });
    </script>
@endsection
