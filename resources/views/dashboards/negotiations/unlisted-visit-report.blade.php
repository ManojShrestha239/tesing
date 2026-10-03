<table>
    <thead>
        <tr>
            <td style="font-size:18px;"> Unlisted Visit Report </td>
        </tr>
        <tr>
            <td colspan="5" style="height:65px;vertical-align:center;font-weight:bold;"> From: {{$from}} <br>To: {{$to}}</td>
        </tr>
        <tr>
            <td colspan="5" style="text-align:center;background-color:#dd3234;color:white;height:100px;vertical-align:center;font-weight:bold;font-size:18px;">{{env('COMPANY_NAME')}} <br> {{env('COMPANY_ADDRESS')}}</td>
        </tr>
        <tr>
            <th style="background-color:#ffbd24;font-weight:bold;"> S/N </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Staff Info</th>
            <!-- <th style="background-color:#ffbd24;font-weight:bold;"> Dealer </th> -->
            <th style="background-color:#ffbd24;font-weight:bold;"> Total Visit </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Purpose wise Visit </th>
            <th style="background-color:#ffbd24;font-weight:bold;">Status</th>
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
            <td style="vertical-align:center;">{{ $lis->user['name'] }} - {{$lis->user_id}}</td>
            <!-- <td style="vertical-align:center;">{{ $lis->dealer_name }} - {{$lis->dealer_address}}</td> -->
            <td style="vertical-align:center;">{{ $lis->visit_count }}</td>
            <td style="vertical-align:center;">
                @foreach($lis->purposes as $purpose)
                    {{$purpose->purposes['purpose']}}- {{$purpose->visit_count}} <br>
                @endforeach
            </td>

            <td style="vertical-align:center;">
                {{$lis->status}}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
