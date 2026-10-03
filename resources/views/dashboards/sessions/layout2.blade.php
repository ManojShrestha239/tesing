<!DOCTYPE html>
<html>
<!-- BEGIN HEAD -->

<head>



    <link rel="stylesheet" href="{{ URL::asset('assets/global/css/bootstrap-select.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css" rel="stylesheet">
    <link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('css/nepali.datepicker.v2.1.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('js/nepalidatepicker.min.js') }}" type="text/javascript"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        $(document).ready(function(){
            // var id = $(".order-hover").attr('id');
            // window.alert(id);             
            // var reference = [];
            $(".orders-class a").click(function(){
            var holder = $(this).attr('class');
            $("body").load("/dashboard/orders/"+ holder);
            // reference.push(holder);
            // $("." + id).addClass('navActive');
        });
        //  $("." + class).addClass('navActive');  
    //     function help(){
    //      window.onload = $(document).ready(function(){
    //         $("." + reference).addClass('navActive');
    //      });
      
    // }
        });
    </script>
         <script>
            $(document).ready(function(){
                $(".cheque-collection").click(function(){
                    $(".page-content").load("/dashboard/cheque-collection");
                });
            });
        </script>

    <script>
        $(document).ready(function(){
            $(".report-class a").click(function(){
            var holder = $(this).attr('class');
            $("body").load("/dashboard/"+ holder);
        });
        });
    </script>
        <style>
                .arrow{
                     display:none !important;
                }

        </style>

</head>
<!-- END HEAD -->

<body class="page-header-fixed page-sidebar-closed-hide-logo">
<!-- BEGIN CONTAINER -->
<div class="wrapper">
    <!-- BEGIN HEADER -->
  
    <!-- END HEADER -->
    <div class="container-fluid">
 
        

    </div>
</div>
<!-- END CONTAINER -->

<!-- BEGIN QUICK NAV -->


<!-- END QUICK NAV -->
<!--[if lt IE 9]>
<script src="{{ URL::asset('assets/global/plugins/respond.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/excanvas.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/ie8.fix.min.js') }}" type="text/javascript" ></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->

<script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('js/nepali.datepicker.v2.1.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/pages/scripts/table-datatables-buttons.js') }}" type="text/javascript"></script>

        
        <script src="{{ URL::asset('js/nepali.datepicker.v2.1.min.js') }}"></script>
        
        <script src="{{ URL::asset('assets/global/scripts/bootstrap-select.js') }}"></script>



<script>
    function closePopover(){
        // document.getElementByClassName('popover').style.display='none';
        $('.popover').css('display','none');
    }
    $(document).ready(function ()
{
    $('#nepaliDateF').nepaliDatePicker({
        ndpEnglishInput: 'englishDateF'
    });

    $('#nepaliDateFa').nepaliDatePicker({
        ndpEnglishInput: 'englishDateFa'
    });

});
        // var content = $('[id="myForm"]');
        $(function() {
            $('[data-toggle="popover"]').popover({
               trigger: 'click',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                placement: 'right',
                title: '<b>Change Order Status</b>',
                // title:' <button onclick="closePopover();" class="btn btn-danger">Close</button>',
                html: true
    }).on('shown.bs.popover', function () {
        
        var popover = $(this);

        var contentEl = popover.next(".popover").find(".popover-content");       
        var myParameter = popover.data('api-parameter');       
          var result = '<form action="/dashboard/order/'+myParameter+'" id="popForm" method="post">' +
                                                    '<input type="hidden" name="_method" value="PATCH">' +
                                                    '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">' +
                                                    '<div><label for="status">Status:</label>'+
                                                        '<select name="status" id="status" class="form-control input-md"><option value="pending">pending</option><option value="ongoing">ongoing</option><option value="completed">completed</option><option value="cancelled">cancelled</option></select><br>'+
                                                        '<label for="status">Remark:</label>'+
                                                        '<input type = "text" name="remark" class="form-control"><br>'+
                                                        '<button type="submit" class="btn btn-primary">Update</button>'+
                                                       
                                                    '</div></form>'+'<img style = "position:absolute;top:-8px; right:-8px;width:25px; height:25px;" onclick="closePopover();" id="popoverClose" src={{ asset('assets/pages/img/quit.svg') }}>';

          
          // contentEl.toggle();
          contentEl.html(result);
        });
        
        $('[data-toggle="popoverche"]').popover({
               trigger: 'click',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                placement: 'left',
                title: '<b>Change Cheque Status</b>',
                html: true
    }).on('shown.bs.popover', function () {
        
        var popover = $(this);

        var contentElche = popover.next(".popover").find(".popover-content");       
        var myParameterche = popover.data('api-parameter');       
          var resultche = '<form action="/dashboard/cheque-collection/'+myParameterche+'" id="popForm" method="post">' +
                                                    '<input type="hidden" name="_method" value="PATCH">' +
                                                    '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">' +
                                                    '<div><label for="status">Status:</label>'+
                                                        '<select name="status" id="status" class="form-control input-md"><option value="sent to office">Sent to Office</option><option value="withdrawn">Withdrawn</option><option value="withdrawn & deposited">Withdrawn & Deposited</option><option value="bounced">Bounced</option></select><br>'+
                                                        '<label for="status">Remark:</label>'+
                                                        '<input type = "text" name="remark" class="form-control"><br>'+
                                                        '<button type="submit" class="btn btn-primary">Update</button>'+
                                                        '</div></form>'+'<img style = "position:absolute;top:-8px; right:-8px;width:25px; height:25px;" onclick="closePopover();" id="popoverClose" src={{ asset('assets/pages/img/quit.svg') }}>';
          
           
          
          // contentEl.toggle();
          contentElche.html(resultche);
        });

            $('[data-toggle="popoverch"]').popover({
                trigger: 'click',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                placement: 'left',
                title: '<b>Change Cheque Status</b>',
                html: true
            }).on('shown.bs.popover', function () {

                var popover = $(this);

                var contentElch = popover.next(".popover").find(".popover-content");
                var myParameterch = popover.data('api-parameter');
                var resultch = '<form action="/dashboard/cheque-collection/'+myParameterch+'" id="popForm" method="post">' +
                    '<input type="hidden" name="_method" value="PATCH">' +
                    '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">' +
                    '<div><label for="status">Status:</label>'+
                    '<select name="status" id="status" class="form-control input-md"><option value="sent to Office">Sent to Office</option><option value="deposited">Deposited</option><option value="bounced">Bounced</option><option value="cashed">Cashed</option></select><br>'+
                    '<label for="status">Remark:</label>'+
                    '<input type = "text" name="remark" class="form-control"><br>'+
                    '<button type="submit" class="btn btn-primary">Update</button>'+
                    '</div></form>'+'<img style = "position:absolute;top:-8px; right:-8px;width:25px; height:25px;" onclick="closePopover();" id="popoverClose" src={{ asset('assets/pages/img/quit.svg') }}>';
                    
                // contentEl.toggle();
                contentElch.html(resultch);
            });

});

    </script>
    <script>

        var date = new Date();
        var cYear = date.getFullYear();
        var cMonth = date.getMonth();
        var cDate = date.getDate();

        $(document).ready(function () {
            $("#from").datepicker({
                dateFormat: 'yy-mm-dd',
                maxDate: new Date(cYear, cMonth, cDate)
            });
        });

        $(document).ready(function () {
            $("#to").datepicker({
                dateFormat: 'yy-mm-dd',
                maxDate: new Date(cYear, cMonth, cDate)
            });
        });

        /*$('.datepicker').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-3d'
        });*/

    </script>

@include('dashboards.partials.js')

</body>

</html>