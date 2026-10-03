@extends('dashboards.sessions.layout')
@section('content')
    <?php
    $url = request()->segment(count(request()->segments()));
    ?>
    <style>
        .page-content-row .page-content-col {
            display: unset;
        }
    </style>
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>All Orders</h1>

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

                {{-- Sidebar --}}
                @include('dashboards.orders.sidenavbar')

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
                                        <i class=" fa fa-envelope font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">order Request</span>
                                    </div>

                                </div>

                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form method="POST" action="/orederrequest" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="user"
                                            class="col-md-3 col-form-label text-right">{{ __('User') }}</label>
                                        <div class="col-md-6">
                                            <select name="user" class="form-control selectpicker"
                                                data-live-search="true">
                                                <option value="">All</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->name }}-{{ $user->email }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="dealer"
                                            class="col-md-3 col-form-label text-right">{{ __('Dealer') }}</label>
                                        <div class="col-md-6">
                                            <select name="dealer" id="dealer" class="form-control selectpicker"
                                                data-live-search="true">
                                                <option value="">All</option>
                                                @foreach ($dealers as $dealer)
                                                    <option value="{{ $dealer->code }}">{{ $dealer->code }} -
                                                        {{ $dealer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="title"
                                            class="col-md-3 col-form-label text-right">{{ __('Image') }}</label>
                                        <div class="col-md-6">
                                            <input type="file" name="images[]" accept="image/jpeg,image/jpg,image/png"
                                                class="form-control" autofocus multiple>
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
                            </div>
                        </div>
                    </div>
                </div>


                <div class="page-content-col">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" fa fa-paint-brush font-red"></i>
                                        <span class="caption-subject font-red bold uppercase"> Order Request List </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light portlet-fit bordered">
                                            <div class="portlet-body">
                                                <table class="table table-striped table-bordered table-hover"
                                                    id="sample_2">
                                                    <thead>
                                                        <tr>
                                                            <th>S.N </th>
                                                            <th>Dealer</th>
                                                            <th>Mobile User</th>
                                                            <th>Submit Date </th>
                                                            <th>Image</th>
                                                            <th>Staus</th>
                                                            <th>Action</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($mobileOrders as $key => $mobileOrder)
                                                            <tr>
                                                                <td> {{ ++$key }} </td>
                                                                <td>{{ $mobileOrder->dealer_code }}</td>
                                                                <td>{{ $mobileOrder->mobileuser->name }}</td>
                                                                <td>{{ $mobileOrder->created_at }}</td>
                                                                <td>{{ $mobileOrder->image }} </td>
                                                                <td>{{ $mobileOrder->status }}</td>
                                                                <td>
                                                                    <a class="btn btn-primary"
                                                                        href="{{ route('addorder', $mobileOrder->id) }}">Add
                                                                        order</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div id="order-popup">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelector('select[name="user"]').addEventListener('change', function() {
            const id = this.value;
            if (id) {
                fetch("/schedule/dealer/" + id)
                    .then(response => response.json())
                    .then(data => {
                        const dealerSelect = document.getElementById('dealer');
                        dealerSelect.innerHTML = '<option value="">All Dealer</option>';
                        const dealers = data.dealers || data;
                        if (Array.isArray(dealers)) {
                            dealers.forEach(dealer => {
                                const option = document.createElement('option');
                                option.value = dealer.code;
                                option.textContent = dealer.code + ' - ' + dealer.name;
                                dealerSelect.appendChild(option);
                            });
                        }
                        $(dealerSelect).selectpicker("refresh");
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    </script>
@endsection
