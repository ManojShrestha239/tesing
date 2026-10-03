@extends('dashboards.blank')

@section('content')
<div class="page-content">
        <div class="breadcrumbs">
        <h1>All Shades</h1>
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
                .img{
                        margin-left:40%;
                        width:25px; 
                        height:25px;
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
                                        <li class="active"><a href="/dashboard/colorant">All Shades </a></li>
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
                                            <span class="caption-subject font-red bold uppercase">shades</span>
                                            </div>
                                            <div class="protlet-body">
                                            <table class="table table-striped table-bordered    " id="sample_3">
                                        <thead>
                                        <tr>
                                            <th> S/N</th>
                                            <th>Name & code</th>
                                            <th>Biraj Distemper</th>
                                            <th>Fewa Distemper</th>
                                            <th>Ever GreenWEE</th>
                                            <th>Glint TouchIE</th>
                                            <th>WallMineIE</th>
                                            <th>Weather WarriorEE</th>
                                           
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($shades as $shade)
                                        <?php 
                                        $r =$shade->rValue;
                                         $g =$shade->gValue;
                                         $b =$shade->bValue;
                                        ?>
                                        <tr class="colorcode" style="background:rgb({{$r}}, {{$g}}, {{$b}});">
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$shade->colorName}} <br>{{$shade->colorCode}} </td>
                                                    <td>
                                                            @if($shade->birajDistemper==1)
                                                            <img src="{{ asset('assets/global/img/tick.svg') }}" class='img' alt="" srcset="">
                                                            @else
                                                            <img src="{{ asset('assets/global/img/close.svg') }}" class='img' alt="" srcset="">
                                                                @endif
                                                        </td>
                                                    <td>
                                                    @if($shade->fewaDistemper== 1)
                                                            <img src="{{ asset('assets/global/img/tick.svg') }}" class='img' alt="" srcset="">
                                                            @else
                                                            <img src="{{ asset('assets/global/img/close.svg') }}" class='img' alt="" srcset="">
                                                                @endif
                                                    </td>
                                                    <td>
                                                    @if($shade->evergreenWEE==1)
                                                            <img src="{{ asset('assets/global/img/tick.svg') }}" class='img' alt="" srcset="">
                                                            @else
                                                            <img src="{{ asset('assets/global/img/close.svg') }}" class='img' alt="" srcset="">
                                                                @endif
                                                    </td>
                                                    <td>
                                                    @if($shade->glintTouchIE== 1)
                                                            <img src="{{ asset('assets/global/img/tick.svg') }}" class='img' alt="" srcset="">
                                                            @else
                                                            <img src="{{ asset('assets/global/img/close.svg') }}" class='img' alt="" srcset="">
                                                                @endif
                                                    </td>
                                                    <td>
                                                    @if($shade->wallMineIE== 1 )
                                                            <img src="{{ asset('assets/global/img/tick.svg') }}" class='img' alt="" srcset="">
                                                            @else
                                                            <img src="{{ asset('assets/global/img/close.svg') }}" class='img' alt="" srcset="">
                                                                @endif
                                                    </td>
                                                    <td>
                                                    @if($shade->weatherWarriorEE==1)
                                                            <img src="{{ asset('assets/global/img/tick.svg') }}" class='img' alt="" srcset="">
                                                            @else
                                                            <img src="{{ asset('assets/global/img/close.svg') }}" class='img' alt="" srcset="">
                                                                @endif
                                                    </td>
                                            </tr>
                                        @endforeach

                                        </tbody>

                                        <span class="link">{{ $shades->links() }}</span>   
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