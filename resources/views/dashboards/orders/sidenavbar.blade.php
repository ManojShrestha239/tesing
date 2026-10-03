<div class="page-sidebar">
  <div class="portlet light portlet-fit">
    <div class="portlet-title">
      <div class="caption">
        <i class=" icon-bar font-red"></i>
        <span class="caption-subject font-red bold uppercase">Quick Navigation</span>
      </div>
    </div>
    <nav class="navbar" role="navigation">
      <!-- Brand and toggle get grouped for better mobile display -->
      <!-- Collect the nav links, forms, and other content for toggling -->
      <ul class="nav navbar-nav margin-bottom-35 orders-class">

        <li @if ($url=='order' || $url=='Order') class="active" @endif><a href="/order">All Orders </a></li>
        <li @if ($url=='orderrequest' || $url=='orderRequest' || Request::is('dashboard/addorder/*') ) class="active"@endif><a href="/orderRequest"> Order Request</a></li>
        <li @if ($url=='pending' || $url=='Pending' ) class="active" @endif><a class="pending" href="/orders/Pending">Pending/Inventory </a></li>
        <li @if ($url=='Approved' || $url=='approved' ) class="active" @endif><a class="approved" href="/orders/approved">Account/Approved </a></li>
        <li @if ($url=='Ongoing' || $url=='ongoing' ) class="active" @endif><a class="ongoing" href="/orders/ongoing">Ongoing </a></li>
        <li @if ($url=='Completed' || $url=='completed' ) class="active" @endif><a class="completed" href="/orders/completed">Completed </a></li>
        <li @if ($url=='Cancelled' || $url=='cancelled' ) class="active" @endif><a class="cancelled" href="/orders/cancelled">Cancelled </a></li>
        <li @if ($url=='Draft' || $url=='draft' ) class="active" @endif><a class="draft" href="/orders/draft">Draft </a></li>

        <!-- <li><a href="/order/challan">Challan </a></li> -->
      </ul>

    </nav>
  </div>

</div>
