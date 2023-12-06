@extends('admin.layout.app')
@section('title', 'Request Samples')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sample Requests</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Sample Requests</h4>
                        </div>
                        <a href="{{ route('admin:sampleRequests.create') }}" class="btn btn-sm btn-primary">Add Sample</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="sample-list" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer Name</th>
                                        <th>Type</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Product</th>
                                        <th>Status</th>

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
    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm Delete
                    </h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this Sample?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger delete_sample" id="model_id">Delete Sample</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')


    <script type="text/javascript">
        var table = $('#sample-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin:sampleRequests') }}",
            columns: [
                // data = array index & name = database column name
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'get_customer.name',
                    name: 'get_customer.name'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'get_customer.email',
                    name: 'get_customer.email'
                },
                {
                    data: 'get_customer.contact',
                    name: 'get_customer.contact'
                },
                {
                    data: 'get_customer.address',
                    name: 'get_customer.address'
                },
                {
                    data: 'get_customer.product',
                    name: 'get_customer.product'
                },
                {
                    data: 'status',
                    name: 'status'
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
            $('#delete').modal('show');
        });

        $(document).on('click', '.delete_sample', function() {
            var id = $(this).val();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:sampleRequests.destroy') }}",
                data: {
                    'sample_id': id,
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
