@inject('helpers','App\Utilities\Helpers')
<table>
    <thead>
        <tr>
            <td colspan="7" style="height:65px;vertical-align:center;font-weight:bold;"> Dealer :@if($dealer){{$dealer->name}}-{{$dealer->code}}@endif <br> From: {{$from}} <br>To: {{$to}}</td>
        </tr>
        <tr>
            <td colspan="7" style="text-align:center;background-color:#373366;color:#ffffff;height:100px;vertical-align:center;font-weight:bold;font-size:18px;">{{env('COMPANY_NAME')}} <br> {{env('COMPANY_ADDRESS')}}</td>
        </tr>
        <tr>
            <th style="background-color:#ffbd24;font-weight:bold;"> ORDER ID </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> USER INFO </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> DEALER INFO</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> DATE </th>
            <!-- <th style="background-color:#ffbd24;font-weight:bold;"> PAYMENT DATE </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> COLLECTION TYPE </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> BANK NAME</th> -->
            <th style="background-color:#ffbd24;font-weight:bold;"> TOTAL AMOUNT </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> STATUS </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> REMARKS </th>
        </tr>
    </thead>
    <tbody>
        @if (!empty($list))

        @foreach($list as $lis)
        <tr>
            <td style="vertical-align:center;"> {{$lis->id}} </td>
            <td style="vertical-align:center;"> {{ $lis->orderBy }}</td>
            <td style="vertical-align:center;">{{$lis->code}}-{{ $lis->name }} <br> Area: {{$lis->area}} </td>
            <td style="vertical-align:center;">{{$helpers->convertDate($lis['date'])}}</td>
            <td style="vertical-align:center;">{{$lis->total_price}}</td>
            <td style="vertical-align:center;">{{$lis->status}}</td>
            <td style="vertical-align:center;">{{$lis->remarks}}</td>
        </tr>
        @endforeach
        @endif

    </tbody>
</table>
