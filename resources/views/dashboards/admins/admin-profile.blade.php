@extends('dashboards.blank')

@section('content')

    <table class="table table-responsive" id="users-table">
        <tbody>
        <tr><td>Name</td><td>{!! $user->name !!}</td></tr>
        <tr><td>Email</td><td>{!! $user->email !!}</td></tr>
        <tr><td>Phone</td><td>{!! $user->phone !!}</td></tr>
        </tbody>
    </table>

@endsection
