@extends('dashboards.blank')

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
                        <ul class="nav navbar-nav margin-bottom-35 products-class">

                            <li><a href="/category-create">Add New Category </a></li>
                            <li><a href="/categories">List Categories </a></li>
                            <li><a href="/size/create">Add New Variant </a></li>
                            <li><a href="/size">List Variants </a></li>
                            <li><a href="/product-create">Add New Product </a></li>
                            <li class="active"><a href="/product">List Product </a></li>
                            <li><a href="/final-product/create">Assign Product with Variants </a></li>
                            <li><a href="/final-product">Final Product List </a></li>

                            <!-- <li><a class="category-create">Add New Category </a></li>
                                <li><a class="categories">List Categories </a></li>
                                <li><a class="product-size-create">Add New Variant </a></li>
                                <li><a class="products-size">List Variants </a></li>
                                <li><a class="product-create">Add New Product </a></li>
                                <li class = "active"><a>List Product </a></li>
                                <li><a class="productfinal-create">Assign Product with Variants </a></li>
                                <li><a href="/productsfinal">Final Product List </a></li> -->

                        </ul>

                    </nav>
                </div>

            </div>

            <style>
                .hostel-image-container {
                    height: 300px;
                    width: 100%;
                    position: relative;
                    margin-bottom: 64px;
                    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);

                }

                .hostel-image-container img {
                    position: absolute;
                    margin: auto;
                    width: 100%;
                    height: 100%;
                }

                .image-cards:hover button {
                    opacity: 1;
                    visibility: visible;
                    z-index: 5;
                }

                .image-cards:hover .image-overlay {
                    opacity: 0.63;
                    visibility: visible;
                }

                .hostel-image-container button {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    opacity: 0;
                    transition: 350ms;
                    visibility: hidden;
                }

                .image-overlay {
                    background: black;
                    position: absolute;
                    top: 0;
                    height: 300px;
                    width: 100%;
                    opacity: 0;
                    visibility: none;
                    transition: 350ms;
                }

                .hostel-image-adding-container {
                    height: 300px;
                    width: 300px;
                    margin-bottom: 64px;
                    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .hostel-image-adding-container img {
                    height: 100px;
                    width: 100px;

                }
            </style>

            <div class="page-content-col">
                @if(session('Status'))

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
                                    <span class="caption-subject font-red bold uppercase"> Product Images </span>
                                </div>

                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    @if(!empty($images))
                                    @foreach($images as $image)
                                    <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 image-cards">
                                        <div class="hostel-image-container">
                                            <img src="{{asset('storage/productImage/'.$image->image)}}" alt="" srcset="">
                                            <div class="image-overlay"></div>
                                            <form action="/image/delete/{{$image->id}}" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <p>No Image Uploaded</p>
                                    @endif
                                </div>
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