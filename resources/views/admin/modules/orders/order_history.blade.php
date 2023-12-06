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
                        @if ($order_history->is_order_pending == 1)
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{ route('admin:pendingOrders') }}">Pending Orders</a></li>
                        @else
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{ route('admin:orders') }}">Orders</a></li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">Order History</li>
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
                                        <td>PALLET TARE</td>
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
                                        <td><button type="button" id="inv_status"
                                                class="status-btn btn text-white btn-sm text-center  {{ $order_history->status == 'pending' ? 'bg-secondary' : ($order_history->status == 'preload' ? 'bg-orange' : ($order_history->status == 'shipping_in_process' ? 'bg-dark-orange' : ($order_history->status == 'shipped' ? 'bg-blue' : ($order_history->status == 'post_loading_documentation' ? 'bg-primary' : ($order_history->status == 'end_stage' ? 'bg-light-green' : ($order_history->status == 'closed' ? 'bg-success' : ($order_history->status == 'cancelled' ? 'bg-danger' : 'bg-secondary'))))))) }}
                                                get_order_id"
                                                data-id="{{ $order_history->id }}"
                                                current-order-status="{{ $order_history->status }}" data-bs-toggle="modal"
                                                data-bs-target="#exampleModalDefault">
                                                {{ $order_history->status == 'pending' ? 'Pending' : ($order_history->status == 'preload' ? 'PreLoaded' : ($order_history->status == 'shipping_in_process' ? 'Shipping In Process' : ($order_history->status == 'shipped' ? 'Shipped' : ($order_history->status == 'post_loading_documentation' ? 'Post Loading Documentation' : ($order_history->status == 'end_stage' ? 'End Stage' : ($order_history->status == 'closed' ? 'Closed' : ($order_history->status == 'cancelled' ? 'Cancelled' : '-'))))))) }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            @if (auth()->check() && session('RoleHasPermission') !== null && session('RoleHasPermission')->nt_price_column == 1)
                                <div class="col-sm-6">
                                    <div class="col-sm-12 mt-2">
                                        <h4>Prices</h4>

                                    </div>
                                    <div class="col-sm-12 mt-2">
                                        <div class="table-responsive">
                                            <table id="nt_grade_prices" class="table table-bordered" style="width:50%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>NT grade</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-dark">
                                                    @forelse ($nt_grades_prices as $key => $v)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $v->nt_grade ?? '' }}</td>
                                                            <td>
                                                                {{ $v->price ?? '' }}
                                                            </td>
                                                        </tr>

                                                    @empty
                                                        <tr>
                                                            <th colspan="3">No record found...</th>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            @endif
                            @if (auth()->check() &&
                                    session('RoleHasPermission') !== null &&
                                    session('RoleHasPermission')->third_party_price_column == 1)
                                <div class="col-sm-6">
                                    <div class="col-sm-12 mt-2">
                                        <h5 class="mb-2">Third Party Prices</h5>

                                    </div>
                                    <div class="col-sm-12 mt-2">
                                        <div class="table-responsive">
                                            <table id="nt_grade_prices" class="table table-bordered" style="width:50%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>NT grade</th>
                                                        <th>Third Party Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-dark">
                                                    @forelse ($nt_grades_prices as $key => $v)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $v->nt_grade ?? '' }}</td>
                                                            <td>
                                                                {{ $v->third_party_price ?? '' }}
                                                            </td>
                                                        </tr>

                                                    @empty
                                                        <tr>
                                                            <th colspan="3">No record found...</th>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Order Scan Out History</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="mt-2 mb-2 text-info"><small><span class="text-warning">Note: </span> Skew no. Format:
                                e.g: <span class="text-warning">W.3.A.L.BK.21.2500.4 <span class="text-info">/</span>
                                    Y.3.A.L.BK.21.2500.4
                                </span></small></p>
                        <p class="mt-2 mb-3 text-info"><small><span class="text-warning">W </span> = Weight Unit, <span
                                    class="text-warning">Y </span>=
                                Yards Unit, <span class="text-warning">3</span> = CGT Slug, <span
                                    class="text-warning">A</span>
                                = NT
                                Slug, <span class="text-warning">L</span> = Product Type Slug, <span class="text-warning">BK
                                </span> = Color Slug, <span class="text-warning">21 </span>
                                = # of rolls, <span class="text-warning">2500 </span>= weight/ yards
                                based on Unit, <span class="text-warning">4 </span>= any random number for avoid
                                duplication.</small></p>
                        <div class="table-responsive">
                            <table id="scan-out-history" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Skew #</th>
                                        <th>Customer</th>
                                        <th>Product Type</th>
                                        <th>CGT Grade</th>
                                        @if (auth()->check() && session('RoleHasPermission') !== null && session('RoleHasPermission')->nt_grade_column == 1)
                                            <th>NT Grade</th>
                                        @endif
                                        <th>Color</th>
                                        <th>Weight</th>
                                        <th>Yards</th>
                                        <th>Action</th>
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
                                                <td>{{ $value->customer_name ?? 'N/A' }}</td>
                                                <td>{{ $value->product_type ?? 'N/A' }}</td>
                                                <td>{{ $value->cgt_grade ?? 'N/A' }}</td>
                                                @if (auth()->check() && session('RoleHasPermission') !== null && session('RoleHasPermission')->nt_grade_column == 1)
                                                    <td>{{ $value->nt_grade ?? 'N/A' }}</td>
                                                @endif
                                                <td>{{ $value->color_name ?? 'N/A' }}</td>
                                                <td>{{ $value->weight ?? '-' }}</td>
                                                <td>{{ $value->yards ?? '-' }}</td>
                                                <td><a href="javascript:void(0);" class="delete_skew_popup"
                                                        skew-id="{{ $value->skew_id }}"
                                                        total_records={{ $total_records ?? 0 }} title="Delete">
                                                        <i class="fa fa-trash text-danger"></i>
                                                    </a></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Order Status History</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="status-history" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Previous Status</th>
                                        <th>Changed To</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody class="text-dark">
                                    @if (isset($order_history->allOrderStatuses) && !empty($order_history->allOrderStatuses))
                                        @foreach ($order_history->allOrderStatuses as $key => $allOrderStatus)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $allOrderStatus->previous_status == 'pending' ? 'Pending' : ($allOrderStatus->previous_status == 'preload' ? 'PreLoaded' : ($allOrderStatus->previous_status == 'shipping_in_process' ? 'Shipping In Process' : ($allOrderStatus->previous_status == 'shipped' ? 'Shipped' : ($allOrderStatus->previous_status == 'post_loading_documentation' ? 'Post Loading Documentation' : ($allOrderStatus->previous_status == 'end_stage' ? 'End Stage' : ($allOrderStatus->previous_status == 'closed' ? 'Closed' : ($allOrderStatus->previous_status == 'cancelled' ? 'Cancelled' : '-'))))))) }}
                                                </td>
                                                <td>{{ $allOrderStatus->changed_to == 'pending' ? 'Pending' : ($allOrderStatus->changed_to == 'preload' ? 'PreLoaded' : ($allOrderStatus->changed_to == 'shipping_in_process' ? 'Shipping In Process' : ($allOrderStatus->changed_to == 'shipped' ? 'Shipped' : ($allOrderStatus->changed_to == 'post_loading_documentation' ? 'Post Loading Documentation' : ($allOrderStatus->changed_to == 'end_stage' ? 'End Stage' : ($allOrderStatus->changed_to == 'closed' ? 'Closed' : ($allOrderStatus->changed_to == 'cancelled' ? 'Cancelled' : '-'))))))) }}
                                                </td>
                                                <td>{{ changeDateFormatToUS($allOrderStatus->created_at) ?? 'N/A' }}
                                                </td>
                                                <td>{{ date('h:i:s a', strtotime($allOrderStatus->created_at)) ?? 'N/A' }}
                                                </td>
                                                <td>{{ $allOrderStatus->getUser->full_name ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Order Documents</h4>
                        </div>
                        <button class="btn btn-sm btn-primary float-right upload_document">Upload Document</button>
                    </div>
                    <div class="card-body card-height">
                        <div class="row">
                            @if ($order_history->OrderFiles && $order_history->OrderFiles->count() > 0)
                                <div class="row">
                                    @foreach ($order_history->OrderFiles as $key => $OrderFile)
                                        <div class="col-md-2">
                                            <div class="card-container mb-3">
                                                <a href="{{ asset('storage/images/orderFiles/' . $OrderFile->file_name) }}"
                                                    target="blank">
                                                    @if (in_array(pathinfo($OrderFile->file_name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'PNG', 'JPEG', 'JPG']))
                                                        <img class="card-img-top"
                                                            src="{{ asset('storage/images/orderFiles/' . $OrderFile->file_name) }}"
                                                            alt="Image Preview">
                                                    @else
                                                        <div class="card-img-top file-icon">
                                                            <img class="card-img-top"
                                                                src="https://www.pngall.com/wp-content/uploads/2018/05/Files-High-Quality-PNG.png">
                                                        </div>
                                                    @endif
                                                </a>
                                                <div class="card-body text-center">
                                                    <p class="card-text word-wrap"
                                                        title="{{ $OrderFile->file_name ?? 'N/A' }}">
                                                        {{ $OrderFile->file_name ?? 'N/A' }}</p>
                                                    <a href="{{ asset('storage/images/orderFiles/' . $OrderFile->file_name) }}"
                                                        download>
                                                        <span class="badge rounded-pill bg-primary">Download</span>
                                                    </a>
                                                    <a href="javaScript:void(0);" class="delete_document"
                                                        document-id="{{ $OrderFile->id ?? '' }}">
                                                        <span class="badge rounded-pill bg-danger"
                                                            style="padding: 2px 17px;">Delete</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="col-md-12 text-dark text-center">
                                    <p>No Document Available</p>
                                </div>
                            @endif

                        </div>
                    </div>

                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Order Status Requirements</h4>
                        </div>
                    </div>
                    <div class="card-body card-height">

                        <div class="row">
                            <div class="accordion" id="status_requirements">
                                <form class="mb-5" id="preload_status_submission" method="post"
                                    action="{{ route('admin:orders.preloadStatusSubmission') }}">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order_history->id ?? '' }}">
                                    <input type="hidden" name="order_status" value="preload">
                                    <input type="hidden" name="order_current_status"
                                        value="{{ $order_history->status ?? '' }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#preloaded" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                Pre-Load Ready
                                            </button>
                                        </h2>
                                        <div id="preloaded" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <label class="mb-2"> Deposit Received </label>
                                                        <br>
                                                        <select class="form-control form-control-sm select2"
                                                            name="deposit_received">
                                                            <option value=""
                                                                @if (($order_status_requirements['preload'] ?? null) && $order_status_requirements['preload']->deposit_received == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (($order_status_requirements['preload'] ?? null) && $order_status_requirements['preload']->deposit_received == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (($order_status_requirements['preload'] ?? null) && $order_status_requirements['preload']->deposit_received == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (($order_status_requirements['preload'] ?? null) && $order_status_requirements['preload']->deposit_received == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>
                                                        @error('deposit_received')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2"> Deposit Amount </label>
                                                        <input type="number" class="form-control form-control-sm"
                                                            name="deposit_amount"
                                                            value="{{ $order_status_requirements['preload']->deposit_amount ?? '' }}"
                                                            id="deposit_amount">
                                                        @error('deposit_amount')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-12"><button type="submit"
                                                            class="btn btn-sm btn-primary mt-3"
                                                            style="float:right">Submit</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <form class="mb-5" id="shipping_in_process_status_submission" method="post"
                                    action="{{ route('admin:orders.shippingInProcessStatusSubmission') }}">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order_history->id ?? '' }}">
                                    <input type="hidden" name="order_status" value="shipping_in_process">
                                    <input type="hidden" name="order_current_status"
                                        value="{{ $order_history->status ?? '' }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#shipping_in_process" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                Shipping In Process
                                            </button>
                                        </h2>
                                        <div id="shipping_in_process" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <label class="mb-2"> Freight Forwarder </label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="freight_forwarder"
                                                            value="{{ $order_status_requirements['shipping_in_process']->freight_forwarder ?? '' }}"
                                                            id="freight_forwarder">
                                                        @error('freight_forwarder')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Best Rate Received</label>
                                                        <input type="number" class="form-control form-control-sm"
                                                            name="best_rate_received"
                                                            value="{{ $order_status_requirements['shipping_in_process']->best_rate_received ?? '' }}"
                                                            id="">
                                                        @error('best_rate_received')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Shipping Line</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="shipping_line"
                                                            value="{{ $order_status_requirements['shipping_in_process']->shipping_line ?? '' }}"
                                                            id="">
                                                        @error('shipping_line')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">ACID Received </label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="acid_received">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->acid_received == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->acid_received == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->acid_received == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->acid_received == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('acid_received')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">ACID Number </label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="acid_number"
                                                            value="{{ $order_status_requirements['shipping_in_process']->acid_number ?? '' }}"
                                                            id="">
                                                        @error('acid_number')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Booking Completed </label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="booking_completed">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->booking_completed == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->booking_completed == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->booking_completed == 'no') selected @endif>No
                                                            </option>
                                                            <option value="customer"
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->booking_completed == 'na') selected @endif>Cust
                                                            </option>
                                                        </select>
                                                        @error('booking_completed')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">ERD </label>
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="erd"
                                                            value="{{ $order_status_requirements['shipping_in_process']->erd ?? '' }}"
                                                            id="">
                                                        @error('erd')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Sailing Date </label>
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="sailing_date"
                                                            value="{{ $order_status_requirements['shipping_in_process']->sailing_date ?? '' }}"
                                                            id="">
                                                        @error('sailing_date')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Trucker Name </label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="truker_name"
                                                            value="{{ $order_status_requirements['shipping_in_process']->truker_name ?? '' }}"
                                                            id="">
                                                        @error('truker_name')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Trucker Quote </label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="trucker_quote"
                                                            value="{{ $order_status_requirements['shipping_in_process']->trucker_quote ?? '' }}"
                                                            id="">
                                                        @error('trucker_quote')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Load Date</label>
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="load_date"
                                                            value="{{ $order_status_requirements['shipping_in_process']->load_date ?? '' }}"
                                                            id="">
                                                        @error('load_date')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Release Notes Submitted</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="release_notes">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->release_notes == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->release_notes == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->release_notes == 'no') selected @endif>No
                                                            </option>
                                                        </select>
                                                        @error('release_notes')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Pre-Shipping Docs Customer Approved</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="pre_shipping_docs">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->pre_shipping_docs == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->pre_shipping_docs == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['shipping_in_process'] ?? null) &&
                                                                        $order_status_requirements['shipping_in_process']->pre_shipping_docs == 'no') selected @endif>No
                                                            </option>
                                                        </select>
                                                        @error('pre_shipping_docs')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-12"><button type="submit"
                                                            class="btn btn-sm btn-primary mt-3"
                                                            style="float:right">Submit</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <form class="mb-5" id="shipped_status_submission" method="post"
                                    action="{{ route('admin:orders.shippedStatusSubmission') }}">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order_history->id ?? '' }}">
                                    <input type="hidden" name="order_status" value="shipped">
                                    <input type="hidden" name="order_current_status"
                                        value="{{ $order_history->status ?? '' }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#shipped" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                Shipped
                                            </button>
                                        </h2>
                                        <div id="shipped" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Items Shipped and Scanned Out</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="item_shipped_scanned_out">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['shipped'] ?? null) &&
                                                                        $order_status_requirements['shipped']->item_shipped_scanned_out == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['shipped'] ?? null) &&
                                                                        $order_status_requirements['shipped']->item_shipped_scanned_out == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['shipped'] ?? null) &&
                                                                        $order_status_requirements['shipped']->item_shipped_scanned_out == 'no') selected @endif>No
                                                            </option>
                                                        </select>
                                                        @error('item_shipped_scanned_out')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-12"><button type="submit"
                                                            class="btn btn-sm btn-primary mt-3"
                                                            style="float:right">Submit</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <form class="mb-5" id="post_loading_documentation_status_submission" method="post"
                                    action="{{ route('admin:orders.postLoadingDocumentationStatusSubmission') }}">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order_history->id ?? '' }}">
                                    <input type="hidden" name="order_status" value="post_loading_documentation">
                                    <input type="hidden" name="order_current_status"
                                        value="{{ $order_history->status ?? '' }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#post_loading_documentation_dropdown" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                Post Load Documentation
                                            </button>
                                        </h2>
                                        <div id="post_loading_documentation_dropdown" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Final Docs Submitted to FF</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="final_doc_submitted_to_ff">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->final_doc_submitted_to_ff == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->final_doc_submitted_to_ff == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->final_doc_submitted_to_ff == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->final_doc_submitted_to_ff == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('final_doc_submitted_to_ff')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">NEXPAC Report Sent</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="nexpac_report_sent">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->nexpac_report_sent == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->nexpac_report_sent == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->nexpac_report_sent == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->nexpac_report_sent == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('nexpac_report_sent')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">KTC Report Sent</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="ktc_report_sent">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->ktc_report_sent == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->ktc_report_sent == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->ktc_report_sent == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->ktc_report_sent == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('ktc_report_sent')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Customer Email and all paperwork
                                                            completed</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="customer_email_all_paper_work">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->customer_email_all_paper_work == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->customer_email_all_paper_work == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->customer_email_all_paper_work == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->customer_email_all_paper_work == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('customer_email_all_paper_work')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">NEXTRADE Invoicing</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="nextrade_invoicing">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->nextrade_invoicing == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->nextrade_invoicing == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->nextrade_invoicing == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->nextrade_invoicing == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('nextrade_invoicing')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Obsolete Report Updated</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="obelete_report_updated">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->obelete_report_updated == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->obelete_report_updated == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->obelete_report_updated == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (
                                                                    ($order_status_requirements['post_loading_documentation'] ?? null) &&
                                                                        $order_status_requirements['post_loading_documentation']->obelete_report_updated == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('obelete_report_updated')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-12"><button type="submit"
                                                            class="btn btn-sm btn-primary mt-3"
                                                            style="float:right">Submit</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <form class="mb-5" id="end_stage_status_submission" method="post"
                                    action="{{ route('admin:orders.endStageStatusSubmission') }}">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order_history->id ?? '' }}">
                                    <input type="hidden" name="order_status" value="end_stage">
                                    <input type="hidden" name="order_current_status"
                                        value="{{ $order_history->status ?? '' }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#end_stage_dropdown" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                End Stage
                                            </button>
                                        </h2>
                                        <div id="end_stage_dropdown" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Final BL Draft submitted to customer and
                                                            approved</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="final_bl_draft_to_customer">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_bl_draft_to_customer == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_bl_draft_to_customer == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_bl_draft_to_customer == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_bl_draft_to_customer == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('final_bl_draft_to_customer')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Release requested</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="release_requested">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->release_requested == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->release_requested == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->release_requested == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->release_requested == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('release_requested')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">BL Received</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="bl_received">
                                                            <option value=""
                                                                @if (($order_status_requirements['end_stage'] ?? null) && $order_status_requirements['end_stage']->bl_received == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (($order_status_requirements['end_stage'] ?? null) && $order_status_requirements['end_stage']->bl_received == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (($order_status_requirements['end_stage'] ?? null) && $order_status_requirements['end_stage']->bl_received == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (($order_status_requirements['end_stage'] ?? null) && $order_status_requirements['end_stage']->bl_received == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('bl_received')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Final Documents submitted to bank</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="final_document_to_bank">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_document_to_bank == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_document_to_bank == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_document_to_bank == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_document_to_bank == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('final_document_to_bank')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Final Documents submitted to customer</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="final_document_to_customer">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_document_to_customer == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_document_to_customer == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_document_to_customer == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_document_to_customer == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('final_document_to_customer')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Final Documents submitted to cargoX</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="final_document_to_cargox">
                                                            <option value=""
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_document_to_cargox == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_document_to_cargox == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_document_to_cargox == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_document_to_cargox == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('final_document_to_cargox')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Final Payment</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="final_payment">
                                                            <option value=""
                                                                @if (($order_status_requirements['end_stage'] ?? null) && $order_status_requirements['end_stage']->final_payment == '') selected @endif>Select
                                                            </option>
                                                            <option value="paid"
                                                                @if (
                                                                    ($order_status_requirements['end_stage'] ?? null) &&
                                                                        $order_status_requirements['end_stage']->final_payment == 'paid') selected @endif>Paid
                                                            </option>
                                                            <option value="bank_doc"
                                                                @if (($order_status_requirements['end_stage'] ?? null) && $order_status_requirements['end_stage']->final_payment == 'bank_doc') selected @endif>Bank
                                                                Documentation
                                                            </option>

                                                        </select>

                                                        @error('final_payment')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-12"><button type="submit"
                                                            class="btn btn-sm btn-primary mt-3"
                                                            style="float:right">Submit</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <form class="mb-5" id="closed_status_submission" method="post"
                                    action="{{ route('admin:orders.closedStatusSubmission') }}">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order_history->id ?? '' }}">
                                    <input type="hidden" name="order_status" value="closed">
                                    <input type="hidden" name="order_current_status"
                                        value="{{ $order_history->status ?? '' }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#end_status_dropdown" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                Closed
                                            </button>
                                        </h2>
                                        <div id="end_status_dropdown" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <label class="mb-2"> FF Invoice </label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="ff_invoivce"
                                                            value="{{ $order_status_requirements['closed']->ff_invoivce ?? '' }}"
                                                            id="">
                                                        @error('ff_invoivce')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">FF Paid </label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="ff_paid">
                                                            <option value=""
                                                                @if (($order_status_requirements['closed'] ?? null) && $order_status_requirements['closed']->ff_paid == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (($order_status_requirements['closed'] ?? null) && $order_status_requirements['closed']->ff_paid == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (($order_status_requirements['closed'] ?? null) && $order_status_requirements['closed']->ff_paid == 'no') selected @endif>No
                                                            </option>
                                                            <option value="na"
                                                                @if (($order_status_requirements['closed'] ?? null) && $order_status_requirements['closed']->ff_paid == 'na') selected @endif>N/A
                                                            </option>
                                                        </select>

                                                        @error('ff_paid')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">FF DATE Paid </label>
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="ff_date_paid"
                                                            value="{{ $order_status_requirements['closed']->ff_date_paid ?? '' }}"
                                                            id="">
                                                        @error('ff_date_paid')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">TRUCKER Invoice </label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="ff_invoice"
                                                            value="{{ $order_status_requirements['closed']->ff_invoice ?? '' }}"
                                                            id="">
                                                        @error('ff_invoice')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">TRUCKER Paid</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="trucker_paid">
                                                            <option value=""
                                                                @if (($order_status_requirements['closed'] ?? null) && $order_status_requirements['closed']->trucker_paid == '') selected @endif>Select
                                                            </option>
                                                            <option value="yes"
                                                                @if (($order_status_requirements['closed'] ?? null) && $order_status_requirements['closed']->trucker_paid == 'yes') selected @endif>Yes
                                                            </option>
                                                            <option value="no"
                                                                @if (($order_status_requirements['closed'] ?? null) && $order_status_requirements['closed']->trucker_paid == 'no') selected @endif>No
                                                            </option>
                                                        </select>
                                                        @error('trucker_paid')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="mb-2">TRUCKER Date Paid </label>
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="trucker_date"
                                                            value="{{ $order_status_requirements['closed']->trucker_date ?? '' }}"
                                                            id="">
                                                        @error('trucker_date')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="mb-2">Final Payment</label>
                                                        <select class="form-control form-control-sm select2"
                                                            name="final_payment_closed">
                                                            <option value=""
                                                                @if (($order_status_requirements['closed'] ?? null) && $order_status_requirements['closed']->final_payment_closed == '') selected @endif>Select
                                                            </option>
                                                            <option value="paid"
                                                                @if (
                                                                    ($order_status_requirements['closed'] ?? null) &&
                                                                        $order_status_requirements['closed']->final_payment_closed == 'paid') selected @endif>Paid
                                                            </option>
                                                            <option value="bank_paid"
                                                                @if (($order_status_requirements['closed'] ?? null) && $order_status_requirements['closed']->final_payment_closed == 'bank_paid') selected @endif>Bank
                                                                Paid
                                                            </option>

                                                        </select>

                                                        @error('final_payment_closed')
                                                            <span class="invalid-feedback" style="display: block;"
                                                                role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-lg-12"><button type="submit"
                                                            class="btn btn-sm btn-primary mt-3"
                                                            style="float:right">Submit</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModalDefault" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" id="form-example-1" action="{{ route('admin:orders.updateOrderScanStatus') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Update Status
                            </h5>
                        </div>
                        <div class="modal-body">
                            <select class="form-control form-control-sm order-status" name="order_status">
                                @if (isset($order_status) & !empty($order_status))
                                    @foreach ($order_status as $key1 => $val)
                                        <option value={{ $val['status'] }}
                                            {{ $val['status'] == $order_history->status ? 'selected' : '' }}>
                                            {{ $val['status'] == 'pending' ? 'Pending' : ($val['status'] == 'preload' ? 'PreLoaded' : ($val['status'] == 'shipping_in_process' ? 'Shipping In Process' : ($val['status'] == 'shipped' ? 'Shipped' : ($val['status'] == 'post_loading_documentation' ? 'Post Loading Documentation' : ($val['status'] == 'end_stage' ? 'End Stage' : ($val['status'] == 'closed' ? 'Closed' : ($val['status'] == 'cancelled' ? 'Cancelled' : '-'))))))) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <input type="hidden" value="{{ $order_history->status }}" name="previous_status">
                            <div class="preload_status d-flex mt-4 d-none">
                                <input type="checkbox" id="deposit_received" name="deposit_received"
                                    {{ $order_history->status == 'preload' && isset($order_status_recent_record) && $order_status_recent_record->deposit_received == 1 ? 'checked' : '' }}>
                                <label for="deposit_received" style="margin-left:35px">Deposit received</label>

                            </div>
                            <div class="shipping_in_process_status d-grid mt-4 d-none">
                                <div class="d-flex">
                                    <input type="checkbox" id="rate_received" name="rate_received"
                                        {{ $order_history->status == 'shipping_in_process' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->rate_received == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="rate_received" style="margin-left:35px">Rate Received</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="rate_approved" name="rate_approved"
                                        {{ $order_history->status == 'shipping_in_process' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->rate_approved == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="rate_approved" style="margin-left:35px">Rate Approved</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="rate_quote" name="rate_quote"
                                        {{ $order_history->status == 'shipping_in_process' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->rate_quote == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="rate_quote" style="margin-left:35px">Rate Quote</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="acid_received" name="acid_received"
                                        {{ $order_history->status == 'shipping_in_process' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->acid_received == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="acid_received" style="margin-left:35px">Acid received(If Egypt shipping
                                        only)</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="acid_number" name="acid_number"
                                        {{ $order_history->status == 'shipping_in_process' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->acid_number == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="acid_number" style="margin-left:35px">Acid Number(If Egypt
                                        Only)</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="booking_completed" name="booking_completed"
                                        {{ $order_history->status == 'shipping_in_process' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->booking_completed == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="booking_completed" style="margin-left:35px">Booking Completed</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="erd" name="erd"
                                        {{ $order_history->status == 'shipping_in_process' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->erd == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="erd" style="margin-left:35px">ERD</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="sailing_date" name="sailing_date"
                                        {{ $order_history->status == 'shipping_in_process' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->sailing_date == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="sailing_date" style="margin-left:35px">Sailing Date</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="arrival_date" name="arrival_date"
                                        {{ $order_history->status == 'shipping_in_process' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->arrival_date == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="arrival_date" style="margin-left:35px">Arrival Date</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="truker_name" name="truker_name"
                                        {{ $order_history->status == 'shipping_in_process' && isset($order_status_recent_record) && $order_status_recent_record->truker_name == 1 ? 'checked' : '' }}>
                                    <label for="truker_name" style="margin-left:35px">Trucker Name</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="trucker_quote" name="trucker_quote"
                                        {{ $order_history->status == 'shipping_in_process' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->trucker_quote == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="trucker_quote" style="margin-left:35px">Truck Quote</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="load_date" name="load_date"
                                        {{ $order_history->status == 'shipping_in_process' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->load_date == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="load_date" style="margin-left:35px">Load Date</label>

                                </div>
                            </div>
                            <div class="shipped_status d-grid mt-4 d-none">
                                <div class="d-flex">
                                    <input type="checkbox" id="item_shipped" name="item_shipped"
                                        {{ $order_history->status == 'shipped' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->item_shipped == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="item_shipped" style="margin-left:35px">Items Shipped and Scanned
                                        Out</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="pre_shipped" name="pre_shipped"
                                        {{ $order_history->status == 'shipped' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->pre_shipped == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="pre_shipped" style="margin-left:35px">Pre-Shipping Docs Customer
                                        approved</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="preliminary_doc" name="preliminary_doc"
                                        {{ $order_history->status == 'shipped' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->preliminary_doc == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="preliminary_doc" style="margin-left:35px">Preliminary Docs submitted to
                                        FF</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="release_notes" name="release_notes"
                                        {{ $order_history->status == 'shipped' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->release_notes == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="release_notes" style="margin-left:35px">Release notes submitted</label>

                                </div>
                            </div>
                            <div class="post_loading_documentation_status d-grid mt-4 d-none">
                                <div class="d-flex">
                                    <input type="checkbox" id="shipment_loaded" name="shipment_loaded"
                                        {{ $order_history->status == 'post_loading_documentation' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->shipment_loaded == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="shipment_loaded" style="margin-left:35px">Shipment loaded and scanned
                                        out</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="final_shipping_doc" name="final_shipping_doc"
                                        {{ $order_history->status == 'post_loading_documentation' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->final_shipping_doc == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="final_shipping_doc" style="margin-left:35px">Final shipping Docs
                                        submitted to FF</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="nextpac_report" name="nextpac_report"
                                        {{ $order_history->status == 'post_loading_documentation' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->nextpac_report == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="nextpac_report" style="margin-left:35px">Nexpac report</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="ktc_report" name="ktc_report"
                                        {{ $order_history->status == 'post_loading_documentation' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->ktc_report == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="ktc_report" style="margin-left:35px">KTC report Sent</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="cus_paperwork_completed" name="cus_paperwork_completed"
                                        {{ $order_history->status == 'post_loading_documentation' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->cus_paperwork_completed == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="cus_paperwork_completed" style="margin-left:35px">Customer Email and all
                                        paperwork completed</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="nextrade_invoicing" name="nextrade_invoicing"
                                        {{ $order_history->status == 'post_loading_documentation' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->nextrade_invoicing == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="nextrade_invoicing" style="margin-left:35px">Nextrade Invoicing</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="obselete_report" name="obselete_report"
                                        {{ $order_history->status == 'post_loading_documentation' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->obselete_report == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="obselete_report" style="margin-left:35px">Obsolete report updated</label>

                                </div>
                            </div>
                            <div class="end_stage_status d-grid mt-4 d-none">
                                <div class="d-flex">
                                    <input type="checkbox" id="final_payment_received" name="final_payment_received"
                                        {{ $order_history->status == 'end_stage' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->final_payment_received == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="final_payment_received" style="margin-left:35px">Final payment
                                        received</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="final_bl_draft" name="final_bl_draft"
                                        {{ $order_history->status == 'end_stage' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->final_bl_draft == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="final_bl_draft" style="margin-left:35px">Final BL Draft submitted to
                                        customer and approved</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="release_requested" name="release_requested"
                                        {{ $order_history->status == 'end_stage' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->release_requested == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="release_requested" style="margin-left:35px">Release requested</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="bl_requested" name="bl_requested"
                                        {{ $order_history->status == 'end_stage' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->bl_requested == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="bl_requested" style="margin-left:35px">BL Received</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="final_doc_to_bank" name="final_doc_to_bank"
                                        {{ $order_history->status == 'end_stage' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->final_doc_to_bank == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="final_doc_to_bank" style="margin-left:35px">Final Documents submitted to
                                        bank</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="final_doc_to_customer" name="final_doc_to_customer"
                                        {{ $order_history->status == 'end_stage' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->final_doc_to_customer == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="final_doc_to_customer" style="margin-left:35px">Final Documents submitted
                                        to customer</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="final_doc_to_cargoX" name="final_doc_to_cargoX"
                                        {{ $order_history->status == 'end_stage' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->final_doc_to_cargoX == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="final_doc_to_cargoX" style="margin-left:35px">Final Documents submitted
                                        to cargoX</label>

                                </div>
                            </div>
                            <div class="closed_status d-grid mt-4 d-none">
                                <div class="d-flex">
                                    <input type="checkbox" id="ff_invoice" name="ff_invoice"
                                        {{ $order_history->status == 'closed' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->ff_invoice == 1
                                            ? 'checked'
                                            : '' }}>

                                    <label for="ff_invoice" style="margin-left:35px">FF invoice</label>
                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="ff_paid" name="ff_paid"
                                        {{ $order_history->status == 'closed' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->ff_paid == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="ff_paid" style="margin-left:35px">FF Paid</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="ff_date_paid" name="ff_date_paid"
                                        {{ $order_history->status == 'closed' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->ff_date_paid == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="ff_date_paid" style="margin-left:35px">FF DATE Paid</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="trucker_invoice" name="trucker_invoice"
                                        {{ $order_history->status == 'closed' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->trucker_invoice == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="trucker_invoice" style="margin-left:35px">TRUCKER invoice</label>

                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="trucker_paid" name="trucker_paid"
                                        {{ $order_history->status == 'closed' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->trucker_paid == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="trucker_paid" style="margin-left:35px">TRUCKER Paid</label>
                                </div>
                                <div class="d-flex">
                                    <input type="checkbox" id="trucker_date" name="trucker_date"
                                        {{ $order_history->status == 'closed' &&
                                        isset($order_status_recent_record) &&
                                        $order_status_recent_record->trucker_date == 1
                                            ? 'checked'
                                            : '' }}>
                                    <label for="trucker_date" style="margin-left:35px">TRUCKER DATE</label>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="user_id" id="user_id" value="{{ auth()->user()->id ?? '' }}">
                        <input type="hidden" name="skew_number" id="skew_number"
                            value="{{ $value->skew_number ?? '' }}">
                        <input type="hidden" name="scan_out_inventory_id" id="scan_out_inventory_id" value="">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-danger change_status">Change</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="order_delete" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Confirm Delete
                        </h5>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this Skew Number?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm btn-danger delete_skew" order_records_count=""
                            id="model_id" scan_out_inventory_id="{{ $order_history->id }}">Delete
                            Skew</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="document_uploads" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" id="form-example-1" action="{{ route('admin:orders.updateOrderDocuments') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Upload Document
                            </h5>
                        </div>
                        <div class="modal-body">
                            <div class="input-field">
                                <div class="input-images-1"></div>
                            </div>
                        </div>
                        <input type="hidden" name="scan_out_inventory_id" value="{{ $order_history->id }}">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-danger submit_document" id="model_id">Upload
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="document_delete_popup" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Confirm Delete
                        </h5>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this Document?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm btn-danger delete_user"
                            id="document_delete_id">Delete
                            Document</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('assets/js/image-uploader.min.js') }}"></script>
    <script type="text/javascript">
        var table = $('#scan-out-history').DataTable({
            // processing: true,
            // serverSide: true,
        });
        var table2 = $('#status-history').DataTable();
        $('.input-images-1').imageUploader();
        // Define the isPreviewableImage function to check if an image is previewable
        function isPreviewableImage(url, callback) {
            var image = new Image();
            image.onload = function() {
                callback(true);
            };
            image.onerror = function() {
                callback(false);
            };
            image.src = url;
        }

        $(document).on('click', '.delete_document', function() {
            var id = $(this).attr('document-id');
            $('#document_delete_id').val(id);
            $('#document_delete_popup').modal('show');
        });

        $(document).on('click', '.delete_user', function() {
            var id = $(this).val();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:orders.deleteDocument') }}",
                data: {
                    'document_id': id,
                    '_token': "{{ csrf_token() }}"
                },
                success: function(result) {
                    if (result.status = true) {
                        toastr.success(result.message);
                        location.reload();
                    } else {
                        toastr.error(result.message);
                    }
                }
            });
        });

        $('input[name="images[]"]').on('change', function() {
            // Loop through all uploaded images
            var files = $(this).get(0).files;
            $('.uploaded-image').each(function(index, item) {
                var file = files[index];
                var $img = $(this).find('img');
                var imgSrc = $img.attr('src');
                var file = $img.attr('title', file.name);
                // Check if the image is previewable using isPreviewableImage()
                isPreviewableImage(imgSrc, function(isPreviewable) {
                    if (isPreviewable) {
                        // console.log('Image is previewable: ' + isPreviewable);
                    } else {
                        // console.log('Image is not previewable');
                        $img.attr({
                            'src': 'https://www.pngall.com/wp-content/uploads/2018/05/Files-High-Quality-PNG.png',
                            'style': 'object-fit: contain !important',
                            'title': file.name
                        });
                    }
                });
            });
        });


        $(document).on('click', '.get_order_id', function() {
            var scan_out_inventory_id = $(this).attr('data-id');
            var current_order_status = $(this).attr('current-order-status');
            if (current_order_status == 'preload') {
                $('.preload_status').removeClass('d-none');
                $('input[name="deposit_received"]').prop('required', true);
            } else if (current_order_status == 'shipping_in_process') {
                $('.shipping_in_process_status').removeClass('d-none');
                $('input[name="rate_received"]').prop('required', true);
                $('input[name="rate_approved"]').prop('required', true);
                $('input[name="rate_quote"]').prop('required', true);
                $('input[name="acid_received"]').prop('required', true);
                $('input[name="acid_number"]').prop('required', true);
                $('input[name="booking_completed"]').prop('required', true);
                $('input[name="erd"]').prop('required', true);
                $('input[name="sailing_date"]').prop('required', true);
                $('input[name="arrival_date"]').prop('required', true);
                $('input[name="truker_name"]').prop('required', true);
                $('input[name="trucker_quote"]').prop('required', true);
                $('input[name="load_date"]').prop('required', true);
            } else if (current_order_status == 'shipped') {
                $('.shipped_status').removeClass('d-none');
                $('input[name="item_shipped"]').prop('required', true);
                $('input[name="pre_shipped"]').prop('required', true);
                $('input[name="preliminary_doc"]').prop('required', true);
                $('input[name="release_notes"]').prop('required', true);
            } else if (current_order_status == 'post_loading_documentation') {
                $('.post_loading_documentation_status').removeClass('d-none');
                $('input[name="shipment_loaded"]').prop('required', true);
                $('input[name="final_shipping_doc"]').prop('required', true);
                $('input[name="nextpac_report"]').prop('required', true);
                $('input[name="ktc_report"]').prop('required', true);
                $('input[name="cus_paperwork_completed"]').prop('required', true);
                $('input[name="nextrade_invoicing"]').prop('required', true);
                $('input[name="obselete_report"]').prop('required', true);
            } else if (current_order_status == 'end_stage') {
                $('.end_stage_status').removeClass('d-none');
                $('input[name="final_payment_received"]').prop('required', true);
                $('input[name="final_bl_draft"]').prop('required', true);
                $('input[name="release_requested"]').prop('required', true);
                $('input[name="bl_requested"]').prop('required', true);
                $('input[name="final_doc_to_bank"]').prop('required', true);
                $('input[name="final_doc_to_customer"]').prop('required', true);
                $('input[name="final_doc_to_cargoX"]').prop('required', true);
            } else if (current_order_status == 'closed') {
                $('.closed_status').removeClass('d-none');
                $('input[name="ff_invoice"]').prop('required', true);
                $('input[name="ff_paid"]').prop('required', true);
                $('input[name="ff_date_paid"]').prop('required', true);
                $('input[name="trucker_invoice"]').prop('required', true);
                $('input[name="trucker_paid"]').prop('required', true);
                $('input[name="trucker_date"]').prop('required', true);
            } else {
                $('.preload_status').addClass('d-none');
                $('.shipping_in_process_status').addClass('d-none');
                $('.shipped_status').addClass('d-none');
                $('.post_loading_documentation_status').addClass('d-none');
                $('.end_stage_status').addClass('d-none');
                $('input').prop('required', false);
            }

            $('.order-status').val(current_order_status);
            $('#scan_out_inventory_id').val(scan_out_inventory_id);
            $('#exampleModalDefault').modal('show');
        });

        $(document).on('click', '.upload_document', function() {
            $('#document_uploads').modal('show');
        })

        $(document).ready(function() {
            var currentOrderStatus = $('select[name=order_status] option:selected').val();
            if (currentOrderStatus == 'preload') {
                $('.preload_status').removeClass('d-none');
            } else if (currentOrderStatus == 'shipping_in_process') {
                $('.shipping_in_process_status').removeClass('d-none');
            } else if (currentOrderStatus == 'shipped') {
                $('.shipped_status').removeClass('d-none');
            } else if (currentOrderStatus == 'post_loading_documentation') {
                $('.post_loading_documentation_status').removeClass('d-none');
            } else if (currentOrderStatus == 'end_stage') {
                $('.end_stage_status').removeClass('d-none');
            } else {
                $('.preload_status').addClass('d-none');
                $('.shipping_in_process_status').addClass('d-none');
                $('.shipped_status').addClass('d-none');
                $('.post_loading_documentation_status').addClass('d-none');
            }
            if (currentOrderStatus == 'closed' || currentOrderStatus == 'cancelled') {
                $('#inv_status').prop('disabled', true);
            } else {
                $('#inv_status').prop('disabled', false);
            }
        });
        $('.order-status').on('change', function() {
            var orderStatus = $('select[name=order_status] option:selected').val()
            if (orderStatus == 'preload') {
                $('.preload_status').removeClass('d-none');
                $('input[name="deposit_received"]').prop('required', true);
                $('.shipping_in_process_status').addClass('d-none');
                $('.shipping_in_process_status input').prop('required', false);
                $('.shipped_status').addClass('d-none');
                $('.shipped_status input').prop('required', false);
                $('.post_loading_documentation_status').addClass('d-none');
                $('.post_loading_documentation_status input').prop('required', false);
                $('.end_stage_status').addClass('d-none');
                $('.end_stage_status input').prop('required', false);
                $('.closed_status').addClass('d-none');
                $('.closed_status input').prop('required', false);
            } else if (orderStatus == 'shipping_in_process') {
                $('.shipping_in_process_status').removeClass('d-none');
                $('input[name="rate_received"]').prop('required', true);
                $('input[name="rate_approved"]').prop('required', true);
                $('input[name="rate_quote"]').prop('required', true);
                $('input[name="acid_received"]').prop('required', true);
                $('input[name="acid_number"]').prop('required', true);
                $('input[name="booking_completed"]').prop('required', true);
                $('input[name="erd"]').prop('required', true);
                $('input[name="sailing_date"]').prop('required', true);
                $('input[name="arrival_date"]').prop('required', true);
                $('input[name="truker_name"]').prop('required', true);
                $('input[name="trucker_quote"]').prop('required', true);
                $('input[name="load_date"]').prop('required', true);
                $('.preload_status').addClass('d-none');
                $('.preload_status input').prop('required', false);
                $('.shipped_status').addClass('d-none');
                $('.shipped_status input').prop('required', false);
                $('.post_loading_documentation_status').addClass('d-none');
                $('.post_loading_documentation_status input').prop('required', false);
                $('.end_stage_status').addClass('d-none');
                $('.end_stage_status input').prop('required', false);
                $('.closed_status').addClass('d-none');
                $('.closed_status input').prop('required', false);
            } else if (orderStatus == 'shipped') {
                $('.shipped_status').removeClass('d-none');
                $('input[name="item_shipped"]').prop('required', true);
                $('input[name="pre_shipped"]').prop('required', true);
                $('input[name="preliminary_doc"]').prop('required', true);
                $('input[name="release_notes"]').prop('required', true);
                $('.preload_status').addClass('d-none');
                $('.preload_status input').prop('required', false);
                $('.shipping_in_process_status').addClass('d-none');
                $('.shipping_in_process_status input').prop('required', false);
                $('.post_loading_documentation_status').addClass('d-none');
                $('.post_loading_documentation_status input').prop('required', false);
                $('.end_stage_status').addClass('d-none');
                $('.end_stage_status input').prop('required', false);
                $('.closed_status').addClass('d-none');
                $('.closed_status input').prop('required', false);
            } else if (orderStatus == 'post_loading_documentation') {
                $('.post_loading_documentation_status').removeClass('d-none');
                $('input[name="shipment_loaded"]').prop('required', true);
                $('input[name="final_shipping_doc"]').prop('required', true);
                $('input[name="nextpac_report"]').prop('required', true);
                $('input[name="ktc_report"]').prop('required', true);
                $('input[name="cus_paperwork_completed"]').prop('required', true);
                $('input[name="nextrade_invoicing"]').prop('required', true);
                $('input[name="obselete_report"]').prop('required', true);
                $('.preload_status').addClass('d-none');
                $('.preload_status input').prop('required', false);
                $('.shipping_in_process_status').addClass('d-none');
                $('.shipping_in_process_status input').prop('required', false);
                $('.shipped_status').addClass('d-none');
                $('.shipped_status input').prop('required', false);
                $('.end_stage_status').addClass('d-none');
                $('.end_stage_status input').prop('required', false);
                $('.closed_status').addClass('d-none');
                $('.closed_status input').prop('required', false);
            } else if (orderStatus == 'end_stage') {
                $('.end_stage_status').removeClass('d-none');
                $('input[name="final_payment_received"]').prop('required', true);
                $('input[name="final_bl_draft"]').prop('required', true);
                $('input[name="release_requested"]').prop('required', true);
                $('input[name="bl_requested"]').prop('required', true);
                $('input[name="final_doc_to_bank"]').prop('required', true);
                $('input[name="final_doc_to_customer"]').prop('required', true);
                $('input[name="final_doc_to_cargoX"]').prop('required', true);
                $('.preload_status').addClass('d-none');
                $('.preload_status input').prop('required', false);
                $('.shipping_in_process_status').addClass('d-none');
                $('.shipping_in_process_status input').prop('required', false);
                $('.shipped_status').addClass('d-none');
                $('.shipped_status input').prop('required', false);
                $('.post_loading_documentation_status').addClass('d-none');
                $('.post_loading_documentation_status input').prop('required', false);
                $('.closed_status').addClass('d-none');
                $('.closed_status input').prop('required', false);
            } else if (orderStatus == 'closed') {
                $('.closed_status').removeClass('d-none');
                $('input[name="ff_invoice"]').prop('required', true);
                $('input[name="ff_paid"]').prop('required', true);
                $('input[name="ff_date_paid"]').prop('required', true);
                $('input[name="trucker_invoice"]').prop('required', true);
                $('input[name="trucker_paid"]').prop('required', true);
                $('input[name="trucker_date"]').prop('required', true);
                $('.preload_status').addClass('d-none');
                $('.preload_status input').prop('required', false);
                $('.shipping_in_process_status').addClass('d-none');
                $('.shipping_in_process_status input').prop('required', false);
                $('.shipped_status').addClass('d-none');
                $('.shipped_status input').prop('required', false);
                $('.post_loading_documentation_status').addClass('d-none');
                $('.post_loading_documentation_status input').prop('required', false);
                $('.end_stage_status').addClass('d-none');
                $('.end_stage_status input').prop('required', false);
            } else {
                $('.preload_status').addClass('d-none');
                $('.shipped_status').addClass('d-none');
                $('.shipping_in_process_status').addClass('d-none');
                $('.post_loading_documentation_status').addClass('d-none');
                $('.end_stage_status').addClass('d-none');
                $('.closed_status').addClass('d-none');
                $('input').prop('required', false);
            }
        });

        $(document).on('click', '.delete_skew_popup', function() {
            var id = $(this).attr('skew-id');
            var total_records = $(this).attr('total_records');
            if (total_records == 1) {
                $('.modal-body').html(
                    "By deleting this last skew number the order will also delete. Do you want to proceed?");
            }
            $('#model_id').val(id);
            $('#model_id').attr('order_records_count', total_records);
            $('#order_delete').modal('show');
        });

        $(document).on('click', '.delete_skew', function() {
            var scan_in_id = $(this).val();
            var scan_out_inventory_id = $(this).attr('scan_out_inventory_id')
            var order_records_count = $(this).attr('order_records_count');
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:orders.skewNumberDelete') }}",
                data: {
                    'scan_in_id': scan_in_id,
                    'scan_out_inventory_id': scan_out_inventory_id,
                    'total_records': order_records_count,
                    '_token': "{{ csrf_token() }}"
                },
                success: function(result) {
                    if (result.status === true) {
                        toastr.success(result.message);
                        if (result.order_delete === true) {
                            // Redirect to the admin orders page
                            window.location.href = "{{ route('admin:orders') }}";
                        } else {
                            // Reload the current page
                            location.reload();
                        }
                    } else {
                        toastr.error(result.message);
                    }

                }
            });
        });

        $('#preload_status_submission').submit(function(e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');

            $.ajax({
                url: url,
                method: method,
                data: form.serialize(),
                success: function(response) {
                    toastr.success(response.message);
                    location.reload();
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    if (typeof errors === 'object') {
                        for (var key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                var errorMessages = errors[key];
                                if (Array.isArray(errorMessages) && errorMessages.length > 0) {
                                    for (var i = 0; i < errorMessages.length; i++) {
                                        toastr.error(errorMessages[i]);
                                    }
                                } else {
                                    toastr.error(errorMessages);
                                }
                            }
                        }
                    } else {
                        toastr.error(response.responseJSON.errors);
                    }
                }
            });
        });

        $('#shipping_in_process_status_submission').submit(function(e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');

            $.ajax({
                url: url,
                method: method,
                data: form.serialize(),
                success: function(response) {
                    toastr.success(response.message);
                    location.reload();
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    if (typeof errors === 'object') {
                        for (var key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                var errorMessages = errors[key];
                                if (Array.isArray(errorMessages) && errorMessages.length > 0) {
                                    for (var i = 0; i < errorMessages.length; i++) {
                                        toastr.error(errorMessages[i]);
                                    }
                                } else {
                                    toastr.error(errorMessages);
                                }
                            }
                        }
                    } else {
                        toastr.error(response.responseJSON.errors);
                    }
                }
            });
        });

        $('#shipped_status_submission').submit(function(e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');

            $.ajax({
                url: url,
                method: method,
                data: form.serialize(),
                success: function(response) {
                    toastr.success(response.message);
                    location.reload();
                },
                error: function(response) {
                    toastr.error(response.responseJSON.errors);
                }
            });
        });

        $('#post_loading_documentation_status_submission').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');

            $.ajax({
                url: url,
                method: method,
                data: form.serialize(),
                success: function(response) {
                    toastr.success(response.message);
                    location.reload();
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    if (typeof errors === 'object') {
                        for (var key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                var errorMessages = errors[key];
                                if (Array.isArray(errorMessages) && errorMessages.length > 0) {
                                    for (var i = 0; i < errorMessages.length; i++) {
                                        toastr.error(errorMessages[i]);
                                    }
                                } else {
                                    toastr.error(errorMessages);
                                }
                            }
                        }
                    } else {
                        toastr.error(response.responseJSON.errors);
                    }
                }
            });
        });

        $('#end_stage_status_submission').submit(function(e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');

            $.ajax({
                url: url,
                method: method,
                data: form.serialize(),
                success: function(response) {
                    toastr.success(response.message);
                    location.reload();
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    if (typeof errors === 'object') {
                        for (var key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                var errorMessages = errors[key];
                                if (Array.isArray(errorMessages) && errorMessages.length > 0) {
                                    for (var i = 0; i < errorMessages.length; i++) {
                                        toastr.error(errorMessages[i]);
                                    }
                                } else {
                                    toastr.error(errorMessages);
                                }
                            }
                        }
                    } else {
                        toastr.error(response.responseJSON.errors);
                    }
                }
            });
        });

        $('#closed_status_submission').submit(function(e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');

            $.ajax({
                url: url,
                method: method,
                data: form.serialize(),
                success: function(response) {
                    toastr.success(response.message);
                    location.reload();
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    if (typeof errors === 'object') {
                        for (var key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                var errorMessages = errors[key];
                                if (Array.isArray(errorMessages) && errorMessages.length > 0) {
                                    for (var i = 0; i < errorMessages.length; i++) {
                                        toastr.error(errorMessages[i]);
                                    }
                                } else {
                                    toastr.error(errorMessages);
                                }
                            }
                        }
                    } else {
                        toastr.error(response.responseJSON.errors);
                    }
                }
            });
        });
    </script>
@endsection
