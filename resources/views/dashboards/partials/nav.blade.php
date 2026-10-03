{{-- filepath: /Users/manojshrestha/ofc-projects/fieldma-projects/fieldma-2.0/resources/views/dashboards/partials/nav.blade.php --}}
<style>
    .today-count {
        position: absolute;
        top: 0;
        right: 0;
        width: 22px;
        height: 22px;
        color: #fff;
        background: #555;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }

    li:hover .today-count {
        color: #000;
        background: #fff;
    }

    .page-logo img {
        height: 200px;
        width: 175px;
    }
</style>

<!-- Brand and toggle get grouped for better mobile display -->
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"
    aria-label="Toggle navigation">
    <span class="sr-only">Toggle navigation</span>
    <span class="toggle-icon">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </span>
</button>
<!-- End Toggle Button -->
@php
    $authUser = auth()->user();
    $permissionRoutes = config('permission_redirects');
    $routePath = '/dashboard'; // default fallback

    foreach ($permissionRoutes as $permission => $route) {
        // Use can() — returns false if the permission doesn't exist,
        // hasPermissionTo() throws PermissionDoesNotExist.
        if ($authUser->can($permission)) {
            $routePath = $route;
            break;
        }
    }
@endphp
<a id="index" class="page-logo" href="{{ $routePath }}">
    <img src="{{ asset('assets/pages/img/logo-fieldma.png') }}" alt="Logo">
</a>

<div class="navbar-collapse hidden-sm hidden-xs navigation">
    <ul class="nav navbar-nav">
        @php
            $isActive = fn($routes) => collect($routes)->contains(fn($r) => request()->is($r)) ? 'navActive' : '';
        @endphp

        {{-- <li class="{{ $isActive(['dashboard', 'activeuser*']) }} classic-menu-dropdown">
        <a href="/dashboard" style="color: #315DA9;">Home</a>
        </li> --}}

        @can('notice')
            <li class="{{ $isActive(['notice*']) }} classic-menu-dropdown">
                <a href="/notice" style="color: #315DA9;">Notice</a>
            </li>
        @endcan

        @can('order')
            <li class="{{ $isActive(['order*']) }} classic-menu-dropdown" style="position:relative;">
                <a href="/order" style="color: #315DA9;">
                    Orders
                    @if (Helper::todayorders() > 0)
                        <span class="today-count">{{ Helper::todayorders() }}</span>
                    @endif
                </a>
            </li>
        @endcan

        @can('collection')
            <li class="{{ $isActive(['collection*', 'cheque*', 'token*', '*-collection']) }} classic-menu-dropdown"
                style="position:relative;">
                <a href="/collection-report" style="color: #315DA9;">
                    Collection
                    @if (Helper::todaycollections() > 0)
                        <span class="today-count">{{ Helper::todaycollections() }}</span>
                    @endif
                </a>
            </li>
        @endcan

        @can('schedule')
            <li class="{{ $isActive(['schedule*', 'repeated-schedule', 'repeated*']) }} classic-menu-dropdown">
                <a href="/schedule" style="color: #315DA9;">Visit Schedule</a>
            </li>
        @endcan

        @can('chat')
            <li class="{{ $isActive(['chat*']) }} classic-menu-dropdown" style="position:relative;">
                <a href="/chat" style="color: #315DA9;">
                    Chat
                </a>
            </li>
        @endcan

        @can('complaint')
            <li class="{{ $isActive(['complaint*']) }} classic-menu-dropdown">
                <a href="/complaint" style="color: #315DA9;">Complaint</a>
            </li>
        @endcan

        @can('reports')
            <li
                class="{{ $isActive(['visit*', 'visitdealer*', 'attendance*', 'negotiation*', 'monthlySummary*', 'tada-report*']) }} classic-menu-dropdown">
                <a href="/visithistory" style="color: #315DA9;">Reports</a>
            </li>
        @endcan

        @canany(['user', 'dealer', 'area', 'product', 'miscellaneous', 'privacy-policy', 'bank', 'upload-due',
            'distance', 'denomination', 'purpose', 'role', 'productinventory', 'rawmaterial'])
            <li
                class="{{ $isActive([
                    'admin*',
                    'user*',
                    'dealer*',
                    'area*',
                    'users-assign*',
                    'product*',
                    'categor*',
                    'bank*',
                    'upload*',
                    'distance*',
                    'create-bank*',
                    'final-product*',
                    'size*',
                    'denomination*',
                    'purpose*',
                    'role*',
                ]) }} classic-menu-dropdown">
                <a class="setup" style="color: #315DA9;">Setup</a>
                <ul class="multi-level">
                    @can('user')
                        <li class="{{ $isActive(['admin*', 'user*']) }} classic-menu-dropdown">
                            <a href="/user" style="color: #315DA9;">Users</a>
                        </li>
                    @endcan
                    @can('dealer')
                        <li class="{{ $isActive(['dealer*']) }} classic-menu-dropdown">
                            <a href="/dealer" style="color: #315DA9;">Dealers</a>
                        </li>
                    @endcan
                    @can('area')
                        <li class="{{ $isActive(['area*', 'users-assign*']) }} classic-menu-dropdown">
                            <a href="/area" style="color: #315DA9;">Areas</a>
                        </li>
                    @endcan
                    @can('product')
                        <li class="{{ $isActive(['product*', 'final-product*', 'categor*', 'size*']) }} classic-menu-dropdown">
                            <a href="/final-product" style="color: #315DA9;">Products</a>
                        </li>
                    @endcan
                    @can('productinventory')
                        <li class="{{ $isActive(['productinventory*', 'producinventory*']) }} classic-menu-dropdown">
                            <a href="/producinventory" style="color: #315DA9;">Product Inventory</a>
                        </li>
                    @endcan
                    @can('rawmaterial')
                        <li
                            class="{{ $isActive(['raw-material*', 'raw*', 'raw-product*', 'rawpurchase*', 'inventory*']) }} classic-menu-dropdown">
                            <a href="/raw" style="color: #315DA9;">Raw Materials</a>
                        </li>
                    @endcan
                    @canany(['bank', 'upload-due', 'distance', 'denomination', 'purpose', 'role'])
                        <li
                            class="{{ $isActive(['bank*', 'upload*', 'distance*', 'bank/create*', 'denomination*', 'purpose*', 'role*']) }} classic-menu-dropdown">
                            @if (Auth::user()->can('bank'))
                                <a href="/bank" style="color: #315DA9;">Miscellaneous</a>
                            @elseif (Auth::user()->can('upload-due'))
                                <a href="/uploadfile/create" style="color: #315DA9;">Miscellaneous</a>
                            @elseif (Auth::user()->can('distance'))
                                <a href="/distance" style="color: #315DA9;">Miscellaneous</a>
                            @elseif (Auth::user()->can('denomination'))
                                <a href="/denomination" style="color: #315DA9;">Miscellaneous</a>
                            @elseif (Auth::user()->can('purpose'))
                                <a href="/purpose" style="color: #315DA9;">Miscellaneous</a>
                            @else
                                <a href="/role" style="color: #315DA9;">Miscellaneous</a>
                            @endif
                        </li>
                    @endcanany
                    @can('privacy-policy')
                        <li class="{{ $isActive(['dashboard/privacy-policy*', 'privacy-policy*']) }} classic-menu-dropdown">
                            <a href="/privacy-policy" style="color: #315DA9;">Privacy-Policy</a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany
        <li
            class="{{ $isActive(['dashboard/site-registration*', 'site-registration*', 'site-map*']) }} classic-menu-dropdown">
            <a href="{{ route('site-registration-v2.index') }}" style="color: #315DA9;">Site Registration</a>
        </li>
    </ul>

    <!-- BEGIN TOPBAR ACTIONS -->
    <div class="topbar-actions">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".dropdown-menu-v2"
            aria-label="User menu">
            <span class="sr-only"></span>
            <span class="toggle-icon">
                <span><i class="fa fa-user" style="color: white;"> {{ Auth::user()->name }}</i></span>
            </span>
        </button>
        <div class="btn-group-img btn-group">
            <button type="button" class="btn btn-sm md-skip dropdown-toggle hidden-sm hidden-xs" data-toggle="dropdown"
                data-hover="dropdown" data-close-others="true" aria-haspopup="true" aria-expanded="false">
                <span style="color: white;"><i class="fa fa-user" style="color: white;">
                        {{ Auth::user()->name }}</i></span>
            </button>
            <ul class="dropdown-menu-v2" role="menu">
                <li>
                    <a href="/admin/{{ Auth::user()->id }}/edit" style="color: #315DA9;">
                        <i class="fa fa-edit" style="color: #315DA9;"></i> Edit Profile
                    </a>
                </li>
                <li>
                    <a href="/admin/{{ Auth::user()->id }}/pass" style="color: #315DA9;">
                        <i class="icon-key" style="color: #315DA9;"></i> Change Password
                    </a>
                </li>
                <li>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                        style="color: #315DA9;">
                        <i class="icon-logout" style="color: #315DA9;"></i> Log Out
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
    <!-- END TOPBAR ACTIONS -->
</div>
