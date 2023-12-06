@extends('admin.layout.app')
@section('title', 'Customer Samples')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:customers') }}">Customers</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Customer Samples</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Customer Samples</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="d-flex justify-content-between mt-3 mb-3">
                                <div class="header-title">
                                    <h5 class="card-title">
                                        Customer Basic Information
                                    </h5>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="Full Name">Full Name : <span class="text-dark">
                                        {{ $customer->name ?? '' }}</span></label>
                            </div>
                            <div class="col-md-6">
                                <label for="Contact">Contact : <span class="text-dark">
                                        {{ $customer->contact ?? '' }}</span></label>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label for="Email">Email : <span class="text-dark">
                                        {{ $customer->email ?? '' }}</span></label>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label for="Country">Country : <span class="text-dark">
                                        {{ $customer->country ?? '' }}</span></label>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label for="Product">Product : <span class="text-dark">
                                        {{ $customer->product ?? '' }}</span></label>
                            </div>
                            <div class="col-md-6 mt-4">
                                <label for="Address">Address : <span class="text-dark">
                                        {{ $customer->address ?? '' }}</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="d-flex justify-content-between mt-3 mb-3">
                                <div class="header-title">
                                    <h5 class="card-title">
                                        Samples
                                    </h5>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="sample-list" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($samples)
                                            @foreach ($samples as $key => $sample)
                                                <tr>
                                                    <td>{{ $key + 1 ?? '-' }}</td>
                                                    <td>{{ $sample->type ?? '-' }}</td>
                                                    <td>{{ $sample->status ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')


    <script type="text/javascript">
        var table = $('#customer-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin:customers.orders') }}",
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
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'country',
                    name: 'country'
                },
                {
                    data: 'product',
                    name: 'product'
                },
                {
                    data: 'address',
                    name: 'address'
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

        $(document).on('click', '.delete_customer', function() {
            var id = $(this).val();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin:customers.destroy') }}",
                data: {
                    'customer_id': id,
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

        $(document).on('click', '.add_sample', function() {
            var customer_id = $(this).attr('customer-id');
            $('.customer_id').val(customer_id);
            $('#add_new_sample').modal('show');
        });

        $('#submit_sample').on('submit', function(event) {
            event.preventDefault();

            var form_data = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: form_data,
                success: function(response) {
                    if (response.status) {
                        // success message
                        $('#add_new_sample').modal('hide');
                        toastr.success('Sample created successfully!');
                        location.reload();
                    } else {
                        // show error messages using toastr
                        $.each(response.errors, function(key, value) {
                            toastr.error(value);
                        });
                    }
                }
            });
        });
    </script>

@endsection
