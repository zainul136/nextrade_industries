<div class="row m-1">
    <div class="col-md-4">
        <h6>Users</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->users) && $role_has_permission->users == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>Roles</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->roles) && $role_has_permission->roles == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>Warehouses</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->warehouses) && $role_has_permission->warehouses == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>Customers</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->customers) && $role_has_permission->customers == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>suppliers</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->suppliers) && $role_has_permission->suppliers == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>CGT Grade</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->cgt_gardes) && $role_has_permission->cgt_gardes == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>NT Grade</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->nt_grades) && $role_has_permission->nt_grades == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>Colors</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->colors) && $role_has_permission->colors == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>Scan In</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->scan_in) && $role_has_permission->scan_in == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>Scan Out</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->scan_out) && $role_has_permission->scan_out == 1 ? 'Yes' : 'No' }}</p>
    </div>
    {{-- <div class="col-md-4">
        <h6>Inventory</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->inventory) && $role_has_permission->inventory == 1 ? 'Yes' : 'No' }}</p>
    </div> --}}
    <div class="col-md-4">
        <h6>Orders</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->orders) && $role_has_permission->orders == 1 ? 'Yes' : 'No' }}</p>
    </div>
    {{-- <div class="col-md-4">
        <h6>Reports</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->reports) && $role_has_permission->reports == 1 ? 'Yes' : 'No' }}</p>
    </div> --}}
    <div class="col-md-4">
        <h6>Product Types</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->product_types) && $role_has_permission->product_types == 1 ? 'Yes' : 'No' }}
        </p>
    </div>
</div>
<hr />
<div class="row m-1">

    <div class="col-sm-12 mb-4">

        <h5>Permission for NT Grade Display in the System</h5>
    </div>

    <div class="col-md-4">
        <h6>Permission</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->id) && $role_has_permission->nt_grade_column == 1 ? 'Yes' : 'No' }}</p>
    </div>

</div>
<hr />
<div class="row m-1">

    <div class="col-sm-12 mb-4">

        <h5 class="card-title">Permission for NT Prices Display in the System</h5>
    </div>

    <div class="col-md-4">
        <h6>Permission</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->id) && $role_has_permission->nt_price_column == 1 ? 'Yes' : 'No' }}</p>
    </div>

</div>
<hr />
<div class="row m-1">

    <div class="col-sm-12 mb-4">

        <h5 class="card-title">Permission for Third Party Prices Display in the System</h5>
    </div>

    <div class="col-md-4">
        <h6>Permission</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_has_permission->id) && $role_has_permission->third_party_price_column == 1 ? 'Yes' : 'No' }}
        </p>
    </div>

</div>
<hr />
<div class="row m-1">

    <div class="col-sm-12 mb-4">

        <h5>Select Permission for each Report</h5>
    </div>

    <div class="col-md-4">
        <h6>Inventory Report</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_report_permission->id) && $role_report_permission->inventory_report == 1 ? 'Yes' : 'No' }}
        </p>
    </div>
    <div class="col-md-4">
        <h6>CGT Summary</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_report_permission->id) && $role_report_permission->cgt_summary == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>NT Summary</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_report_permission->id) && $role_report_permission->nt_summary == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>Color Summary</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_report_permission->id) && $role_report_permission->color_summary == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>Cumulative CGT Grade</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_report_permission->id) && $role_report_permission->commulative_cgt == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>Cumulative NT Grade</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_report_permission->id) && $role_report_permission->commulative_nt == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>Customer Summary</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_report_permission->id) && $role_report_permission->customer_summary == 1 ? 'Yes' : 'No' }}
        </p>
    </div>
    <div class="col-md-4">
        <h6>NEXPAC Report</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_report_permission->id) && $role_report_permission->nexpac_report == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>Internal Report</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_report_permission->id) && $role_report_permission->internal_report == 1 ? 'Yes' : 'No' }}</p>
    </div>
    <div class="col-md-4">
        <h6>Billing Report</h6>
    </div>
    <div class="col-md-2">
        <p>{{ isset($role_report_permission->id) && $role_report_permission->billing_report == 1 ? 'Yes' : 'No' }}</p>
    </div>

</div>
