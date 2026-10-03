 <li class="{{ request()->is('productmanufac/create') ? 'active' : '' }}"><a href="/productmanufac/create">Add Product
         Manufacture </a></li>
 <li class="{{ request()->is('productmanufac') ? 'active' : '' }}">
     <a href="/productmanufac">List Product Manufacture </a>
 </li>
 <li class="{{ request()->is('product-dealer/create') ? 'active' : '' }}"><a href="/product-dealer/create">Product
         Dispatch </a></li>
 <li class="{{ request()->is('product-dealer') ? 'active' : '' }}">
     <a href="/product-dealer">List Of Product Dispatch </a>
 </li>
 <li class="{{ request()->is('producinventory') ? 'active' : '' }}"><a href="/producinventory">Inventory </a></li>
 {{-- <li class="{{ request()->is('subdealer-inventory/create') ? 'active' : '' }}"><a href="/subdealer-inventory/create">Add
         Subdealer Inventory</a></li>
 <li class="{{ request()->is('subdealer-inventory') ? 'active' : '' }}"><a href="/subdealer-inventory">Subdealer
         Inventory </a></li>
 <li class="{{ request()->is('subdealer-inventory-reduce/create') ? 'active' : '' }}"><a
         href="/subdealer-inventory-reduce/create">Reduce
         Subdealer Inventory</a></li> --}}
