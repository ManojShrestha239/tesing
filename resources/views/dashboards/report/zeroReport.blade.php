@inject('helpers','App\Utilities\Helpers')
<table>
  <thead>
    <tr>
      <td style="font-weight:bold;font-size:16px;">Monthly Report of for the month of {{$month}}</td>
    </tr>
    <tr>
      <td style="font-weight:bold;font-size:13px;">Date : {{$from}} to {{$to}}</td>
      <td></td>
      <td style="font-weight:bold;font-size:13px;">No. of working days: {{$totaldays}}</td>
    </tr>
    @if (isset($userName))
    <tr>
      <td style="font-weight:bold;font-size:13px;">User Name: {{$userName}}</td>
    </tr>
    @endif
    <tr>
      <td style="font-weight:bold;font-size:13px;">Area: </td>
    </tr>
    <tr>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th style="font-weight:bold;font-size:13px;" width="36"> A. Collection Summary</th>
      <th style="font-weight:bold;font-size:13px;" width="15"> No. </th>
      <th style="font-weight:bold;font-size:13px;" width="15"> Amount </th>
    </tr>
    <tr>
      <td style="font-size:13px;">Old Collection</td>
      <td style="font-size:13px;"></td>
      <td style="font-size:13px;">{{$oldCollection}}</td>
    </tr>
    <tr>
      <td style="font-size:13px;">Less: Cash Discount </td>
      <td style="font-size:13px;"></td>
      <td style="font-size:13px;">{{$discount}}</td>
    </tr>
    <tr>
      <td style="font-size:13px;">Less: Token</td>
      <td style="font-size:13px;"></td>
      <td style="font-size:13px;">{{$token}}</td>
    </tr>
    <tr>
      <td style="font-size:13px;">Less: Other</td>
      <td style="font-size:13px;"></td>
      <td style="font-size:13px;">{{$other}}</td>
    </tr>
    <?php
    $netCollection = $oldCollection - $discount - $token - $other;
    ?>
    <tr>
      <th style="font-weight:bold;font-size:13px;" width="36">Net Collection</th>
      <th style="font-weight:bold;font-size:13px;" width="15"></th>
      <th style="font-weight:bold;font-size:13px;" width="15">{{$netCollection}}</th>
    </tr>
    <tr>
      <td style="font-size:13px;">Less: Bank Deposit (B) </td>
      <td style="font-size:13px;"></td>
      <td style="font-size:13px;">{{$bankDeposit}}</td>
    </tr>
    <tr>
      <td style="font-size:13px;">Less: Direct Deposit by Party (C)</td>
      <td style="font-size:13px;"></td>
      <td style="font-size:13px;">{{$direct}}</td>
    </tr>
    <tr>
      <td style="font-size:13px;">Less: Cheque Sent to office (D)</td>
      <td style="font-size:13px;"></td>
      <td style="font-size:13px;">{{$sentOffice}}</td>
    </tr>
    <?php
    $difference = $netCollection - $bankDeposit - $direct - $sentOffice;
    ?>
    <tr>
      <th style="font-weight:bold;font-size:13px;" width="36">Difference</th>
      <th style="font-weight:bold;font-size:13px;" width="15"></th>
      <th style="font-weight:bold;font-size:13px;" width="15">{{$difference}}</th>
    </tr>
    <tr>
      <td style="font-size:13px;">Note: Difference must be zero.</td>
    </tr>
    <tr></tr>
    <tr>
      <th style="font-weight:bold;font-size:13px;" width="36">B. Disbursal - Bank Deposits</th>
    </tr>
    <tr>
      <th style="font-weight:bold;font-size:13px;" width="36">Name OF Bank</th>
      <th style="font-weight:bold;font-size:13px;" width="15"> Chq. Amount </th>
      <th style="font-weight:bold;font-size:13px;" width="15">Cash Amount </th>
    </tr>
    <?php
    $totalchequeAmt = 0;
    $totalcashAmt = 0;
    ?>
    @foreach ($banks as $bank)
    @if ($bank->chequeAmt > 0)
    <tr>
      <td style="font-size:13px;">{{$bank->name}}</td>
      <td style="font-size:13px;">{{$bank->chequeAmt}}</td>
      <td style="font-size:13px;">0</td>
    </tr>

    <?php
    $totalchequeAmt = $totalchequeAmt + $bank->chequeAmt;
    $totalcashAmt = $totalcashAmt + $bank->cashAmt;
    ?>
    @endif
    @endforeach

    <tr>
      <td style="font-weight:bold;font-size:13px;">Total</td>
      <td style="font-weight:bold;font-size:13px;">{{$totalchequeAmt}}</td>
      <td style="font-weight:bold;font-size:13px;">{{$totalcashAmt}}</td>
    </tr>



  </tbody>
</table>