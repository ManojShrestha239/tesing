@extends('dashboards.sessions.layout')

<link rel="stylesheet" href="{{ asset('css/custom-site-registration.css') }}">

@section('content')
    @php
        $isEdit = isset($site) && $site;
        $stakeholderRows = old(
            'stakeholders',
            $isEdit
                ? $site->contacts
                    ->map(function ($contact) {
                        return [
                            'id' => $contact->id,
                            'name' => $contact->name,
                            'role' => $contact->role,
                            'contact_number' => $contact->phone,
                        ];
                    })
                    ->values()
                    ->toArray()
                : [],
        );
        $productRows = old(
            'products',
            $isEdit
                ? $site->products
                    ->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'quantity' => $product->pivot->quantity ?? 1,
                        ];
                    })
                    ->values()
                    ->toArray()
                : [],
        );
        $latestFollowUp = $isEdit ? $site->followUps->first() : null;
        $pageTitle = $isEdit ? 'Edit Site Registration' : 'Site Registration';
        $formAction = $isEdit ? route('site-registration-v2.update', $site->id) : route('site-registration-v2.store');
        $submitLabel = $isEdit ? 'Update Site' : 'Register Site';
        $backUrl = $isEdit ? route('site-registration-v2.show', $site->id) : url('/site-registration-v2');

        if (!is_array($stakeholderRows)) {
            $stakeholderRows = [];
        }

        if (!is_array($productRows)) {
            $productRows = [];
        }
    @endphp

    <div class="page-content">
        <div class="breadcrumbs">
            <h1>{{ $pageTitle }}</h1>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">
                <span class="sr-only">Toggle navigation</span>
                <span class="toggle-icon">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </span>
            </button>
        </div>

        <div class="page-content-container">
            <div class="page-content-row" id="replecable">
                <div class="page-sidebar">
                    <div class="portlet light portlet-fit">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-bar font-red"></i>
                                <span class="caption-subject font-red bold uppercase">Site Registration</span>
                            </div>
                        </div>
                        <nav class="navbar" role="navigation">
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

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>Please fix the highlighted fields.</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="msjk-siteReg-form-container-section">
                        <div class="msjk-siteReg-form-container">
                            <div class="msjk-sitereg-container">
                                <div class="msjk-sitereg-header">
                                    <div class="msjk-sitereg-title-wrap">
                                        <a href="{{ $backUrl }}">
                                            <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                    stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <path d="M5 12H19M5 12L11 6M5 12L11 18" stroke="#1743d1"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    </path>
                                                    </path>
                                                </g>
                                            </svg>
                                        </a>
                                        <h2>{{ $pageTitle }}</h2>
                                    </div>
                                    {{-- <button type="button" class="msjk-sitereg-draft-btn">Save Draft</button> --}}
                                </div>

                                <form id="siteRegistrationForm" action="{{ $formAction }}" method="POST"
                                    enctype="multipart/form-data" data-submit-label="{{ $submitLabel }}">
                                    @csrf
                                    @if ($isEdit)
                                        @method('PUT')
                                    @endif

                                    <div class="msjk-sitereg-steps">
                                        <div class="msjk-sitereg-step msjk-sitereg-step-active" data-step="1">
                                            <span>1</span>
                                            <p>Basic Info</p>
                                        </div>
                                        <div class="msjk-sitereg-step" data-step="2">
                                            <span>2</span>
                                            <p>Contacts</p>
                                        </div>
                                        <div class="msjk-sitereg-step" data-step="3">
                                            <span>3</span>
                                            <p>Site Details</p>
                                        </div>
                                        <div class="msjk-sitereg-step" data-step="4">
                                            <span>4</span>
                                            <p>Products</p>
                                        </div>
                                        <div class="msjk-sitereg-step" data-step="5">
                                            <span>5</span>
                                            <p>Follow-up</p>
                                        </div>
                                        <div class="msjk-sitereg-step" data-step="6">
                                            <span>6</span>
                                            <p>Photos</p>
                                        </div>
                                    </div>

                                    @include('dashboards.new-site.site-partials.basic-form')
                                    @include('dashboards.new-site.site-partials.contact-form')
                                    @include('dashboards.new-site.site-partials.site-details')
                                    @include('dashboards.new-site.site-partials.products-form')
                                    @include('dashboards.new-site.site-partials.follow-up')
                                    @include('dashboards.new-site.site-partials.photos-form')

                                    <div class="msjk-sitereg-footer">
                                        <button type="button" class="msjk-sitereg-prev-btn" style="visibility: hidden">
                                            Back
                                        </button>
                                        <button type="button" class="msjk-sitereg-next-btn">Next: Contacts</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.__siteLocationGoogleReady = false;
        window.initSiteLocationAutocomplete = function() {
            window.__siteLocationGoogleReady = true;
            if (window.SiteRegistrationGoogleMaps && typeof window.SiteRegistrationGoogleMaps.init === 'function') {
                window.SiteRegistrationGoogleMaps.init();
            }
        };
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3dbB5PCrN-1AW1ipmdM6RFrsdNdks3cU&callback=initSiteLocationAutocomplete"
        async defer></script>
    <script src="{{ URL::asset('js/custom-create-site.js') }}"></script>
@endsection
