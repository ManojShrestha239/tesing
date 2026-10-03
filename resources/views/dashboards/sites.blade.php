@extends('dashboards.blank')

@section('content')


    <div class="page-content-col">
        @if(session('Status'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <b>{{ session('Status') }}</b>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    </div>

    <table class="table table-responsive" id="sites-table">
        <thead>
        <tr>
            <th>Id</th>
            <th>House owner</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Status</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($sites as $site)
            <tr>
                <td>{!! $site->id !!}</td>
                <td>{!! $site->house_owner !!}</td>
                <td>{!! $site->address !!}</td>
                <td>{!! $site->phone !!}</td>
                <td>{!! $site->status['name'] !!}</td>
                <td>
                    {{--{!! Form::open(['route' => ['types.destroy', $role->id], 'method' => 'delete']) !!}--}}
                    <form type="hidden" method="POST" action="/dashboard/site/{{ $site->id }}">
                        {{ method_field('DELETE') }}
                        @csrf
                        <div class='btn-group'>
                            <a href="/dashboard/site/{{ $site->id }}/show" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                            <a href="/dashboard/site/{{ $site->id }}/edit" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                            <button type="submit" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-trash"></i></button>
                        </div>
                    </form>
                    {{--{!! Form::close() !!}--}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>


@endsection
