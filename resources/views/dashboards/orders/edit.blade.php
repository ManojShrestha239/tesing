@extends('dashboards.sessions.layout')

@section('content')
    <style>
        #goback:hover {
            background: red;
            border: 2px solid #f26e25;
            cursor: pointer;
            transition: 0.4s;
        }

        #goback:hover h3 {
            color: white;
        }

        #goback:hover img {
            color: white;
        }

        #goback {
            border: 2px solid #0d2444;
            width: 100px;
            border-radius: 32px;
        }

        #goback img {
            margin-top: 6px;
            margin-left: 4px;
            margin-right: 4px;
        }

        #goback h3 {
            margin-top: 6px;
            font-size: 20px;
            font-weight: 600;
        }
    </style>
    <div class="page-content">
        <div id="goback" onclick="goBack()">
            <img style="float:left;width:25px; height:25px;" onclick="goBack()"
                src={{ asset('assets/global/img/left-arrow-key.svg') }}>
            <h3> Back</h3>
        </div>
        <script>
            function goBack() {
                window.history.back();
            }
        </script> <!-- BEGIN BREADCRUMBS -->
        <div class="breadcrumbs">


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
        <!-- END BREADCRUMBS -->
        <!-- BEGIN SIDEBAR CONTENT LAYOUT -->

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet light portlet-fit bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class=" fa fa-paint-brush font-red"></i>
                            <span class="caption-subject font-red bold uppercase">Edit Order for Challan</span>
                        </div>

                    </div>
                    @foreach ($orders as $order)
                        <form method="POST" action="/challan/edit/{{ $order->id }}">
                            {{ method_field('PATCH') }}
                            @csrf
                            <div class="form-group row">
                                <label for="user"
                                    class="col-md-3 col-form-label text-right">{{ __('Product ') }}</label>
                                <div class="col-md-6">
                                    <input type="text" value="{{ $order->name }}" name="product" class="form-control"
                                        disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="title" class="col-md-3 col-form-label text-right">{{ __('Variant') }}</label>
                                <div class="col-md-6">
                                    <input type="text" name="variant" value="{{ $order->size }}" class="form-control"
                                        disabled autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="user" class="col-md-3 col-form-label text-right">{{ __('Rate') }}</label>
                                <div class="col-md-6">
                                    <input type="text" name="rate" class="form-control" value="{{ $order->price }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="quantity" class="col-md-3 col-form-label text-right">Quantity</label>

                                <div class="col-md-6">
                                    <input type="text" value="{{ $order->quantity }}" autocomplete="off" name="quantity"
                                        class="form-control" required autofocus>
                                </div>

                                <span class="field-validation-valid" data-valmsg-for="education"
                                    data-valmsg-replace="true"></span>
                                @if ($errors->has('quantity'))
                                    <span class="help-block required">
                                        <strong>{{ $errors->first('quqntity') }}</strong>
                                    </span>
                                @endif
                                @if (session('status'))
                                    <span class="help-block required">
                                        <strong>{{ session('status') }}</strong>
                                @endif
                            </div>


                            <div class="form-group row mb-0">
                                <div class="col-md-6 col-md-offset-3">
                                    <button type="submit" class="btn btn-primary" style="width: 200px;">
                                        {{ __('Save') }}
                                    </button>
                                </div>
                            </div>

                        </form>
                    @endforeach

                </div>
            @endsection
