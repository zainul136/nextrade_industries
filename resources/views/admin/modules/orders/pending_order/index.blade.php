@extends('admin.layout.app')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pending Orders</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Pending Orders</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pending-orders-list" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Release #</th>
                                        <th>Customer</th>
                                        <th>Warehouse</th>
                                        <th>Tare Factor</th>
                                        <th>Seal</th>
                                        <th>Order Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-dark">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModalDefault" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm Delete
                    </h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this Order?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger delete_order" id="model_id">Delete
                        Order</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var table = $('#pending-orders-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin:pendingOrders') }}",
            columns: [
                // data = array index & name = database column name
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'release_number',
                    name: 'release_number',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'get_customers.name',
                    name: 'get_customers.name',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'get_ware_house.name',
                    name: 'get_ware_house.name',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'tear_factor',
                    name: 'tear_factor',
                    render: function(data, type, full, meta) {
                        return data ? data : '0';
                    }
                },
                {
                    data: 'seal',
                    name: 'seal',
                    render: function(data, type, full, meta) {
                        return data ? data : '0';
                    }
                },
                {
                    data: 'order_date',
                    name: 'created_at',
                },
                {
                    data: 'order_status',
                    name: 'status',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
        });

        $(document).on('click', '.delete', function() {
            var id = $(this).attr('data-id');
            $('#model_id').val(id);
            $('#exampleModalDefault').modal('show');
        });

        $(document).on('click', '.delete_order', function() {
            var scan_out_inventory_id = $(this).val();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:orders.destroy') }}",
                data: {
                    'scan_out_inventory_id': scan_out_inventory_id,
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
    </script>
@endsection
