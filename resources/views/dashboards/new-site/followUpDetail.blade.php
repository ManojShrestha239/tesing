@extends('dashboards.sessions.layout')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/custom-site-registration.css') }}">
@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Site Registration</h1>
        </div>

        <div class="page-content-container">
            <div class="page-content-row" id="replecable">
                <div class="page-sidebar">
                    <div class="portlet light portlet-fit">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-bar font-red"></i>
                                <span class="caption-subject font-red bold uppercase">Navigation</span>
                            </div>

                        </div>
                        <nav class="navbar" role="navigation">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            @include('dashboards.dynamicNav.siteRegistration')
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

                    {{-- Main Content Here --}}
                    <div class="msjk-followup-wrapper">
                        <section>
                            <div class="msjk-whole-sectiom-container">
                                <div class="container-fluid">
                                    <div class="row msjk-header-section">
                                        <div class="col-sm-6">
                                            <h2 class="msjk-page-title">Follow-ups</h2>

                                            <p class="msjk-page-subtitle">
                                                Manage and track pending site actions and
                                                inspections.
                                            </p>
                                        </div>

                                        {{-- <div class="col-sm-6 text-right msjk-header-actions">
                                            <div class="btn-group msjk-view-toggle">
                                                <button type="button" class="btn btn-default active">
                                                    <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M8 6.00067L21 6.00139M8 12.0007L21 12.0015M8 18.0007L21 18.0015M3.5 6H3.51M3.5 12H3.51M3.5 18H3.51M4 6C4 6.27614 3.77614 6.5 3.5 6.5C3.22386 6.5 3 6.27614 3 6C3 5.72386 3.22386 5.5 3.5 5.5C3.77614 5.5 4 5.72386 4 6ZM4 12C4 12.2761 3.77614 12.5 3.5 12.5C3.22386 12.5 3 12.2761 3 12C3 11.7239 3.22386 11.5 3.5 11.5C3.77614 11.5 4 11.7239 4 12ZM4 18C4 18.2761 3.77614 18.5 3.5 18.5C3.22386 18.5 3 18.2761 3 18C3 17.7239 3.22386 17.5 3.5 17.5C3.77614 17.5 4 17.7239 4 18Z"
                                                                stroke="#000000" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </g>
                                                    </svg>
                                                    List
                                                </button>

                                                <button type="button" class="btn btn-default">
                                                    <svg fill="#000000" width="24px" height="24px" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M19,4H17V3a1,1,0,0,0-2,0V4H9V3A1,1,0,0,0,7,3V4H5A3,3,0,0,0,2,7V19a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V7A3,3,0,0,0,19,4Zm1,15a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V12H20Zm0-9H4V7A1,1,0,0,1,5,6H7V7A1,1,0,0,0,9,7V6h6V7a1,1,0,0,0,2,0V6h2a1,1,0,0,1,1,1Z">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                    Calendar
                                                </button>
                                            </div>

                                            <button type="button" class="btn btn-primary msjk-add-followup">
                                                <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                        stroke-linejoin="round"></g>
                                                    <g id="SVGRepo_iconCarrier">
                                                        <path d="M4 12H20M12 4V20" stroke="#ffffff" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </g>
                                                </svg>
                                                Add Follow-up
                                            </button>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <div class="msjk-whole-filter-container">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="msjk-filter-wrapper">
                                                <button type="button" class="btn msjk-filter-btn active">
                                                    All Active
                                                </button>

                                                <button type="button" class="btn msjk-filter-btn">
                                                    Today
                                                    @if (!empty($today) && $today > 0)
                                                        <span class="msjk-filter-count">({{ $today }})</span>
                                                    @endif
                                                </button>

                                                <button type="button" class="btn msjk-filter-btn">
                                                    Overdue
                                                    @if (!empty($overdue) && $overdue > 0)
                                                        <span class="msjk-filter-count">({{ $overdue }})</span>
                                                    @endif
                                                </button>

                                                <button type="button" class="btn msjk-filter-btn">
                                                    Upcoming
                                                    @if (!empty($upcoming) && $upcoming > 0)
                                                        <span class="msjk-filter-count">({{ $upcoming }})</span>
                                                    @endif
                                                </button>

                                                <button type="button" class="btn msjk-filter-btn">
                                                    Completed
                                                    @if (!empty($completed) && $completed > 0)
                                                        <span class="msjk-filter-count">({{ $completed }})</span>
                                                    @endif
                                                </button>
                                                <div class="msjk-search-wrapper">
                                                    <span class="glyphicon glyphicon-search msjk-search-icon"></span>
                                                    <input type="text" class="form-control msjk-search-input"
                                                        placeholder="Search by site name, type, or assigned to...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <div class="msjk-followup-container">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <div class="msjk-card-body">
                                                <div class="table-responsive">
                                                    <table class="table msjk-followup-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Site Name</th>
                                                                <th>Follow-up Type</th>
                                                                <th>Date</th>
                                                                <th>Status</th>
                                                                <th>Assigned To</th>
                                                                <th>Created By</th>
                                                                {{-- <th>Actions</th> --}}
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            @if (count($followUps) > 0)
                                                                @foreach ($followUps as $followUp)
                                                                    <tr class="msjk-active-row">
                                                                        <td>
                                                                            <strong>{{ $followUp->site->name }}</strong>
                                                                        </td>

                                                                        <td>{{ strtoupper($followUp->follow_up_type) }}</td>

                                                                        <td class="text-muted">
                                                                            {{ $followUp->follow_up_date }}
                                                                        </td>

                                                                        <td>
                                                                            <span class="msjk-status msjk-status-pending">
                                                                                <span class="msjk-status-dot"></span>
                                                                                {{ strtoupper($followUp->status) }}
                                                                            </span>
                                                                        </td>

                                                                        <td>
                                                                            @php
                                                                                $nameParts = array_filter(
                                                                                    explode(
                                                                                        ' ',
                                                                                        trim(
                                                                                            $followUp->assignedTo->name,
                                                                                        ),
                                                                                    ),
                                                                                );
                                                                                if (!empty($nameParts)) {
                                                                                    $firstLetter = strtoupper(
                                                                                        substr($nameParts[0], 0, 1),
                                                                                    );
                                                                                    $lastLetter = strtoupper(
                                                                                        substr(end($nameParts), 0, 1),
                                                                                    );
                                                                                    $initials =
                                                                                        $firstLetter . $lastLetter;
                                                                                } else {
                                                                                    $initials = '';
                                                                                }
                                                                            @endphp
                                                                            <div class="msjk-user">
                                                                                <span class="msjk-avatar">
                                                                                    {{ $initials }}
                                                                                </span>

                                                                                {{ $followUp->assignedTo->name }}
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            @php
                                                                                $createdByName =
                                                                                    $followUp->createdBy->name ??
                                                                                    $followUp->createdByDashboard->name;
                                                                                $nameParts = array_filter(
                                                                                    explode(' ', trim($createdByName)),
                                                                                );
                                                                                if (!empty($nameParts)) {
                                                                                    $firstLetter = strtoupper(
                                                                                        substr($nameParts[0], 0, 1),
                                                                                    );
                                                                                    $lastLetter = strtoupper(
                                                                                        substr(end($nameParts), 0, 1),
                                                                                    );
                                                                                    $initials =
                                                                                        $firstLetter . $lastLetter;
                                                                                } else {
                                                                                    $initials = '';
                                                                                }
                                                                            @endphp
                                                                            <div class="msjk-user">
                                                                                <span class="msjk-avatar">
                                                                                    {{ $initials }}
                                                                                </span>

                                                                                {{ $createdByName }}
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="6" class="text-center text-muted py-4">
                                                                        No follow-ups found.
                                                                    </td>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="panel-footer table-pagination-custom">

                                                    <div class="pagination-status text-on-surface-variant font-body-sm">
                                                        Showing {{ $followUps->firstItem() }}-{{ $followUps->lastItem() }}
                                                        of {{ $followUps->total() }} entries
                                                    </div>

                                                    <div class="pagination-controls">

                                                        @if ($followUps->onFirstPage())
                                                            <button class="btn btn-default msjk-nav-btn disabled" disabled
                                                                type="button">
                                                                <span class="glyphicon glyphicon-chevron-left"></span>
                                                            </button>
                                                        @else
                                                            <a href="{{ $followUps->previousPageUrl() }}"
                                                                class="btn btn-default msjk-nav-btn">
                                                                <span class="glyphicon glyphicon-chevron-left"></span>
                                                            </a>
                                                        @endif

                                                        <div class="page-numbers">
                                                            @php
                                                                $currentPage = $followUps->currentPage();
                                                                $lastPage = $followUps->lastPage();
                                                                $sideLinks = 1; // Controls the padding size around the current page
                                                            @endphp

                                                            @if ($currentPage == 1)
                                                                <button class="btn btn-primary active-page-btn"
                                                                    type="button">1</button>
                                                            @else
                                                                <a href="{{ $followUps->url(1) }}"
                                                                    class="btn btn-default page-num-btn">1</a>
                                                            @endif

                                                            @if ($currentPage > $sideLinks + 2)
                                                                <span class="pagination-ellipsis"
                                                                    style="padding: 6px 12px; color: #6c757d;">...</span>
                                                            @endif

                                                            @for ($i = max(2, $currentPage - $sideLinks); $i <= min($lastPage - 1, $currentPage + $sideLinks); $i++)
                                                                @if ($i == $currentPage)
                                                                    <button class="btn btn-primary active-page-btn"
                                                                        type="button">{{ $i }}</button>
                                                                @else
                                                                    <a href="{{ $followUps->url($i) }}"
                                                                        class="btn btn-default page-num-btn">{{ $i }}</a>
                                                                @endif
                                                            @endfor

                                                            @if ($currentPage < $lastPage - $sideLinks - 1)
                                                                <span class="pagination-ellipsis"
                                                                    style="padding: 6px 12px; color: #6c757d;">...</span>
                                                            @endif

                                                            @if ($lastPage > 1)
                                                                @if ($currentPage == $lastPage)
                                                                    <button class="btn btn-primary active-page-btn"
                                                                        type="button">{{ $lastPage }}</button>
                                                                @else
                                                                    <a href="{{ $followUps->url($lastPage) }}"
                                                                        class="btn btn-default page-num-btn">{{ $lastPage }}</a>
                                                                @endif
                                                            @endif
                                                        </div>

                                                        @if ($followUps->hasMorePages())
                                                            <a href="{{ $followUps->nextPageUrl() }}"
                                                                class="btn btn-default msjk-nav-btn">
                                                                <span class="glyphicon glyphicon-chevron-right"></span>
                                                            </a>
                                                        @else
                                                            <button class="btn btn-default msjk-nav-btn disabled" disabled
                                                                type="button">
                                                                <span class="glyphicon glyphicon-chevron-right"></span>
                                                            </button>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    </section>
                </div>
                {{-- End Main Content Here --}}

            </div>
        </div>
    </div>

    <!-- END SIDEBAR CONTENT LAYOUT -->
    </div>
    <script src="{{ URL::asset('js/custom-followUp.js') }}"></script>
@endsection
