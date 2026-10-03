<div class="page-content2">
    <img style="float:right;width:25px; height:25px;" onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg') }}>
    <div class="breadcrumbs">
        <h1>Add Images of Product</h1>
        <!-- Sidebar Toggle Button -->
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar"></button>
        <!-- Sidebar Toggle Button -->
    </div>

    <div class="page-content-container">
        <div class="page-content-row" id="replecable">
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
                                    <i class=" fa fa-paint-brush font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">Add Images</span>
                                </div>

                            </div>


                            @if(count($errors) > 0)
                            @foreach($errors->all() as $error)
                            <p class="alert alert-danger">{{ $error }}</p>
                            @endforeach
                            @endif

                            <form method="POST" action="/product/images" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{$product->id}}" name="product_id" />
                                <div class="form-group row">
                                    <label for="image" class="col-md-3 col-form-label text-right">{{ __('Images') }}</label>

                                    <div class="col-md-6">
                                        <input type="file" name="image[]" class="form-control" required multiple autofocus>
                                    </div>

                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button type="submit" class="btn btn-primary" style="width: 200px;">
                                            {{ __('Upload') }}
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