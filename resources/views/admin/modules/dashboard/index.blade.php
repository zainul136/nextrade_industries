@extends('admin.layout.app')
@section('title', 'Dashboard')
@section('content')
    <style>
        .weight-section, .yard-section {
            display: none;
        }
    </style>
    <div class="container-fluid content-inner mt-n5 py-0 mt-5">
        <div class="row">
            <div class="col-md-12 col-lg-12 text-center">
                <div class="row">
                    <div class="col-md-8 col-lg-8">
                    </div>
                    <div class="col-md-4 col-lg-4 mt-5">
                        <label class="mb-2 text-dark" for="year">Year</label>
                        <select class="form-control form-control-sm year select2" name="dashboard_year">
                            <option value="all"> All</option>
                            <option value="current" {{ session('date') == 'current' ? 'selected' : '' }}> Current</option>
                            @foreach (getListOfYears() as $key => $year)
                                <option value="{{ $year }}" {{ session('date') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-12 text-center mt-4">
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="card" data-aos="fade-up" data-aos-delay="500">
                            <div class="card-body">
                                <div>
                                    <h2 class="mb-2">{{ $data['cgt'] ?? 0 }}</h2>
                                    <p class="mb-0 text-secondary">Grand Total Of Weight in LBS</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="card" data-aos="fade-up" data-aos-delay="500">
                            <div class="card-body">
                                <div>
                                    <h2 class="mb-2">{{ $data['yards'] ?? 0 }}</h2>
                                    <p class="mb-0 text-secondary">Grand Total Of Yards in LBS</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-12">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="card" data-aos="fade-up" data-aos-delay="500">
                            <div class="text-center card-body">
                                <div class="row">
                                    <div class="col-md-3 col-sm-12">
                                        <h2 class="mb-2">{{ $data['users'] ?? 0 }}</h2>
                                        <p class="mb-0 text-secondary">Users</p>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <h2 class="mb-2">{{ $data['customers'] ?? 0 }}</h2>
                                        <p class="mb-0 text-secondary">Customers</p>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <h2 class="mb-2">{{ $data['suppliers'] ?? 0 }}</h2>
                                        <p class="mb-0 text-secondary">Suppliers</p>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <h2 class="mb-2">{{ $data['warehouses'] ?? 0 }}</h2>
                                        <p class="mb-0 text-secondary">Warehouses</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">CGT Weight</h4>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filter --}}
                    <form class="mb-5" id="filter" method="post" action="#">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="mb-2" for="Warehouses">Warehouses</label>
                                <select class="form-control form-control-sm warehouses select2" name="warehouse_id">
                                    <option value=""> All</option>
                                    @if (isset($warehouses) & !empty($warehouses))
                                        @foreach ($warehouses as $key => $value)
                                            <option value="{{ $value->id }}">
                                                {{ $value->name ?? '' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="mb-2" for="Year">Year</label>
                                <select class="form-control form-control-sm year select2" name="year">
                                    <option value=""> All Time</option>
                                    <option value="current" selected> Current</option>
                                    @foreach (getListOfYears() as $key => $year)
                                        <option value="{{ $year }}"> {{ $year }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="mb-2" for="From">From</label>
                                <input type="Date" name="from_date" class="form-control form-control-sm from mr-20" value="id=&quot;datepicker&quot;">
                            </div>
                            <div class="col-md-3">
                                <label class="mb-2" for="To">To</label>
                                <input type="Date" name="to_date" class="form-control form-control-sm to" value="id=&quot;datepicker&quot;">
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="mb-2" for="type">Type</label><br/>
                                <input type="radio" name="inv_types" value="weights" id="type_weights" checked>
                                <label for="type_weights">Weight</label>
                                <input type="radio" name="inv_types" value="yards" id="type_yards">
                                <label for="type_yards">Yards</label>
                            </div>
                        </div>
                    </form>
                    <div class="flex-wrap d-flex align-items-center justify-content-between">
                        <div class="col-lg-12 col-md-12">
                            <div class="row">
                                @if (isset($cgt_inventory) && !empty($cgt_inventory))
                                    @foreach ($cgt_inventory as $key => $value)
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <div>
                                                                <span class="badge bg-primary">CGT Weight</span>
                                                            </div>
                                                            <div class="mt-2">
                                                                <h3 class="counter">
                                                                    {{ $key ?? 'N/A' }}
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-2">
                                                        <div><span>Pallet Count</span></div>
                                                        <div><span>0</span></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-2">
                                                        <div><span>Rolls</span></div>
                                                        <div><span>0</span></div>
                                                    </div>
                                                    <div class="weight-section justify-content-between  d-flex">
                                                        <div><span>Weight</span></div>
                                                        <div><span class="counter">{{ $value->weight ?? '0' }}</span></div>
                                                    </div>

                                                    <div class="yard-section justify-content-between d-flex">
                                                        <div><span>Yards</span></div>
                                                        <div><span class="counter">{{ $value->yards ?? '0' }}</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-12 col-lg-12">
            <div class="row mb-3">
                <div class="col-sm-12 text-center">
                    <h5>Product Type Weight</h5>
                </div>
            </div>
            <div class="row row-cols-1">
                <div class="overflow-hidden d-slider1">
                    <ul class="p-0 m-0 mb-2 swiper-wrapper list-inline">
                        @if (isset($product_type_inventory) && !empty($product_type_inventory))
                            @foreach ($product_type_inventory as $key => $value)
                                <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="700">
                                    <div class="card-body text-center">
                                        {{-- <div class="progress-widget">
                                            <div id="circle-progress-02"
                                                class="text-center circle-progress-01 circle-progress circle-progress-primary"
                                                data-min-value="0" data-max-value="100" data-value="90"
                                                data-type="percent">
                                                <svg class="card-slie-arrow " width="24" height="24px"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z" />
                                                </svg>
                                            </div> --}}
                                        <div class="progress-detail">
                                            <p class="mb-2">{{ $key ?? 'N/A' }}</p>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h4 class="counter">{{ $value->weight ?? '0' }}</h4>
                                                    <p class="mb-3">Weight</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h4 class="counter">{{ $value->yards ?? '0' }}</h4>
                                                    <p class="mb-3">Yards</p>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- </div> --}}
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                    <div class="swiper-button swiper-button-next"></div>
                    <div class="swiper-button swiper-button-prev"></div>
                </div>
            </div>
        </div>
        @if (auth()->check() && session('RoleHasPermission') !== null && session('RoleHasPermission')->nt_grade_column == 1)
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">NT Grades</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Filter --}}
                        <form class="mb-5" id="filter" method="post" action="#">
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="mb-2" for="Warehouses">Warehouses</label>
                                    <select class="form-control form-control-sm warehouses select2"
                                            name="warehouse_id">
                                        <option value=""> All</option>
                                        @if (isset($warehouses) & !empty($warehouses))
                                            @foreach ($warehouses as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="mb-2" for="Year">Year</label>
                                    <select class="form-control form-control-sm year select2" name="year">
                                        <option value=""> All Time</option>
                                        <option value="current" selected> Current</option>
                                        @foreach (getListOfYears() as $key => $year)
                                            <option value="{{ $year }}"> {{ $year }} </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-3">
                                    <label class="mb-2" for="From">From</label>
                                    <input type="Date" name="from_date"
                                           class="form-control form-control-sm from mr-20"
                                           value="id=&quot;datepicker&quot;">
                                </div>
                                <div class="col-md-3">
                                    <label class="mb-2" for="To">To</label>
                                    <input type="Date" name="to_date" class="form-control form-control-sm to"
                                           value="id=&quot;datepicker&quot;">
                                </div>

                                <div class="col-md-3 mt-2">

                                    <label class="mb-2" for="type">Type</label><br/>
                                    <input type="radio" name="inv_type" value="weight" id="type_weight" checked>
                                    <label for="type_weight">Weight</label>

                                    <input type="radio" name="inv_type" value="yards" id="type_yard">
                                    <label for="type_yard">Yards</label>

                                </div>
                            </div>
                        </form>
                        <div class="flex-wrap d-flex align-items-center justify-content-between">
                            <div class="col-lg-12 col-md-12">
                                <div class="row" id="inventory_summary_cols_">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            $('input[name="inv_types"]').change(function () {
                var selectedValue = $(this).val();

                if (selectedValue === 'weights') {
                    $('.yard-section').addClass('d-none');
                    $('.weight-section').removeClass('d-none');
                } else if (selectedValue === 'yards') {
                    $('.weight-section').addClass('d-none');
                    $('.yard-section').removeClass('d-none');
                }
            });
        });
    </script>



    <script type="text/javascript">
        // get NT summary
        getNTInventorySummary()
        $('select[name=dashboard_year]').change(function () {
            getDashboardDataDateWise();
        });
        // check year is all or not
        $('select[name=year]').change(function () {
            let val = $(this, 'option:selected').val()

            if (val != '') {

                $('input[name=from_date],input[name=to_date]').val('').prop('disabled', true)

            } else {

                $('input[name=from_date],input[name=to_date]').val('').prop('disabled', false)

            }

        })

        // any field change
        $('select[name=year],select[name=warehouse_id],input[name=from_date],input[name=to_date],input[name=inv_type]')
            .change(function () {
                getNTInventorySummary()
            })

        function getNTInventorySummary() {

            $.ajax({
                type: 'POST',
                url: "{{ route('admin:dashboard.getNTInventoryByFilter') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    warehouse_id: $('select[name=warehouse_id] option:selected').val(),
                    year: $('select[name=year] option:selected').val(),
                    from_date: $('input[name=from_date]').val(),
                    to_date: $('input[name=to_date]').val(),
                    inv_type: $('input[name=inv_type]:checked').val()
                },
                success: function (response) {
                    console.log(response)

                    if (response.status === true) {

                        $('#inventory_summary_cols_').html(response.data)

                    }
                }
            });
        }

        function getDashboardDataDateWise() {
            var dashboard_year = $('select[name=dashboard_year]').val();
            window.location.href = "{{ route('admin:dashboard') }}/" + (dashboard_year || '');
        }
    </script>
@endsection
