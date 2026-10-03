@inject('helpers', 'App\Utilities\Helpers')
@extends('dashboards.sessions.layout')
@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>All Areas</h1>
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

                                <li><a href="/notice/create">Create Notice </a></li>
                                <li><a href="/notice">Send Notices </a></li>
                                <li class="active"><a>Files </a></li>

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
                            <div class="portlet light portlet-fit">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-envelope font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">
                                            Notice Files
                                        </span>
                                    </div>
                                    <div class="tools"></div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                                        <thead>
                                            <tr>
                                                <th> S.N.</th>
                                                <th> Files </th>
                                                <th> Actions </th>
                                            </tr>
                                        </thead>
                                        <tbody>


                                            @foreach ($notices->noticeimage as $notice)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $notice->file }}</td>
                                                    <td>
                                                        <form action="/noticefile/{{ $notice->id }}" method="post">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="submit" class='btn red btn-outline btn-xs'
                                                                onclick="return confirm('Are you sure you want to delete this notice image?');"></button>
                                                            Delete</button><br>
                                                        </form>
                                                        <a href="/storage/notice/{{ $notice->file }}"
                                                            class='btn green btn-outline btn-xs' target="_blank">view</a>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <!-- END EXAMPLE TABLE PORTLET-->
                        </div>

                        <!-- END SIDEBAR CONTENT LAYOUT -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
