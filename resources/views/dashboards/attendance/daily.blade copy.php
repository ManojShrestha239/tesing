<style>
    .bold-content{
        font-weight: bold;
    }
</style>
    <div class="page-content2 ">
        <img style = "float:right;width:25px; height:25px;" onclick="document.getElementById('order-popup').style.display='none'" src={{ asset('assets/pages/img/quit.svg') }}>
        <!-- BEGIN BREADCRUMBS -->
        <div class="breadcrumbs">
            <h1>{{$att->username}} </h1>

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
                            <span class="caption-subject font-red bold uppercase">Attendance Report - {{$att->date}}</span>
                        </div>

                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover" id="sample_2">
                            <thead>
                            <tr>
                                <th> s/n </th>
                                <th> Time</th>
                                <th> Status </th>
                                <th> Address </th>
                            </tr>
                            </thead>
                             <tbody>
                            @if (!empty($attendances))

                                @foreach($attendances as $attendance)
                                    <tr>
                                        <td> {{ $loop->iteration }}</td>         
                                        <td> {{ $attendance->checkin_date }}</td>
                                        <td> {{ $attendance->status }}</td>
                                        <td> {{$attendance->address}} </td>
                                    </tr>
                                @endforeach
                                <tr> 
                                    <td><span class="bold-content"> Total Working Hours: </span> @if(count($attendances)>1)
                                    <?php
                                                $total_sec = strtotime($attendances[count($attendances)-1]->checkin_date)-strtotime($attendances[0]->checkin_date);
                                                $total_min = intdiv($total_sec, 60);
                                                $total_hr = abs(intdiv($total_min, 60));
                                                $rem_min = abs($total_min % 60);
                                                $rem_sec = abs($total_sec % 60);
                                    ?>
                                    {{$total_hr}}hr {{$rem_min}}min {{$rem_sec}}sec
                                    @endif </td>                      
                                </tr>
                            @endif

                            </tbody> 
                        </table>
                    </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>

            <!-- END SIDEBAR CONTENT LAYOUT -->
        </div>

    </div>
