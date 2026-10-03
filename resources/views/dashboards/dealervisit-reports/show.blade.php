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
                                <div class="caption">
                                    <i class=" fa fa-globe font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">
                                        @if (!empty($de))

                                            @if (empty($from) && empty($to))
                                                Total Dealer Visit - ({{ $de }})
                                            @elseif(!empty($from) && empty($to))
                                                Dealer Visit({{ $from }} TO Now) -({{ $de }})
                                            @elseif(empty($from) && !empty($to))
                                                Dealer Visit (till {{ $to }}) -({{ $de }})
                                            @else
                                                Dealer visit ({{ $from }} TO {{ $to }})
                                                -({{ $de }})
                                            @endif
                                        @else
                                            @if (empty($from) && empty($to))
                                                Total Dealer Visit
                                            @elseif(!empty($from) && empty($to))
                                                Dealer Visit({{ $from }} TO Now)
                                            @elseif(empty($from) && !empty($to))
                                                Dealer Visit (till {{ $to }})
                                            @else
                                                Dealer visit ({{ $from }} TO {{ $to }})
                                            @endif
                                        @endif
                                        - @if (!empty($list))
                                            {{ count($list) }}
                                        @else
                                            0 @endif <br><br>
                                        &nbsp; &nbsp;
                                    </span>
                                </div>
                                <div style="float:right">
                                    <form action="/dealer-visit-detailed-export" method="post">
                                        @csrf
                                        <input type="hidden" name="dealer"
                                            @if (isset($dealer)) @if ($dealer) value="{{ $dealer->code }}" @endif
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

                                <br>

                                <div class="uptable">
                                    <div class="row">
                                        @if (!empty($amt))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Total Collection</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format($amt, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                        @endif

                                        @if (!empty($discount))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Total Discount</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format($discount, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                        @endif

                                        @if (!empty($discount))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Total TDS</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format(($discount * 15) / 100, 2, '.', ',') }}
                                                        </td>
                                                </table>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        @if (!empty($cashamt))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cash Collection</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format($cashamt, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                        @endif

                                        @if (!empty($discountcash))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cash Discount</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format($discountcash, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                        @endif

                                        @if (!empty($discountcash))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cash TDS</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.{{ number_format(($discountcash * 15) / 100, 2, '.', ',') }}
                                                        </td>
                                                </table>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        @if (!empty($chequeamt))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cheque Collection</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs. {{ number_format($chequeamt, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                        @endif

                                        @if (!empty($discountcheque))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cheque Discount</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs. {{ number_format($discountcheque, 2, '.', ',') }} </td>
                                                </table>
                                            </div>
                                        @endif

                                        @if (!empty($discountcheque))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Cheque TDS</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs.
                                                            {{ number_format(($discountcheque * 15) / 100, 2, '.', ',') }}
                                                        </td>
                                                </table>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        @if (!empty($tokenamt))
                                            <div class="column">
                                                <table class="up">
                                                    <tr>
                                                        <th>Token Collection</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Rs. {{ number_format($tokenamt, 2, '.', ',') }}</td>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover" id="sample_2">
                                    <thead>
                                        <tr>
                                            <th> Visit No. </th>
                                            <th> User Info </th>
                                            <th> Dealer/Retailer Info</th>
                                            <th> Purpose </th>
                                            <th> Date </th>
                                            <th> Start to End Time</th>
                                            <th> Status </th>
                                            <th> Remarks </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($list))

                                            @foreach ($list as $lis)
                                                <tr>
                                                    <td> {{ $lis->id }} </td>
                                                    <td> {{ $lis->user['id'] }}-{{ $lis->user['name'] }}</td>
                                                    <td>{{ $lis->dealer['name'] }} ({{ $lis->dealer_code }})</td>
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
                                                    <td>{{ $helpers->convertDate($lis['date']) }}</td>
                                                    <td> {{ $lis->start_time }} To {{ $lis->end_time }}
                                                        <?php
                                                        $start = new DateTime($lis->start_time);
                                                        $end = new DateTime($lis->end_time);

                                                        $interval = $end->diff($start);
                                                        echo '(' . $interval->format('%h') . ' hrs ' . $interval->format('%i') . ' min' . $interval->format('%s') . ' sec)';
                                                        ?>
                                                    </td>
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
                                                    <td> {{ $lis['remarks'] }} </td>
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
@endsection
