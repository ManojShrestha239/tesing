<table>
    <thead>
        <tr>
            <td style="font-size:18px;"> Area Visit  Report </td>
        </tr>
        <tr>
            <td colspan="7" style="height:65px;vertical-align:center;font-weight:bold;"> Area:@if($area){{$area->area}}-{{$area->areacode}}@endif <br> From: {{$from}} <br>To: {{$to}}</td>
        </tr>
        <tr>
            <td colspan="7" style="text-align:center;background-color:#dd3234;color:white;height:100px;vertical-align:center;font-weight:bold;font-size:18px;">{{env('COMPANY_NAME')}} <br> {{env('COMPANY_ADDRESS')}}</td>
        </tr>
        <tr>
            <th style="background-color:#ffbd24;font-weight:bold;"> S/N. </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Area Code </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Area </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Total Visit </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Purpose Wise Visit </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Total Dealer </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Total Collection </th>
        </tr>
    </thead>
    <tbody>
        @if (!empty($list))

        @foreach($list as $lis)
        <tr>
            <td style="vertical-align:center;">{{ $loop->iteration }}</td>
            <td style="vertical-align:center;">@if($lis->area) {{ $lis->areacode }} @else Unlisted Dealer Visit @endif</td>
            <td style="vertical-align:center;">@if($lis->area){{ $lis->area->area }} @else Unlisted Dealer Visit @endif</td>
            <td style="vertical-align:center;"> {{$lis->visit_count}} </td>
            <td style="vertical-align:center;"> @foreach($lis->purposes as $purpose)
                {{$purpose->purposes['purpose']}}- {{$purpose->visit_count}} <br>
                @endforeach
            </td>
            <td style="vertical-align:center;">
                @if($lis->area){{ $lis->dealer_count }}@else 0 @endif
            </td>
            <td style="vertical-align:center;">
                {{ number_format($lis->collection,2) }}
            </td>
        </tr>
        @endforeach
        @endif

    </tbody>
</table>
