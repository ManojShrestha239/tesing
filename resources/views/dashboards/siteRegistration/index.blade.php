@extends('dashboards.sessions.layout')

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
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" fa fa-user font-red"></i><span
                                        class="caption-subject font-red bold uppercase"> Site Registration </span>
                                </div>
                                <div style="float: right;">
                                    <a href="{{ route('export.site.registrations') }}"
                                        class="btn btn-success btn-sm mt-3">
                                        <i class="fa fa-file-excel-o"></i> Export to Excel
                                    </a>
                                </div>
                                <div class="tools"> </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="sample_2">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>Date</th>
                                                <th>Dealer</th>
                                                <th>Contractor</th>
                                                <th>House Owner</th>
                                                <th>Under Coat</th>
                                                <th>Top Coat</th>
                                                <th>Digital Wall</th>
                                                <th>Location</th>
                                                <th>Status</th>
                                                <th>Submitted By</th>
                                                <th>Remarks</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($siteRegistrations as $siteRegistration)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td class="text-center">
                                                    {{ $siteRegistration->date }}
                                                </td>
                                                <td class="text-center">
                                                    @if (!empty($siteRegistration->dealer))
                                                    <strong>Dealer Name:-</strong>
                                                    {{ $siteRegistration->dealer->name }}
                                                    <br>
                                                    <strong>Dealer Code:-</strong>
                                                    {{ $siteRegistration->dealer->code }}
                                                    @else
                                                    N/A
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div><strong>Name:</strong>
                                                        {{ $siteRegistration->contractor->name }}
                                                    </div>
                                                    <div><strong>Pass Book:</strong>
                                                        {{ $siteRegistration->contractor->pass_book_no }}
                                                    </div>
                                                    <div><strong>Contact:</strong>
                                                        {{ $siteRegistration->contractor->contact }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div><strong>Name:</strong>
                                                        {{ $siteRegistration->house_hold_name }}
                                                    </div>
                                                    <div><strong>Address:</strong>
                                                        {{ $siteRegistration->house_hold_address }}
                                                    </div>
                                                    <div><strong>Contact:</strong>
                                                        {{ $siteRegistration->house_hold_contact }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div><strong>Exterior:</strong>
                                                        {{ $siteRegistration->under_coat_exterior }}
                                                    </div>
                                                    <div><strong>Interior:</strong>
                                                        {{ $siteRegistration->under_coat_interior }}
                                                    </div>
                                                    <div><strong>Total:</strong>
                                                        {{ $siteRegistration->under_coat_total }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div><strong>Exterior:</strong>
                                                        {{ $siteRegistration->top_coat_exterior }}
                                                    </div>
                                                    <div><strong>Interior:</strong>
                                                        {{ $siteRegistration->top_coat_interior }}
                                                    </div>
                                                    <div><strong>Total:</strong>
                                                        {{ $siteRegistration->top_coat_total }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div><strong>Putty:</strong>
                                                        {{ $siteRegistration->digital_wall_putty }}
                                                    </div>
                                                    <div><strong>Safe Putty:</strong>
                                                        {{ $siteRegistration->digital_wall_safe_putty }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <form
                                                        action="{{ route('site.origin', $siteRegistration->id) }}"
                                                        method="post" target="_blank">
                                                        @csrf
                                                        <button class="btn btn-primary btn-sm" type="submit">View
                                                            on Map</button>
                                                    </form>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-{{ $siteRegistration->status == 'Active' ? 'success' : 'secondary' }}">{{ $siteRegistration->status }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span>{{ $siteRegistration->submittedBy->name }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="text-muted">{{ $siteRegistration->remarks }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('site-registration.show', $siteRegistration->id) }}"
                                                        class='btn btn-outline btn-primary btn-xs'>View Image</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- END SIDEBAR CONTENT LAYOUT -->
</div>
@endsection