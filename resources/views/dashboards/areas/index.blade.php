@extends('dashboards.sessions.layout')

@section('content')
    <style>
        .dt-buttons {
            display: none;
        }

        .actions-cell {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            align-items: center;
        }

        .area-action-form {
            display: inline !important;
            margin: 0 !important;
        }

        .area-action-form button {
            margin: 0 !important;
        }
    </style>

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
                            <ul class="nav navbar-nav margin-bottom-35 areas-class">
                                <li><a href="/area/create">Add New Area </a></li>
                                <li class="active"><a href="/area">List Area </a></li>
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
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class=" icon-map font-red"></i>
                                        <span class="caption-subject font-red bold uppercase"> Area List</span>
                                    </div>
                                    <div class="tools"> </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="sample_2">
                                        <thead>
                                            <tr>
                                                <th> Areacode </th>
                                                <th> Name </th>
                                                <th>Status</th>
                                                <th> Actions </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($areas as $area)
                                                <tr>
                                                    <td>{!! $area->areacode !!}</td>
                                                    <td>{!! $area->area !!}</td>
                                                    <td>
                                                        @if ($area->status == 'Published')
                                                            <span style="color:green; font-weight:bold">Enabled </span>
                                                        @endif
                                                        @if ($area->status == 'Deleted')
                                                            <span style="color:Red; font-weight:bold">Disabled </span>
                                                        @endif
                                                    </td>
                                                    <td class="actions-cell">
                                                        <button type="button"
                                                            class="btn green btn-outline btn-xs order-view-popup"
                                                            data-area-id="{{ $area->areacode }}">
                                                            Edit
                                                        </button>

                                                        <button type="button"
                                                            class="btn btn-primary btn-outline btn-xs subarea-view-popup"
                                                            data-area-id="{{ $area->areacode }}">
                                                            Sub Area
                                                        </button>

                                                        @if ($area->status == 'Published')
                                                            <form action="/area/{{ $area->areacode }}" method="POST"
                                                                class="area-action-form"
                                                                onsubmit="return confirm('Are you sure you want to disable this area?');">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit"
                                                                    class="btn btn-danger btn-xs">Disable</button>
                                                            </form>
                                                        @elseif ($area->status == 'Deleted')
                                                            <form action="/area/{{ $area->areacode }}" method="POST"
                                                                class="area-action-form"
                                                                onsubmit="return confirm('Are you sure you want to enable this area?');">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit"
                                                                    class="btn btn-info btn-xs">Enable</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="order-popup"></div>
                            <script>
                                $(document).ready(function() {
                                    function setupModal(modal) {
                                        window.onclick = function(event) {
                                            if (event.target === modal) {
                                                modal.style.display = "none";
                                            }
                                        }
                                    }

                                    $(".order-view-popup").click(function() {
                                        var areaId = $(this).data('area-id');
                                        $("#order-popup").load("/area/" + encodeURIComponent(areaId) + "/edit");
                                        document.getElementById('order-popup').style.display = "block";
                                        setupModal(document.getElementById('order-popup'));
                                    });

                                    $(".subarea-view-popup").click(function() {
                                        var areaId = $(this).data('area-id');
                                        $("#order-popup").load("/subarea/" + encodeURIComponent(areaId));
                                        document.getElementById('order-popup').style.display = "block";
                                        setupModal(document.getElementById('order-popup'));
                                    });

                                    // Close modal when clicking outside
                                    $(document).click(function(e) {
                                        var modal = document.getElementById('order-popup');
                                        if ($(e.target).is(modal)) {
                                            modal.style.display = "none";
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
@endsection
