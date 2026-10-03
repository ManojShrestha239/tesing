<div class="page-content2">
        <img style = "float:right;width:25px; height:25px;" onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg') }}>
        <!-- BEGIN BREADCRUMBS -->
        <div class="breadcrumbs">
            <h1>Notice</h1>

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
                            <span class="caption-subject font-red bold uppercase">notice</span>
                        </div>

                    </div>
                    <div class="portlet-body">
                      

                       <p> {{$noti->notice}}  </p>
                        </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>

            <!-- END SIDEBAR CONTENT LAYOUT -->
        </div>

    </div>
