@inject('helpers','App\Utilities\Helpers')
<table>
    <thead>
        <tr>
            <td colspan="6" style="text-align:center;background-color:#373366;color:#ffffff;height:100px;vertical-align:center;font-weight:bold;">
                <span style="font-size:20px;">
                    {{ env('COMPANY_NAME') }} <br> {{ env('COMPANY_ADDRESS') }} </span>
                <br> <span style="font-size:18px;"> Date : &nbsp; &nbsp; {{ $from }} - {{ $to }} <br> @if ($user)
                    {{ $user->name }}-{{ $user->id }}
                    @endif </span>
            </td>
        </tr>
        <tr>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Name </th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Dealer Info</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Cash Amount</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Cheque Amount</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Direct Amount</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Token Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $cashAmount = 0;
        $chequeAmount = 0;
        $directAmount = 0;
        $tokenAmount = 0;
        $discountamt = 0;
        $tdsamt = 0;
        ?>
        @if (!empty($payments))

        @foreach ($payments as $payment)

        <tr>
            <td style="vertical-align:center;text-align: center;"> {{ $payment->user['name'] }} - {{ $payment->user['id'] }}</td>
            <td style="vertical-align:center;text-align: center;">{{ $payment->dealer['code'] }}-{{ $payment->dealer['name'] }}</td>
            <td style="vertical-align:center;text-align: center;">{{ $payment->cashamt }}</td>
            <td style="vertical-align:center;text-align: center;">{{ $payment->chequeamt }}</td>
            <td style="vertical-align:center;text-align: center;">{{ $payment->directamt }}</td>
            <td style="vertical-align:center;text-align: center;">{{ $payment->tokenamt }}</td>
        </tr>
        <?php
        $cashAmount = $cashAmount + $payment->cashamt;
        $tokenAmount = $tokenAmount + $payment->tokenamt;
        $directAmount = $directAmount + $payment->directamt;
        $chequeAmount = $chequeAmount + $payment->chequeamt;
        ?>
        @endforeach
        <?php
        $grandTotal = $cashAmount + $chequeAmount + $directAmount + $tokenAmount;
        ?>
        <tr></tr>
        <tr>
            <td colspan="2" style="font-weight:bold;text-align: right;"> AMOUNT </td>
            <td style="background-color:#ffbd24;font-weight:bold;text-align: center;"> {{ $cashAmount }} </td>
            <td style="background-color:#ffbd24;font-weight:bold;text-align: center;"> {{ $chequeAmount }} </td>
            <td style="background-color:#ffbd24;font-weight:bold;text-align: center;"> {{ $directAmount }} </td>
            <td style="background-color:#ffbd24;font-weight:bold;text-align: center;"> {{ $tokenAmount }} </td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="2" style="font-weight:bold;text-align: right;"> TOTAL COLLECTION </td>
            <td colspan="4" style="background-color:#ffbd24;font-weight:bold;text-align: center;"> {{ $grandTotal }} </td>
        </tr>
        @endif

    </tbody>
</table>