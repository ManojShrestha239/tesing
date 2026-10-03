@inject('helpers', 'App\Utilities\Helpers')
<table>
    <thead>
        <tr>
            <td colspan="8" style="height:65px;vertical-align:center;font-weight:bold;font-size:18px;">Order Collection
                Report - {{ $month }} {{ $year }} </td>
        </tr>
        <tr>

            <th style="background-color:#ffbd24;font-weight:bold;"> Date </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> SN </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Dealer Name</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Area </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Total O/S</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Running Days </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Order Value (Rs.)</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Payment Commitment Amount</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Actual Amt </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Sort/Excess </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Payment Commitment Date </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Collection Received Date </th>


        </tr>
    </thead>
    <tbody>
        @if (!empty($list))
            <?php
            $amt = 0;
            $camt = 0;
            $count = count($list);
            ?>
            @foreach ($list as $key => $lis)
                <tr>
                    <td style="vertical-align:center;">{{ $helpers->convertDate($lis->date) }}</td>
                    <td style="vertical-align:center;"> {{ $key + 1 }} </td>
                    <td style="vertical-align:center;"> {{ $lis->dealer_name }}</td>
                    <td style="vertical-align:center;"> {{ $lis->area->areacode }} - {{ $lis->area->area }}</td>
                    <td style="vertical-align:center;"> </td>
                    <td style="vertical-align:center;"> </td>
                    <td style="vertical-align:center;"> {{ $lis->total_amount }}</td>
                    <td style="vertical-align:center;"> {{ $lis->total_committed_amount }}</td>
                    <td style="vertical-align:center;"> </td>
                    <td style="vertical-align:center;"> </td>
                    <td style="vertical-align:center;">
                        @if ($lis->payment_commitment_date)
                            {{ $helpers->convertDate($lis->payment_commitment_date) }}
                        @endif
                    </td>
                    <td style="vertical-align:center;">
                        @if ($lis->dealer->collection)
                            {{ $helpers->convertDate($lis->dealer->collection->collection_date) }}
                        @endif
                    </td>
                </tr>
                @if ($key == $count - 1)
                    <?php
                    $amt = $amt + $lis->total_amount;
                    $camt = $camt + $lis->total_committed_amount;
                    ?>
                    <tr>
                        <td colspan="6" style="text-align:center;"> <strong> Total Amount </strong></td>
                        <td style="text-align:center;"><strong> Rs.{{ $amt }}</strong></td>
                        <td style="text-align:center;"><strong> Rs.{{ $camt }}</strong></td>
                        <td colspan="4"></td>
                    </tr>
                @else
                    @if ($lis->areacode != $list[$key + 1]->areacode)
                        <tr>
                            <?php
                            $amt = $amt + $lis->total_amount;
                            $camt = $camt + $lis->total_committed_amount;
                            ?>
                            <td colspan="6" style="text-align:center;"> <strong> Total Amount </strong></td>
                            <td style="text-align:center;"><strong> Rs.{{ $amt }}</strong></td>
                            <td style="text-align:center;"><strong> Rs.{{ $camt }}</strong></td>
                            <td colspan="4"></td>
                        </tr>
                        <?php
                        $amt = 0;
                        $camt = 0;
                        ?>
                    @else
                        <?php
                        $amt = $amt + $lis->total_amount;
                        $camt = $camt + $lis->total_committed_amount;
                        ?>
                    @endif
                @endif
            @endforeach
        @endif

    </tbody>
</table>
