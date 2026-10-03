@inject('helpers', 'App\Utilities\Helpers')
@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Product Manufacture List</h1>
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
                            <ul class="nav navbar-nav margin-bottom-35 products-class">
                                @include('dashboards.dynamicNav.producinventory')
                            </ul>

                        </nav>
                    </div>

                </div>

                <div class="page-content-col">
                    @if (session('Status'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <b>{!! session('Status') !!}</b>
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
                                        <span class="caption-subject font-red bold uppercase">Product Manufacture
                                            List</span>
                                    </div>

                                </div>

                                @if (count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <p class="alert alert-danger">{{ $error }}</p>
                                    @endforeach
                                @endif



                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover" id="inventoryTable">
                                        <thead>
                                            <tr>
                                                <th>SN</th>
                                                <th>Batch Number</th>
                                                <th> Product Name </th>
                                                <th>Size </th>
                                                <th>Total No. Of Products Manufactured</th>
                                                <th>Manufactured Date</th>

                                                {{-- <th> Status </th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @foreach ($productmanufs as $productmanuf)
                                                <tr>
                                                    <td>{{ $productmanuf->id }}</td> <!-- Batch Number -->

                                                    <td>{{ $productmanuf->product->name }}</td>

                                                    <td>

                                                        {{ $productmanuf->size->size ?? 'N/A' }}
                                                    </td>

                                                    <td>{{ $productmanuf->total_qty }}</td>
                                                    <td>


                                                        <a href="{{ route('productmanufac.show', $productmanuf->id) }}"
                                                            class="btn green btn-outline btn-xs" style="margin-right:5px;">
                                                            Details
                                                        </a>





                                                    </td>

                                                </tr>
                                            @endforeach --}}
                                            @foreach ($productmanufs as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->batch_number }}</td>
                                                    <td>{{ $item->product->name }}</td>
                                                    <td>{{ $item->size->size ?? 'N/A' }}</td>
                                                    </td>
                                                    <td>{{ $item->total_qty }}</td>
                                                    <td>{!! $helpers->convertDate($item->date) !!}</td>

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
    <script>
        $(document).ready(function() {
            $('#inventoryTable').DataTable();
        });
    </script>

@endsection
