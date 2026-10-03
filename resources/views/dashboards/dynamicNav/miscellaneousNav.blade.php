 <ul class="nav navbar-nav margin-bottom-35 bank-class">
     @can('bank')
         <li class="{{ request()->is('bank/create') ? 'active' : '' }}"><a href="/bank/create">Add New Bank </a></li>
         <li class="{{ request()->is('bank') ? 'active' : '' }}"><a href="/bank">List Banks </a></li>
     @endcan
     @can('upload-due')
         <li class="{{ request()->is('uploadfile/create') ? 'active' : '' }}"><a href="/uploadfile/create">Upload Due </a>
         </li>
     @endcan
     @can('distance')
         <li class="{{ request()->is('distance') ? 'active' : '' }}"><a href="/distance">Set Distance</a></li>
     @endcan
     @can('denomination')
         <li class="{{ request()->is('denomination/create') ? 'active' : '' }}"><a href="/denomination/create">Add New
                 Denomination </a></li>
         <li class="{{ request()->is('denomination') ? 'active' : '' }}"><a href="/denomination">List Denominations </a>
         </li>
     @endcan
     @can('purpose')
         <li class="{{ request()->is('purpose/create') ? 'active' : '' }}"><a href="/purpose/create">Add New Purpose </a>
         </li>
         <li class="{{ request()->is('purpose') ? 'active' : '' }}"><a href="/purpose">List Purpose </a></li>
     @endcan
     @can('role')
         <li class="{{ request()->is('role/create') ? 'active' : '' }}"><a href="/role/create">Add New Role </a></li>
         <li class="{{ request()->is('role') ? 'active' : '' }}"><a href="/role">List Role </a></li>
     @endcan
     @can('auto-checkout')
         <li class="{{ request()->is('checkout-time') ? 'active' : '' }}"><a href="/checkout-time">Set Auto Checkout
                 Time</a></li>
     @endcan
 </ul>
