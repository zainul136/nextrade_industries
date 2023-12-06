@extends('admin.layout.app')
@section('title', 'Edit Role')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:roles') }}">Roles</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Role</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Edit Role</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin:role.update', $role->id) }}">
                            @csrf @method('PUT')
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="name">Role Name</label>
                                    <input class="form-control form-control-sm" id="name" name="name" type="text"
                                        value="{{ old('name', $role->name ?? '') }}" />
                                    @error('name')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <input type="hidden" name="role_has_permission_id"
                                value={{ isset($role_has_permission->id) ? $role_has_permission->id : '' }}>
                            <input type="hidden" name="role_report_permission_id"
                                value={{ isset($role_report_permission->id) ? $role_report_permission->id : '' }}>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">Users</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" name="users"
                                            {{ isset($role_has_permission->id) && $role_has_permission->users == 1 ? 'checked' : '' }}
                                            type="checkbox" role="switch" id="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="name">Roles</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                            {{ isset($role_has_permission->id) && $role_has_permission->roles == 1 ? 'checked' : '' }}
                                            name="roles" role="switch" id="">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">WareHouse</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="warehouses"
                                            {{ isset($role_has_permission->id) && $role_has_permission->warehouses == 1 ? 'checked' : '' }}
                                            role="switch" id="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="name">Customers</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="customers"
                                            {{ isset($role_has_permission->id) && $role_has_permission->customers == 1 ? 'checked' : '' }}
                                            role="switch" id="">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">Suppliers</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="suppliers"
                                            {{ isset($role_has_permission->id) && $role_has_permission->suppliers == 1 ? 'checked' : '' }}
                                            role="switch" id="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="name">CGT Grade</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="cgt_gardes"
                                            {{ isset($role_has_permission->id) && $role_has_permission->cgt_gardes == 1 ? 'checked' : '' }}
                                            role="switch" id="">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">NT Grade</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="nt_grades"
                                            {{ isset($role_has_permission->id) && $role_has_permission->nt_grades == 1 ? 'checked' : '' }}
                                            role="switch" id="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="name">Colors</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="colors"
                                            {{ isset($role_has_permission->id) && $role_has_permission->colors == 1 ? 'checked' : '' }}
                                            role="switch" id="">
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
                                            {{ isset($role_has_permission->id) && $role_has_permission->product_types == 1 ? 'checked' : '' }}
                                            role="switch" id="">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="name">Scan In</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="scan_in"
                                            {{ isset($role_has_permission->id) && $role_has_permission->scan_in == 1 ? 'checked' : '' }}
                                            role="switch" id="">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">Scan Out</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="scan_out"
                                            {{ isset($role_has_permission->id) && $role_has_permission->scan_out == 1 ? 'checked' : '' }}
                                            role="switch" id="">
                                    </div>
                                </div>
                                {{-- <div class="col-sm-2">
                                    <label for="name">Inventory</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="inventory"
                                            {{ $role_has_permission->inventory == 1 ? 'checked' : '' }} role="switch"
                                            id="">
                                    </div>
                                </div> --}}
                                <div class="col-sm- hide">
                                    <label for="name">Reports</label>
                                </div>
                                <div class="col-sm-4 hide">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input reports-switch " type="checkbox" name="reports"
                                            {{ isset($role_has_permission->id) && $role_has_permission->reports == 1 ? 'checked' : '' }}
                                            role="switch" id="">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                    <label for="name">Orders</label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="orders"
                                            {{ isset($role_has_permission->id) && $role_has_permission->orders == 1 ? 'checked' : '' }}
                                            role="switch" id="">
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
                                            <input class="form-check-input" name="nt_grade_column"
                                                {{ isset($role_has_permission->id) && $role_has_permission->nt_grade_column == 1 ? 'checked' : '' }}
                                                type="checkbox" role="switch" id="">
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
                                                {{ isset($role_has_permission->id) && $role_has_permission->nt_price_column == 1 ? 'checked' : '' }}
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
                                                type="checkbox"
                                                {{ isset($role_has_permission->id) && $role_has_permission->third_party_price_column == 1 ? 'checked' : '' }}
                                                role="switch" id="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br>

                            <div class="individual-report-permission hide mb-3 mt-3">
                                <div class="header-title">
                                    <h4 class="card-title">Select Permission for each Report </h4>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <label for="name">Inventory Report</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="inventory_report"
                                                {{ isset($role_report_permission->id) && $role_report_permission->inventory_report == 1 ? 'checked' : '' }}
                                                role="switch" id="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="name">CGT Summary</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="cgt_summary"
                                                {{ isset($role_report_permission->id) && $role_report_permission->cgt_summary == 1 ? 'checked' : '' }}
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
                                            <input class="form-check-input" type="checkbox" name="nt_summary"
                                                {{ isset($role_report_permission->id) && $role_report_permission->nt_summary == 1 ? 'checked' : '' }}
                                                role="switch" id="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="name">Color Summary</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="color_summary"
                                                {{ isset($role_report_permission->id) && $role_report_permission->color_summary == 1 ? 'checked' : '' }}
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
                                            <input class="form-check-input" type="checkbox" name="commulative_cgt"
                                                {{ isset($role_report_permission->id) && $role_report_permission->commulative_cgt == 1 ? 'checked' : '' }}
                                                role="switch" id="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="name">Cumulative NT Grade</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="commulative_nt"
                                                {{ isset($role_report_permission->id) && $role_report_permission->commulative_nt == 1 ? 'checked' : '' }}
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
                                            <input class="form-check-input" type="checkbox" name="customer_summary"
                                                {{ isset($role_report_permission->id) && $role_report_permission->customer_summary == 1 ? 'checked' : '' }}
                                                role="switch" id="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="name">NEXPAC Report</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="nexpac_report"
                                                {{ isset($role_report_permission->id) && $role_report_permission->nexpac_report == 1 ? 'checked' : '' }}
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
                                            <input class="form-check-input" type="checkbox" name="internal_report"
                                                {{ isset($role_report_permission->id) && $role_report_permission->internal_report == 1 ? 'checked' : '' }}
                                                role="switch" id="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="name">Billing Report</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="billing_report"
                                                {{ isset($role_report_permission->id) && $role_report_permission->billing_report == 1 ? 'checked' : '' }}
                                                role="switch" id="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <label for="name">PNL Report</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" name="pnl_report" type="checkbox"
                                                {{ isset($role_report_permission->id) && $role_report_permission->pnl_report == 1 ? 'checked' : '' }}
                                                role="switch" id="">
                                        </div>
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
            if ($('input[name="reports"]').is(':checked')) {
                $('.individual-report-permission').removeClass('hide');
            } else {
                $('.individual-report-permission').addClass('hide');
            }
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
