<ul class="nav navbar-nav margin-bottom-35 report-class">
        <li class="{{ request()->is('visithistory') || request()->is('history') ? 'active' : '' }}"><a
                        href="/visithistory">Daily Visit </a></li>
        <li class="{{ request()->is('visitarea') || request()->is('visit/areas') ? 'active' : '' }}"><a href="/visitarea">Area
                        Visit </a></li>
        <li class="{{ request()->is('visitdealer') || request()->is('visithistory-dealer') ? 'active' : '' }}"><a
                        href="/visitdealer">Dealer Visit </a></li>
        <li class="{{ request()->is('negotiation') || request()->is('negotiation-report') ? 'active' : '' }}"><a
                        href="/negotiation">Unlisted Dealer Visit </a></li>
        <li class="{{ request()->is('tada-report') || request()->is('tada-report/*') ? 'active' : '' }}"><a
                        href="/tada-report">Daily Report </a></li>
        <li class="{{ request()->is('expense-report') ? 'active' : '' }}"><a
                        href="{{ route('expense-report.index') }}">Expenses Reports</a></li>
        <li class="{{ request()->is('attendance') || request()->is('attendance/filter') ? 'active' : '' }}"><a
                        href="/attendance">Attendance List </a></li>
</ul>
