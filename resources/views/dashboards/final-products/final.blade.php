@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>All Final Products</h1>
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
                    <style>
                        .productImage {
                            height: 100px;
                            width: 100px;
                            margin-left: 22%;
                        }
                    </style>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" fa fa-paint-brush font-red"></i>
                                        <span class="caption-subject font-red bold uppercase"> Product List </span>
                                    </div>
                                    <a href="/exportproduct" class="btn btn-outline btn-xs btn-primary"
                                        style="float:right">.xlsx</a>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_3">
                                        <thead>
                                            <tr>
                                                <th>Product Image</th>
                                                <th> Product Name </th>
                                                <th> Variant </th>
                                                <th> Price </th>
                                                <th>Status</th>
                                                <th> Actions </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($products as $product)
                                                <tr>
                                                    <td>
                                                        @if ($product->product->image)
                                                            <img class="productImage"
                                                                src="{{ asset('storage/productImage/' . $product->product['image']) }}"
                                                                srcset="">
                                                        @else
                                                            <img class="productImage"
                                                                src="{{ asset('/images/image_not_found.png') }}"
                                                                srcset="">
                                                        @endif
                                                    </td>

                                                    <td>{!! $product->product['name'] !!}</td>
                                                    <td>{!! $product->size['size'] !!}</td>
                                                    <td>Rs. {!! $product->price !!}</td>
                                                    <td>
                                                        @if ($product->status == 1)
                                                            Enable
                                                        @endif
                                                        @if ($product->status == 0)
                                                            Disable
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="/final-product/{{ $product->id }}/edit"
                                                            class='btn green btn-outline btn-xs'
                                                            style="margin-right:5px;">Edit</a>
                                                        @if (
                                                            $product->product->category->status == 'Active' &&
                                                                $product->size->status == 'Active' &&
                                                                $product->product->status == 'Active')
                                                            @if ($product->status == 1)
                                                                <a href="" class='btn btn-danger btn-xs'
                                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to disable this final product?')){ document.getElementById('status{{ $product->id }}').submit(); }">Disable</a>
                                                            @else
                                                                <a href="" class='btn btn-primary btn-xs'
                                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to enable this final product?')){ document.getElementById('status{{ $product->id }}').submit(); }">Enable</a>
                                                            @endif
                                                            <form action="/final-product/{{ $product->id }}" method="post"
                                                                id="status{{ $product->id }}">
                                                                {{ method_field('DELETE') }}
                                                                @csrf
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- END EXAMPLE TABLE PORTLET-->
                        </div>

                        <!-- END SIDEBAR CONTENT LAYOUT -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
