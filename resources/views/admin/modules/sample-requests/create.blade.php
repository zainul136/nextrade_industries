@extends('admin.layout.app')
@section('title', 'Create Sample Request')
@section('content')
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin:sampleRequests') }}">Sample Requests</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Sample Request</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Add Sample Request</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin:sampleRequests.store') }}" method="POST">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="customerName">Customer Name</label>
                                    <select class="form-control form-control-sm selected_customer select2"
                                        name="customer_id">
                                        <option value="">Select</option>
                                        @if (isset($customers) & !empty($customers))
                                            @foreach ($customers as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->name ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('customer_id')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-sm btn-primary add_customer mt-4">Add New
                                        Customer</button>
                                </div>
                            </div>

                            <div class="form-group row mt-4">
                                <div class="col-md-6">
                                    <label for="type">Type</label>
                                    <input type="text" class="form-control form-control-sm" id="type" name="type"
                                        value="{{ old('type') }}" />
                                    @error('type')
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email">Status</label>
                                    <select class="form-control form-control-sm selected_customer select2" name="status">
                                        <option value="requested" selected>Requested</option>
                                        <option value="paid">Paid</option>
                                        <option value="approved">Approved</option>
                                        <option value="shipped">Shipped</option>
                                    </select>
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

    <div class="modal fade" id="add_customer_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="add_customer_form" action="{{ route('admin:sampleRequests.addCustomer') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add New Customer
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">

                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="customerName">Customer Name</label>
                                            <input class="form-control form-control-sm" id="customerName" name="name"
                                                type="text" value="{{ old('name') }}" required />
                                            @error('name')
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="contact">Contact</label>
                                            <input type="text" class="form-control form-control-sm" id="contact"
                                                name="contact" value="{{ old('contact') }}" required />
                                            @error('contact')
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mt-4">

                                        <div class="col-md-6">
                                            <label for="email">Email</label>
                                            <input class="form-control form-control-sm" id="email" name="email"
                                                type="email" value="{{ old('email') }}" required />
                                            @error('email')
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="country">Country</label>
                                            <input class="form-control form-control-sm" id="country" name="country"
                                                type="text" value="{{ old('country') }}" required />
                                            @error('country')
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mt-4">
                                        <div class="col-md-6">
                                            <label for="product">Product</label>
                                            <input class="form-control form-control-sm" id="product" name="product"
                                                type="text" value="{{ old('product') }}" required />
                                            @error('product')
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="address">Address</label>
                                            <input class="form-control form-control-sm" id="address" name="address"
                                                type="text" value="{{ old('address') }}" required>
                                            @error('address')
                                                <span class="invalid-feedback" style="display: block;" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger delete_sample" id="model_id">Add
                            Customer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@section('script')
    <script type="text/javascript">
        $(document).on('click', '.add_customer', function() {
            $('#add_customer_modal').modal('show');
        });

        $('#add_customer_form').on('submit', function(event) {
            event.preventDefault();

            var form_data = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: form_data,
                success: function(response) {
                    if (response.status) {
                        // success message
                        $('#add_customer_modal').modal('hide');
                        var new_customer_id = response.data.id;
                        var new_customer_name = response.data.name;
                        $('select[name="customer_id"]').append('<option value="' + new_customer_id +
                            '">' +
                            new_customer_name + '</option>');
                        $('select[name="customer_id"]').val(new_customer_id);
                        toastr.success('Customer created successfully!');
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
