
    <div class="page-content2 ">
        <img style = "float:right;width:25px; height:25px;" onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg') }}>
        <!-- BEGIN BREADCRUMBS -->
        <div class="breadcrumbs">
            <h1>Challan Edit</h1>

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
<style>
    .printOnly{
        display:none;
    }
</style>
        <div class="row">
            <div class="col-md-12">
            <span style="position:absolute;right:5%;top:5%;" class="challan"><a href="/order/singleChallan/{{$o->id}}" class="btn btn-success" > Create Challan </a></span>
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet light portlet-fit bordered" id="printMe">

                    @if (!empty($order))
                    <p class="printOnly"><?php echo date('Y-m-d'); ?></p>
                    <p class="printOnly"> Marketing Officer:{{ $mo}}</p>
                    <p class="printOnly"> Delaer : {{ $dealer }} </p>

                    @endif

                    <div class="portlet-title">
                        <div class="caption">
                            <i class=" fa fa-paint-brush font-red"></i>
                            <span class="caption-subject font-red bold uppercase">Orders</span>
                        </div>

                    </div>
                    <div class="portlet-body" >

                    </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>

            <!-- END SIDEBAR CONTENT LAYOUT -->
        </div>

    </div>
