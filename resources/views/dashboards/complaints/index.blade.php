@extends('dashboards.sessions.layout')

@section('content')
    <link href="{{ asset('css/popup-readmore.css') }}" rel="stylesheet">
    {{-- <style>
        .dt-buttons {
            display: none;
        }

        .tab-nav {
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .tab-nav>a {
            margin-left: 15px;
        }

        .tab_active {
            text-decoration: underline;
        }

    </style> --}}

    </style>
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Complaint List</h1>
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
                            <ul class="nav navbar-nav margin-bottom-35 dealer-class">
                                <li class="active"><a href="/complaint">View Complaint</a></li>
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
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase">Complaint List</span>
                                    </div>

                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Title</th>
                                                <th>Details</th>
                                                {{-- <th>Customer Details</th>
                                                <th>Product Details</th> --}}
                                                <th>Status</th>
                                                <th>Created on</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @if (!empty($complaints)) --}}
                                            @forelse($complaints as $complaint)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $complaint->issuetype['type'] }}</td>
                                                    <td>
                                                        {{ $complaint->issue_desc }}
                                                    </td>
                                                    <td>{{ $complaint->status }}</td>
                                                    <td>{{ $complaint->created_at }}</td>
                                                    <td>
                                                        <a href="/complaint/{{ $complaint->id }}"
                                                            class='btn green btn-outline btn-xs'>View file</a>
                                                        @if ($complaint->issuetype['type'] == 'Product')
                                                            {{-- <a id="{{ $complaint->id }}" onclick="document.getElementById('order-popup').style.display='block'" class='btn green btn-outline btn-xs complaint-view-popup' style="margin-right:5px;">Complaint Details</a> --}}
                                                        @endif
                                                        @if ($complaint->status == 'Open')
                                                            <a onclick="event.preventDefault(); if(confirm('Are you sure you want to close this complaint?')){ document.getElementById('change-status{{ $complaint->id }}').submit(); }"
                                                                href="/complaint/{{ $complaint->id }}"
                                                                class='btn green btn-outline btn-xs'
                                                                style="margin-right:5px;">Closed</a>
                                                            <form id="change-status{{ $complaint->id }}" method="POST"
                                                                action="{{ route('complaint.destroy', $complaint->id) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endif

                                                    </td>

                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="105"
                                                        style="text-align: center; color: red; font-weight:700;">
                                                        <span>No
                                                            data found.</span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                            {{-- @endif --}}

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
    <div id="order-popup">

    </div>
    <script>
        $(document).ready(function() {
            $(".complaint-view-popup").click(function() {
                var id = $(this).attr('id');
                $("#order-popup").load("/complaint/" + id + "/edit");
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
@endsection
