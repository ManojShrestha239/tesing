@inject('helpers', 'App\Utilities\Helpers')
@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Visit History</h1>
            <!-- Sidebar Toggle Button -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".page-sidebar">
                <span class="sr-only">Toggle navigation</span>
                <span class="toggle-icon">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </span>
            </button>
            <!-- Sidebar Toggle Button -->
        </div>

        <div class="page-content-container">
            <div class="page-content-row" id="replecable">
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet light portlet-fit bordered">
                            <div class="portlet-title">
                                {{-- <div style="margin-top: 15px;">
                                    <label
                                        style="font-weight: bold; margin-right: 15px; display: inline-block;">Type:</label>
                                    <select name="type" id="typeSelect"
                                        style="padding: 6px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; cursor: pointer;">
                                        <option value="" {{ empty($type) ? 'selected' : '' }}>All</option>
                                        <option value="dealer" {{ isset($type) && $type === 'dealer' ? 'selected' : '' }}>
                                            Dealer</option>
                                        <option value="retailer"
                                            {{ isset($type) && $type === 'retailer' ? 'selected' : '' }}>Retailer
                                        </option>
                                    </select>
                                </div> --}}
                                <div class="caption">
                                    <i class=" fa fa-globe font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">
                                        @if (!empty($ar))
                                            @if (empty($from) && empty($to))
                                                area Visit of the day - ({{ $ar }})
                                            @elseif(empty($from) && !empty($to))
                                                Area Visit Details (Till {{ $to }} ) - {{ $ar }}
                                            @elseif(!empty($from) && empty($to))
                                                Area Visit Details ({{ $from }} TO now) - {{ $ar }}
                                            @else
                                                Area Visit Details ({{ $from }} TO {{ $to }}) -
                                                {{ $ar }}

                                            @endif
                                        @else
                                            @if (empty($from) && empty($to))

                                                Totla Area Visit
                                            @elseif(empty($from) && !empty($to))
                                                All Area Visit Details (Till {{ $to }} )
                                            @elseif(!empty($from) && empty($to))
                                                Area Visit Details ({{ $from }} TO Now)
                                            @else
                                                Visit Details ({{ $from }} TO {{ $to }})

                                            @endif

                                        @endif
                                    </span>
                                </div>
                                <div style="float:right">
                                    <form action="/area-visit-detailed-export" method="post">
                                        @csrf
                                        <input type="hidden" name="area"
                                            @if (isset($areas)) @if ($areas) value="{{ $areas->areacode }}" @endif
                                            @endif>
                                        <input type="hidden" name="from"
                                            @if (isset($from)) @if ($from) value="{{ $from }}" @endif
                                            @endif>
                                        <input type="hidden" name="to"
                                            @if (isset($to)) @if ($to) value="{{ $to }}" @endif
                                            @endif>

                                        <button class="btn btn-primary btn-xs"><i class="fa fa-download"
                                                style="padding-right: 5px;"></i>Excel</button>
                                    </form>
                                </div>
                                <div class="uptable">
                                    <div class="row">
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Total Collection</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($amt, 2) }}</td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Total Discount</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($discount, 2) }}</td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Total TDS</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format(($discount * 15) / 100, 2) }}</td>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cash Collection</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($cashamt, 2) }}</td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cash Discount</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format($discountcash, 2) }}</td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cash TDS</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs.{{ number_format(($discountcash * 15) / 100, 2) }}</td>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cheque Collection</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs. {{ number_format($chequeamt, 2) }}</td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cheque Discount</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs. {{ number_format($discountcheque, 2) }} </td>
                                            </table>
                                        </div>
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Cheque TDS</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs. {{ number_format(($discountcheque * 15) / 100, 2) }}</td>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="column">
                                            <table class="up">
                                                <tr>
                                                    <th>Token Collection</th>
                                                </tr>
                                                <tr>
                                                    <td>Rs. {{ number_format($tokenamt, 2) }}</td>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover" id="sample_2">
                                    <thead>
                                        <tr>
                                            <th> visit No. </th>
                                            <th> Area </th>
                                            <th> User Info </th>
                                            <th> Dealer/Retailer Info </th>
                                            <th> Date </th>
                                            <th> Start To End Time </th>
                                            <th> Purpose </th>
                                            <th> Remarks </th>
                                            <th> Visit Status </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($list))

                                            @foreach ($list as $lis)
                                                <tr>
                                                    <td>{{ $lis->id }}</td>
                                                    <td>
                                                        @if ($lis->area)
                                                            {{ $lis->areacode }} - {{ $lis->area['area'] }}
                                                        @else
                                                            Unlisted Dealer Visit
                                                        @endif
                                                    </td>
                                                    <td> {{ $lis->user_id }}-{{ $lis->user['name'] }}</td>
                                                    <td>
                                                        @if ($lis->dealer_code)
                                                            {{ $lis->dealer['name'] }} ({{ $lis->dealer_code }})
                                                        @else
                                                            {{ $lis->dealer_name }}
                                                            @foreach (['address' => 'Address', 'contact' => 'Contact'] as $field => $label)
                                                                @if (!empty($lis->$field))
                                                                    <br>
                                                                    <span
                                                                        class="unlisted-dealers">{{ $label }}:</span>{{ $lis->$field }}
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td>{{ $helpers->convertDate($lis['date']) }}</td>
                                                    <td> {{ $lis['start_time'] }} To {{ $lis['end_time'] }}
                                                        <?php
                                                        $start = new DateTime($lis->start_time);
                                                        $end = new DateTime($lis->end_time);
                                                        
                                                        $interval = $end->diff($start);
                                                        echo '(' . $interval->format('%h') . ' hrs ' . $interval->format('%i') . ' min' . $interval->format('%s') . ' sec)';
                                                        ?>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $purposes = [];
                                                            foreach ($lis->visits as $visit) {
                                                                $purposes[] = $visit->purposes['purpose'];
                                                            }
                                                            $count = count($purposes);
                                                        @endphp

                                                        @if ($count === 1)
                                                            {{ $purposes[0] }}
                                                        @elseif ($count === 2)
                                                            {{ $purposes[0] }} and {{ $purposes[1] }}
                                                        @else
                                                            @foreach ($purposes as $index => $purpose)
                                                                @if ($index === count($purposes) - 1)
                                                                    and {{ $purpose }}
                                                                @elseif ($index === count($purposes) - 2)
                                                                    {{ $purpose }}
                                                                @else
                                                                    {{ $purpose }},{{ ' ' }}
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td> {{ $lis->remarks }}</td>
                                                    <td>
                                                        @if (strtolower($lis['status']) === 'online')
                                                            <button
                                                                class='btn btn-success'><strong>{{ $lis['status'] }}</strong></button>
                                                        @elseif (strtolower($lis['status']) === 'unlisted')
                                                            <button
                                                                class='btn btn-warning'><strong>{{ $lis['status'] }}</strong></button>
                                                        @else
                                                            <button
                                                                class='btn btn-danger'><strong>{{ $lis['status'] }}</strong></button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- <script>
        (() => {
            const typeSelect = document.getElementById('typeSelect');
            if (!typeSelect) return;

            const id = "{{ $list[0]->areacode ?? 'Unlisted_dealer_visit' }}";
            const from = "{{ $from ?? '' }}";
            const to = "{{ $to ?? '' }}";

            typeSelect.addEventListener('change', () => {
                if (!id) return;

                const params = new URLSearchParams({
                    type: typeSelect.value,
                    from,
                    to
                });
                window.location.replace(`/visitarea/${id}?${params.toString()}`);
            });
        })();
    </script> --}}

@endsection
