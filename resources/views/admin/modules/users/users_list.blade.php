@extends('admin.layout.app')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Users</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Users</h4>
                        </div>
                        <a href="{{ route('admin:user.create') }}" class="btn btn-sm btn-primary">Add User</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="users-list" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>FUll Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Role</th>
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
    <div class="modal fade" id="exampleModalDefault" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm Delete
                    </h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger delete_user" id="model_id">Delete User</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var table = $('#users-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin:users') }}",
            columns: [
                // data = array index & name = database column name
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'full_name',
                    name: 'full_name'
                },
                {
                    data: 'email',
                    name: 'email',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'contact',
                    name: 'contact',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'address',
                    name: 'address',
                    render: function(data, type, full, meta) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: 'user_role.name',
                    name: 'user_role.name'
                },
                {
                    data: 'user_status',
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
            $('#exampleModalDefault').modal('show');
        });

        $(document).on('click', '.delete_user', function() {
            var id = $(this).val();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:user.destroy') }}",
                data: {
                    'user_id': id,
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
