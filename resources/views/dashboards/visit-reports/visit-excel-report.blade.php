@inject('helpers', 'App\Utilities\Helpers')
<table>
    <thead>
        <tr>
            <td colspan="8" style="height:60px;vertical-align:center;font-weight:bold;"> Staff Name:
                {{ $user->name }} - {{ $user->id }} <br> From: {{ $from }} <br>To: {{ $to }}
            </td>
        </tr>
        <tr>
            <td colspan="9"
                style="text-align:center;background-color:#dd3234;color:white;height:100px;vertical-align:center;font-weight:bold;font-size:18px;">
                {{ env('COMPANY_NAME') }} <br> {{ env('COMPANY_ADDRESS') }}</td>
        </tr>
        <tr>
            <th style="background-color:#ffbd24;font-weight:bold;"> Visit No.</th>
            <th style="background-color:#ffbd24;font-weight:bold;"> User </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Dealer </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Purpose </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Date </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Start To End Time </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Remarks </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Visit Status </th>
            <th style="background-color:#ffbd24;font-weight:bold;"> Km </th>

        </tr>
    </thead>
    <tbody>
        @php
            $shownDates = [];
            $dateCounts = $list->groupBy('date')->map->count();
        @endphp
        @foreach ($list as $lis)
            <tr>
                <td> {{ $lis->id }} </td>
                <td> {{ $lis->user['name'] }}</td>
                <td>
                    @if ($lis->dealer_code)
                        {{ $lis->dealer['name'] }}
                    @else
                        {{ $lis->dealer_name }}
                    @endif
                </td>
                <td>
                    @foreach ($lis->visits as $visit)
                        {{ $visit->purposes['purpose'] }},
                    @endforeach
                </td>
                <td> {{ $helpers->convertDate($lis->date) }}</td>
                <td> {{ $lis->start_time }} To {{ $lis->end_time }}
                    <?php
                    $start = new DateTime($lis->start_time);
                    $end = new DateTime($lis->end_time);

                    $interval = $end->diff($start);
                    $time = '(' . $interval->format('%h') . ' hrs ' . $interval->format('%i') . ' min' . $interval->format('%s') . ' sec)';
                    ?>
                    {{ $time }}
                </td>
                <td> {{ $lis->remarks }}</td>
                <td>
                    @if ($lis->status == 'Online')
                        {{ $lis->status }}
                    @else
                        {{ $lis->status }}
                    @endif
                </td>
                {{-- <td>
                    {{ number_format($lis['distance'] ?? 0, 2) }} km
                </td> --}}
                @if (!in_array($lis->date, $shownDates))
                    <td style="text-align: center; vertical-align: middle;" rowspan="{{ $dateCounts[$lis->date] }}">
                        {{ number_format($list->where('date', $lis->date)->sum('distance'), 2) }} km
                    </td>

                    @php $shownDates[] = $lis->date; @endphp
                @endif


            </tr>
        @endforeach
    </tbody>
</table>
