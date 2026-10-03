@inject('helpers','App\Utilities\Helpers')
<table>
    <thead>
        <tr>
            <td colspan="8" style="height:65px;vertical-align:center;font-weight:bold;"> User :@if($user){{$user->name}}-{{$user->id}}@endif <br> From: {{$from}} <br>To: {{$to}}</td>
        </tr>
        <tr>
            <td colspan="8" style="text-align:center;background-color:#dd3234;color:white;height:100px;vertical-align:center;font-weight:bold;font-size:18px;">{{env('COMPANY_NAME')}} <br> {{env('COMPANY_ADDRESS')}}</td>
        </tr>
        <tr>
            <td style="font-size:18px;"> Unlisted Visit Detailed Report </td>
        </tr>
        <tr>
            <th style="background-color:#ffbd24;font-weight:bold;"> Visit No. </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> User Info </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Dealer Info</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Purpose </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Date </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Start to End Time</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Status </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Remarks </th>
        </tr>
    </thead>
    <tbody>
        @if (!empty($list))

        @foreach($list as $lis)
        <tr>
            <td style="vertical-align:center;"> {{$lis->id}} </td>
            <td style="vertical-align:center;"> {{ $lis->user['id'] }}-{{ $lis->user['name'] }}</td>
            <td style="vertical-align:center;">{{ $lis->dealer_name }} - {{$lis->dealer_address}} ({{$lis->contact}}) </td>
            <td style="vertical-align:center;">@foreach($lis->visits as $visit)
                {{$visit->purposes['purpose']}},
                @endforeach
            </td>
            <td style="vertical-align:center;">{{$helpers->convertDate($lis['date'])}}</td>
            <td style="vertical-align:center;"> {{$lis->start_time}} To {{ $lis->end_time }}
                <?php
                $start = new DateTime($lis->start_time);
                $end = new DateTime($lis->end_time);

                $interval = $end->diff($start);
                echo "(" . $interval->format('%h') . " hrs " . $interval->format('%i') . " min" . $interval->format('%s') . " sec)";
                ?>
            </td>
            <td style="vertical-align:center;">{{$lis->status}}</td>
            <td style="vertical-align:center;"> {{ $lis->remarks }} </td>
        </tr>
        @endforeach
        @endif

    </tbody>
</table>
