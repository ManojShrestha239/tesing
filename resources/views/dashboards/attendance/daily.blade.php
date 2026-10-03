@inject('helpers', 'App\Utilities\Helpers')
<style>
    .bold-content {
        font-weight: bold;
    }
</style>
<div class="page-content2 ">
    <img style="float:right;width:25px; height:25px;"
        onclick="document.getElementById('order-popup').style.display='none'"
        src={{ asset('assets/pages/img/quit.svg') }}>
    <div class="breadcrumbs">
        <h1>{{ $attendances->mobileuser->name }} </h1>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">
            <span class="sr-only">Toggle navigation</span>
            <span class="toggle-icon">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </span>
        </button>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" fa fa-paint-brush font-red"></i>
                        <span class="caption-subject font-red bold uppercase">Attendance Report -
                            {{ $helpers->convertDate($attendances->date) }}</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>KM</th>
                                <th>Visit Method</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($attendances))

                                @foreach ($attendances->attendance_status as $attendance)
                                    <tr>
                                        <td> {{ $loop->iteration }}</td>
                                        <td> {{ $attendance->time }}</td>
                                        <td> {{ $attendance->status }}</td>
                                        <td>{{ $attendance->km }}</td>
                                        <td>{{ $attendance->visit_method }}</td>
                                        <td>{{ $attendance->remarks }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td><span class="bold-content"> Total Working Hours: </span>
                                        @if (count($attendances->attendance_status) > 1)
                                            <?php
                                            $start = new DateTime($attendances->attendance_status[0]->time);
                                            $end = new DateTime($attendances->attendance_status[count($attendances->attendance_status) - 1]->time);

                                            $interval = $end->diff($start);
                                            echo '(' . $interval->format('%h') . ' hrs ' . $interval->format('%i') . ' min' . $interval->format('%s') . ' sec)';
                                            ?>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="bold-content"> Total Distance Travelled: </span>
                                        {{ $distance }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
