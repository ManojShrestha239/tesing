@extends('dashboards.sessions.layout')

@section('content')
<div class="page-content">
    <div class="breadcrumbs">
        <h1>Attendance Image</h1>

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
                                    <i class=" fa fa-user font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">Attendance images</span>
                                </div>
                                <a style="float: right;" class="updatekm-view-popup" id="{{$attendance->id}}" date-attribute="{{ $date }}">
                                    <div class="btn btn-primary btn-sm" onclick="document.getElementById('order-popup').style.display='block'">Update KM</div>
                                </a>

                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Image preview</th>
                                            <th>Image</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attendanceimages as $attendanceimage)
                                        <tr>
                                            <td>
                                                <div style="height:100px;"> <img height="95px" src="{{asset('storage/attendance/'.$attendanceimage->image)}}" alt="{{$attendanceimage->image}}" srcset=""> </div>
                                            </td>
                                            <td>{{$attendanceimage->image}}</td>
                                            <td>
                                                <a href="{{asset('storage/attendance/'.$attendanceimage->image)}}" target="_blank" class='btn green btn-outline btn-xs order-view-popup'>view</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pop-up Form -->
<div id="order-popup">
</div>

<script>
    $(document).ready(function() {
        $(".updatekm-view-popup").click(function() {
            var id = $(this).attr('id');
            var date = $(this).attr('date-attribute');
            $("#order-popup").load("/attendance/updatekm/" + id + "?date=" + date);
            $("#order-popup").css("display", "block");
            var modal = document.getElementById('order-popup');
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        });

    });
</script>

@endsection