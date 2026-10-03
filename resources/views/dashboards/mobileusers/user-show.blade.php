@extends('dashboards.blank')
@section('content')

    <table class="table table-responsive" id="mobileusers-table">
        <tr>
            <tr><td>Name</td><td>{{ $mobileusers->name }}</td></tr>
            <tr><td>Email</td><td>{{ $mobileusers->email }}</td></tr>
            <tr><td>Phone</td><td>{{ $mobileusers->phone }}</td></tr>

            {{--<tr><td>Area Code</td><td>{{ $mobileusers->areacode }}</td></tr>--}}
            <tr><td>Area</td><td>
            @foreach ($mobileusers->area as $area)
                {{ $area['area'] }},
            @endforeach
                </td></tr>
            {{--<tr><td>Area</td><td>{{ $mobileusers->pivot->area }}</td></tr>--}}

            {{--<tr><td>Area</td><td>{{ $mobileusers->area }}</td></tr>--}}
            <tr><td>Role</td><td>{{ $mobileusers->role['role'] }}</td></tr>
        </tbody>
    </table>

@endsection
