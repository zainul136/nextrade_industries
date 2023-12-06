@extends('admin.layout.app')
@section('title', 'Warehouses')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Warehouses</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Warehouses</h4>
                        </div>
                        <a href="{{ route('admin:warehouses.create') }}" class="btn btn-sm btn-primary">Add Warehouse</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="warehouses-list" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Warehouses</th>
                                        <th>Contact</th>
                                        <th>Location</th>
                                        <th class="dnr">Action</th>

                                    </tr>
                                </thead>
                                <tbody></tbody>
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
                    Are you sure you want to delete this Warehouse?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger delete_warehouse" id="model_id">Delete
                        Warehouse</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')


    <script type="text/javascript">
        var table = $('#warehouses-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin:warehouses') }}",
            // "language": {
            //     search: '<i class="fa fa-search" aria-hidden="true"></i>',
            //     searchPlaceholder: 'filter records',
            //     oPaginate: {
            //         sNext: '<span aria-hidden="true">&raquo;</span>',
            //         sPrevious: '<span aria-hidden="true">&laquo;</spanW>'
            //     }
            // },
            columns: [
                // data = array index & name = database column name
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'contact',
                    name: 'contact'
                },
                {
                    data: 'location',
                    name: 'location'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });


        $(document).on('click', '.delete', function() {
            var id = $(this).attr('data-id');
            $('#model_id').val(id);
            $('#exampleModalDefault').modal('show');
        });

        $(document).on('click', '.delete_warehouse', function() {
            var id = $(this).val();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:warehouses.destroy') }}",
                data: {
                    'warehouse_id': id,
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
