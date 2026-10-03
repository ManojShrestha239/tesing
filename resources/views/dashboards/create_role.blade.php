@extends('dashboards.blank')

@section('content')
    {{--<link href="../assets/pages/css/login.min.css" rel="stylesheet" type="text/css" />--}}

    @if(count($errors) > 0)
        @foreach($errors->all() as $error)
            <p class="alert alert-danger">{{ $error }}</p>
        @endforeach
    @endif

    <form method="POST" action="{{ route('role.create') }}">
        @csrf

        <div class="form-group row">
            <label for="role" class="col-md-4 col-form-label text-md-right">{{ __('New Role Name') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ old('role') }}" name="role" placeholder="Role Name" required autofocus>

                {{--@if ($errors->has('name'))--}}
                {{--<span class="invalid-feedback" role="alert">--}}
                {{--<strong>{{ $errors->first('name') }}</strong>--}}
                {{--</span>--}}
                {{--@endif--}}
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    {{ __('Create') }}
                </button>
            </div>
        </div>
    </form>

    {{--<script src="../assets/pages/scripts/login.min.js" type="text/javascript"></script>--}}
@endsection
