@extends('admin.layout.app')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Scan In Summary</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Scan In Summary</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="inventory-list" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Reference #</th>
                                        <th>Supplier</th>
                                        <th>Warehouse</th>
                                        <th>nexpac Bill</th>
                                        <th>Scan In Date</th>
                                        <th>History</th>
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
                    Are you sure you want to delete this Scanned In Record?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger delete_record" id="model_id">Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        var table = $('#inventory-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin:scanInInventory') }}",
            columns: [
                // data = array index & name = database column name
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'reference_number',
                    name: 'reference_number',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'supplier.name',
                    name: 'supplier.name',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'warehouse.name',
                    name: 'warehouse.name',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'nexpac_bill',
                    name: 'nexpac_bill',
                    render: function(data, type, full, meta) {
                        return data ? data : '0';
                    }
                },
                {
                    data: 'scanin_date',
                    name: 'scanin_date',
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

        $(document).on('click', '.delete_record', function() {
            var scan_in_inventory_id = $(this).val();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:ScanInInventory.destroy') }}",
                data: {
                    'scan_in_inventory_id': scan_in_inventory_id,
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
