@extends('dashboards.sessions.layout')
@section('content')
    <link href="{{ asset('css/popup-readmore.css') }}" rel="stylesheet">

    <style>
        td.te1xt {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            /* number of lines to show */
            -webkit-box-orient: vertical;
        }

        td.ar1ea {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            /*number of lines to show */
            -webkit-box-orient: vertical;
        }

        td.s1ubarea {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            /*number of lines to show */
            -webkit-box-orient: vertical;
        }
    </style>

    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Visit Schedule List</h1>
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
                            <ul class="nav navbar-nav margin-bottom-35">

                                <li><a href="/schedule/create">Add New visit Schedule</a></li>
                                <li><a href="/schedule">List of Visit Schedule </a></li>
                                <li class="active"><a href="/repeated">Repeated Schedule</a></li>
                                <li><a href="/repeated/create">Add Repeated Schedule</a></li>
                            </ul>

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
                            <!-- BEGIN SIDEBAR CONTENT LAYOUT -->
                            <form method="get" class="page-content-container" action="/repeated/search">

                                <div class="row">

                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label class="control-label bolds">Marketing Officer<span
                                                    class="required">*</span></label>



                                            <select class="form-control selectpicker" data-live-search="true"
                                                data-live-search-style='startsWith' name="user" id="user">
                                                <option name="user" value="">Select Marketing Officer</option>
                                                @foreach ($users as $user)
                                                    <option
                                                        @if (isset($user_id)) @if ($user_id == $user->id) selected @endif
                                                    @else @if (old('user') == $user->id) selected @endif
                                                        @endif
                                                        value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="field-validation-valid" data-valmsg-for="resCountry"
                                                data-valmsg-replace="true"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label class="control-label bolds">Day<span class="required">*</span></label>
                                            <select class="form-control" name="day" id="day">
                                                <option name="user" value="">Select Day</option>
                                                <option value="Sunday"
                                                    @if (isset($day)) @if ($day == 'Sunday') selected @endif
                                                @else @if (old('user') == 'Sunday') selected @endif @endif>Sunday</option>
                                                <option value="Monday"
                                                    @if (isset($day)) @if ($day == 'Monday') selected @endif
                                                @else @if (old('user') == 'Monday') selected @endif @endif>Monday</option>
                                                <option value="Tuesday"
                                                    @if (isset($day)) @if ($day == 'Tuesday') selected @endif
                                                @else @if (old('user') == 'Tuesday') selected @endif @endif>Tuesday</option>
                                                <option value="Wednesday"
                                                    @if (isset($day)) @if ($day == 'Wednesday') selected @endif
                                                @else @if (old('user') == 'Wednesday') selected @endif @endif>Wednesday</option>
                                                <option value="Thursday"
                                                    @if (isset($day)) @if ($day == 'Thursday') selected @endif
                                                @else @if (old('user') == 'Thursday') selected @endif @endif>Thursday</option>
                                                <option value="Friday"
                                                    @if (isset($day)) @if ($day == 'Friday') selected @endif
                                                @else @if (old('user') == 'Friday') selected @endif @endif>Friday</option>
                                            </select>
                                            <span class="field-validation-valid" data-valmsg-for="resCountry"
                                                data-valmsg-replace="true"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary"
                                                style="margin-top:24px;width: 100%;">View</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-envelope font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">
                                            @if (empty($mr) && empty($date))
                                                Visit Schedule
                                            @elseif(!empty($mr) && empty($date))
                                                visit Schedule of {{ $mr }}
                                            @elseif(empty($mr) && !empty($date))
                                                visit Schedule - {{ $date }}
                                            @else
                                                visit Schedule of {{ $mr }} - {{ $date }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="tools"></div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                                        <thead>
                                            <tr>
                                                <th> Schedule No.</th>
                                                <th>Day</th>
                                                <th>Marketing Officer</th>
                                                <th>Dealer Name</th>
                                                <th>Area</th>
                                                <th>Subarea</th>
                                                <th>Remarks</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($schedules))
                                                @foreach ($schedules as $schedule)
                                                    <tr>
                                                        <td> {{ $schedule->id }} </td>
                                                        <td>
                                                            @if (is_numeric($schedule->day))
                                                                {{ $schedule->day }} {{ 'of the month' }}
                                                            @else
                                                                {{ $schedule->day }}
                                                            @endif
                                                        </td>
                                                        <td>{!! $schedule->user['name'] !!}</td>
                                                        <td class="text">
                                                            <?php
                                                            $str = '';
                                                            ?>
                                                            @foreach ($schedule->dealer as $d)
                                                                {{-- {{$d->name}}({{$d->code}}) , --}}
                                                                <?php
                                                                $str = $str . ',' . $d->name . '(' . $d->code . ')';
                                                                ?>
                                                            @endforeach
                                                            <span id="dealer_name{{ $schedule->id }}"
                                                                style="display:none">{{ $str }} </span>
                                                            @if (strlen($str) > 40)
                                                                <?php echo $trimstr = substr($str, 0, 40) . '...'; ?>
                                                                <!-- Trigger/Open The Modal -->
                                                                <a>
                                                                    <div id="myBtn{{ $schedule->id }}"
                                                                        onclick="mypopup('dealer_name{{ $schedule->id }}', 'Dealer-Name' )">
                                                                        read more</div>
                                                                </a>
                                                            @else
                                                                {{ $str }}
                                                            @endif
                                                        </td>

                                                        <td class="area">
                                                            @php $ar = ''; @endphp
                                                            @foreach ($schedule->area as $a)
                                                                {{-- {{$a->area}}, --}}
                                                                @php
                                                                    $ar = $ar . ',' . $a->area;
                                                                    // $str = $str.','.$d->name.'('.$d->code.')';
                                                                @endphp
                                                            @endforeach
                                                            <span id="area{{ $schedule->id }}" style="display:none">
                                                                {{ $ar }} </span>
                                                            @if (strlen($ar) > 40)
                                                                <?php echo $trimstr = substr($ar, 0, 40) . '...'; ?>
                                                                <!-- Trigger/Open The Modal -->
                                                                <a>
                                                                    <div id="myBtn{{ $schedule->id }}"
                                                                        onclick="mypopup('area{{ $schedule->id }}', 'Area' )">
                                                                        read more</div>
                                                                </a>
                                                            @else
                                                                {{ $ar }}
                                                            @endif
                                                        </td>
                                                        <td class="subarea">
                                                            <?php
                                                            $subar = '';
                                                            ?>
                                                            @foreach ($schedule->subarea as $s)
                                                                {{-- {{$s->subarea}}, --}}
                                                                <?php
                                                                $subar = $subar . ', ' . $s->subarea;
                                                                ?>
                                                            @endforeach
                                                            <span id="sub_area{{ $schedule->id }}"
                                                                style="display:none">{{ $subar }} </span>
                                                            @if (strlen($subar) > 40)
                                                                <?php echo $trimstr = substr($subar, 0, 40) . '...'; ?>
                                                                <!-- Trigger/Open The Modal -->
                                                                <a>
                                                                    <div id="myBtn{{ $schedule->id }}"
                                                                        onclick="mypopup('sub_area{{ $schedule->id }}', 'Sub-Area' )">
                                                                        read more</div>
                                                                </a>
                                                            @else
                                                                {{ $subar }}
                                                            @endif
                                                        </td>
                                                        <td>{!! $schedule['remarks'] !!}</td>
                                                        <td>
                                                            <!-- <a class="btn btn-primary btn-xs" href="/repeated/{{ $schedule->id }}"> View </a> -->
                                                            <a class="btn btn-warning btn-xs"
                                                                href="/repeated/{{ $schedule->id }}/edit"> Edit </a>
                                                            <form action="/repeated/{{ $schedule['id'] }}"
                                                                method="post">
                                                                @method('delete')
                                                                @csrf
                                                                <button type="submit" class='btn red btn-outline btn-xs'
                                                                    onclick="return confirm('Are you sure you want to delete this repeated schedule?');">
                                                                    Delete
                                                                </button>
                                                            </form>

                                                        </td>
                                                    </tr>
                                                @endforeach

                                                <!-- The Modal -->
                                                <div id="myModal" class="modal">
                                                    <!-- Modal content -->
                                                    <div class="modal-content">
                                                        <div class="breadcrumbs">
                                                            <h1 id="header-title"></h1>
                                                            <div class="cross">
                                                                <span class="close0" id="close_first"
                                                                    onclick="document.getElementById('myModal').style.display='none';">&times;</span>
                                                            </div>
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
                                                                            <span
                                                                                class="caption-subject font-red bold uppercase areas-title"></span>
                                                                        </div>

                                                                    </div>
                                                                    <div class="portlet-body" id="abc">
                                                                        {{-- {{ implode(', ', $mobileuser->areas) }} --}}
                                                                    </div>
                                                                </div>
                                                                <!-- END EXAMPLE TABLE PORTLET-->
                                                            </div>

                                                            <!-- END SIDEBAR CONTENT LAYOUT -->
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </tbody>

                                    </table>


                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- END SIDEBAR CONTENT LAYOUT -->
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        function mypopup(content_id, area_title) {
            // let btn = document.getElementById(a).innerText;
            let modal = document.getElementById("myModal");
            let spancontent = document.getElementById(content_id).innerText;
            let abc = document.getElementById("abc");
            let title = document.querySelector(".areas-title");
            // console.log(abc);
            title.innerHTML = area_title;
            modal.style.display = "block";
            abc.innerHTML = spancontent;
        }
    </script>
@endsection
