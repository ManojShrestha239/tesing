@inject('helpers','App\Utilities\Helpers')
<table>
    <thead>
        {{-- <tr>
            <td colspan="12" style="height:65px;vertical-align:center;font-weight:bold;"> User :@if ($user)
                {{ $user->name }}-{{ $user->id }}
        @endif <br> From: {{ $from }} <br>To: {{ $to }}
        </td>
        </tr>--}}
        <tr>
            <td colspan="12" style="text-align:center;background-color:#373366;color:#ffffff;height:100px;vertical-align:center;font-weight:bold;">
                <span style="font-size:20px;">
                    {{ env('COMPANY_NAME') }} <br> {{ env('COMPANY_ADDRESS') }} </span>
                <br> <span style="font-size:18px;"> Date : &nbsp; &nbsp; {{ $from }} - {{ $to }} <br> @if ($user)
                    {{ $user->name }}-{{ $user->id }}
                    @endif </span>
            </td>
        </tr>
        <tr>
            <!-- <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Collection No. </th> -->
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Name </th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Dealer Info</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Collection Date</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Collection Type</th>
            <!-- <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Receipt Number</th> -->
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Bank</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Cheque Number</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Payment Date </th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Cash Amount</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Cheque Amount</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Direct Amount</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Token Amount</th>
            <!-- <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Discount (Rs.)</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> TDS (Rs.)</th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Status </th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Remarks </th>
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Start Time To End Time </th> -->
            <th style="background-color:#ffbd24;text-align: center;font-weight:bold;"> Visit Remarks </th>
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
            <!-- <td style="vertical-align:center;text-align: center;"> {{ $payment->id }} </td> -->
            <td style="vertical-align:center;text-align: center;"> {{ $payment->user['name'] }} - {{ $payment->user['id'] }}</td>
            <td style="vertical-align:center;text-align: center;">
                {{ $payment->dealer['code'] }}-{{ $payment->dealer['name'] }}
            </td>
            <td style="vertical-align:center;text-align: center;">{{ $helpers->convertDate($payment->collection_date) }}</td>
            <td style="vertical-align:center;text-align: center;">{{ $payment->payment_type }}</td>
            <!-- <td style="vertical-align:center;text-align: center;">{{ $payment->receipt_num }}</td> -->
            <td style="vertical-align:center;text-align: center;">
                @if ($payment->bank)
                {{ $payment->bank['name'] }}
                @endif
            </td>
            <td style="vertical-align:center;text-align: center;">{{ $payment->cheque_num }}</td>
            <td style="vertical-align:center;text-align: center;">
                @if ($payment->payment_date)
                {{ $helpers->convertDate($payment->payment_date) }}
                @endif
            </td>
            @if ($payment->payment_type == 'cash (regular)' || $payment->payment_type == 'cash (against)')
            <!-- Cash -->
            <td style="vertical-align:center;text-align: center;">{{ $payment->amount }}</td>
            @else
            <td style="vertical-align:center;text-align: center;">0</td>
            @endif
            @if ($payment->payment_type == 'cheque (bearer)'|| $payment->payment_type == 'cheque (payee)'|| $payment->payment_type == 'cheque'|| $payment->payment_type =='cash (account payee)')
            <!-- Cheque -->
            <td style="vertical-align:center;text-align: center;">{{ $payment->amount }}</td>
            @else
            <td style="vertical-align:center;text-align: center;">0</td>
            @endif
            @if ($payment->payment_type == 'direct')
            <!-- Direct -->
            <td style="vertical-align:center;text-align: center;">{{ $payment->amount }}</td>
            @else
            <td style="vertical-align:center;text-align: center;">0</td>
            @endif
            <!-- Token -->
            @if ($payment->payment_type == 'token')
            <td style="vertical-align:center;text-align: center;">{{ $payment->amount }}</td>
            @else
            <td style="vertical-align:center;text-align: center;">0</td>
            @endif
            <!-- <td style="vertical-align:center;text-align: center;">{{ $payment->discount }}</td>
            <td style="vertical-align:center;text-align: center;">{{ $payment->TDS }}</td>
            <td style="vertical-align:center;text-align: center;">{{ $payment->status }}</td> -->
            {{-- @if (isset($payment->remarks))
            <td style="vertical-align:center;text-align: center;"> {{ $payment->remarks }} </td>
            @else
            <td style="vertical-align:center;text-align: center;"> {{ $payment->mRemarks }} </td>
            @endif --}}
            @if ($payment->visit)
            <!-- <td style="vertical-align:center;text-align: center;"> ({{ $payment->visit->start_time }}) to ({{$payment->visit->end_time}}) </td> -->
            <td style="vertical-align:center;text-align: center;"> {{ $payment->visit->remarks }} </td>
            @else
            <!-- <td style="vertical-align:center;text-align: center;"></td> -->
            <td style="vertical-align:center;text-align: center;"></td>
            @endif
        </tr>
        <?php
        if ($payment->payment_type == 'cash (regular)' || $payment->payment_type == 'cash (against)') {
            $cashAmount = $cashAmount + $payment->amount;
        } elseif ($payment->payment_type == 'token') {
            $tokenAmount = $tokenAmount + $payment->amount;
        } elseif ($payment->payment_type == 'direct') {
            $directAmount = $directAmount + $payment->amount;
        } else {
            $chequeAmount = $chequeAmount + $payment->amount;
        }
        $discountamt = $discountamt + $payment->discount;
        $tdsamt = $tdsamt + $payment->TDS;
        ?>
        @endforeach
        <?php
        $grandTotal = $cashAmount + $chequeAmount + $directAmount + $tokenAmount;
        ?>
        <tr></tr>
        <tr>
            <td colspan="7" style="font-weight:bold;text-align: right;"> AMOUNT </td>
            <td style="background-color:#ffbd24;font-weight:bold;text-align: center;"> {{ $cashAmount }} </td>
            <td style="background-color:#ffbd24;font-weight:bold;text-align: center;"> {{ $chequeAmount }} </td>
            <td style="background-color:#ffbd24;font-weight:bold;text-align: center;"> {{ $directAmount }} </td>
            <td style="background-color:#ffbd24;font-weight:bold;text-align: center;"> {{ $tokenAmount }} </td>
            <td colspan="1" style="font-weight:bold;text-align: center;"></td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="7" style="font-weight:bold;text-align: right;"> TOTAL COLLECTION </td>
            <td colspan="4" style="background-color:#ffbd24;font-weight:bold;text-align: center;"> {{ $grandTotal }} </td>
            <td colspan="1" style="font-weight:bold;text-align: center;"></td>
        </tr>
        @endif

    </tbody>
</table>