@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>All Purpose</h1>

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
                            <ul class="nav navbar-nav margin-bottom-35 bank-class">

                                <li><a href="/privacy-policy/create">Add </a></li>
                                <li class="active"><a href="/privacy-policy">List </a></li>


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
                                        <i class=" fa fa-user font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Purpose List</span>
                                    </div>

                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>SN</th>
                                                <th>Title</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($policies as $policy)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $policy->title }}</td>
                                                    <td>{{ $policy->status }}</td>
                                                    <td>
                                                        <a href="/privacy-policy/{{ $policy->id }}/edit"
                                                            class='btn green btn-outline btn-xs order-view-popup'>Edit</a>
                                                        <a href="/privacy-policy/status/{{ $policy->id }}"
                                                            class='btn green btn-outline btn-xs order-view-popup'
                                                            onclick="return confirm('Are you sure you want to change the status of this privacy policy?');">
                                                            @if ($policy->status == 'Active')
                                                                Deactivate
                                                            @else
                                                                Activate
                                                            @endif
                                                        </a>
                                                        <a class="btn btn-xs btn-primary"
                                                            onclick="if(confirm('Are you sure you want to delete this privacy policy?')){ document.getElementById('delete{{ $policy->id }}').submit(); }">
                                                            Delete </a>
                                                        <form action="/privacy-policy/{{ $policy->id }}" method="POST"
                                                            id="delete{{ $policy->id }}">
                                                            @csrf
                                                            {{ method_field('DELETE') }}
                                                        </form>
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
@endsection
