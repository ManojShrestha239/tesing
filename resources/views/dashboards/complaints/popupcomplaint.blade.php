<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="page-content2">
    <img style="float:right;width:25px; height:25px;" onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg') }}>
    <div class="breadcrumbs">
        <h1>Complaint</h1>
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
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" fa fa-user font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">Complaint Detail</span>
                                </div>

                            </div>

                            @if (count($errors) > 0)
                            @foreach ($errors->all() as $error)
                            <p class="alert alert-danger">{{ $error }}</p>
                            @endforeach
                            @endif
                            <form method="POST" action="/dashboard/complaint/{{ $complaint->id }}/edit">
                                {{-- {{ method_field('PATCH') }} --}}
                                @csrf
                                <div style="margin-left: 10px;">
                                    @foreach ($complaint->productcomplaints as $prdcom)
                                    <span style="font-weight: 700;">Customer
                                        Name:-</span>{{ $prdcom->customer_name }}<br />
                                    <span style="font-weight: 700;">Customer
                                        Email:-</span>{{ $prdcom->customer_email }}<br />
                                    <span style="font-weight: 700;">Customer
                                        Mobile:-</span>{{ $prdcom->customer_mobile }}<br />
                                    <span style="font-weight: 700;">Customer
                                        Address:-</span>{{ $prdcom->customer_address }}<br />
                                    <span style="font-weight: 700;">Color
                                        Code:-</span>{{ $prdcom->customer_address }}<br />
                                    <span style="font-weight: 700;">Product
                                        Name:-</span>{{ $prdcom->product_name }}<br />
                                    <span style="font-weight: 700;">Product
                                        Size:-</span>{{ $prdcom->product_size }}<br />
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>