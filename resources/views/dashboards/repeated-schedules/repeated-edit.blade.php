@extends('dashboards.sessions.layout')

@section('content')
<style>
    #goback:hover {
        background: red;
        border: 2px solid #f48220;
        cursor: pointer;
        transition: 0.4s;
    }

    #goback:hover h3 {
        color: white;
    }

    #goback:hover img {
        color: white;
    }

    #goback {
        border: 2px solid #0d2444;
        width: 100px;
        border-radius: 32px;
    }

    #goback img {
        margin-top: 6px;
        margin-left: 4px;
        margin-right: 4px;
    }

    #goback h3 {
        margin-top: 6px;
        font-size: 20px;
        font-weight: 600;
    }

    .checkbox-item {
        height: 20px;
        width: 20px;
    }

    .span-dealer {
        margin-left: 20px;
    }

    .chkbox-subarea {
        max-height: 200px;
        overflow: auto;
    }

    .chkbox-dealer {
        max-height: 200px;
        overflow: auto;
    }

    .col-md-6 {
        padding-right: 0;
    }
</style>
<div class="page-content2">
    <div id="goback" onclick="goBack()">
        <img style="float:left;width:25px; height:25px;" onclick="goBack()" src={{ asset('assets/global/img/left-arrow-key.svg') }}>
        <h3> Back</h3>
    </div>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
    <div class="breadcrumbs">
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
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class=" icon-map font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">Edit Visit Schedule</span>
                                </div>

                            </div>

                            @if(count($errors) > 0)
                            @foreach($errors->all() as $error)
                            <p class="alert alert-danger">{{ $error }}</p>
                            @endforeach
                            @endif

                            <form method="POST" action="/repeated/{{$schedule->id}}">
                                @csrf
                                @method('PATCH')
                                <div class="form-group row">
                                    <label for="user_id" class="col-md-3 col-form-label text-right">{{ __('Marketing Officer') }}</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" value="{{$schedule->mobile->name}}" disabled>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="day" class="col-md-3 col-form-label text-right">Day</label>
                                    <div class="col-md-6">
                                        <select name="day" class="form-control selectpicker" data-live-search="true" id="day" required>
                                            <option value="">Select Day</option>
                                            <option @if($schedule->day=='Sunday') selected @endif value="Sunday">Sunday</option>
                                            <option @if($schedule->day=='Monday') selected @endif value="Monday">Monday</option>
                                            <option @if($schedule->day=='Tuesday') selected @endif value="Tuesday">Tuesday</option>
                                            <option @if($schedule->day=='Wednesday') selected @endif value="Wednesday">Wednesday</option>
                                            <option @if($schedule->day=='Thursday') selected @endif value="Thursday">Thursday</option>
                                            <option @if($schedule->day=='Friday') selected @endif value="Friday">Friday</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row area-class">

                                    <label for="area" class="col-md-3 col-form-label text-right">{{ __('Area') }}</label>
                                    <div class="col-md-6 chkbox-area">
                                        <input type="text" id="area-search" onkeyup="searchArea()" placeholder="Search for area.." class="form-control">
                                        <ul id="myUl-area" style="list-style-type: none;overflow-y: auto;">
                                            @foreach($allareas as $all)
                                            <li>
                                                <a style="text-decoration: none;color: black;">
                                                    <input type="checkbox" class="area-checkbox" value="{{$all->areacode}}" name="area[]" @foreach(explode(' :: ', $schedule->areacode) as $ar) @if($all->areacode == $ar) checked @endif @endforeach><span>{{$all->area}}</span><br>
                                                 </a>
                                        </li>
                                            @endforeach
                                        </ul>
                                        </div>
                                    </div>



                                    <div class="form-group row sub-area-class" >

                                        <label for="subarea" class="col-md-3 col-form-label text-right">{{ __('Sub Area') }}</label>
                                        <div class="col-md-6 chkbox-subarea">
                                        <input type="text" id="sub-area-search" onkeyup="searchSubArea()" placeholder="Search for subarea.." class="form-control">
                                        <ul id="myUL-subarea" class="select-subarea" style="list-style-type: none;overflow: auto;">
                                        @foreach($data as $s)
                                        <li>
                                            <a style="text-decoration:none; color:black;">
                                                <input type="checkbox" class="new-subarea subarea-checkbox" value="{{$s->subarea}}" name="subarea[]" @foreach($schedule->subarea as $sub) @if($sub->id == $s->subarea_id) checked @endif @endforeach><span class="new-subarea">{{$s->subarea}}</span><br>
                                            </a>
                                        </li>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="form-group row dealer-class" >

                                    <label for="dealer" class="col-md-3 col-form-label text-right">{{ __('Dealers') }}</label>
                                    <div class="col-md-6 chkbox-dealer">
                                    <input type="text" id="dealer-search" onkeyup="searchDealer()" placeholder="Search for dealer.." class="form-control">
                                        <ul id="myUL-dealer" class="select-dealer chkbox-dealer" style="list-style-type: none;overflow: auto;">
                                                 @foreach($dealers as $s)
                                                 <li>
                                                    <a style="text-decoration:none; color:black;">
                                                        <input type="checkbox" class="new-dealer dealer-checkbox" name="dealer[]" value="{{$s->code}}" @foreach($schedule->dealer as $sub) @if($sub->code == $s->code) checked @endif @endforeach><span class="new-dealer">{{$s->name}}</span><br>

                                                    </a>
                                                 </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="remarks" class="col-md-3 col-form-label text-right">{{ __('Remarks') }}</label>

                                        <div class="col-md-6">
                                            <textarea class="form-control" name="remarks" placeholder="Remarks" required autofocus>{{$schedule->remarks}}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row hidden">
                                        <input name="status" type="text" value="not-approved">
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button type="submit" class="btn btn-primary" style="width: 200px;">
                                                {{ __('Update') }}
                                            </button>
                                        </div>
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
 function searchArea() {
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById("area-search");
        filter = input.value.toUpperCase();
        ul = document.getElementById("myUl-area");
        li = ul.getElementsByTagName("li");
        // li = ul.getElementsByTagName("input:checkbox");
        console.log(li.length);
        // li = document.getElementsByClassName("checkbox-area");
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("a")[0];
            // a = li[i].getElementsByTagName("label")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }
    function searchSubArea() {
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById("sub-area-search");
        // console.log(input.value);
        filter = input.value.toUpperCase();
        ul = document.getElementById("myUL-subarea");
        li = ul.getElementsByTagName("li");
        // li = ul.getElementsByTagName("input:checkbox");
        console.log(li.length);
        // li = document.getElementsByClassName("checkbox-area");
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("a")[0];
            // a = li[i].getElementsByTagName("label")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }
    function searchDealer() {
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById("dealer-search");
        // console.log(input.value);
        filter = input.value.toUpperCase();
        console.log(filter);
        ul = document.getElementById("myUL-dealer");
        li = ul.getElementsByTagName("li");
        // console.log(li.length);
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("a")[0];
            // a = li[i].getElementsByTagName("label")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }
jQuery(document).ready(function(){
// jQuery(' select[name="user_id" ]').on('change',function(){ // var id=jQuery(this).val(); // if(id){ // jQuery.ajax({ // url:"/schedule/area/"+id, // type:"get", // dataType:"json", // success:function(data) // { // $('.chkbox-area').empty(); // $(".area-class").css('display','block'); // // jQuery('select[name="Dealer_code" ]').empty(); // // $('select[name="Dealer_code" ]').append('<option value="">Select Dealer</option>');
                                                    // data.forEach(function(e){
                                                    // $('.chkbox-area').append('<input type="checkbox" class="checkbox-item" id="area-data" value="' + String(e.areacode) + '" name="area[]" autofocus><span class="span-dealer">' + e.area + '</span> <br>');

                                                    // // $('select[name="Dealer_code"]').append('<option value="'+e.code+'">'+ e.name +'</option>');
                                                    // });
                                                    // }
                                                    // });
                                                    // }
                                                    // });
                                                    $(document).on('click','.area-checkbox',function(e){
                                                    $('.new-subarea').remove();
                                                    var searchIDs = [];
                                                    console.log($("input[class='area-checkbox']:checked"));
                                                    var data = $("input[class='area-checkbox']:checked").map(function(){
                                                    searchIDs.push($(this).val());
                                                    $.ajax({
                                                    url:"/getsubarea",
                                                    type: "get",
                                                    data:{
                                                    data : searchIDs,
                                                    },

                                                    success:function(response){
                                                    $('.new-subarea').remove();
                                                    response.map(function(data){
                                                    $('.select-subarea').prepend('<li><a style="text-decoration: none;color: black;"><input type="checkbox" class="new-subarea" name="subarea[]" value="' + data.subarea + '"><label style="margin-left:5px;" class="new-subarea">"' + data.subarea + '"</label></a></li>')

                                            // foreach(var i=0; i<response.lenhth) $('.chkbox-subarea').prepend('<input type="checkbox" class="new-subarea subarea-checkbox" id="subarea-data" name="subarea[]" value="' + data.subarea + '"><label style="margin-left:5px;" class="new-subarea">' + data.subarea + '</label> <br>' )

                                                });

                                                },
                                                });
                                                });

                                                });
                                                $(document).on('click','.subarea-checkbox',function(e){
                                                $('.new-dealer').remove();
                                                var searchIDs = [];
                                                $(".subarea-checkbox:checkbox:checked").map(function(e){
                                                searchIDs.push($(this).val());
                                                console.log(searchIDs.push($(this).val()));
                                                $.ajax({
                                                url:"/getdealer",
                                                type: "get",
                                                data:{
                                                data : searchIDs,
                                                },

                                                success:function(response){
                                                $('.new-dealer').remove();
                                                response.map(function(data){
                                                $('.chkbox-dealer').append('<li><a style="text-decoration: none;color: black;"><input type="checkbox" class="new-dealer" id="dealer-data" name="dealer[]" value="' + data.code + '"><label style="margin-left:5px;" class="new-dealer">"' + data.name + '"</label></a></li>')

                                                // foreach(var i=0; i<response.lenhth) $('.chkbox-dealer').prepend("<input type='checkbox' class='new-dealer dealer-checkbox' name='dealer[]' value=" + data.code + "><label style='margin-left:5px;' class='new-dealer'>" + data.name + "</label> <br>" )
                                                    });

                                                    },
                                                    });
                                                    });
                                                    });
                                                    });
                                                    </script>
                                                    @endsection