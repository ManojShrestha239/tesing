@inject('helpers', 'App\Utilities\Helpers')
<table>
    <thead>
        <tr>
            <td colspan="8" style="height:65px;vertical-align:center;font-weight:bold;font-size:18px;">Order Tracking
                Report Daily Basic - {{ $month }} </td>
        </tr>
        <tr>

            <th style="background-color:#ffbd24;font-weight:bold;"> Date </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> SN </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Dealer Name</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Area </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Sales 2078</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> PDC Cheque </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Total Outstanding</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Over 90 Days OD</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Aging </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Order Value </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> SO/ ASM Recomandation </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Payment Commitment Date </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Credit Limit </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Remarks for Approval </th>



        </tr>
    </thead>
    <tbody>
        @if (!empty($lists))
            <?php
            $totalAmt = 0;
            $pdcAmt = 0;
            ?>

            @foreach ($lists as $list)
                <tr>
                    <td style="vertical-align:center;">{{ $helpers->convertDate($list->date) }}</td>
                    <td style="vertical-align:center;"> {{ $loop->iteration }} </td>
                    <td style="vertical-align:center;"> {{ $list->dealer->name }}</td>
                    <td style="vertical-align:center;"> {{ $list->areacode }} - {{ $list->area->area }}</td>
                    <td style="vertical-align:center;"> </td>
                    <td style="vertical-align:center;"> {{ $list->pdc }}</td>
                    <td style="vertical-align:center;"> </td>
                    <td style="vertical-align:center;"> </td>
                    <td style="vertical-align:center;"> </td>
                    <td style="vertical-align:center;">{{ $list->total_collection }} </td>
                    <td style="vertical-align:center;">{{ $list->user->name }}</td>
                    <td style="vertical-align:center;">
                        @if ($list->payment_commitment_date)
                            {{ $helpers->convertDate($list->payment_commitment_date) }}
                            ({{ $list->committed_amount }})
                        @endif
                    </td>
                    <td style="vertical-align:center;">{{ $list->dealer->order_limit }}</td>

                    @if ($list->status === 'Approved' && $list->remarks !== null)
                        <td style="vertical-align:center;">{{ $list->approvedRemarks }}</td>
                    @else
                        <td style="vertical-align:center;">-</td>
                    @endif
                </tr>


                <?php
                $totalAmt = $totalAmt + $list->total_collection;
                $pdcAmt = $pdcAmt + $list->pdc;
                ?>
            @endforeach
            <tr>
                <td colspan="5" style="text-align:center;"><strong>Total</strong></td>
                <td style="text-align:center;"><strong>{{ $pdcAmt }}</strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align:center;"><strong>{{ $totalAmt }}</strong></td>
                <td colspan="8"></td>
            </tr>

        @endif

    </tbody>
</table>
