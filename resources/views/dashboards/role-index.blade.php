@extends('dashboards.blank')

@section('content')

    <table class="table table-responsive" id="roles-table">
        <thead>
        <tr>
            <th>Id</th>
            <th>Role Name</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{!! $role->id !!}</td>
                <td>{!! $role->role !!}</td>
                <td>
                    {{--{!! Form::open(['route' => ['types.destroy', $role->id], 'method' => 'delete']) !!}--}}
                    <div class='btn-group'>
                        {{--<a class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>--}}
                        <a class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        <a class='btn btn-default btn-xs'><i class="glyphicon glyphicon-trash"></i></a>
                    </div>
                    {{--{!! Form::close() !!}--}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>


@endsection
