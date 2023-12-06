@extends('admin.layout.app')
@section('title', 'Create Role')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:roles') }}">Roles</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Role</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Add Role</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin:role.store') }}">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="name">Role Name</label>
                                    <input class="form-control form-control-sm" id="name" name="name" type="text"
                                        value="{{ old('name') }}" />
                                    @error('name')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">Users</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" name="users" type="checkbox" role="switch"
                                            id="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="name">Roles</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="roles" role="switch"
                                            id="">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">WareHouse</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="warehouses" role="switch"
                                            id="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="name">Customers</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="customers" role="switch"
                                            id="">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">Suppliers</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="suppliers" role="switch"
                                            id="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="name">CGT Grade</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="cgt_gardes" role="switch"
                                            id="">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">NT Grade</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="nt_grades" role="switch"
                                            id="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="name">Colors</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="colors" role="switch"
                                            id="">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">Product Type</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="product_types"
                                            role="switch" id="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="name">Scan In</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="scan_in" role="switch"
                                            id="">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">Scan Out</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="scan_out" role="switch"
                                            id="">
                                    </div>
                                </div>
                                {{-- <div class="col-sm-2">
                                    <label for="name">Inventory</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="inventory" role="switch"
                                            id="">
                                    </div>
                                </div> --}}
                                <div class="col-sm-2 hide">
                                    <label for="name">Reports</label>
                                </div>
                                <div class="col-sm-4 hide">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input reports-switch" type="checkbox" name="reports"
                                            role="switch" id="reports-switch" checked>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">Orders</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="orders" role="switch"
                                            id="">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="mb-3 mt-3">
                                <div class="header-title">
                                    <h5 class="card-title">Permission for NT Grade Display in the System</h5>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-2">
                                        <label for="name">Permission</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" name="nt_grade_column" type="checkbox"
                                                role="switch" id="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br>
                            <div class="mb-3 mt-3">
                                <div class="header-title">
                                    <h5 class="card-title">Permission for NT Prices Display in the System</h5>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-2">
                                        <label for="name">Permission</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" name="nt_price_column" type="checkbox"
                                                role="switch" id="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br>

                            <div class="mb-3 mt-3">
                                <div class="header-title">
                                    <h5 class="card-title">Permission for Third Party Prices Display in the System</h5>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-2">
                                        <label for="name">Permission</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" name="third_party_price_column"
                                                type="checkbox" role="switch" id="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br>
                            <div class="individual-report-permission mb-3 mt-3">
                                <div class="header-title">
                                    <h4 class="card-title">Select Permission for each Report </h4>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <label for="name">Inventory Report</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" name="inventory_report" type="checkbox"
                                                role="switch" id="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="name">CGT Summary</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="cgt_summary"
                                                role="switch" id="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <label for="name">NT Summary</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" name="nt_summary" type="checkbox"
                                                role="switch" id="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="name">Color Summary</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="color_summary"
                                                role="switch" id="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <label for="name">Cumulative CGT Grade</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" name="commulative_cgt" type="checkbox"
                                                role="switch" id="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="name">Cumulative NT Grade</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="commulative_nt"
                                                role="switch" id="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <label for="name">Customer Summary</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" name="customer_summary" type="checkbox"
                                                role="switch" id="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="name">NEXPAC Report</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="nexpac_report"
                                                role="switch" id="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <label for="name">Internal Report</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" name="internal_report" type="checkbox"
                                                role="switch" id="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="name">Billing Report</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="billing_report"
                                                role="switch" id="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3">
                                    <label for="name">PNL Report</label>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" name="pnl_report" type="checkbox" role="switch"
                                            id="">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 d-flex justify-content-start">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.reports-switch').change(function() {
                if ($('input[name="reports"]').is(':checked')) {
                    $('.individual-report-permission').removeClass('hide');
                } else {
                    $('.individual-report-permission').addClass('hide');
                }
            });
        });
    </script>
@endsection
