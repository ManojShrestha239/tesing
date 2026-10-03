@extends('dashboards.blank')

@section('content')
<div class="page-content">
        <div class="breadcrumbs">
        <h1>All Base</h1>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">
                <span class="sr-only"> Toogle Navigation </span>
                <span class="toggle-icon">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </span>
        </button>
        </div>
        <style>
                .link{
                        position:absolute;
                        bottom: 20px ;
                        right:44px ;
                }
        </style>
        <div class="page-content-container">
                <div class="page-content-row" id="replecable">
                        <!-- <div class="page-sidebar">
                                <div class="portlet light protlet-fit">
                                        <div class="portlet-title">
                                                <div class="caption">
                                                <i class=" icon-bar font-red"></i>
                                                <span class="caption-subject font-red bold uppercase">Quick Navigation</span>
                                                </div>
                                        </div>
                                        <nav class="navbar" role="navigation">

                                        <ul class="nav navbar-nav margin-bottom-35 products-class">
                                        <li class="active"><a href="/dashboard/base">All Base </a></li>
                                        </ul>

                                        </nav>
                                </div>

                        </div> -->

                        <div class="page-content-col">
                        @if(session('Status'))
                        
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
                                            <span class="caption-subject font-red bold uppercase">Base in Liter</span>
                                            </div>
                                            <div class="protlet-body">
                                            <table class="table table-striped table-bordered table-hover" id="sample_3">
                                        <thead>
                                        <tr>
                                            <th> S/N</th>
                                            <th>Base Name</th>
                                            <th>1 Kg/ltr</th>
                                            <th>5 Kg/ltr</th>
                                            <th>10 Kg/ltr</th>
                                            <th>20 Kg/ltr</th>
                                             <th>Action</th> 
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($bases as $base)
                                            <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$base->base}}</td>
                                                <td><input type="number" class="form-control" value="{{$base->unitPrice1}}"></td>
                                                <td><input type="number" class="form-control" value="{{$base->unitPrice2}}"></td>
                                                <td><input type="number" class="form-control" value="{{$base->unitPrice3}}"></td>
                                                <td><input type="number" class="form-control" value="{{$base->unitPrice4}}"></td>
                                                <td> 
                                                <form method="post" action="/dashboard/basecolor/{{$base->id}}">
                                                    @method('PATCH')
                                                    @csrf
                                                    <button class="btn btn-primary btn-xs">Update</button>
                                                </form>
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>

                                        <span class="link">{{ $bases->links() }}</span>   
                                    </table>
                                          </div>
                                    </div>    
                            </div>

                    </div>
            </div>
                        </div>

                </div>
        </div>
</div>
@endsection