@extends('dashboards.sessions.layout')

<link rel="stylesheet" href="{{ asset('css/custom-site-registration.css') }}">
@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Site Registration</h1>
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
                                <span class="caption-subject font-red bold uppercase">Site Registration</span>
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
                    @if (session('Status') || session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <b>{{ session('Status') ?? session('success') }}</b>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Main Content Here --}}
                    <div class="msjk-followup-wrapper">
                        <section>
                            <div class="msjk-site-header">
                                <div class="msjk-breadcrumb">
                                    <a href="#">Sites</a>
                                    <span>›</span>
                                    <span>{{ $site->name }}</span>
                                </div>

                                <div class="msjk-header-top">
                                    <div class="msjk-header-left">
                                        <div class="msjk-title-row">
                                            <h1 class="msjk-site-title">{{ $site->name }}</h1>

                                            <span class="msjk-site-status msjk-site-status-{{ $site->status }}">
                                                <span class="msjk-status-dot"></span>
                                                {{ ucwords(str_replace('_', ' ', $site->status)) }}
                                            </span>
                                        </div>

                                        {{-- <div class="msjk-site-location">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 22S19 16 19 10A7 7 0 1 0 5 10C5 16 12 22 12 22Z"
                                                    stroke="currentColor" stroke-width="2" />
                                                <circle cx="12" cy="10" r="2.5" stroke="currentColor"
                                                    stroke-width="2" />
                                            </svg>

                                            <span> 1420 Business Parkway, Metro District, NY 10001 </span>
                                        </div> --}}
                                    </div>

                                    <div class="msjk-header-actions">
                                        <a href="{{ route('site-registration-v2.edit', $site->id) }}"
                                            class="btn msjk-btn-outline" style="display:inline-flex;align-items:center;">
                                            <svg width="20px" height="20px" viewBox="0 0 17 17" version="1.1"
                                                xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                    stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <path
                                                        d="M15.529 2.857l-1.403-1.404c-0.565-0.566-1.555-0.566-2.122 0l-9.057 9.058-1.722 5.288 5.248-1.765 9.055-9.056c0.586-0.584 0.586-1.536 0.001-2.121zM3.094 13.294l0.645-1.979 1.934 1.935-1.963 0.66-0.616-0.616zM4.355 10.518l5.493-5.493 2.111 2.11-5.494 5.494-2.11-2.111zM10.555 4.317l0.729-0.729 2.111 2.11-0.729 0.729-2.111-2.11zM14.822 4.271l-0.72 0.72-2.111-2.11 0.72-0.721c0.189-0.189 0.518-0.189 0.707 0l1.403 1.404c0.196 0.196 0.196 0.512 0.001 0.707z"
                                                        fill="#000000"></path>
                                                </g>
                                            </svg>

                                            Edit Details
                                        </a>

                                        {{-- <button class="btn btn-primary msjk-btn-primary">
                                            <svg width="20px" height="20px" viewBox="0 -1 22 22" version="1.1"
                                                xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                    stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <title>camera [#952]</title>
                                                    <desc>Created with Sketch.</desc>
                                                    <defs></defs>
                                                    <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <g id="Dribbble-Light-Preview"
                                                            transform="translate(-378.000000, -3959.000000)" fill="#ffffff">
                                                            <g id="icons" transform="translate(56.000000, 160.000000)">
                                                                <path
                                                                    d="M326,3814 L326,3815 L327,3815 C327.552,3815 328,3815.448 328,3816 C328,3816.552 327.552,3817 327,3817 L326,3817 L326,3818 C326,3818.552 325.552,3819 325,3819 C324.448,3819 324,3818.552 324,3818 L324,3817 L323,3817 C322.448,3817 322,3816.552 322,3816 C322,3815.448 322.448,3815 323,3815 L324,3815 L324,3814 C324,3813.448 324.448,3813 325,3813 C325.552,3813 326,3813.448 326,3814 L326,3814 Z M334,3814.5 C332.897,3814.5 332,3813.603 332,3812.5 C332,3811.397 332.897,3810.5 334,3810.5 C335.103,3810.5 336,3811.397 336,3812.5 C336,3813.603 335.103,3814.5 334,3814.5 L334,3814.5 Z M334,3808.5 C331.791,3808.5 330,3810.291 330,3812.5 C330,3814.709 331.791,3816.5 334,3816.5 C336.209,3816.5 338,3814.709 338,3812.5 C338,3810.291 336.209,3808.5 334,3808.5 L334,3808.5 Z M337,3803 L331,3803 L331,3802 C331,3801.448 331.448,3801 332,3801 L336,3801 C336.552,3801 337,3801.448 337,3802 L337,3803 Z M343,3803 L343,3802 C343,3801.448 342.552,3801 342,3801 C341.448,3801 341,3801.448 341,3802 L341,3803 L339,3803 L339,3801 C339,3799.895 338.105,3799 337,3799 L331,3799 C329.895,3799 329,3799.895 329,3801 L329,3803 L326,3803 C324.895,3803 324,3803.895 324,3805 L324,3811 L326,3811 L326,3806 C326,3805.448 326.448,3805 327,3805 L341,3805 C341.552,3805 342,3805.448 342,3806 L342,3814 C342,3814.552 341.552,3815 341,3815 L340,3815 L340,3817 L342,3817 C343.105,3817 344,3816.105 344,3815 L344,3803 L343,3803 Z"
                                                                    id="camera-[#952]"></path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </g>
                                            </svg>

                                            Log Update
                                        </button> --}}
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="msjk-site-tabs">
                            <button type="button" class="msjk-site-tab-btn msjk-site-tab-active"
                                data-target="#msjk-site-overview">
                                Overview
                            </button>

                            <button type="button" class="msjk-site-tab-btn" data-target="#msjk-site-timeline">
                                Timeline
                            </button>

                            <button type="button" class="msjk-site-tab-btn" data-target="#msjk-site-contacts">
                                Contacts
                            </button>

                            <button type="button" class="msjk-site-tab-btn" data-target="#msjk-site-photos">
                                Photos
                            </button>

                            <button type="button" class="msjk-site-tab-btn" data-target="#msjk-site-products">
                                Products
                            </button>
                        </div>

                        <div class="msjk-site-tab-content">
                            <div id="msjk-site-overview" class="msjk-site-tab-pane msjk-site-tab-pane-active">
                                <div class="msjk-site-content-wrapper">
                                    <div class="row">
                                        <div class="col-12 col-md-9 msjk-site-content-wrapper-col">
                                            <div class="msjk-overview-section">
                                                <div class="container-fluid">
                                                    <div class="row msjk-stats-row">
                                                        <div class="col-lg-3 col-md-4 col-sm-6 col-12 msjk-stats-col">
                                                            <div class="msjk-stat-card">
                                                                <div class="msjk-stat-label">Total Floors</div>

                                                                <div class="msjk-stat-value">{{ $site->floors }}</div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 col-md-4 col-sm-6 col-12 msjk-stats-col">
                                                            <div class="msjk-stat-card">
                                                                <div class="msjk-stat-label">Built-up Area</div>

                                                                <div class="msjk-stat-value">
                                                                    {{ $site->approx_area_sqft }}

                                                                    <span class="msjk-stat-unit"> sqft </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 col-md-4 col-sm-6 col-12 msjk-stats-col">
                                                            <div class="msjk-stat-card">
                                                                <div class="msjk-stat-label">Paint Req.</div>

                                                                <div class="msjk-stat-value">
                                                                    {{ $site->estimated_paint_litres }}

                                                                    <span class="msjk-stat-unit"> Liters </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 col-md-4 col-sm-6 col-12 msjk-stats-col">
                                                            <div class="msjk-stat-card">
                                                                <div class="msjk-stat-label">Current Stage</div>

                                                                <div class="msjk-stat-value">
                                                                    {{ $site->currentStage->name ?? 'N/A' }}

                                                                    {{-- <span class="msjk-stat-unit">
                                                                        {{ $site->currentStage->name ?? 'N/A' }} </span> --}}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- <div class="col-lg-3 col-md-4 col-sm-6 col-12 msjk-stats-col">
                                                            <div class="msjk-stat-card msjk-completion-card">
                                                                <div class="msjk-stat-label">Completion</div>

                                                                <div class="msjk-completion-value">68%</div>

                                                                <div class="progress msjk-progress">
                                                                    <div class="progress-bar" role="progressbar"
                                                                        style="width: 68%"></div>
                                                                </div>
                                                            </div>
                                                        </div> --}}
                                                    </div>
                                                </div>
                                                <div class="msjk-site-specification-section-container">
                                                    <div class="msjk-site-specification-section">
                                                        <div class="msjk-card">
                                                            <div class="msjk-card-body">
                                                                <div class="row">
                                                                    <div class="col-lg-4 col-md-6 col-12 msjk-spec-col">
                                                                        <div class="msjk-card-header">
                                                                            <h4 class="msjk-card-title">
                                                                                Site Specifications
                                                                            </h4>
                                                                        </div>
                                                                        <div class="msjk-spec-item">
                                                                            <span class="msjk-spec-label">
                                                                                Project Type
                                                                            </span>

                                                                            <span class="msjk-spec-value">
                                                                                {{ $site->siteType->name ?? 'N/A' }}
                                                                            </span>
                                                                        </div>

                                                                        <div class="msjk-spec-item">
                                                                            <span class="msjk-spec-label">
                                                                                Primary Contractor
                                                                            </span>

                                                                            <span class="msjk-spec-value msjk-blue-text">
                                                                                {{ $site->contractor->name ?? 'N/A' }}
                                                                            </span>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="col-lg-4 col-md-6 col-12 msjk-spec-col msjk-spec-col-divider">
                                                                        <div class="msjk-spec-item">
                                                                            <span class="msjk-spec-label">
                                                                                Preferred Brand
                                                                            </span>

                                                                            <span class="msjk-spec-value">
                                                                                {{ $site->preferred_brand ?? 'N/A' }}
                                                                            </span>
                                                                        </div>

                                                                        <div class="msjk-spec-item">
                                                                            <span class="msjk-spec-label">
                                                                                Competitor Brand
                                                                            </span>

                                                                            <span class="msjk-spec-approved-brands">
                                                                                <span
                                                                                    class="msjk-spec-approved-value">{{ $site->competitor_brand_observed ?? 'N/A' }}</span>
                                                                                {{-- <span
                                                                                    class="msjk-spec-approved-value">Asian
                                                                                    Paints</span> --}}
                                                                            </span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-4 col-md-6 col-12 msjk-map-col">
                                                                        <iframe
                                                                            src="https://maps.google.com/maps?q={{ $site->lat }},{{ $site->lng }}&z=15&output=embed"
                                                                            width="600" height="450" loading="lazy"
                                                                            referrerpolicy="no-referrer-when-downgrade">
                                                                        </iframe>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="msjk-skyline-card">
                                                <div class="msjk-card-header">
                                                    <h4 class="msjk-card-key-title">Key Contacts</h4>
                                                    @php
                                                        $totalContacts = count($site->contacts);
                                                    @endphp
                                                    @if ($totalContacts > 0)
                                                        <div class="text-center">
                                                            <button type="button" class="msjk-view-all msjk-site-tab-btn"
                                                                data-target="#msjk-site-contacts">
                                                                View All
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="msjk-card-body msjk-contact-list">
                                                    @if ($totalContacts > 0)
                                                        @foreach ($site->contacts->take(3) as $contact)
                                                            <div class="msjk-contact-item">
                                                                <div class="msjk-contact-avatar">
                                                                    {{ Str::of($contact->name)->explode(' ')->map(fn($w) => Str::substr($w, 0, 1))->join('') }}
                                                                </div>
                                                                <div class="msjk-contact-info">
                                                                    <div class="msjk-contact-name">{{ $contact->name }}
                                                                    </div>
                                                                    <div class="msjk-contact-role">{{ $contact->role }}
                                                                    </div>
                                                                    <a href="tel:{{ $contact->phone }}"
                                                                        class="msjk-contact-phone">
                                                                        {{ $contact->phone }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="msjk-no-contacts">
                                                            <p>No contacts added yet.</p>
                                                            {{-- <a href="#"
                                                                class="btn btn-primary msjk-add-contact-btn">
                                                                + Add Contact
                                                            </a> --}}
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="msjk-skyline-card" style="margin-top: 24px">
                                                <div class="msjk-card-header">
                                                    <h4 class="msjk-card-key-title">Activity Timeline</h4>

                                                    <span class="msjk-timeline-filter"> Recent </span>
                                                </div>

                                                <div class="msjk-card-body">
                                                    <div class="msjk-timeline">
                                                        @forelse ($site->activityLogs->take(3) as $activityLog)
                                                            @php
                                                                $createByName =
                                                                    $activityLog->causer->name ??
                                                                    $activityLog->causerDashboard->name;
                                                            @endphp
                                                            <div class="msjk-timeline-item">
                                                                <div class="msjk-timeline-icon msjk-success">
                                                                    <i class="fa-solid fa-times-circle"></i>
                                                                </div>

                                                                <div class="msjk-timeline-content">
                                                                    <div class="msjk-timeline-header">
                                                                        <span class="msjk-timeline-title">
                                                                            {{ $activityLog->event }}
                                                                        </span>

                                                                        <span class="msjk-timeline-time">
                                                                            {{ $activityLog->created_at->diffForHumans() }}
                                                                        </span>
                                                                    </div>

                                                                    <div class="msjk-timeline-desc">
                                                                        {{ $activityLog->description }}
                                                                    </div>

                                                                    <div class="msjk-timeline-user">
                                                                        <span
                                                                            class="msjk-activity-avatar">{{ Str::of($createByName)->explode(' ')->map(fn($w) => Str::substr($w, 0, 1))->join('') }}</span>
                                                                        <span>{{ $createByName }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="msjk-no-activity">
                                                                <p>No activity logged yet.</p>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                    @if (count($site->activityLogs) > 3)
                                                        <div class="text-center">
                                                            <button type="button" class="msjk-view-all msjk-site-tab-btn"
                                                                data-target="#msjk-site-timeline">
                                                                View All Activity
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="msjk-site-timeline" class="msjk-site-tab-pane">
                            <div class="msjk-site-content-wrapper-time">
                                <div class="msjk-timeline-wrapper">
                                    @forelse ($site->activityLogs as $activityLog)
                                        <!-- Item -->
                                        <div class="msjk-site-timeline-item" data-id="{{ $activityLog->id }}">
                                            <div class="msjk-site-timeline-left">
                                                <div class="msjk-site-timeline-icon">
                                                    🕧
                                                </div>
                                            </div>

                                            <div class="msjk-site-timeline-card">
                                                <div class="msjk-site-timeline-top">
                                                    <span class="msjk-site-timeline-date">
                                                        {{ $activityLog->created_at->diffForHumans() }} </span>
                                                </div>

                                                <h4 class="msjk-site-timeline-title">
                                                    {{ $activityLog->event }}
                                                </h4>

                                                <p class="msjk-site-timeline-desc">
                                                    {{ $activityLog->description }}
                                                </p>

                                                <div class="msjk-site-timeline-user">
                                                    <svg width="20px" height="20px" viewBox="0 0 20 20"
                                                        version="1.1" xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <title>profile_round [#667085]</title>
                                                            <desc>Created with Sketch.</desc>
                                                            <defs></defs>
                                                            <g id="Page-1" stroke="none" stroke-width="1"
                                                                fill="none" fill-rule="evenodd">
                                                                <g id="Dribbble-Light-Preview"
                                                                    transform="translate(-140.000000, -2159.000000)"
                                                                    fill="#667085">
                                                                    <g id="icons"
                                                                        transform="translate(56.000000, 160.000000)">
                                                                        <path
                                                                            d="M100.562548,2016.99998 L87.4381713,2016.99998 C86.7317804,2016.99998 86.2101535,2016.30298 86.4765813,2015.66198 C87.7127655,2012.69798 90.6169306,2010.99998 93.9998492,2010.99998 C97.3837885,2010.99998 100.287954,2012.69798 101.524138,2015.66198 C101.790566,2016.30298 101.268939,2016.99998 100.562548,2016.99998 M89.9166645,2004.99998 C89.9166645,2002.79398 91.7489936,2000.99998 93.9998492,2000.99998 C96.2517256,2000.99998 98.0830339,2002.79398 98.0830339,2004.99998 C98.0830339,2007.20598 96.2517256,2008.99998 93.9998492,2008.99998 C91.7489936,2008.99998 89.9166645,2007.20598 89.9166645,2004.99998 M103.955674,2016.63598 C103.213556,2013.27698 100.892265,2010.79798 97.837022,2009.67298 C99.4560048,2008.39598 100.400241,2006.33098 100.053171,2004.06998 C99.6509769,2001.44698 97.4235996,1999.34798 94.7348224,1999.04198 C91.0232075,1998.61898 87.8750721,2001.44898 87.8750721,2004.99998 C87.8750721,2006.88998 88.7692896,2008.57398 90.1636971,2009.67298 C87.1074334,2010.79798 84.7871636,2013.27698 84.044024,2016.63598 C83.7745338,2017.85698 84.7789973,2018.99998 86.0539717,2018.99998 L101.945727,2018.99998 C103.221722,2018.99998 104.226185,2017.85698 103.955674,2016.63598"
                                                                            id="profile_round-[#667085]"></path>
                                                                    </g>
                                                                </g>
                                                            </g>
                                                        </g>
                                                    </svg>
                                                    <span>Logged by:
                                                        {{ $activityLog->causer->name ?? $activityLog->causerDashboard->name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="msjk-no-timeline">
                                            <p>No timeline events yet.</p>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Hidden input for site ID -->
                                <input type="hidden" id="site-id" value="{{ $site->id }}">
                                {{-- <input type="hidden" id="last_id" value="{{ $activityLog->id }}"> --}}

                                <!-- Load More Button -->
                                @if ($site->activityLogs->count() >= 5)
                                    <div class="text-center">
                                        <button type="button" class="msjk-view-all msjk-site-tab-btn"
                                            id="load-more-activity">
                                            Load More
                                        </button>
                                    </div>
                                @endif

                                <!-- Container for new logs -->
                                <div id="activity-logs-container"></div>

                            </div>
                        </div>

                        <div id="msjk-site-contacts" class="msjk-site-tab-pane">
                            <div class="msjk-site-content-wrapper-contact">
                                <div class="msjk-contact-section">
                                    <div class="msjk-contact-header">
                                        <h2 class="msjk-contact-heading">Key Contacts</h2>

                                        <a href="" class="btn btn-primary msjk-contact-add-btn">
                                            + Add Contact
                                        </a>
                                    </div>

                                    <div class="row msjk-contact-section-row">
                                        <div class="col-12 col-md-6 col-lg-4 msjk-contact-section-col">
                                            <div class="msjk-contact-card">
                                                <div class="msjk-contact-avatar">
                                                    {{ Str::of($site->contractor->name)->explode(' ')->map(fn($w) => Str::substr($w, 0, 1))->join('') }}
                                                </div>

                                                <div class="msjk-contact-content">
                                                    <h4 class="msjk-contact-name">{{ $site->contractor->name }}
                                                        ({{ $site->contractor->pass_book_no }})</h4>

                                                    <div class="msjk-contact-role"> Contractor </div>

                                                    <a href="tel: {{ $site->contractor->contact }}"
                                                        class="msjk-contact-phone">
                                                        {{ $site->contractor->contact }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4 msjk-contact-section-col">
                                            <div class="msjk-contact-card">
                                                <div class="msjk-contact-avatar">
                                                    {{ Str::of($site->dealer->name)->explode(' ')->map(fn($w) => Str::substr($w, 0, 1))->join('') }}
                                                </div>

                                                <div class="msjk-contact-content">
                                                    <h4 class="msjk-contact-name">{{ $site->dealer->name }}
                                                        ({{ $site->dealer->code }})</h4>

                                                    <div class="msjk-contact-role"> Dealer </div>

                                                    <a href="tel: {{ $site->dealer->mobile }}"
                                                        class="msjk-contact-phone">
                                                        {{ $site->dealer->mobile ?? 'N/A' }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @forelse($site->contacts as $contact)
                                            <div class="col-12 col-md-6 col-lg-4 msjk-contact-section-col">
                                                <div class="msjk-contact-card">
                                                    <div class="msjk-contact-avatar">
                                                        {{ Str::of($contact->name)->explode(' ')->map(fn($w) => Str::substr($w, 0, 1))->join('') }}
                                                    </div>

                                                    <div class="msjk-contact-content">
                                                        <h4 class="msjk-contact-name">{{ $contact->name }}</h4>

                                                        <div class="msjk-contact-role">{{ $contact->role }}</div>

                                                        <a href="tel: {{ $contact->phone }}" class="msjk-contact-phone">
                                                            {{ $contact->phone }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="msjk-no-contacts">
                                                <p>No contacts added yet.</p>
                                                <a href="#" class="btn btn-primary msjk-add-contact-btn">
                                                    + Add Contact
                                                </a>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="msjk-site-photos" class="msjk-site-tab-pane">
                            <div class="msjk-site-content-wrapper-photo">
                                <div class="msjk-gallery-card">
                                    <div class="msjk-gallery-header">
                                        <h3 class="msjk-gallery-title">Site Gallery</h3>

                                        <div class="msjk-gallery-filters">
                                            <button class="msjk-gallery-filter msjk-gallery-filter-active"
                                                data-filter="all">
                                                All Photos
                                            </button>
                                            <button class="msjk-gallery-filter" data-filter="site_photo">
                                                Site
                                            </button>
                                            <button class="msjk-gallery-filter" data-filter="progress_photo">
                                                Progress
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row msjk-contact-section-row">
                                        @forelse ($site->photos as $photo)
                                            <div class="col-lg-3 col-md-4 col-sm-2 col-12 msjk-contact-section-col"
                                                data-category="{{ $photo->photo_type }}">
                                                <div class="msjk-gallery-item">
                                                    <img src="{{ $photo->file_path }}" alt="" />

                                                    <div class="msjk-gallery-overlay">
                                                        <h4>{{ Str::headline($photo->photo_type) }}</h4>

                                                        <span>{{ $photo->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="msjk-no-photos">
                                                <p>No photos uploaded yet.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="msjk-site-products" class="msjk-site-tab-pane">
                            <div class="msjk-site-content-wrapper-product">
                                <div class="msjk-product-card">
                                    <div class="msjk-product-header">
                                        <h3 class="msjk-product-title">Product Assignment & Usage</h3>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table msjk-product-table">
                                            <thead>
                                                <tr>
                                                    <th>PRODUCT NAME</th>
                                                    <th>CATEGORY</th>
                                                    <th>QUANTITY</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($site->products as $product)
                                                    <tr>
                                                        <td>{{ $product->name }}</td>
                                                        <td>{{ $product->types }}</td>
                                                        <td>{{ $product->pivot->quantity }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">
                                                            No products assigned yet.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- End Main Content Here --}}

                </div>
            </div>
        </div>

        <!-- END SIDEBAR CONTENT LAYOUT -->
    </div>
    <script src="{{ URL::asset('js/custom-site-detail.js') }}"></script>
    <script src="{{ URL::asset('js/custom-site-activities.js') }}"></script>
@endsection
