@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>All Admin</h1>

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
        @include('dashboards.sessions.date')
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
                            <ul class="nav navbar-nav margin-bottom-35 user-class">
                                @role('Super-Admin')
                                    <li><a href="/admin/create">Add New Admin </a></li>
                                    <li class="active"><a href="/admin">List Admin </a></li>
                                    <li><a href="/role-permission">Roles</a></li>
                                @endrole
                                @can('user')
                                    <li><a href="/user/create">Add New User </a></li>
                                    <li><a href="/user">List Users </a></li>
                                @endcan
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
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-user font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Admin List</span>
                                    </div>

                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_3">
                                        <thead>
                                            <tr>
                                                <th> Name </th>
                                                <th> User Type </th>
                                                <th> Email </th>
                                                <th> Phone </th>
                                                <th> Status </th>
                                                <th> Actions </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>{!! $user->name !!}</td>
                                                    <td> {{ $user->user_type }} </td>
                                                    <td>{!! $user->email !!}</td>
                                                    <td>{!! $user->phone !!}</td>
                                                    <td> {{ $user->status }} </td>
                                                    <td>
                                                        <a href="/admin/{{ $user->id }}/edit"
                                                            class='btn btn-outline btn-primary btn-xs'>Edit</a>
                                                        <a href="/admin/{{ $user->id }}/pass"
                                                            class='btn green btn-outline btn-xs'>Change Password</a>
                                                        @if ($user->id != Auth::user()->id)
                                                            @if (strtolower($user->status) === 'active')
                                                                <a href="" class='btn btn-danger btn-xs'
                                                                    onclick="event.preventDefault();
                                                     document.getElementById('{{ $user->id }}').submit();">Disable</a>
                                                            @else
                                                                <a href="" class='btn btn-primary btn-xs'
                                                                    onclick="event.preventDefault();
                                                     document.getElementById('{{ $user->id }}').submit();">Enable</a>
                                                            @endif
                                                            <form action="/admin/{{ $user->id }}" method="post"
                                                                id="{{ $user->id }}">
                                                                {{ method_field('DELETE') }}
                                                                @csrf
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- pop up after clicking view  --}}
                            <div id="order-popup">


                            </div>
                            <script>
                                $(document).ready(function() {
                                    $(".order-view-popup").click(function() {
                                        var id = $(this).attr('id');
                                        $("#order-popup").load("/admin/" + id + "/edit");
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
                            <!-- END EXAMPLE TABLE PORTLET-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END SIDEBAR CONTENT LAYOUT -->
    </div>
@endsection
