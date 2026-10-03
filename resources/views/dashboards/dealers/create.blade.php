@extends('dashboards.sessions.layout')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Register New Dealer</h1>
            <!-- Sidebar Toggle Button -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">
                <span class="sr-only">Toggle navigation</span>
                <span class="toggle-icon">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </span>
            </button>
            <!-- Sidebar Toggle Button -->
        </div>

        <div class="page-content-container">
            <div class="page-content-row" id="replecable">
                <div class="page-sidebar">
                    <div class="portlet light portlet-fit">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-bar font-red"></i>
                                <span class="caption-subject font-red bold uppercase">Quick Navigation</span>
                            </div>

                        </div>
                        <nav class="navbar" role="navigation">
                            <ul class="nav navbar-nav margin-bottom-35">
                                <li class="active"><a href="/dealer/create">Add New Dealer </a></li>
                                <li><a href="/dealer">List Dealer </a></li>
                                <li><a href="/dealers-map">Dealers Map </a></li>
                            </ul>
                        </nav>
                    </div>

                </div>

                <div class="page-content-col">
                    @if (session('Status'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <b>{{ session('Status') }}</b>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            @if ($errors->any())
                                <div class="alert alert-danger" style="max-height: 300px; overflow-y: auto;">
                                    <h3>Errors</h5>
                                        @foreach ($errors->getMessages() as $rowKey => $messages)
                                            <strong>{{ $rowKey }}:</strong>
                                            <ul class="mb-2">
                                                @foreach ($messages as $message)
                                                    <li>{{ $message }}</li>
                                                @endforeach
                                            </ul>
                                        @endforeach
                                </div>
                            @endif
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Add New Dealer</span>
                                    </div>

                                </div>


                                <form method="POST" action="/dealer">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="code"
                                            class="col-md-3 col-form-label text-right">{{ __('Dealer Code') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ old('code') }}"
                                                name="code" placeholder="Dealer Code" required autofocus
                                                pattern="[A-Za-z0-9]+" title="Only letters and numbers allowed">
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label for="name"
                                            class="col-md-3 col-form-label text-right">{{ __('Dealer Name') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ old('name') }}"
                                                name="name" placeholder="Dealer Name" required autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="address"
                                            class="col-md-3 col-form-label text-right">{{ __('Address') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ old('address') }}"
                                                name="address" placeholder="Address" autofocus required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email"
                                            class="col-md-3 col-form-label text-right">{{ __('Email') }}</label>

                                        <div class="col-md-6">
                                            <input type="email" class="form-control" value="{{ old('email') }}"
                                                name="email" placeholder="Email Address" autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="mobile"
                                            class="col-md-3 col-form-label text-right">{{ __('Mobile') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ old('mobile') }}"
                                                name="mobile" placeholder="98XXXXXXXX" autofocus
                                                pattern="^(98|97)[0-9]{8}$"
                                                title="Enter a valid phone number starting with 97 or 98. Phone number must be exactly 10 digits"
                                                minlength="10" maxlength="10"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="phone"
                                            class="col-md-3 col-form-label text-right">{{ __('Phone') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ old('phone') }}"
                                                name="phone" placeholder="98XXXXXXXX" autofocus
                                                pattern="^(98|97)[0-9]{8}$"
                                                title="Enter a valid phone number starting with 97 or 98. Phone number must be exactly 10 digits"
                                                minlength="10" maxlength="10"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="order_limit"
                                            class="col-md-3 col-form-label text-right">{{ __('Order Limit') }}</label>

                                        <div class="col-md-6">
                                            <input type="number" class="form-control" value="{{ old('order_limit') }}"
                                                name="order_limit" placeholder="Order Limit" min="0"
                                                step="1" autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="area_code"
                                            class="col-md-3 col-form-label text-right">{{ __('Area') }}</label>
                                        <div class="col-md-6">
                                            <select name="area_code" class="form-control area select2" required>
                                                <option value="">Select Area</option>
                                                @foreach ($areas as $area)
                                                    <option value="{{ $area->areacode }}">{{ $area->area }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="subarea"
                                            class="col-md-3 col-form-label text-right">{{ __('Sub Area') }}</label>
                                        <div class="col-md-6">
                                            <select name="subarea" id="dropdown_subarea"
                                                class="form-control subarea select2" required>
                                                <option value="">Select Area First</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary" style="width: 200px;">
                                                {{ __('Create') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <br>
                                <p style="text-align: center;font-weight:600;font-size:20">OR</p><br>
                                <form action="/dealers/excel" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="excel_file"
                                            class="col-md-3 col-form-label text-right">{{ __('Upload File') }}</label>

                                        <div class="col-md-6">
                                            <input type="file" class="form-control" name="excel_file" required
                                                autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary" style="width: 200px;">
                                                {{ __('Upload') }}
                                            </button>
                                            <a href="{{ asset('sampleTemplate/dealer_template.xlsx') }}"
                                                class="btn btn-primary"><i class="fa fa-download"
                                                    style="padding-right: 5px;"></i>Sample</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.area').select2({
                placeholder: "Select Area",
                allowClear: true,
                width: '100%'
            });

            $('#dropdown_subarea').select2({
                placeholder: "Select Sub Area",
                allowClear: true,
                width: '100%'
            });

            $('.area').change(function(e) {
                var value = $(this).val();
                $.ajax({
                    url: "/getsubarea",
                    type: "get",
                    data: {
                        data: [value],
                    },
                    success: function(response) {
                        $('#dropdown_subarea').empty().append(
                            '<option value="">Select Sub Area</option>'
                        );
                        response.map(function(data) {
                            $('#dropdown_subarea').append(
                                '<option value="' + data.subarea + '">' + data
                                .subarea + '</option>');
                        });
                        $('#dropdown_subarea').select2('destroy').select2({
                            placeholder: "Select Sub Area",
                            allowClear: true,
                            width: '100%'
                        });
                    }
                });
            });
        });
    </script>
@endsection
