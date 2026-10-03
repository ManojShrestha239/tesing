@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>All Products</h1>
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
                                        <i class="fa fa-paint-brush font-red"></i>
                                        <span class="caption-subject font-red bold uppercase"> Product List </span>
                                    </div>
                                </div>
                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif
                                <style>
                                    .productImage {
                                        height: 100px;
                                        width: 100px;
                                        margin-left: 22%;
                                    }
                                </style>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                                        <thead>
                                            <tr>
                                                <th> Product Image</th>
                                                <th>Product Name</th>
                                                <th>Category</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($products as $product)
                                                <tr>
                                                    <td>
                                                        <a href="/product/images/{{ $product->id }}/all">
                                                            @if ($product->image)
                                                                <img class="productImage"
                                                                    src="{{ asset('/storage/productImage/' . $product->image) }}"
                                                                    srcset="">
                                                            @else
                                                                <img class="productImage"
                                                                    src="{{ asset('/images/image_not_found.png') }}"
                                                                    srcset="">
                                                            @endif
                                                        </a>
                                                    </td>
                                                    <td>{!! $product->name !!}</td>
                                                    <td>{!! $product->category['name'] !!}</td>
                                                    <td>
                                                        @if ($product->category['status'] == 'Inactive')
                                                            Inactive
                                                            @else{{ $product->status }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a id="{{ $product->id }}"
                                                            onclick="document.getElementById('order-popup').style.display='block'"
                                                            class='btn green btn-outline btn-xs order-view-popup'
                                                            style="margin-right:5px;">Edit</a>
                                                        <a id="{{ $product->id }}"
                                                            onclick="document.getElementById('order-popup').style.display='block'"
                                                            class="btn btn-primary btn-outline btn-xs image-view-popup"
                                                            style="margin-right:5px;">Add Images</a>
                                                        @if ($product->category['status'] == 'Active')
                                                            @if ($product->status == 'Active')
                                                                <a href="" class='btn btn-danger btn-xs'
                                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to disable this product?')){ document.getElementById('status{{ $product->id }}').submit(); }">Disable</a>
                                                            @else
                                                                <a href="" class='btn btn-primary btn-xs'
                                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to enable this product?')){ document.getElementById('status{{ $product->id }}').submit(); }">Enable</a>
                                                            @endif
                                                            <form action="/product/{{ $product->id }}" method="post"
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
                            {{-- pop up after clicking view  --}}
                            <div id="order-popup">



                            </div>
                            <script>
                                $(document).ready(function() {
                                    $(".order-view-popup").click(function() {
                                        var id = $(this).attr('id');
                                        $("#order-popup").load("/product/" + id + "/edit");
                                        $("#order-pop").css("display", "block");
                                        var modal = document.getElementById('order-popup');
                                        window.onclick = function(event) {
                                            if (event.target == modal) {
                                                modal.style.display = "none";
                                            }
                                        }
                                    });

                                    $(".image-view-popup").click(function() {
                                        var id = $(this).attr('id');
                                        // alert(id);
                                        $("#order-popup").load("/product/images/" + id);
                                        $("#order-pop").css("display", "block");
                                        var modal = document.getElementById('order-popup');
                                        window.onclick = function(event) {
                                            if (event.target == modal) {
                                                modal.style.display = "none";
                                            }
                                        }
                                    });

                                });
                            </script>
                            <!-- END EXAMPLE TABLE PORTLET-->
                        </div>

                        <!-- END SIDEBAR CONTENT LAYOUT -->
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
