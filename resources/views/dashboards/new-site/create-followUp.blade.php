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
                    <div class="msjk-follow-form-container-section">
                        <div class="msjk-follow-form-container">
                            <div class="msjk-step-navigation">
                                <div class="msjk-step-item msjk-step-item-active" data-step="1">
                                    <div class="msjk-step-number">1</div>
                                    <div class="msjk-step-text">
                                        <h4>Follow-up Details</h4>
                                        <p>Interaction information</p>
                                    </div>
                                </div>

                                <div class="msjk-step-item" data-step="2">
                                    <div class="msjk-step-number">2</div>
                                    <div class="msjk-step-text">
                                        <h4>Site Progress</h4>
                                        <p>Update stage</p>
                                    </div>
                                </div>

                                <div class="msjk-step-item" data-step="3">
                                    <div class="msjk-step-number">3</div>
                                    <div class="msjk-step-text">
                                        <h4>Schedule</h4>
                                        <p>Next contact</p>
                                    </div>
                                </div>
                            </div>

                            <div class="msjk-step-content msjk-step-content-active" id="msjk-page-1">
                                <h2 class="msjk-follow-title">Follow-up Details</h2>

                                <p class="msjk-follow-subtitle">
                                    Record the outcome of your latest visit or interaction at Skyline
                                    Heights.
                                </p>

                                <div class="row">
                                    <!-- Follow Up Type -->
                                    <div class="col-md-6">
                                        <div class="msjk-form-group">
                                            <label>Follow-up Type</label>

                                            <select class="msjk-form-control">
                                                <option>Site Visit</option>
                                                <option>Phone Call</option>
                                                <option>Meeting</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Interaction Status -->
                                    <div class="col-md-6">
                                        <div class="msjk-form-group">
                                            <label>Interaction Status</label>

                                            <div class="msjk-status-group">
                                                <button type="button" class="msjk-status-btn msjk-status-btn-active">
                                                    Positive
                                                </button>

                                                <button type="button" class="msjk-status-btn">Neutral</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="msjk-form-group msjk-notes-group">
                                    <label>Notes & Remarks</label>

                                    <textarea class="msjk-form-control msjk-notes-box"
                                        placeholder="Mention key discussions, material requirements, or issues identified..."></textarea>
                                </div>
                            </div>

                            <div class="msjk-step-content" id="msjk-page-2">
                                <div class="msjk-follow-card">
                                    <h2 class="msjk-follow-title">Update Site Progress</h2>

                                    <p class="msjk-follow-subtitle">
                                        Select the current visual milestone achieved at the project site.
                                    </p>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="msjk-stage-card">
                                                <div class="msjk-stage-icon">
                                                    <svg fill="#000000" version="1.1" id="Layer_1"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="30px"
                                                        height="30px" viewBox="0 0 256 242"
                                                        enable-background="new 0 0 256 242" xml:space="preserve">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M108.327,30.668c11.331,0,20.516,9.185,20.516,20.516S119.657,71.7,108.327,71.7S87.81,62.515,87.81,51.184 S96.996,30.668,108.327,30.668z M33.069,154.994l-0.189-0.189l0,0c-0.851-0.567-1.607-1.04-2.269-1.796l2.269,26L4.327,219.474 c-4.065,5.862-2.647,13.804,3.025,17.869c5.862,4.065,13.804,2.647,17.869-3.025l30.822-44.436c1.607-2.269,2.647-5.295,2.269-8.32 l-1.04-12.574L33.069,154.994z M117.876,239.612h122.341l-22.88-71.381c0,0-12.953,3.215-38.574,30.443 c-0.567,0.567-1.229,1.229-1.796,1.796l-61.832-35.738c1.04-1.985,1.229-4.255,0.851-6.713L94.05,76.711 c-1.796-7.942-8.887-13.993-17.207-13.993H35.905c-3.025,0-6.051,1.607-7.753,4.255L7.731,102.427 c-2.647,4.255-1.04,9.738,3.215,12.196l22.88,13.142L30.8,132.87c-3.687,6.051-1.607,13.804,4.633,17.207L86.77,179.67v46.894 c0,7.091,5.673,12.764,12.764,12.764s12.764-5.673,12.764-12.764v-53.512l56.821,32.902c-11.724,6.713-25.149,7.942-34.698,13.614 C122.697,226.564,117.876,239.612,117.876,239.612z M38.174,120.296l-15.033-8.698l0,0l17.869-30.822h20.044L38.174,120.296z M71.171,139.394l14.182-24.771l10.589,38.952L71.171,139.394z M254,53.634c0,3.239-2.626,5.953-5.953,5.953 c-1.751,0-3.414-0.788-4.465-2.014l-7.004-6.479v55.594h-25.065l-21.578,0h-25.41V50.658l-8.055,7.267 c-1.051,1.051-2.539,1.663-4.115,1.663c-3.239,0-5.953-2.626-5.953-5.953c0-1.839,0.875-3.59,2.189-4.64L200.77,2.33l15.671,14.271 V4.519h13.395v24.339l22.675,20.749C253.475,50.658,254,52.058,254,53.634z">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                </div>

                                                <h4>Foundation</h4>

                                                <p>Earthwork & Piling</p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="msjk-stage-card msjk-stage-card-active">
                                                <div class="msjk-stage-icon msjk-stage-icon-active">
                                                    <svg width="30px" height="30px" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <defs>
                                                                <style>
                                                                    .cls-1 {
                                                                        fill: none;
                                                                        stroke: #020202;
                                                                        stroke-linecap: square;
                                                                        stroke-miterlimit: 10;
                                                                        stroke-width: 1.91px;
                                                                    }
                                                                </style>
                                                            </defs>
                                                            <g id="sitemap">
                                                                <rect class="cls-1" x="9.14" y="1.5" width="5.73"
                                                                    height="5.73"></rect>
                                                                <rect class="cls-1" x="10.09" y="18.68" width="3.82"
                                                                    height="3.82"></rect>
                                                                <rect class="cls-1" x="1.5" y="18.68" width="3.82"
                                                                    height="3.82"></rect>
                                                                <rect class="cls-1" x="18.68" y="18.68" width="3.82"
                                                                    height="3.82"></rect>
                                                                <line class="cls-1" x1="12" y1="18.68"
                                                                    x2="12" y2="8.18"></line>
                                                                <polyline class="cls-1"
                                                                    points="3.41 18.68 3.41 12.96 20.59 12.96 20.59 18.68">
                                                                </polyline>
                                                            </g>
                                                        </g>
                                                    </svg>
                                                </div>

                                                <h4>Structure</h4>

                                                <p>Slab & Beams (Current)</p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="msjk-stage-card">
                                                <div class="msjk-stage-icon">
                                                    <svg width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M9.5 19.5V18H4.5C3.95 18 3.45 17.78 3.09 17.41C2.72 17.05 2.5 16.55 2.5 16C2.5 14.97 3.3 14.11 4.31 14.01C4.37 14 4.43 14 4.5 14H19.5C19.57 14 19.63 14 19.69 14.01C20.17 14.05 20.59 14.26 20.91 14.59C21.32 14.99 21.54 15.56 21.49 16.18C21.4 17.23 20.45 18 19.39 18H14.5V19.5C14.5 20.88 13.38 22 12 22C10.62 22 9.5 20.88 9.5 19.5Z"
                                                                stroke="#292D32" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                                            <path
                                                                d="M20.17 5.3L19.69 14.01C19.63 14 19.57 14 19.5 14H4.50001C4.43001 14 4.37001 14 4.31001 14.01L3.83001 5.3C3.65001 3.53 5.04001 2 6.81001 2H17.19C18.96 2 20.35 3.53 20.17 5.3Z"
                                                                stroke="#292D32" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                                            <path d="M7.98999 2V7" stroke="#292D32" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                                            <path d="M12 2V4" stroke="#292D32" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </g>
                                                    </svg>
                                                </div>

                                                <h4>Plastering</h4>

                                                <p>Internal & External</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="msjk-stage-card">
                                                <div class="msjk-stage-icon">
                                                    <svg width="30px" height="30px" viewBox="0 0 24 24"
                                                        id="Layer_1" data-name="Layer 1"
                                                        xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <defs>
                                                                <style>
                                                                    .cls-1 {
                                                                        fill: none;
                                                                        stroke: #020202;
                                                                        stroke-miterlimit: 10;
                                                                        stroke-width: 2px;
                                                                    }
                                                                </style>
                                                            </defs>
                                                            <path class="cls-1"
                                                                d="M18.48,15.92h0A10.4,10.4,0,0,1,8.08,5.52h0l13.1-4.37a1.32,1.32,0,0,1,1.35.32h0a1.32,1.32,0,0,1,.32,1.35Z">
                                                            </path>
                                                            <path class="cls-1"
                                                                d="M3.2,22.92h0A2.12,2.12,0,0,1,1.08,20.8h0a2.13,2.13,0,0,1,.62-1.5l4.88-4.88,3,3L4.7,22.3A2.13,2.13,0,0,1,3.2,22.92Z">
                                                            </path>
                                                            <line class="cls-1" x1="17.08" y1="6.92"
                                                                x2="8.08" y2="15.92"></line>
                                                        </g>
                                                    </svg>
                                                </div>

                                                <h4>Putty</h4>

                                                <p>Wall Finishing</p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="msjk-stage-card">
                                                <div class="msjk-stage-icon">
                                                    <svg fill="#000000" height="30px" width="30px" version="1.1"
                                                        id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 612 612"
                                                        xml:space="preserve">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <g>
                                                                <path
                                                                    d="M590.253,398.519l-46.635-46.635l47.511-47.511c5.583-5.581,5.585-14.631,0-20.213L311.156,4.187 c-5.583-5.583-14.635-5.583-20.211,0l-123.314,123.31c-5.585,5.585-5.585,14.631,0,20.216l279.976,279.971 c5.581,5.586,14.63,5.586,20.211,0l38.401-38.402l27.937,27.937l-78.505,78.505l-162.69-162.686 c-4.959-4.959-11.686-7.746-18.7-7.746s-13.74,2.788-18.699,7.747l-33.679,33.679c-14.017-13.677-36.467-13.591-50.363,0.301 L24.494,514.046c-13.996,13.999-13.992,36.704,0.004,50.7L61.252,601.5c13.999,13.996,36.704,14.003,50.703,0.004l147.02-147.027 c13.898-13.894,13.976-36.341,0.305-50.362l14.982-14.982l162.69,162.685c5.163,5.163,11.933,7.746,18.7,7.746 c6.767,0,13.535-2.583,18.7-7.746l115.904-115.901c4.959-4.959,7.746-11.686,7.746-18.699 C597.999,410.205,595.212,403.479,590.253,398.519z">
                                                                </path>
                                                            </g>
                                                        </g>
                                                    </svg>
                                                </div>

                                                <h4>Painting</h4>

                                                <p>Final Coat Application</p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="msjk-stage-card">
                                                <div class="msjk-stage-icon">
                                                    <svg width="30px" height="30px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M14.01 21C13.52 21 13.06 20.77 12.68 20.57C12.44 20.45 12.15 20.3 12 20.3C11.85 20.3 11.55 20.45 11.32 20.57C10.84 20.82 10.24 21.12 9.59999 20.95C8.93999 20.78 8.57999 20.2 8.27999 19.74C8.14999 19.53 7.96999 19.25 7.84999 19.18C7.72999 19.11 7.40999 19.1 7.13999 19.08C6.59999 19.05 5.92999 19.02 5.44999 18.55C4.96999 18.06 4.93999 17.39 4.90999 16.85C4.89999 16.59 4.87999 16.26 4.80999 16.14C4.74999 16.03 4.45999 15.85 4.25999 15.72C3.79999 15.42 3.22999 15.05 3.04999 14.4C2.87999 13.76 3.17999 13.16 3.42999 12.68C3.54999 12.44 3.69999 12.15 3.69999 12C3.69999 11.85 3.54999 11.55 3.42999 11.32C3.17999 10.84 2.87999 10.24 3.04999 9.60002C3.21999 8.94002 3.79999 8.58002 4.25999 8.28002C4.46999 8.15002 4.74999 7.97002 4.81999 7.85002C4.88999 7.73002 4.89999 7.41002 4.91999 7.14002C4.94999 6.60002 4.97999 5.93002 5.44999 5.45002C5.93999 4.97002 6.60999 4.94002 7.14999 4.91002C7.40999 4.90002 7.73999 4.88002 7.85999 4.81002C7.96999 4.75002 8.14999 4.46002 8.27999 4.26002C8.57999 3.80002 8.94999 3.23002 9.59999 3.05002C10.24 2.88002 10.84 3.18002 11.32 3.43002C11.56 3.55002 11.85 3.70002 12 3.70002C12.15 3.70002 12.45 3.55002 12.68 3.43002C13.16 3.19002 13.76 2.88002 14.4 3.05002C15.06 3.22002 15.42 3.80002 15.72 4.26002C15.85 4.47002 16.03 4.75002 16.15 4.82002C16.27 4.89002 16.59 4.90002 16.86 4.92002C17.4 4.95002 18.07 4.98002 18.55 5.45002C19.03 5.94002 19.06 6.61002 19.09 7.15002C19.1 7.41002 19.12 7.74002 19.19 7.86002C19.25 7.97002 19.54 8.15002 19.74 8.28002C20.2 8.58002 20.77 8.95002 20.95 9.60002C21.12 10.24 20.82 10.84 20.57 11.32C20.45 11.56 20.3 11.85 20.3 12C20.3 12.15 20.45 12.45 20.57 12.68C20.82 13.16 21.12 13.76 20.95 14.4C20.78 15.06 20.2 15.42 19.74 15.72C19.53 15.85 19.25 16.03 19.18 16.15C19.11 16.27 19.1 16.59 19.08 16.86C19.05 17.4 19.02 18.07 18.55 18.55C18.06 19.03 17.39 19.06 16.85 19.09C16.59 19.1 16.26 19.12 16.14 19.19C16.03 19.25 15.85 19.54 15.72 19.74C15.42 20.2 15.05 20.77 14.4 20.95C14.27 20.99 14.14 21 14.01 21ZM9.99999 4.50002C9.99999 4.50002 9.99999 4.50002 9.98999 4.50002C9.88999 4.54002 9.65999 4.88002 9.54999 5.07002C9.30999 5.44002 9.03999 5.86002 8.60999 6.11002C8.16999 6.36002 7.66999 6.39002 7.21999 6.41002C6.99999 6.42002 6.58999 6.44002 6.49999 6.51002C6.43999 6.59002 6.41999 6.99002 6.40999 7.21002C6.38999 7.66002 6.35999 8.16002 6.10999 8.59002C5.85999 9.02002 5.43999 9.29002 5.06999 9.53002C4.87999 9.65002 4.53999 9.87002 4.49999 9.98002C4.48999 10.09 4.65999 10.43 4.75999 10.63C4.95999 11.03 5.19999 11.48 5.19999 11.99C5.19999 12.5 4.96999 12.95 4.75999 13.35C4.65999 13.55 4.47999 13.89 4.49999 14C4.53999 14.1 4.87999 14.33 5.06999 14.44C5.43999 14.68 5.85999 14.95 6.10999 15.38C6.35999 15.82 6.38999 16.32 6.40999 16.77C6.41999 16.99 6.43999 17.4 6.50999 17.49C6.58999 17.55 6.98999 17.57 7.20999 17.58C7.65999 17.6 8.15999 17.63 8.58999 17.88C9.01999 18.13 9.28999 18.55 9.52999 18.92C9.64999 19.11 9.86999 19.45 9.97999 19.49C10.08 19.52 10.43 19.33 10.63 19.23C11.03 19.03 11.48 18.79 11.99 18.79C12.5 18.79 12.95 19.02 13.35 19.23C13.55 19.33 13.87 19.52 14 19.49C14.1 19.45 14.33 19.11 14.44 18.92C14.68 18.55 14.95 18.13 15.38 17.88C15.82 17.63 16.32 17.6 16.77 17.58C16.99 17.57 17.4 17.55 17.49 17.48C17.55 17.4 17.57 17 17.58 16.78C17.6 16.33 17.63 15.83 17.88 15.4C18.13 14.97 18.55 14.7 18.92 14.46C19.11 14.34 19.45 14.12 19.49 14.01C19.5 13.9 19.33 13.56 19.23 13.36C19.03 12.96 18.79 12.51 18.79 12C18.79 11.49 19.02 11.04 19.23 10.64C19.33 10.44 19.5 10.1 19.49 9.99002C19.45 9.88002 19.1 9.66002 18.92 9.54002C18.55 9.30002 18.13 9.03002 17.88 8.60002C17.63 8.16002 17.6 7.66002 17.58 7.21002C17.57 6.99002 17.55 6.58002 17.48 6.49002C17.4 6.43002 17 6.41002 16.78 6.40002C16.33 6.38002 15.83 6.35002 15.4 6.10002C14.97 5.85002 14.7 5.43002 14.46 5.06002C14.34 4.87002 14.12 4.53002 14.01 4.49002C13.91 4.46002 13.56 4.65002 13.36 4.75002C12.96 4.95002 12.51 5.19002 12 5.19002C11.49 5.19002 11.04 4.96002 10.64 4.75002C10.44 4.65002 10.12 4.49002 9.99999 4.49002V4.50002ZM10.49 15.51C10.29 15.51 10.1 15.43 9.95999 15.29L7.44999 12.78C7.15999 12.49 7.15999 12.01 7.44999 11.72C7.73999 11.43 8.21999 11.43 8.50999 11.72L10.49 13.7L15.48 8.71002C15.77 8.42002 16.25 8.42002 16.54 8.71002C16.83 9.00002 16.83 9.48002 16.54 9.77002L11.02 15.29C10.88 15.43 10.69 15.51 10.49 15.51Z"
                                                                fill="#000000"></path>
                                                        </g>
                                                    </svg>
                                                </div>

                                                <h4>Completed</h4>

                                                <p>Handover Ready</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="msjk-step-content" id="msjk-page-3">
                                <div class="msjk-follow-card">
                                    <h2 class="msjk-follow-title">Schedule Next Contact</h2>

                                    <p class="msjk-follow-subtitle">
                                        When should the next follow-up occur for Skyline Heights?
                                    </p>

                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="msjk-form-group">
                                                <label>Appointment Date</label>

                                                <input type="date" class="msjk-form-control" />
                                            </div>

                                            <div class="msjk-form-group">
                                                <label>Time Slot</label>

                                                <div class="msjk-time-slot-wrapper">
                                                    <button type="button" class="msjk-time-slot msjk-time-slot-active">
                                                        02:30 PM
                                                    </button>

                                                    <button type="button" class="msjk-time-slot">
                                                        10:00 AM
                                                    </button>

                                                    <button type="button" class="msjk-time-slot">
                                                        04:00 PM
                                                    </button>

                                                    <button type="button" class="msjk-time-slot">
                                                        05:30 PM
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="msjk-notification-card">
                                                <h4>Notifications</h4>

                                                <div class="msjk-toggle-row">
                                                    <span>Send SMS Reminder</span>

                                                    <label class="msjk-switch">
                                                        <input type="checkbox" checked />
                                                        <span class="msjk-slider"></span>
                                                    </label>
                                                </div>

                                                <div class="msjk-toggle-row">
                                                    <span>Email Calendar Invite</span>

                                                    <label class="msjk-switch">
                                                        <input type="checkbox" />
                                                        <span class="msjk-slider"></span>
                                                    </label>
                                                </div>

                                                <div class="msjk-reminder-note">
                                                    Setting a reminder will automatically alert the Site
                                                    Supervisor 24 hours prior to the date.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="msjk-step-footer">
                                <button class="btn btn-default msjk-prev-btn" style="visibility: hidden">
                                    Back
                                </button>

                                <div class="msjk-pagination-dots">
                                    <span class="msjk-pagination-dots-active"></span>
                                    <span></span>
                                    <span></span>
                                </div>

                                <button class="btn btn-primary msjk-next-btn">Next</button>
                            </div>
                        </div>
                    </div>
                    {{-- End Main Content Here --}}

                </div>
            </div>
        </div>

        <!-- END SIDEBAR CONTENT LAYOUT -->
    </div>
    <script src="{{ URL::asset('js/custom-create-followUp.js') }}"></script>
@endsection
