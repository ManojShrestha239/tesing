
    <div class="page-content2">
        <img style = "float:right;width:25px; height:25px;" onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg') }}>
        <div class="breadcrumbs">
            <h1>Edit Dealer</h1>
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
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Edit Dealer</span>
                                    </div>

                                </div>

                            @if(count($errors) > 0)
        @foreach($errors->all() as $error)
            <p class="alert alert-danger">{{ $error }}</p>
        @endforeach
    @endif

    <form method="POST" action="/dashboard/dealer/{{ $dealer->code }}">
        {{ method_field('PATCH') }}
        @csrf

        <div class="form-group row">
            <label for="name" class="col-md-3 col-form-label text-right">{{ __('Dealer Name') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ $dealer->name }}" name="name" required autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="next_phone" class="col-md-3 col-form-label text-right">{{ __('Address') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ $dealer->address }}" name="address" autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="email" class="col-md-3 col-form-label text-right">{{ __('Email') }}</label>

            <div class="col-md-6">
                <input type="email" class="form-control" value="{{ $dealer->email }}" name="email" autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="mobile" class="col-md-3 col-form-label text-right">{{ __('Mobile') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ $dealer->mobile }}" name="mobile" required autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="phone" class="col-md-3 col-form-label text-right">{{ __('Phone') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ $dealer->phone }}" name="phone" autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="order_limit" class="col-md-3 col-form-label text-right">{{ __('Order Limit') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ $dealer->order_limit }}" name="order_limit" autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="area" class="col-md-3 col-form-label text-right">{{ __('Area') }}</label>
            <div class="col-md-6">
                <select name="area" class="form-control select-area">
                    <option value="">Select Area</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->areacode }}" @if ($dealer->area_code == $area->areacode) selected @endif>{{ $area->area }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="subarea" class="col-md-3 col-form-label text-right">{{ __('Sub Area') }}</label>
            <div class="col-md-6">
                <select id="select-subarea" name="subarea" class="form-control">
                    <option class="select-subarea" value="">Sub Area</option>
                    @foreach($subareas as $subarea)
                        <option class="select-subarea" value="{{ $subarea->subarea }}" @if ($dealer->subarea == $subarea->subarea) selected @endif>{{ $subarea->subarea }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="status" class="col-md-3 col-form-label text-right">{{ __('Status') }}</label>
            <div class="col-md-6">
                <select name="status" class="form-control">
                    <option value="">Select Status</option>
                    <option value="approved" @if ($dealer->status == "approved") selected @endif>approved</option>
                    <option value="not-approved" @if ($dealer->status == "not-approved") selected @endif>not-approved</option>
                    <option value="pending" @if ($dealer->status == "pending") selected @endif>pending</option>
                </select>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 col-md-offset-3">
                <button type="submit" class="btn btn-primary" style="width: 200px;">
                    {{ __('Update') }}
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
    <script>
    $(document).on('change','.select-area',function(e){
        $('.select-subarea').remove();
                        var value = $(this).val();

                        $.ajax({
                            url:"/dashboard/subarea",
                            type: "get",
                            data:{
                                data : value,
                            },
                            success: function (response) {
                            
                            $("#select-subarea").append("<option class='select-subarea' value=''>Select Sub Area</option>");

                                response.map(function(data){
                                var name = data.subarea;
                                var value = data.subarea;
                                
                                $("#select-subarea").append("<option class='select-subarea' value='"+value+"'>"+name+"</option>");
                            });
                        }
                        });
                    });
    </script>


