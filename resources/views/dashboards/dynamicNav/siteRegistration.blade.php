<ul class="nav navbar-nav margin-bottom-35 report-class">

        {{-- New Site Registration --}}
        <li class="{{ request()->is(['site-registration-v2', 'site-registration-v2/*']) ? 'active' : '' }}"><a
                        href="/site-registration-v2">Sites</a>
        </li>
        <li class="{{ request()->is('followUp*') ? 'active' : '' }}"><a href="/followUp">Follow Ups</a></li>
        <li class="{{ request()->is('site-registration-v2-map') ? 'active' : '' }}"><a href="/site-registration-v2-map">For
                        New
                        Site Map</a>
        </li>
</ul>