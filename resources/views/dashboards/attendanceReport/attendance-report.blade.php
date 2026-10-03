@inject('helpers', 'App\Utilities\Helpers')
<table>
    <thead>
        <tr>
            <td colspan="11" style="height:65px;vertical-align:center;font-weight:bold;"> User :@if ($user)
                {{ $user->name }}-{{ $user->id }}
                @endif <br> From: {{ $data['from'] }} <br>To: {{ $data['to'] }}
            </td>
        </tr>
        <tr>
            <td colspan="11" style="text-align:center;background-color:#dd3234;color:white;height:100px;vertical-align:center;font-weight:bold;font-size:18px;">
                {{ env('COMPANY_NAME') }} <br> {{ env('COMPANY_ADDRESS') }}
            </td>
        </tr>
        <tr>
            <th colspan="2"></th>
            <th colspan="3"> CHECKIN</th>
            <th colspan="3">CHECKOUT</th>
            <th colspan="3"></th>
        </tr>
        <tr>
            <th style="background-color:#ffbd24;font-weight:bold;"> S/N </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> USER </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> DATE </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> IN-TIME </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> EARLY </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> LATE </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> OUT-TIME </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> EARLY </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> LATE </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> LAST STATUS </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> PUNCH COUNT </th>
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
            <td style="vertical-align:center;">{{ $helpers->convertDate($attendance->date) }}</td>
            <td style="vertical-align:center;"> {{ $attendance->time }} </td>
            <td style="vertical-align:center;"> {{ $attendance->early }} </td>
            <td style="vertical-align:center;"> {{ $attendance->late }} </td>
            <td style="vertical-align:center;"> {{ $attendance->checkout }} </td>
            <td style="vertical-align:center;"> {{ $attendance->earlycheckout }} </td>
            <td style="vertical-align:center;"> {{ $attendance->latecheckout }} </td>
            <td style="vertical-align:center;"> {{ $attendance->status }} </td>
            <td style="vertical-align:center;"> {{ $attendance->total_punch }} </td>
            <td style="vertical-align:center;"> @if($attendance->attendance) {{ round($attendance->attendance->totalkm,2) }} KM @endif </td>
        </tr>
        @endforeach
        @endif

    </tbody>
</table>
