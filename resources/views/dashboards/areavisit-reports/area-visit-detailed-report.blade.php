@inject('helpers','App\Utilities\Helpers')
<table>
    <thead>
        <tr>
            <td style="font-size:18px;"> Area Visit Detailed Report </td>
        </tr>
        <tr>
            <td colspan="9" style="height:65px;vertical-align:center;font-weight:bold;"> Area:@if($area){{$area->area}}-{{$area->areacode}}@endif <br> From: {{$from}} <br>To: {{$to}}</td>
        </tr>
        <tr>
            <td colspan="9" style="text-align:center;background-color:#dd3234;color:white;height:100px;vertical-align:center;font-weight:bold;font-size:18px;">{{env('COMPANY_NAME')}} <br> {{env('COMPANY_ADDRESS')}}</td>
        </tr>
        <tr>
            <th style="background-color:#ffbd24;font-weight:bold;"> visit No. </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Area </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> User Info </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Dealer Info </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Date </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Start To End Time </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Purpose </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Remarks </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Visit Status </th>
        </tr>
    </thead>
    <tbody>
        @foreach($list as $lis)
        <tr>
            <td style="vertical-align:center;">{{ $lis->id }}</td>
            <td style="vertical-align:center;">@if($lis->area) {{ $lis->areacode }} - {{$lis->area['area']}} @else Unlisted Dealer Visit @endif</td>
            <td style="vertical-align:center;"> {{$lis->user_id}}-{{ $lis->user['name'] }}</td>
            <td style="vertical-align:center;">@if($lis->dealer_code)
                {{$lis->dealer_code}}-{{ $lis->dealer['name'] }}
                @else
                {{$lis->dealer_name}} . <br> <span class='unlisted-dealers'> Address:</span>{{$lis->address}} <br> <span class='unlisted-dealers'> Contact:</span>{{$lis->contact}}
                @endif
            </td>
            <td style="vertical-align:center;">{{$helpers->convertDate($lis['date'])}}</td>
            <td style="vertical-align:center;"> {{$lis['start_time']}} To {{ $lis['end_time'] }}
                <?php
                $start = new DateTime($lis->start_time);
                $end = new DateTime($lis->end_time);

                $interval = $end->diff($start);
                echo "(" . $interval->format('%h') . " hrs " . $interval->format('%i') . " min" . $interval->format('%s') . " sec)";
                ?>
            </td>

            <td style="vertical-align:center;">
                @foreach($lis->visits as $visit)
                {{$visit->purposes['purpose']}},
                @endforeach
            </td>
            <td style="vertical-align:center;"> {{ $lis->remarks }}</td>
            <td style="vertical-align:center;"> {{ $lis->status }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
