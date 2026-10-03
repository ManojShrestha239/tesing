<ul class="nav navbar-nav margin-bottom-35 dealer-class">
    <li class="{{ request()->routeIs('dealer.create') ? 'active' : '' }}"><a href="/dealer/create">Add New Dealer </a>
    </li>
    <li class="{{ request()->routeIs('dealer.index') ? 'active' : '' }}"><a href="/dealer">List Dealer </a></li>
    <li class="{{ request()->routeIs('dealers.map') ? 'active' : '' }}"><a href="/dealers-map">Dealers Map </a></li>
    </li>
</ul>
