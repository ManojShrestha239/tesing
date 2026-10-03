@inject('helpers', 'App\Utilities\Helpers')
<table>
    <thead>
        <tr>
            <td colspan="13" style="text-align:center;vertical-align:center;font-weight:bold;font-size:11px;">SOA MIS
                Report</td>
        </tr>
        <tr>
            <td colspan="13" style="text-align:center;vertical-align:center;font-weight:bold;font-size:11px;">All Nepal
                Daily Summary Report</td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align:center;vertical-align:center;font-weight:bold;font-size:11px;">Date:@if ($from == $to)
                    {{ $to }}
                @else
                    {{ $from }} to {{ $to }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="13" style="text-align:center;vertical-align:center;font-weight:bold;font-size:11px;">Sales Team
                Report:</td>
        </tr>
        <tr>
            <th style="font-weight:bold; background-color: #AEAAAA;"> SN No.</th>
            <th style="font-weight:bold; background-color: #AEAAAA;"> Name </th>
            <th style="font-weight:bold; background-color: #AEAAAA;"> Design </th>
            <th style="font-weight:bold; background-color: #AEAAAA;"> Area </th>
            <th style="font-weight:bold; background-color: #AEAAAA;"> Route </th>
            <th style="font-weight:bold; background-color: #AEAAAA;"> Date </th>
            <th style="font-weight:bold; background-color: #AEAAAA;"> Check In </th>
            <th style="font-weight:bold; background-color: #AEAAAA;"> Check Out </th>
            <th colspan="2" style="font-weight:bold; background-color: #AEAAAA;"> Daily Visit </th>
            <!-- <th style="background-color: #AEAAAA;"></th> -->
            <th style="font-weight:bold; background-color: #AEAAAA;"> Spread </th>
            <th style="font-weight:bold; background-color: #AEAAAA;"> Collection (Lac) </th>
            <th style="font-weight:bold; background-color: #AEAAAA;"> Order (Lac) </th>
            <th style="font-weight:bold; background-color: #AEAAAA;"> KM </th>
            <th style="font-weight:bold; background-color: #AEAAAA;"> Remarks </th>
        </tr>
        <tr>
            <th style="background-color: #AEAAAA;"></th>
            <th style="background-color: #AEAAAA;"></th>
            <th style="background-color: #AEAAAA;"></th>
            <th style="background-color: #AEAAAA;"></th>
            <th style="background-color: #AEAAAA;"></th>
            <th style="background-color: #AEAAAA;"></th>
            <th style="background-color: #AEAAAA;"></th>
            <th style="background-color: #AEAAAA;"></th>
            <th style="background-color: #AEAAAA;">Planned</th>
            <th style="background-color: #AEAAAA;">Actual</th>
            <th style="background-color: #AEAAAA;"></th>
            <th style="background-color: #AEAAAA;"></th>
            <th style="background-color: #AEAAAA;"></th>
            <th style="background-color: #AEAAAA;"></th>
            <th style="background-color: #AEAAAA;"></th>
        </tr>
    </thead>
    <tbody>
        @forelse($list as $lis)
            <tr>
                <td style="text-align:center;vertical-align:center;">{{ $loop->iteration }}</td>
                <td style="text-align:center;vertical-align:center;">{{ $lis->mobileuser->name }}</td>
                <td style="text-align:center;vertical-align:center;">{{ $lis->mobileuser->role->role }}</td>
                <td style="text-align:center;vertical-align:center;">{{ $lis->mobileuser->areas }}</td>
                <td style="text-align:center;vertical-align:center;">-</td>
                <td style="text-align:center;vertical-align:center;">
                    @if ($lis->checkin)
                        {{ $helpers->convertDate($lis->checkin->date) }}
                    @endif
                </td>
                <td style="text-align:center;vertical-align:center;">
                    @if ($lis->checkin)
                        {{ $lis->checkin->time }}
                    @endif
                </td>
                <td style="text-align:center;vertical-align:center;">
                    @if ($lis->checkout)
                        {{ $lis->checkout->time }}
                    @endif
                </td>
                <td style="text-align:center;vertical-align:center;">0</td>
                <td style="text-align:center;vertical-align:center;">{{ $lis->visit_count }}</td>
                <td style="text-align:center;vertical-align:center;">{{-- Spread --}}0</td>
                <td style="text-align:center;vertical-align:center;">RS.
                    {{ number_format($lis->collection, 2, '.', ',') }}</td>
                <td style="text-align:center;vertical-align:center;">RS. {{ number_format($lis->order, 2, '.', ',') }}
                </td>
                <td style="text-align:center;vertical-align:center;">{{ round((float) $lis->distance, 2) }} KM</td>
                <td style="text-align:center;vertical-align:center;"></td>
            </tr>
        @empty
        @endforelse
        <?php
        // dd($list->sum('distance'));
        $total_visit = $list->sum('visit_count');
        $total_collection = $list->sum('collection');
        $total_order = $list->sum('order');
        $total_distance = $list->sum('distance');
        ?>
        <tr>
            <td colspan="8" style="text-align:center;vertical-align:center;font-weight:bold;font-size:11px;">Total
            </td>
            <td style="text-align:center;vertical-align:center;font-weight:bold;font-size:11px;">{{ $total_visit }}
            </td>
            <td style="text-align:center;vertical-align:center;font-weight:bold;font-size:11px;">{{-- Spread --}}0
            </td>
            <td style="text-align:center;vertical-align:center;font-weight:bold;font-size:11px;">RS.
                {{ number_format($total_collection, 2, '.', ',') }}</td>
            <td style="text-align:center;vertical-align:center;font-weight:bold;font-size:11px;">RS.
                {{ number_format($total_order, 2, '.', ',') }}</td>
            <td style="text-align:center;vertical-align:center;font-weight:bold;font-size:11px;">
                {{ round($total_distance, 2) }} KM</td>
            <td></td>
        </tr>
    </tbody>
</table>
