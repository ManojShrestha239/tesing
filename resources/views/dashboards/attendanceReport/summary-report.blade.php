@inject('helpers', 'App\Utilities\Helpers')
<table>
    <thead>
        <tr>
            <td colspan="9" style="height:65px;vertical-align:center;font-weight:bold;"> From: {{ $from }}
                <br>To: {{ $to }} <br>
                Total Days : {{ $total }}
            </td>
        </tr>
        <tr>
            <td colspan="9" style="text-align:center;background-color:#dd3234;color:white;height:100px;vertical-align:center;font-weight:bold;font-size:18px;">
                {{ env('COMPANY_NAME') }} <br> {{ env('COMPANY_ADDRESS') }}
            </td>
        </tr>
        <tr>
            <th colspan="2"></th>
            <th colspan="2" style="text-align: center;"> Checkin</th>
            <th colspan="2" style="text-align: center;">Checkout</th>
            <th colspan="3"></th>
        </tr>
        <tr>
            <th style="background-color:#ffbd24;font-weight:bold;"> S/N </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> USER </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> EARLY </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> LATE </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> EARLY </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> LATE </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> TOTAL DAYS </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> TOTAL PRESENT </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> TOTAL ABSENT</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> DISTANCE TRAVEL </th>
        </tr>
    </thead>
    <tbody>

        @if (!empty($attendances))
        @foreach ($attendances as $attendance)
        <tr>
            <td style="vertical-align:center;">{!! $loop->iteration !!}</td>
            <td style="vertical-align:center;">
                {{ $attendance->mobileuser['name'] }}-({{ $attendance->mobileuser['id'] }})
            </td>
            <td style="vertical-align:center;"> {{ $attendance->early }} </td>
            <td style="vertical-align:center;"> {{ $attendance->late }} </td>
            <td style="vertical-align:center;"> {{ $attendance->earlycheckout }} </td>
            <td style="vertical-align:center;"> {{ $attendance->latecheckout }} </td>
            <td style="vertical-align:center;"> {{ $total }} </td>
            <td style="vertical-align:center;"> {{ $attendance->total_present }} </td>
            <td style="vertical-align:center;"> {{ $attendance->absent }} </td>
            <td style="vertical-align:center;"> {{round($attendance->final_km,2)}} KM </td>
        </tr>
        @endforeach
        @endif

    </tbody>
    <tbody>
