@extends('dashboards.blank')

@section('content')

    @if(count($errors) > 0)
        @foreach($errors->all() as $error)
            <p class="alert alert-danger">{{ $error }}</p>
        @endforeach
    @endif

    <form method="POST" action="/dashboard/site/{{ $site->id }}">
        {{ method_field('PATCH') }}
        @csrf

        <div class="form-group row">
            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ $site->phone }}" name="phone" required autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="next_phone" class="col-md-4 col-form-label text-md-right">{{ __('Other Phone') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ $site->next_phone }}" name="next_phone" autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="coloring_date" class="col-md-4 col-form-label text-md-right">{{ __('Tentative Coloring Date') }}</label>

            <div class="col-md-6">
                <input type="date" class="form-control" value="{{ $site->coloring_date }}" name="coloring_date" required autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="next_visit" class="col-md-4 col-form-label text-md-right">{{ __('Next Visit') }}</label>

            <div class="col-md-6">
                <input type="date" class="form-control" value="{{ $site->next_visit }}" name="next_visit" autofocus>
            </div>
        </div>

        <div class="form-group row">
            <label for="painter_visit" class="col-md-4 col-form-label text-md-right">{{ __('Painter Visit?') }}</label>
            <div class="col-md-6">
                <select name="painter_visit" class="form-control">
                    <option value="{{ $site->painter_visit }}">{{ $site->visit['name'] }}</option>
                    @foreach($visits as $visit)
                        <option value="{{ $visit->id }}">{{ $visit->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="painter_name" class="col-md-4 col-form-label text-md-right">{{ __('Painter Name') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" value="{{ $site->painter_name }}" name="painter_name" autofocus>
            </div>
        </div>

        {{--<div class="form-group row">--}}
            {{--<label for="image" class="col-md-4 col-form-label text-md-right">{{ __('Image') }}</label>--}}

            {{--<div class="col-md-6">--}}
                {{--<input data-preview="#preview" type="file" class="form-control" name="image" accept="image/*" required autofocus>--}}
                {{--<img class="col-sm-6" id="preview"  src="">--}}
                {{--<p class="help-block">Example block-level help text here.</p>--}}
            {{--</div>--}}
        {{--</div>--}}

        <div class="form-group row">
            <label for="status_id" class="col-md-4 col-form-label text-md-right">{{ __('Approved?') }}</label>
            <div class="col-md-6">
                <select name="status_id" class="form-control">
                    <option value="{{ $site->status_id }}">{{ $site->status['name'] }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    {{ __('Update') }}
                </button>
            </div>
        </div>
    </form>

@endsection
