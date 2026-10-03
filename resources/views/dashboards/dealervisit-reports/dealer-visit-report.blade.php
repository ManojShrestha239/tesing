<table>
    <thead>
        <tr>
            <td style="font-size:18px;"> Dealer Visit Report </td>
        </tr>
    <tr>
            <td colspan="6" style="height:65px;vertical-align:center;font-weight:bold;"> Dealer:@if($dealer){{$dealer->name}}-{{$dealer->code}}@endif <br> From: {{$from}} <br>To: {{$to}}</td>
        </tr>
        <tr>
            <td colspan="6" style="text-align:center;background-color:#dd3234;color:white;height:100px;vertical-align:center;font-weight:bold;font-size:18px;">{{env('COMPANY_NAME')}} <br> {{env('COMPANY_ADDRESS')}}</td>
        </tr>
        <tr>
            <th style="background-color:#ffbd24;font-weight:bold;"> S/N </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Dealer Code</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Dealer </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Total Visit </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Purpose wise Visit </th>
            <th style="background-color:#ffbd24;font-weight:bold;">Total Collection</th>
        </tr>
    </thead>
    <tbody>
        @foreach($list as $lis)
        <?php
        // if (!$lis->dealer_code) {
        //     continue;
        // }
        ?>
        <tr>
            <td style="vertical-align:center;"> {{$loop->iteration}} </td>
            <td style="vertical-align:center;">@if($lis->dealer_code) {{ $lis->dealer_code }} @else Unlisted Dealer Visit @endif</td>
            <td style="vertical-align:center;">@if($lis->dealer_code){{ $lis->dealer['name'] }} @else Unlisted Dealer Visit @endif</td>
            <td style="vertical-align:center;">{{ $lis->visit_count }}</td>
            <td style="vertical-align:center;">@foreach($lis->purposes as $purpose)
                {{$purpose->purposes['purpose']}}- {{$purpose->visit_count}} <br>
                @endforeach
            </td>

            <td style="vertical-align:center;">
                {{$lis->collection}}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
