@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>All Roles</h1>

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
                            @include('dashboards.dynamicNav.miscellaneousNav')
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
                                        <i class=" fa fa-user font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Roles List</span>
                                    </div>

                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($roles as $data)
                                                <tr>
                                                    <td>{!! $data->role !!}</td>
                                                    <td>{{ $data->status }}</td>
                                                    <td>
                                                        <a id="{{ $data->id }}"
                                                            onclick="document.getElementById('order-popup').style.display='block'"
                                                            class='btn green btn-outline btn-xs order-view-popup'>Edit</a>

                                                        <!-- <a class="btn btn-xs @if ($data->status == 'Active') btn-danger @else btn-primary @endif"  onclick="document.getElementById('delete{{ $data->id }}').submit();"> @if ($data->status == 'Active')
    Deactivate
@else
    Activate
    @endif </a>
                        <form action="/role/{{ $data->id }}" method="POST" id="delete{{ $data->id }}">
                        @csrf
                            {{ method_field('DELETE') }}
                        </form> -->
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                                {{-- pop up after clicking view  --}}
                                <div id="order-popup">

                                </div>
                                <script>
                                    $(document).ready(function() {
                                        $(".order-view-popup").click(function() {
                                            var id = $(this).attr('id');
                                            $("#order-popup").load("/role/" + id + "/edit");
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
