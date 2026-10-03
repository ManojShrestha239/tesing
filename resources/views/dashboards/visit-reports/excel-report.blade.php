<table>
    <thead>
        <tr>
            <td style="font-size:18px;"> Visit Report </td>
        </tr>
        <tr>
            <td colspan="6" style="height:35px;vertical-align:center;font-weight:bold;"> From: {{$from}} <br>To: {{$to}}</td>
        </tr>
        <tr>
            <td colspan="6" style="text-align:center;background-color:#dd3234;color:white;height:100px;vertical-align:center;font-weight:bold;font-size:18px;">{{env('COMPANY_NAME')}} <br> {{env('COMPANY_ADDRESS')}}</td>
        </tr>
        <tr>
            <th style="background-color:#ffbd24;font-weight:bold;"> S/N</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Staff no. </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Staff Name </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Total Visit </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Purpose Wise Visit </th>
            <th style="background-color:#ffbd24;font-weight:bold;">Collection Amount</th>
        </tr>
    </thead>
    <tbody>
        @if (!empty($list))

        @foreach($list as $lis)
        <tr>
            <td> {{$loop->iteration}} </td>
            <td> {{$lis->user_id}} </td>
            <td> {{ $lis->user['name'] }}</td>
            <td> {{$lis->visit_count}} </td>
            <td> @foreach($lis->purposes as $purpose)
                {{$purpose->purposes['purpose']}}- {{$purpose->visit_count}} <br>
                @endforeach
            </td>
            <td>
                {{number_format($lis->collection,2)}}
            </td>
        </tr>
        @endforeach

        @endif

    </tbody>
</table>
