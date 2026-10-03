@extends('dashboards.sessions.layout')

@section('content')
    <link href="{{ asset('css/popup-readmore.css') }}" rel="stylesheet">

    <style>
        .mobileuser-info {
            font-weight: bold;
            line-height: 1.7;
        }
    </style>
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
                                    <li><a href="/admin">List Admin </a></li>
                                    <li><a href="/role-permission">Roles </a></li>
                                @endrole
                                <li><a href="/user/create">Add New User </a></li>
                                <li class="active"><a href="/user">List Users </a></li>
                            </ul>

                        </nav>
                    </div>

                </div>

                <div class="page-content-col">
                    @if (session('Status'))
                        <div class="m-heading-1 border-green m-bordered">
                            <div class="page-content-col">
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <b>{{ session('Status') }}</b>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" fa fa-user font-red"></i><span
                                            class="caption-subject font-red bold uppercase"> User List </span>
                                    </div>
                                    <div class="tools"> </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                                        <thead>
                                            <tr>
                                                <th> S/N</th>
                                                <th> User Info. </th>
                                                <th>Status</th>
                                                <th> Areas </th>
                                                <th>Sub Area</th>
                                                <th> Actions </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mobileusers as $mobileuser)
                                                @php
                                                    if ($mobileuser->is_super_senior === true) {
                                                        $role = '(Admin)';
                                                    } elseif ($mobileuser->is_senior === true) {
                                                        $role = 'SR.';
                                                    } else {
                                                        $role = 'JR.';
                                                    }

                                                @endphp
                                                <tr style="height:65px;">
                                                    <td> {{ $loop->iteration }}</td>
                                                    <td>
                                                        <span class="mobileuser-info">Name:</span>
                                                        <strong>{{ $role }}</strong>
                                                        {!! $mobileuser->name !!}<br>
                                                        <span
                                                            class="mobileuser-info">Address:</span>{!! $mobileuser->address !!}<br>
                                                        <span class="mobileuser-info">Email:</span>
                                                        {!! $mobileuser->email !!}<br>
                                                        <span
                                                            class="mobileuser-info">Contact1:</span>{!! $mobileuser->phone !!}<br>
                                                        <span class="mobileuser-info">Role:</span> {!! $mobileuser->role['role'] !!}
                                                    </td>
                                                    <td>{!! $mobileuser->status !!}</td>
                                                    <td>
                                                        <span id="span{{ $mobileuser->id }}" style="display:none">
                                                            {{ implode(', ', $mobileuser->areas) }}</span>
                                                        <?php $str = implode(', ', $mobileuser->areas); ?>
                                                        @if (strlen($str) > 100)
                                                            <?php echo $trimstr = substr($str, 0, 100) . '...'; ?>

                                                            <!-- Trigger/Open The Modal -->
                                                            <a>
                                                                <div id="myBtn{{ $mobileuser->id }}"
                                                                    onclick="mypopup('span{{ $mobileuser->id }}','Area' )">
                                                                    read more</div>
                                                            </a>
                                                        @else
                                                            {{ implode(', ', $mobileuser->areas) }}
                                                        @endif
                                                    </td>
                                                    {{-- <td> {{$mobileuser->sub_areas}} </td> --}}
                                                    <td>
                                                        <span id="subspan{{ $mobileuser->id }}" style="display:none">
                                                            {{ $mobileuser->sub_areas }}</span>
                                                        <?php $str1 = $mobileuser->sub_areas; ?>
                                                        @if (strlen($str1) > 100)
                                                            <?php echo $trimstr = substr($str1, 0, 100) . '...'; ?>
                                                            <!-- Trigger/Open The Modal -->
                                                            <a>
                                                                <div id="myBtn{{ $mobileuser->id }}"
                                                                    onclick="mypopup('subspan{{ $mobileuser->id }}','Sub-Area' )">
                                                                    read more</div>
                                                            </a>
                                                        @else
                                                            {{ $mobileuser->sub_areas }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="/user/{{ $mobileuser->id }}"
                                                            class='btn red btn-outline btn-xs'
                                                            onclick="return confirm('Are you sure you want to reset this user?');">
                                                            Reset
                                                        </a>
                                                        <!-- <a id = {{ $mobileuser['id'] }} onclick = "document.getElementById('order-popup').style.display='block'"
                                                                                                                                                                                                                                                                                                                                                                            class='btn green btn-outline btn-xs order-view-popup'>Edit</a> -->
                                                        <a href="/user/{{ $mobileuser->id }}/edit"
                                                            class='btn green btn-outline btn-xs'>Edit</a>

                                                        <a id="{{ $mobileuser->id }}"
                                                            onclick="document.getElementById('order-popup').style.display='block'"
                                                            class='btn green btn-outline btn-xs password-view-popup'>Change
                                                            Password</a>
                                                        @if ($mobileuser->status == 'Active')
                                                            <a href="" class='btn btn-danger btn-xs'
                                                                onclick="event.preventDefault(); if(confirm('Are you sure you want to disable this user?')){ document.getElementById('mouser{{ $mobileuser->id }}').submit(); }">Disable</a>
                                                        @else
                                                            <a href="" class='btn btn-primary btn-xs'
                                                                onclick="event.preventDefault(); if(confirm('Are you sure you want to enable this user?')){ document.getElementById('mouser{{ $mobileuser->id }}').submit(); }">Enable</a>
                                                        @endif
                                                        <form action="/user/{{ $mobileuser->id }}" method="post"
                                                            id="mouser{{ $mobileuser->id }}">
                                                            {{ method_field('DELETE') }}
                                                            @csrf
                                                        </form>

                                                        {{-- <a class='btn btn-default btn-xs'><i class="glyphicon glyphicon-trash"></i></a> --}}
                                                    </td>
                                                </tr>
                                            @endforeach

                                            <!-- The Modal -->
                                            <div id="myModal" class="modal">
                                                <!-- Modal content -->
                                                <div class="modal-content">
                                                    <div class="breadcrumbs">
                                                        <h1>Areas</h1>
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

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- END EXAMPLE TABLE PORTLET-->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- END SIDEBAR CONTENT LAYOUT -->
    </div>
    <div id="order-popup">


    </div>
    <script>
        $(document).ready(function() {
            $(".order-view-popup").click(function() {
                var id = $(this).attr('id');
                $("#order-popup").load("/user/" + id + "/edit");
                $("#order-pop").css("display", "block");
                var modal = document.getElementById('order-popup');
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
            });
            // $(document).ready(function(){
            $(".password-view-popup").click(function() {
                var id = $(this).attr('id');
                $("#order-popup").load("/userPassword/" + id + "/edit");
                $("#order-pop").css("display", "block");
                var modal = document.getElementById('order-popup');
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
            });

        });

        // readmore popup model
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
