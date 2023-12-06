@extends('admin.layout.app')
@section('title', 'CGT Grades')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">CGT Grades</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between flex-wrap">
                        <div class="header-title">

                            <h4 class="card-title">CGT Grades</h4>
                        </div>
                        <a href="{{ route('admin:cgt_grades.create') }}" class="btn btn-sm btn-primary">Add CGT Grades</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="cgt-list" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Grade Name</th>
                                        <th>Slug</th>
                                        <th>Price</th>
                                        <th>Billing Code</th>
                                        {{-- <th>PNL</th> --}}
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
                    Are you sure you want to delete this CGT Grade?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger delete_cgt" id="model_id">Delete CGT Grade</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')


    <script type="text/javascript">
        var table = $('#cgt-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin:cgt_grades') }}",
            columns: [
                // data = array index & name = database column name
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'grade_name',
                    name: 'grade_name'
                },
                {
                    data: 'slug',
                    name: 'slug'
                },
                {
                    data: 'price',
                    name: 'price',
                    render: function(data, type, full, meta) {
                        return data ? data : '0';
                    }
                },
                {
                    data: 'billing_code',
                    name: 'billing_code',
                    render: function(data, type, full, meta) {
                        return data ? data : '-';
                    }
                },
                // {
                //     data: 'pnl',
                //     name: 'pnl',
                //     render: function(data, type, full, meta) {
                //         return data ? data : '-';
                //     }
                // },
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

        $(document).on('click', '.delete_cgt', function() {
            var id = $(this).val();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:cgt_grades.destroy') }}",
                data: {
                    'cgt_id': id,
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
