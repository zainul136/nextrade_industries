@extends('admin.layout.app')
@section('title', 'Roles')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Roles</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Roles</h4>
                        </div>
                        <a href="{{ route('admin:role.create') }}" class="btn btn-sm btn-primary">Add Role</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="role-list" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Role Name</th>
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
    <div class="modal fade" id="exampleModalDefault" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header m-3">
                    <h5 class="modal-title" id="exampleModalLabel">Role Permission Details
                    </h5>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
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
                    Are you sure you want to delete this Role?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger delete_role" id="model_id">Delete Role</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')


    <script type="text/javascript">
        var table = $('#role-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin:roles') }}",
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
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $(document).on('click', '.show_permission_details', function() {
            var role_id = $(this).attr('data-id');
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:role.show') }}",
                data: {
                    'role_id': role_id,
                    '_token': "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status == true) {
                        $('#exampleModalDefault').modal('show');
                        $("#exampleModalDefault .modal-body").html(response.data);
                    }
                }
            });
        });

        $(document).on('click', '.delete', function() {
            var id = $(this).attr('data-id');
            $('#model_id').val(id);
            $('#delete').modal('show');
        });

        $(document).on('click', '.delete_role', function() {
            var id = $(this).val();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:role.destroy') }}",
                data: {
                    'role_id': id,
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
