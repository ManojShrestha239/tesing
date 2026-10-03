@inject('helpers', 'App\Utilities\Helpers')
@extends('dashboards.sessions.layout')
@section('content')

    <div class="page-content">
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
                                <li><a href="/notice/create">Create Notice </a></li>
                                <li class="active"><a href="/notice">Send Notices </a></li>
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
                        <div class="col-md-12 notice-content">
                            <form method="post" class="page-content-container" action="/notice/search">
                                @csrf
                                <div class="row">

                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label class="control-label bolds">Officer<span
                                                    class="required">*</span></label>
                                            <select class="form-control selectpicker" data-live-search="true"
                                                data-live-search-style="startsWith" name="user" id="user">
                                                <option value="">Select Officer</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        @if (isset($userid)) @if ($userid == $user->id) selected @endif
                                                    @else @if (old('user') == $user->id) selected @endif
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
                                                    class="required has-feedback{{ $errors->has('start-date') ? ' has-error' : '' }}">*</span></label>
                                            <div class="date" data-provide="datepicker" class="form-control">
                                                <input type="text" id="nepaliDateFa" autocomplete="off" name="date"
                                                    placeholder="YYYY-MM-DD" value="{{ $from ?? old('date') }}"
                                                    class="form-control nepali-calendar">
                                            </div>
                                            <span class="field-validation-valid" data-valmsg-for="education"
                                                data-valmsg-replace="true"></span>
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
                                            @if (!empty($start))

                                                @if (empty($mr) && empty($from))
                                                    All Notices
                                                @endif
                                            @else
                                                @if (empty($mr) && empty($from))
                                                    All General Notices
                                                @elseif(!empty($mr) && empty($from))
                                                    All Notices of {{ $mr }}
                                                @elseif(empty($mr) && !empty($from))
                                                    All Notices list - ({{ $from }})
                                                @else
                                                    Notice list of {{ $mr }} - ({{ $from }})
                                                @endif

                                            @endif

                                        </span>
                                    </div>
                                    <div class="tools"></div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                                        <thead>
                                            <tr>
                                                <th> Notice No.</th>
                                                <th> Send to </th>
                                                <th> Title </th>
                                                <th> Notice </th>
                                                <th> Expiry Date </th>
                                                <th> Status </th>
                                                <th> Actions </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($notices))

                                                @foreach ($notices as $notice)
                                                    <tr>
                                                        <td>{{ $notice->id }}</td>
                                                        <td>
                                                            @if ($notice->user_id == null)
                                                                All
                                                            @else
                                                                {{ $notice->user['name'] }}
                                                            @endif
                                                        </td>
                                                        <td>{!! $notice->title !!}</td>

                                                        <td>
                                                            <?php $str = $notice->notice; ?>
                                                            @if (strlen($notice->notice) > 250)
                                                                <div class="notice-content-wrapper"
                                                                    id="notice-{{ $notice->id }}">
                                                                    <div class="notice-preview">
                                                                        <?php echo substr($str, 0, 250) . '....'; ?>
                                                                    </div>
                                                                    <a class="toggle-notice-detail"
                                                                        href="javascript:void(0);"
                                                                        data-notice-id="{{ $notice->id }}">
                                                                        <div class="btn btn-md">read more</div>
                                                                    </a>
                                                                </div>
                                                                <div class="notice-full-content"
                                                                    id="full-notice-{{ $notice->id }}"
                                                                    style="display:none;">
                                                                    {!! $notice->notice !!}
                                                                    <a class="toggle-notice-detail"
                                                                        href="javascript:void(0);"
                                                                        data-notice-id="{{ $notice->id }}">
                                                                        <div class="btn btn-md">read less</div>
                                                                    </a>
                                                                </div>
                                                            @else
                                                                {!! $notice->notice !!}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {!! $helpers->convertDate($notice->expiry_date) !!}
                                                        </td>
                                                        <td>
                                                            <?php $date = date('Y-m-d'); ?>

                                                            @if ($notice->publish == 1)
                                                                Published
                                                            @else
                                                                Draft
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="/notice/{{ $notice->id }}"
                                                                class='btn green btn-outline btn-xs'> View File </a>
                                                            <form action="/notice/{{ $notice->id }}" method="post">
                                                                @method('delete')
                                                                @csrf
                                                                <button type="submit" class='btn red btn-outline btn-xs'
                                                                    onclick="return confirm('Are you sure you want to delete this notice?');">
                                                                    Delete</button><br>
                                                            </form>
                                                            @if ($notice->publish == 0)
                                                                <form action="/noticePush/{{ $notice->id }}"
                                                                    method="post">
                                                                    @method('patch')
                                                                    @csrf
                                                                    @if ($notice->expiry_date >= $notice->today)
                                                                        <button type="submit"
                                                                            class='btn btn-primary btn-outline btn-xs'
                                                                            onclick="return confirm('Are you sure you want to publish this notice?');">
                                                                            Publish
                                                                        </button><br>
                                                                    @endif
                                                                </form>
                                                                <a href="/notice/{{ $notice->id }}/edit"
                                                                    class='btn green btn-outline btn-xs'>Edit</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <!-- END EXAMPLE TABLE PORTLET-->
                            <style>
                                .notice-full-content {
                                    padding: 15px 0;
                                    line-height: 1.6;
                                }
                            </style>
                            <script>
                                $(document).ready(function() {
                                    $(".toggle-notice-detail").click(function(e) {
                                        e.preventDefault();
                                        var noticeId = $(this).data('notice-id');
                                        var wrapper = $("#notice-" + noticeId);
                                        var fullContent = $("#full-notice-" + noticeId);

                                        wrapper.slideToggle(300);
                                        fullContent.slideToggle(300);
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
