@extends('dashboards.blank')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Add New Product</h1>
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
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            @include('dashboards.dynamicNav.productNav')
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
                                        <span class="caption-subject font-red bold uppercase">Add New Product</span>
                                    </div>

                                </div>


                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif

                                <form method="POST" action="/product" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="name"
                                            class="col-md-3 col-form-label text-right">{{ __('New Product Name') }}</label>

                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="{{ old('name') }}"
                                                name="name" placeholder="Product Name" required autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="role_id"
                                            class="col-md-3 col-form-label text-right">{{ __('Category') }}</label>
                                        <div class="col-md-6">
                                            <select name="category_id" class="form-control selectpicker"
                                                data-live-search="true" required>
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="image"
                                            class="col-md-3 col-form-label text-right">{{ __('Product Image') }}</label>

                                        <div class="col-md-6">
                                            <input type="file" name="image" class="form-control" autofocus>
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

                                <form action="/products/excel" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="excel_file"
                                            class="col-md-3 col-form-label text-right">{{ __('Upload File') }}</label>

                                        <div class="col-md-6">
                                            <input type="file" class="form-control" name="excel_file" required autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Upload') }}
                                            </button>
                                            <a href="{{ asset('sampleTemplate/product_template.xlsx') }}"
                                                class="btn btn-primary"><i class="fa fa-download"
                                                    style="padding-right: 5px;"></i>Sample</a>
                                        </div>
                                    </div>
                                </form>
                                <br>
                                {{-- <p style="text-align: center;font-weight:600;font-size:20">OR</p><br>

                                <form action="/products/excel/upload" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('patch')
                                    <div class="form-group row">
                                        <label for="excel_file"
                                            class="col-md-3 col-form-label text-right">{{ __('Upload File') }}</label>

                                        <div class="col-md-6">
                                            <input type="file" class="form-control" name="excel_file" required autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary" style="width: 200px;">
                                                {{ __('Update') }}
                                            </button>
                                        </div>
                                    </div>
                                </form> --}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
