<meta charset="utf-8" />
@php
    $titleName = strtolower(config('app.env')) === 'local' ? 'Dev Dashboard': config('app.company_name');
@endphp
<title>{{ $titleName }}</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport" />
<meta content="" name="author" />
<!-- BEGIN LAYOUT FIRST STYLES -->
<link href="//fonts.googleapis.com/css?family=Oswald:400,300,700" rel="stylesheet" type="text/css" />
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link rel="stylesheet" href="{{ URL::asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/global/plugins/bootstrap/css/bootstrap.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME GLOBAL STYLES -->
<link rel="stylesheet" href="{{ URL::asset('assets/global/css/components.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/global/css/plugins.min.css') }}">

<!-- END THEME GLOBAL STYLES -->
<!-- BEGIN THEME LAYOUT STYLES -->
<link rel="stylesheet" href="{{ URL::asset('assets/layouts/layout5/css/layout.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/layouts/layout5/css/custom.min.css') }}">
<!-- END THEME LAYOUT STYLES -->
<link rel="shortcut icon" href="{{{ asset('assets/pages/img/logo-fieldma.png') }}}" />
<style>
  .navActive {
    color: #315DA9 !important;
    border-bottom: 2px solid #315DA9;
    font-weight: 600 !important;
  }

  .quickNav {
    color: white !important;
    background: #315DA9 !important;
  }

  #quickNav {
    color: white !important;
    background: #315DA9 !important;
  }

  .active {
    background: #315DA9 !important;
  }
</style>
