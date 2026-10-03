@extends('dashboards.sessions.layout')

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/custom-site-registration.css') }}">

@section('content')

    {{-- ============================================================ --}}
    {{--  Responsive overrides for the Site Registration page         --}}
    {{--  Mobile-first, additive — does not affect desktop layout.    --}}
    {{-- ============================================================ --}}
    <style>
        /* ---------- Global safety: prevent horizontal overflow ---------- */
        .msjk-site-content-wrapper {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* ---------- Layout row: sidebar + content ---------- */
        .page-content-row {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 20px;
            max-width: 100%;
        }

        .page-content-row .page-sidebar {
            flex: 0 0 240px;
            max-width: 240px;
        }

        .page-content-row .page-content-col {
            flex: 1 1 0;
            min-width: 0;
        }

        @media (max-width: 991px) {

            .page-content-row .page-sidebar,
            .page-content-row .page-content-col {
                flex: 1 1 100%;
                max-width: 100%;
            }
        }

        /* ---------- Page header (title + buttons) ---------- */
        .msjk-sites-header-section .row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            row-gap: 12px;
        }

        .msjk-site-header-btns {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .msjk-site-header-btns .msjk-site-export-btn,
        .msjk-site-header-btns .msjk-register-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            white-space: nowrap;
        }

        @media (max-width: 767px) {
            .msjk-site-header-btns {
                justify-content: stretch;
                width: 100%;
            }

            .msjk-site-header-btns .msjk-site-export-btn,
            .msjk-site-header-btns .msjk-register-btn {
                flex: 1 1 100%;
                justify-content: center;
                text-align: center;
            }
        }

        /* ============================================================ */
        /*  Advanced Filters — layout + interaction states              */
        /* ============================================================ */

        /* Form itself must never exceed parent */
        .msjk-filters-form {
            width: 100%;
            max-width: 100%;
        }

        /* Global box-sizing for anything inside the filter form */
        .msjk-filters-form *,
        .msjk-filters-form *::before,
        .msjk-filters-form *::after {
            box-sizing: border-box;
        }

        /* Row negative-margin fix: reduces gutter from 15px to 7px and
                                                                                                                                                                   keeps the row inside the card. */
        .msjk-filters-form .msjk-filter-row {
            margin-left: -7px;
            margin-right: -7px;
            row-gap: 14px;
            flex-wrap: wrap;
        }

        .msjk-filters-form .msjk-filter-row>[class*="col-"] {
            padding-left: 7px;
            padding-right: 7px;
            min-width: 0;
            /* allows flex children to shrink */
            max-width: 100%;
        }

        .msjk-filters-form .msjk-form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 0;
            width: 100%;
            max-width: 100%;
        }

        .msjk-filters-form .msjk-form-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
            color: #5b6167;
            margin: 0;
        }

        .msjk-filters-form .msjk-form-control {
            width: 100%;
            max-width: 100%;
            height: 42px;
            border-radius: 6px;
            border: 1px solid #d7dce2;
            background: #fff;
            font-size: 14px;
            padding: 8px 12px;
            transition: border-color .15s ease, box-shadow .15s ease;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .msjk-filters-form .msjk-form-control:focus {
            outline: none;
            border-color: #dd3234;
            box-shadow: 0 0 0 3px rgba(58, 139, 253, .15);
        }

        /* Custom dropdown arrow since we disabled native appearance */
        .msjk-filters-form select.msjk-form-control {
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235b6167' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            padding-right: 34px;
        }

        /* ---------- Search input with clear button ---------- */
        .msjk-search-wrap {
            position: relative;
            width: 100%;
            max-width: 100%;
        }

        .msjk-search-wrap .msjk-form-control {
            padding-right: 36px;
        }

        .msjk-clear-search {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            line-height: 20px;
            text-align: center;
            border-radius: 50%;
            background: #e6e9ed;
            color: #5b6167;
            text-decoration: none;
            font-size: 16px;
            font-weight: 700;
        }

        .msjk-clear-search:hover {
            background: #d7dce2;
            color: #1f2937;
            text-decoration: none;
        }

        /* ---------- Action row ---------- */
        .msjk-filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px dashed #e6e9ed;
            flex-wrap: wrap;
        }

        .msjk-filter-reset,
        .msjk-filter-apply {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 40px;
            padding: 0 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: background-color .15s ease, color .15s ease;
            white-space: nowrap;
        }

        .msjk-filter-reset {
            background: #f3f5f7;
            color: #4b5563;
            border-color: #e6e9ed;
        }

        .msjk-filter-reset:hover {
            background: #e6e9ed;
            color: #1f2937;
            text-decoration: none;
        }

        .msjk-filter-apply {
            background: #dd3234;
            color: #fff;
        }

        .msjk-filter-apply:hover {
            background: #1f6fe0;
            color: #fff;
        }

        /* ---------- Mobile layout for filters ---------- */
        @media (max-width: 575px) {
            .msjk-filters-form .msjk-filter-row {
                margin-left: 0;
                margin-right: 0;
            }

            .msjk-filters-form .msjk-filter-row>[class*="col-"] {
                flex: 0 0 100%;
                max-width: 100%;
                padding-left: 0;
                padding-right: 0;
            }

            .msjk-filter-actions {
                flex-direction: column-reverse;
                gap: 8px;
            }

            .msjk-filter-reset,
            .msjk-filter-apply {
                width: 100%;
                height: 44px;
            }

            /* Give card body breathing room */
            .msjk-registersites-card .msjk-card-body {
                padding: 12px;
            }

            .msjk-registersites-card .msjk-registered-card-header {
                padding: 12px;
            }

            /* Reduce container side padding on phones */
            .msjk-sites-filter-section .container-fluid,
            .msjk-sites-header-section .container-fluid,
            .msjk-sites-table-section .container-fluid {
                padding-left: 8px;
                padding-right: 8px;
            }
        }

        /* ---------- Loading state while submitting ---------- */
        .msjk-filters-form.is-submitting {
            opacity: 0.6;
            pointer-events: none;
        }

        .msjk-filters-form.is-submitting::after {
            content: "";
            display: block;
            height: 2px;
            margin-top: 8px;
            background: linear-gradient(90deg, transparent, #dd3234, transparent);
            animation: msjk-shimmer 1.2s infinite linear;
        }

        @keyframes msjk-shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        /* ============================================================ */
        /*  Table                                                       */
        /* ============================================================ */
        .msjk-card-body-table {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .msjk-sites-table {
            min-width: 1000px;
            margin-bottom: 0;
        }

        .msjk-sites-table th,
        .msjk-sites-table td {
            vertical-align: middle;
            white-space: nowrap;
        }

        .msjk-sites-table td .msjk-site-info {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 180px;
        }

        .msjk-sites-table .msjk-site-thumb img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }

        .msjk-sites-table .msjk-site-name {
            font-weight: 600;
            white-space: normal;
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        @media (max-width: 991px) {
            .msjk-sites-table {
                min-width: 900px;
            }
        }

        /* ============================================================ */
        /*  Pagination footer                                           */
        /* ============================================================ */
        .table-pagination-custom {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
        }

        .table-pagination-custom .pagination-status {
            font-size: 13px;
            color: #555;
        }

        .table-pagination-custom .pagination-controls {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .table-pagination-custom .page-numbers {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        @media (max-width: 767px) {
            .table-pagination-custom {
                justify-content: center;
                text-align: center;
            }

            .table-pagination-custom .pagination-status {
                flex: 1 1 100%;
                order: 2;
            }

            .table-pagination-custom .pagination-controls {
                flex: 1 1 100%;
                justify-content: center;
                order: 1;
            }

            .table-pagination-custom .page-numbers {
                display: none;
            }

            .table-pagination-custom .msjk-nav-btn {
                min-width: 48px;
                min-height: 44px;
            }
        }

        /* ---------- Touch target minimums ---------- */
        @media (hover: none) and (pointer: coarse) {

            .msjk-site-header-btns a,
            .msjk-filters-form .msjk-form-control,
            .msjk-sites-table a {
                min-height: 44px;
            }

            .msjk-register-btn {
                padding: 10px 16px;
            }
        }

        /* Skeleton loader */
        .msjk-skeleton-row td {
            padding: 12px 8px;
        }

        .msjk-skeleton-bar {
            display: block;
            height: 14px;
            border-radius: 4px;
            background: linear-gradient(90deg, #eef1f5 25%, #e2e6eb 37%, #eef1f5 63%);
            background-size: 400% 100%;
            animation: msjk-skeleton 1.4s ease infinite;
        }

        @keyframes msjk-skeleton {
            0% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0 0;
            }
        }
    </style>

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
                                <i class="icon-bar font-red"></i>
                                <span class="caption-subject font-red bold uppercase">Navigation</span>
                            </div>
                        </div>
                        <nav class="navbar" role="navigation">
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

                    <div class="msjk-followup-wrapper msjk-site-content-wrapper">

                        {{-- ============ Page header ============ --}}
                        <div class="msjk-sites-header-section">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12 col-md-7">
                                        <h1 class="msjk-page-title">Registered Sites</h1>
                                        <p class="msjk-page-description">
                                            Manage and track all active construction sites across regions.
                                        </p>
                                    </div>

                                    <div class="col-12 col-md-5 msjk-site-header-btns">
                                        <a href="{{ route('export.site.registrations.v2') }}" class="msjk-site-export-btn">
                                            <i class="fa fa-file-excel-o"></i> Export to Excel
                                        </a>

                                        <a class="msjk-register-btn" href="{{ route('site-registration-v2.create') }}">
                                            <svg fill="#ffffff" width="20" height="20" viewBox="0 0 32 32"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                    stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <path
                                                        d="M16 0c-8.836 0-16 7.163-16 16s7.163 16 16 16c8.837 0 16-7.163 16-16s-7.163-16-16-16zM16 30.032c-7.72 0-14-6.312-14-14.032s6.28-14 14-14 14 6.28 14 14-6.28 14.032-14 14.032zM23 15h-6v-6c0-0.552-0.448-1-1-1s-1 0.448-1 1v6h-6c-0.552 0-1 0.448-1 1s0.448 1 1 1h6v6c0 0.552 0.448 1 1 1s1-0.448 1-1v-6h6c0.552 0 1-0.448 1-1s-0.448-1-1-1z">
                                                    </path>
                                                </g>
                                            </svg>
                                            <span>Register New Site</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============ Advanced Filters (fully functional) ============ --}}
                        <div class="msjk-sites-filter-section">
                            <div class="container-fluid">
                                <div class="msjk-registersites-card">

                                    <div class="msjk-registered-card-header">
                                        <svg width="20" height="20" viewBox="0 0 16 16" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                            </g>
                                            <g id="SVGRepo_iconCarrier">
                                                <path d="M0 3H16V1H0V3Z" fill="#87898b"></path>
                                                <path d="M2 7H14V5H2V7Z" fill="#87898b"></path>
                                                <path d="M4 11H12V9H4V11Z" fill="#87898b"></path>
                                                <path d="M10 15H6V13H10V15Z" fill="#87898b"></path>
                                            </g>
                                        </svg>
                                        <h4 class="msjk-registered-card-title">Advanced Filters</h4>
                                    </div>

                                    <div class="msjk-card-body">
                                        <form method="GET" action="{{ url()->current() }}"
                                            data-base-url="{{ url()->current() }}" id="siteFiltersForm"
                                            class="msjk-filters-form">

                                            {{-- Preserve non-filter query params (e.g. per_page) --}}
                                            @foreach (request()->except(['stage_id', 'type_id', 'status', 'search', 'page']) as $key => $value)
                                                @if (is_scalar($value))
                                                    <input type="hidden" name="{{ $key }}"
                                                        value="{{ $value }}">
                                                @endif
                                            @endforeach

                                            <div class="row msjk-filter-row">

                                                {{-- Current Stage --}}
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                    <div class="msjk-form-group">
                                                        <label class="msjk-form-label" for="filter_stage">Current
                                                            Stage</label>
                                                        <select name="stage_id" id="filter_stage"
                                                            class="form-control msjk-form-control js-auto-submit">
                                                            <option value="">All Stages</option>
                                                            @if (!empty($stages))
                                                                @foreach ($stages as $stage)
                                                                    <option value="{{ $stage->id }}"
                                                                        @selected((string) request('stage_id') === (string) $stage->id)>
                                                                        {{ strtoupper($stage->name) }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Site Type --}}
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                    <div class="msjk-form-group">
                                                        <label class="msjk-form-label" for="filter_type">Site Type</label>
                                                        <select name="type_id" id="filter_type"
                                                            class="form-control msjk-form-control js-auto-submit">
                                                            <option value="">All Types</option>
                                                            @if (!empty($siteTypes))
                                                                @foreach ($siteTypes as $type)
                                                                    <option value="{{ $type->id }}"
                                                                        @selected((string) request('type_id') === (string) $type->id)>
                                                                        {{ strtoupper($type->name) }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Status --}}
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                    <div class="msjk-form-group">
                                                        <label class="msjk-form-label" for="filter_status">Status</label>
                                                        <select name="status" id="filter_status"
                                                            class="form-control msjk-form-control js-auto-submit">
                                                            <option value="">All Statuses</option>
                                                            @foreach ([
            'new_registration' => 'New',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'lost' => 'Lost',
        ] as $value => $label)
                                                                <option value="{{ $value }}"
                                                                    @selected(request('status') === $value)>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Search --}}
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                    <div class="msjk-form-group">
                                                        <label class="msjk-form-label" for="filter_search">Search</label>
                                                        <div class="msjk-search-wrap">
                                                            <input type="text" name="search" id="filter_search"
                                                                class="form-control msjk-form-control js-search-input"
                                                                placeholder="Registration no, name, dealer..."
                                                                autocomplete="off" value="{{ request('search') }}">
                                                            @if (request('search'))
                                                                <a href="{{ url()->current() . '?' . http_build_query(request()->except('search', 'page')) }}"
                                                                    class="msjk-clear-search"
                                                                    title="Clear search">&times;</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            {{-- Action row --}}
                                            <div class="msjk-filter-actions">
                                                <a href="{{ url()->current() }}" class="msjk-filter-reset">
                                                    <i class="fa fa-refresh"></i> Reset
                                                </a>
                                                <button type="submit" class="msjk-filter-apply">
                                                    <i class="fa fa-search"></i> Apply Filters
                                                </button>
                                            </div>

                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ============ Sites table ============ --}}
                        <div class="msjk-sites-table-section">
                            <div class="container-fluid">
                                <div class="msjk-card">
                                    <div class="msjk-card-header">
                                        <h4 class="msjk-card-title">Registered Sites</h4>
                                    </div>

                                    <div class="msjk-card-body-table">
                                        <div class="table-responsive">
                                            <table class="table msjk-sites-table">
                                                <thead>
                                                    <tr>
                                                        <th>Registration No</th>
                                                        <th>Name</th>
                                                        <th>Type</th>
                                                        <th>Current Stage</th>
                                                        <th>Dealer</th>
                                                        <th>Product Name</th>
                                                        <th>Quantity</th>
                                                        <th>Next Follow-up</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @if ($sites->count() > 0)
                                                        @foreach ($sites as $site)
                                                            @php
                                                                $siteProducts = $site->products;
                                                                if ($siteProducts->isEmpty()) {
                                                                    $siteProducts = collect([null]);
                                                                }
                                                                $siteRowspan = $siteProducts->count();
                                                            @endphp

                                                            @foreach ($siteProducts as $productIndex => $product)
                                                                <tr>
                                                                    @if ($productIndex === 0)
                                                                        <td class="msjk-site-reg"
                                                                            rowspan="{{ $siteRowspan }}">
                                                                            {{ $site->site_registration_no }}
                                                                        </td>

                                                                        <td rowspan="{{ $siteRowspan }}">
                                                                            <a href="{{ route('site-registration-v2.show', $site->id) }}"
                                                                                class="msjk-site-link"
                                                                                style="text-decoration: none; color: inherit; display: block;">
                                                                                <div class="msjk-site-info">
                                                                                    <div class="msjk-site-thumb">
                                                                                        <img src="{{ $site->photos->first() ? $site->photos->first()->file_path : asset('images/default-site.png') }}"
                                                                                            alt="{{ $site->name }}"
                                                                                            onerror="this.onerror=null; this.src='{{ asset('images/default-site.png') }}';" />
                                                                                    </div>
                                                                                    <div>
                                                                                        <div class="msjk-site-name">
                                                                                            {{ $site->name }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                        </td>

                                                                        <td rowspan="{{ $siteRowspan }}">
                                                                            {{ $site->siteType->name ?? '-' }}
                                                                        </td>

                                                                        <td rowspan="{{ $siteRowspan }}">
                                                                            <span class="msjk-stage-dot"></span>
                                                                            {{ $site->currentStage->name ?? '-' }}
                                                                        </td>

                                                                        <td rowspan="{{ $siteRowspan }}">
                                                                            <div>{{ $site->dealer?->name ?? '-' }}</div>
                                                                            <small>({{ $site->dealer?->code ?? '-' }})</small>
                                                                        </td>
                                                                    @endif

                                                                    <td>
                                                                        @if ($product)
                                                                            {{ $product->name }}
                                                                            <small>({{ $product->types }})</small>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>

                                                                    <td>
                                                                        {{ $product ? $product->pivot->quantity : '-' }}
                                                                    </td>

                                                                    @if ($productIndex === 0)
                                                                        <td rowspan="{{ $siteRowspan }}">
                                                                            {{ $site->next_followup_at ?? '-' }}
                                                                        </td>

                                                                        <td rowspan="{{ $siteRowspan }}">
                                                                            <span
                                                                                class="msjk-status-badge msjk-status-{{ $site->status }}">
                                                                                {{ ucwords(str_replace('_', ' ', $site->status)) }}
                                                                            </span>
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="9" class="text-center">
                                                                No sites found.
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- ============ Pagination ============ --}}
                                        <div class="panel-footer table-pagination-custom">
                                            <div class="pagination-status text-on-surface-variant font-body-sm">
                                                Showing {{ $sites->firstItem() }}-{{ $sites->lastItem() }}
                                                of {{ $sites->total() }} entries
                                            </div>

                                            <div class="pagination-controls">
                                                @if ($sites->onFirstPage())
                                                    <button class="btn btn-default msjk-nav-btn disabled" disabled
                                                        type="button">
                                                        <span class="glyphicon glyphicon-chevron-left"></span>
                                                    </button>
                                                @else
                                                    <a href="{{ $sites->previousPageUrl() }}"
                                                        class="btn btn-default msjk-nav-btn">
                                                        <span class="glyphicon glyphicon-chevron-left"></span>
                                                    </a>
                                                @endif

                                                <div class="page-numbers">
                                                    @php
                                                        $currentPage = $sites->currentPage();
                                                        $lastPage = $sites->lastPage();
                                                        $sideLinks = 1;
                                                    @endphp

                                                    <a href="{{ $sites->url(1) }}"
                                                        class="btn page-num-btn {{ $currentPage == 1 ? 'btn-primary active' : 'btn-default' }}">
                                                        1
                                                    </a>

                                                    @if ($currentPage > $sideLinks + 2)
                                                        <span class="pagination-ellipsis"
                                                            style="padding: 6px 12px; color: #6c757d;">...</span>
                                                    @endif

                                                    @for ($i = max(2, $currentPage - $sideLinks); $i <= min($lastPage - 1, $currentPage + $sideLinks); $i++)
                                                        <a href="{{ $sites->url($i) }}"
                                                            class="btn page-num-btn {{ $i == $currentPage ? 'btn-primary active' : 'btn-default' }}">
                                                            {{ $i }}
                                                        </a>
                                                    @endfor

                                                    @if ($currentPage < $lastPage - $sideLinks - 1)
                                                        <span class="pagination-ellipsis"
                                                            style="padding: 6px 12px; color: #6c757d;">...</span>
                                                    @endif

                                                    @if ($lastPage > 1)
                                                        <a href="{{ $sites->url($lastPage) }}"
                                                            class="btn page-num-btn {{ $currentPage == $lastPage ? 'btn-primary active' : 'btn-default' }}">
                                                            {{ $lastPage }}
                                                        </a>
                                                    @endif
                                                </div>

                                                @if ($sites->hasMorePages())
                                                    <a href="{{ $sites->nextPageUrl() }}"
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
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{--  Filter auto-submit + debounced search                       --}}
    {{-- ============================================================ --}}
    {{-- <script>
        (function() {
            var form = document.getElementById('siteFiltersForm');
            if (!form) return;

            var submitting = false;

            function submitForm() {
                if (submitting) return;
                submitting = true;
                form.classList.add('is-submitting');
                form.submit();
            }

            // ---- Auto-submit when a select changes ----
            form.querySelectorAll('.js-auto-submit').forEach(function(el) {
                el.addEventListener('change', submitForm);
            });

            // ---- Debounced submit for the search box ----
            var searchInput = form.querySelector('.js-search-input');
            if (searchInput) {
                var timer = null;
                var initial = searchInput.value;

                searchInput.addEventListener('input', function() {
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        if (searchInput.value === initial) return;
                        submitForm();
                    }, 600);
                });

                // Enter submits immediately (no debounce)
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(timer);
                        submitForm();
                    }
                });
            }
        })();
    </script> --}}

    <script src="{{ URL::asset('js/custom-site.js') }}?v={{ filemtime(public_path('js/custom-site.js')) }}"></script>
@endsection
