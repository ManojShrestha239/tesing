@inject('helpers', 'App\Utilities\Helpers')
@extends('dashboards.sessions.layout')

@section('content')
    <div class="page-content">
        <div class="breadcrumbs">
            <h1>Unlisted Dealer History</h1>
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
                                            Dealer
                                        </option>
                                        <option value="retailer"
                                            {{ isset($type) && $type === 'retailer' ? 'selected' : '' }}>
                                            Retailer
                                        </option>
                                    </select>
                                </div> --}}
                                <div class="caption">
                                    <i class=" fa fa-globe font-red"></i>
                                    <span class="caption-subject font-red bold uppercase">
                                        @if (empty($from) && empty($to))
                                            Total Unlisted Dealer Visits
                                        @elseif(!empty($from) && empty($to))
                                            Unlisted Dealer Visit({{ $from }} TO Now)
                                        @elseif(empty($from) && !empty($to))
                                            Unlisted Dealer Visit (till {{ $to }})
                                        @else
                                            Unlisted Dealer visit ({{ $from }} TO {{ $to }})
                                        @endif
                                        - @if (!empty($list))
                                            {{ count($list) }}
                                        @else
                                            0 @endif &nbsp;&nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp;
                                    </span>
                                </div>
                                <div style="float:right">
                                    <form action="/unlisted-visit-detailed-export" method="post">
                                        @csrf
                                        <input type="hidden" name="user"
                                            @if (isset($user)) @if ($user) value="{{ $user->id }}" @endif
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
                            </div>

                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover" id="sample_2">
                                <thead>
                                    <tr>
                                        <th> Visit No.</th>
                                        <th> User </th>
                                        <th> Dealer </th>
                                        <th> Address </th>
                                        <th> Contact </th>
                                        <th> Purpose </th>
                                        <th> Date </th>
                                        <th> Start To End Time </th>
                                        <th> Remarks </th>
                                        <th> Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($list))
                                        @foreach ($list as $lis)
                                            <tr>
                                                <td> {{ $lis->id }} </td>
                                                <td> {{ $lis->user['name'] }}</td>
                                                <td> {{ $lis->dealer_name }} </td>
                                                <td> {{ $lis->dealer_address }} </td>
                                                <td> {{ $lis->contact }} </td>
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
                                                <td> {{ $helpers->convertDate($lis['date']) }} </td>
                                                <td> {{ $lis->start_time }} To {{ $lis->end_time }}
                                                    <?php
                                                    $start = new DateTime($lis->start_time);
                                                    $end = new DateTime($lis->end_time);
                                                    
                                                    $interval = $end->diff($start);
                                                    echo '(' . $interval->format('%h') . ' hrs ' . $interval->format('%i') . ' min' . $interval->format('%s') . ' sec)';
                                                    ?>
                                                </td>
                                                <td> {{ $lis->remarks }} </td>
                                                <td>
                                                    @if ($lis->latitude)
                                                        <a href="/negotiation/map/{{ $lis->id }}"
                                                            class="btn btn-primary btn-sm">View Map</a>
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

    {{-- <script>
        (() => {
            const typeSelect = document.getElementById('typeSelect');
            if (!typeSelect) return;

            const id = "{{ $mouser->id ?? '' }}";
            const from = "{{ $from ?? '' }}";
            const to = "{{ $to ?? '' }}";

            typeSelect.addEventListener('change', () => {
                if (!id) return;

                const params = new URLSearchParams({
                    type: typeSelect.value,
                    from,
                    to
                });
                window.location.replace(`/visithistory/${id}?${params.toString()}`);
            });
        })();
    </script> --}}
@endsection
