@extends('admin.layout.app')
@section('title', 'Customers')
@section('content')
    <style>
        .modal {
            z-index: 1051;
        }

        .select2-container {
            width: 100% !important;
        }
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .popup-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }
    </style>
    <div class="m-4 p-3">
        <div class="row">
            <div class="col-sm-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin:dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Customers</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">

                            <h4 class="card-title">Customers</h4>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button id="email-button"  class="btn btn-sm btn-info">Send Email</button>
                                <a href="{{ route('admin:customers.create') }}" class="btn btn-sm btn-primary">Add Customer</a>

                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="customer-list" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Country</th>
                                    <th>Product</th>
                                    <th>Address</th>
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

    <div id="email-popup" class="popup">
        <div id="email-content" class="popup-content">
            <span class="close" id="close-email-popup">&times;</span>
            <textarea id="editor"></textarea>
            <p id="selected-emails"></p>
            <button type="submit" class="btn btn-primary" id="submit-email-button">Submit</button> <!-- Add the "Submit" button -->
        </div>
    </div>

    <div class="modal fade" id="email-popup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm Delete
                    </h5>
                </div>
                <div class="modal-body">
                    <p id="selected-emails"></p>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger delete_customer" id="model_id">Delete
                        Customer</button>
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
                    Are you sure you want to delete this Customer?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger delete_customer" id="model_id">Delete
                        Customer</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_new_sample" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="submit_sample" action="{{ route('admin:customer.addSample') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Sample
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <input type="hidden" class="customer_id" name="customer_id" value="">
                            <div class="col-md-6">
                                <label for="type">Type</label>
                                <input type="text" class="form-control form-control-sm" id="type" name="type"
                                       value="{{ old('type') }}" required />
                                @error('type')
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email">Status</label>
                                <br>
                                <select class="form-control form-control-sm selected_customer select2 w-100" name="status"
                                        required>
                                    <option value="requested" selected>Requested</option>
                                    <option value="paid">Paid</option>
                                    <option value="approved">Approved</option>
                                    <option value="shipped">Shipped</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger" id="model_id"> Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('script')


    <script type="text/javascript">
        var selectedEmails = [];

        var table = $('#customer-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin:customers') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full, meta) {
                        return '<input type="checkbox" class="customer-checkbox" value="' + full.email + '">';
                    }
                },
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: true,
                    searchable: true,
                    render: function (data, type, full, meta) {
                        return meta.row + 1; // Display S.No starting from 1
                    }
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

        // Add the "Select All" checkbox in the header row
        $('#customer-list thead th:first').html('<input type="checkbox" id="select-all-checkbox">');

        // Handle the "Select All" functionality
        $('#select-all-checkbox').change(function () {
            var checked = this.checked;
            $('.customer-checkbox').prop('checked', checked);
            updateSelectedEmails();
        });

        // Handle individual checkbox selection
        $('#customer-list tbody').on('change', '.customer-checkbox', function () {
            updateSelectedEmails();
        });

        // Show the email popup when the "Email" button is clicked
        $('#email-button').click(function () {
            showEmailPopup();
        });

        // Close the email popup when the close button is clicked
        $('#close-email-popup').click(function () {
            closeEmailPopup();
        });

        function updateSelectedEmails() {
            selectedEmails = [];
            $('.customer-checkbox:checked').each(function () {
                selectedEmails.push($(this).val());
            });
        }

        function showEmailPopup() {
            $('#selected-emails').text(selectedEmails.join(', '));
            $('#email-popup').show();
        }

        function closeEmailPopup() {
            $('#email-popup').hide();
        }

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

        function getSelectedEmails() {
            var selectedEmails = [];
            // Implement your logic to gather selected email addresses
            // For example, if your checkboxes have a class "customer-checkbox":
            $('.customer-checkbox:checked').each(function () {
                selectedEmails.push($(this).val());
            });
            return selectedEmails;
        }

        // Show the email popup when the "Email" button is clicked
        $('#email-button').click(function () {
            var selectedEmails = getSelectedEmails();
            $('#selected-emails').text(selectedEmails.join(', '));
            $('#email-popup').show();
        });

        // Close the email popup when the close button is clicked
        $('#close-email-popup').click(function () {
            $('#email-popup').hide();
        });

        // Handle the "Submit" button click within the email popup
        $('#submit-email-button').click(function () {
            sendEmail();
        });
    </script>
    // Define the sendEmail function
    <script src="https://cdn.tiny.cloud/1/rno6huxe617bayvqwsis71413s26cqx8exjt7t07w46rcmr6/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- JavaScript code -->
   <script>
       $(document).ready(function () {
           let selectedImage = null;
           let selectedPdf = null;

           // Initialize TinyMCE editor
           tinymce.init({
               selector: 'textarea#editor',
               skin: 'bootstrap',
               plugins: 'lists link image media',
               toolbar: 'h1 h2 bold italic strikethrough filepicker blockquote bullist numlist backcolor | link image media | removeformat help',
               menubar: false,

               file_picker_types: 'image',
               file_picker_callback: customImagePicker,
           });

           // Define the custom image picker dialog
           function customImagePicker(callback) {
               const input = document.createElement('input');
               input.type = 'file';
               input.accept = 'image/*';
               input.accept = 'pdf/*';

               input.addEventListener('change', function () {
                   const file = input.files[0];
                   if (file) {
                       selectedImage = file;
                       const imageUrl = URL.createObjectURL(file);
                       callback(imageUrl, { alt: '' });
                   }
               });

               input.click();
           }

           // Define the custom PDF picker dialog
           function customPdfPicker(callback) {
               const input = document.createElement('input');
               input.type = 'file';
               input.accept = 'application/pdf';
               input.addEventListener('change', function () {
                   const file = input.files[0];
                   if (file) {
                       selectedPdf = file;
                       callback(file);
                   }
               });

               input.click();
           }

           // Define the getSelectedEmails function
           function getSelectedEmails() {
               const selectedEmails = $('.customer-checkbox:checked').map(function () {
                   return $(this).val();
               }).get();
               return selectedEmails;
           }

           // Define the sendEmail function
           function sendEmail() {
               const selectedEmails = getSelectedEmails();
               const emailContent = tinymce.activeEditor.getContent();

               const formData = new FormData();
               formData.append('emails', selectedEmails);
               formData.append('emailContent', emailContent);
               formData.append('image', selectedImage);
               formData.append('pdf', selectedPdf);

               $.ajax({
                   type: 'POST',
                   url: "{{ route('admin:customers.send-email') }}",
                   data: formData,
                   headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                   contentType: false,
                   processData: false,
                   success: function (result) {
                       toastr[result.status ? 'success' : 'error'](result.message);
                       $('#email-popup').hide();
                   },
                   error: function (xhr, status, error) {
                       toastr.error(xhr.responseJSON.message);
                   },
               });
           }

           // Show the email popup when the "Email" button is clicked
           $('#email-button').click(function () {
               const selectedEmails = getSelectedEmails();
               $('#selected-emails').text(selectedEmails.join(', '));
               $('#email-popup').show();
           });

           // Close the email popup when the close button is clicked
           $('#close-email-popup').click(function () {
               $('#email-popup').hide();
           });

           // Handle the "Submit" button click within the email popup
           $('#submit-email-button').click(function () {
               sendEmail();
           });
       });

   </script>

@endsection
