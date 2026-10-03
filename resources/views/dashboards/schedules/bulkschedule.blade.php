@extends('dashboards.sessions.layout')
@section('content')
    <style>
        .checkbox-item {
            height: 20px;
            width: 20px;
        }

        .span-dealer {
            margin-left: 20px;
        }

        .chkbox-dealer {
            max-height: 200px !important;
            overflow: auto;

        }

        .col-md-6 {
            padding-right: 0 !important;
        }

        .open {
            width: 100%;
        }
    </style>
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>ADD NEW VISIT SCHEDULE</h1>
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
                                <li class="active"><a href="/schedule/create">Add New visit Schedule</a></li>
                                <li><a href="/schedule">List visit schedule </a></li>
                                <li><a href="/repeated">Repeated Schedule</a></li>
                                <li><a href="/repeated/create">Add Repeated Schedule</a></li>
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
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Add New Visit Schedule</span>
                                    </div>
                                </div>
                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif
                                <form method="POST" action="/schedule">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="user_id"
                                            class="col-md-3 col-form-label text-right">{{ __('Marketing Officer') }}</label>
                                        <div class="col-md-6">
                                            <select name="user_id" class="form-control selectpicker" data-live-search="true"
                                                required>
                                                <option value="">Select Marketing Officer</option>
                                                @foreach ($mobiles as $mobile)
                                                    <option value="{{ $mobile->id }}">
                                                        {{ $mobile->id }}&nbsp;&nbsp;{{ $mobile->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <script>
                                        jQuery(document).ready(function() {
                                            const config = {
                                                dealersData: [],
                                                $dealerContainer: $('.chkbox-dealer'),
                                                $dealerClass: $('.dealer-class'),
                                                $searchInput: $('#select-dealer-search'),
                                                searchTimeout: null
                                            };

                                            // Handle marketing officer selection
                                            jQuery('select[name="user_id"]').on('change', function() {
                                                const userId = this.value;
                                                if (userId) {
                                                    jQuery.ajax({
                                                        url: "/schedule/dealer/" + userId,
                                                        type: "GET",
                                                        dataType: "json",
                                                        success: function(data) {
                                                            config.dealersData = data;
                                                            renderDealers(data);
                                                            config.$dealerClass.show();
                                                        },
                                                        error: function() {
                                                            alert('Error loading dealers');
                                                        }
                                                    });
                                                }
                                            });

                                            function renderDealers(dealers) {
                                                const html = dealers.map(dealer => `
                                                    <span class="dealer-item" data-dealer-id="${dealer.code}" data-dealer-name="${dealer.name.toLowerCase()}">
                                                        <label class="span-dealer">
                                                            <input type="checkbox" class="checkbox-item" value="${dealer.code}" name="dealer[]">
                                                            ${dealer.name}
                                                        </label>
                                                    </span> <br>`).join('');

                                                config.$dealerContainer.html(`
                                                    <input type="button" value="Check All" style="width:30%;height:20%" class="btn btn-primary check-all">
                                                    <input type="button" value="Uncheck All" style="width:30%;height:20%;margin-left:10px;" class="btn btn-primary uncheck-all"><br>
                                                    ${html}
                                                `);
                                                config.$searchInput.val('').focus();
                                            }

                                            // Debounced search filter for better performance
                                            config.$searchInput.on('keyup', function() {
                                                clearTimeout(config.searchTimeout);
                                                config.searchTimeout = setTimeout(() => {
                                                    filterDealers(this.value.toLowerCase());
                                                }, 200);
                                            });

                                            function filterDealers(searchTerm) {
                                                if (!config.dealersData.length) return;

                                                const $items = config.$dealerContainer.find('.dealer-item');
                                                let visibleCount = 0;

                                                $items.each(function() {
                                                    const dealerName = ($(this).data('dealer-name') || '').toString();
                                                    const isMatch = dealerName.includes(searchTerm);
                                                    $(this).toggle(isMatch);
                                                    $(this).next('br').toggle(isMatch);
                                                    if (isMatch) visibleCount++;
                                                });

                                                // Show/hide "no results" message
                                                const $noResults = config.$dealerContainer.find('#no-dealers-found');
                                                if (visibleCount === 0 && searchTerm) {
                                                    if (!$noResults.length) {
                                                        config.$dealerContainer.append(
                                                            '<p id="no-dealers-found" class="text-muted">No dealers found</p>');
                                                    }
                                                } else {
                                                    $noResults.remove();
                                                }
                                            }

                                            // Check all visible dealers
                                            $(document).on('click', '.check-all', function() {
                                                config.$dealerContainer.find('.dealer-item:visible input[type="checkbox"]').prop('checked',
                                                    true);
                                                config.$dealerContainer.find('.check-all').hide();
                                                config.$dealerContainer.find('.uncheck-all').show();
                                            });

                                            // Uncheck all dealers
                                            $(document).on('click', '.uncheck-all', function() {
                                                config.$dealerContainer.find('input[type="checkbox"]').prop('checked', false);
                                                config.$dealerContainer.find('.uncheck-all').hide();
                                                config.$dealerContainer.find('.check-all').show();
                                            });
                                        });
                                    </script>
                                    <div class="form-group row dealer-class" style="display:none">
                                        <label for="Dealer_code"
                                            class="col-md-3 col-form-label text-right">{{ __('Dealer') }}</label>
                                        <div class="col-md-6 " style="padding-right:1px;">
                                            <input type="text" id="select-dealer-search"
                                                placeholder="Search for dealer..." class="form-control">
                                            <div class="chkbox-dealer" id="myUl-dealer"
                                                style="padding-right:1px; list-style-type: none;overflow-y: auto;">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="areas"
                                            class="col-md-3 col-form-label text-right">{{ __('Purpose') }}</label>
                                        <div class="col-md-6">
                                            <select name="purpose_id[]" class="selectpicker form-control"
                                                id="number-multiple" data-live-search="true" title="Select Role"
                                                data-hide-disabled="true" data-actions-box="true" required multiple>
                                                @foreach ($purpose as $purposes)
                                                    <option value="{{ $purposes->id }}">{{ $purposes->purpose }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="expiry_date"
                                            class="col-md-3 col-form-label text-right">{{ __('Date') }}</label>
                                        <div class="col-md-6">
                                            <input type="text" id="nepaliDateFa" autocomplete="off" name="expiry_date"
                                                placeholder="YYYY-MM-DD" value="{{ old('expiry_date') }}"
                                                class="form-control nepali-calendar" required>
                                        </div>
                                        <span class="field-validation-valid" data-valmsg-for="education"
                                            data-valmsg-replace="true"></span>
                                        @if ($errors->has('expiry_date'))
                                            <span class="help-block required">
                                                <strong>{{ $errors->first('expiry_date') }}</strong>
                                            </span>
                                        @endif
                                        @if (session('status'))
                                            <span class="help-block required">
                                                <strong>{{ session('status') }}</strong>
                                        @endif
                                        </span>
                                    </div>
                                    <div class="form-group row">
                                        <label for="remarks"
                                            class="col-md-3 col-form-label text-right">{{ __('Remarks') }}</label>

                                        <div class="col-md-6">
                                            <textarea class="form-control" name="remarks" placeholder="Remarks" required autofocus></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row hidden">
                                        <input name="status" type="text" value="not-approved">
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary" style="width: 200px;">
                                                {{ __('Create') }}
                                            </button>
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
@endsection
