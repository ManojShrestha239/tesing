@extends('dashboards.blank')
@section('content')

    <table class="table table-responsive" id="site-table">
        <tbody>
        <tr><td>House Owner</td><td>{{ $sites->house_owner }}</td></tr>
        <tr><td>Address</td><td>{{ $sites->address }}</td></tr>
        <tr><td>Phone</td><td>{{ $sites->phone }}</td></tr>
        <tr><td>Other phone</td><td>{{ $sites->next_phone }}</td></tr>
        <tr><td>Latitude</td><td>{{ $sites->latitude }}</td></tr>
        <tr><td>Longitude</td><td>{{ $sites->longitude }}</td></tr>
        <tr><td>Visit Date</td><td>{{ $sites->visit_date }}</td></tr>
        <tr><td>Tentative Coloring Date</td><td>{{ $sites->coloring_date }}</td></tr>
        <tr><td>Next Visit</td><td>{{ $sites->next_visit }}</td></tr>
        <tr><td>Painter Visit?</td><td>{{ $sites->painter_visit }}</td></tr>
        <tr><td>Painter Name</td><td>{{ $sites->painter_name }}</td></tr>
        <tr><td>Status</td><td>{{ $sites->status['name'] }}</td></tr>
        </tbody>
    </table>

@endsection
