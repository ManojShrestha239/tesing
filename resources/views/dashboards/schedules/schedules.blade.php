@inject('helpers', 'App\Utilities\Helpers')
@extends('dashboards.sessions.layout')
@section('content')
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
                            <ul class="nav navbar-nav margin-bottom-35">
                                <li><a href="/schedule/create">Add New visit Schedule</a></li>
                                <li class="active"><a href="/schedule">List of Visit Schedule </a></li>
                                <li><a href="/repeated">Repeated Schedule</a></li>
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
                            <form method="post" class="page-content-container" action="/schedule/search">
                                @csrf
                                <div class="row">

                                    <div class="col-lg-5">
                                        @extends('dashboards.sessions.date')
                                        <div class="form-group">
                                            <label class="control-label bolds">Marketing Officer<span
                                                    class="required">*</span></label>

                                            <select class="form-control selectpicker" data-live-search="true"
                                                data-live-search-style="startsWith" name="user" id="user">
                                                <option name="user" value="">Select Marketing Officer</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        @if (isset($user_id)) @if ($user->id == $user_id) selected @endif
                                                        @endif>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="field-validation-valid" data-valmsg-for="resCountry"
                                                data-valmsg-replace="true"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">

                                        <div class="form-group">
                                            <label class="control-label bolds">Date<span
                                                    class="required has-feedback{{ $errors->has('date') ? ' has-error' : '' }}">*</span></label>
                                            <input type="text" id="nepaliDateFa" autocomplete="off" name="date"
                                                value="{{ $date ?? old('date') }}" placeholder="YYYY-MM-DD"
                                                class="form-control nepali-calendar">
                                            @if ($errors->has('date'))
                                                <span class="help-block required">
                                                    <strong>{{ $errors->first('date') }}</strong>
                                                </span>
                                            @endif
                                            @if (session('status'))
                                                <span class="help-block required">
                                                    <strong>{{ session('status') }}</strong>
                                            @endif
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
                                                <th>Dealer Code</th>
                                                <th>Dealer Name</th>
                                                <th>User Name</th>
                                                <th>Date</th>
                                                <th>Remarks</th>
                                                <th>Status</th>
                                                <th>Purpose</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($schedules))
                                                @foreach ($schedules as $pur)
                                                    <tr>
                                                        <td> {{ $pur->id }} </td>
                                                        <td>{!! $pur->dealer_code !!}</td>
                                                        <td>{{ $pur->dealer['name'] }}</td>
                                                        <td>{{ $pur->user ? $pur->user['name'] : '-' }}</td>
                                                        <td>{!! $helpers->convertDate($pur->date) !!}</td>
                                                        <td>{!! $pur->remarks !!}</td>
                                                        <td>
                                                            @if ($pur->is_visit == 0)
                                                                Remaining
                                                            @else
                                                                Visited
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ implode(',', $pur->purpose) }}

                                                        </td>
                                                        <td>
                                                            @if ($pur->is_visit == 0)
                                                                <a href="/schedule/{{ $pur->id }}/edit"
                                                                    class='btn green btn-outline btn-xs'
                                                                    style="margin-right:5px;">Edit</a>
                                                            @endif
                                                            <form action="/schedule/{{ $pur->id }}" method="post">
                                                                @method('delete')
                                                                @csrf
                                                                <button type="submit" class='btn red btn-outline btn-xs'
                                                                    onclick="return confirm('Are you sure you want to delete this schedule?');">
                                                                    Delete
                                                                </button>
                                                            </form>

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>

                                    </table>


                                </div>
                            </div>
                            <!-- END EXAMPLE TABLE PORTLET-->
                            {{-- pop up after clicking view  --}}
                            <div id="order-popup">



                            </div>
                            <script>
                                $(document).ready(function() {
                                    $(".order-view-popup").click(function() {
                                        var id = $(this).attr('id');
                                        $("#order-popup").load("/schedule/" + id + "/edit");
                                        $("#order-pop").css("display", "block");
                                        var modal = document.getElementById('order-popup');
                                        window.onclick = function(event) {
                                            if (event.target == modal) {
                                                modal.style.display = "none";
                                            }
                                        }
                                    });

                                });
                            </script>
                        </div>

                        <!-- END SIDEBAR CONTENT LAYOUT -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
