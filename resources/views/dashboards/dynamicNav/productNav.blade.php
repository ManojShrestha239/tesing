<ul class="nav navbar-nav margin-bottom-35 products-class">
    <li class="{{ request()->is('category/create') ? 'active' : '' }}"><a href="/category/create">Add New Category </a>
    </li>
    <li class="{{ request()->is('category') ? 'active' : '' }}"><a href="/category">List Categories </a></li>
    <li class="{{ request()->is('size/create') ? 'active' : '' }}"><a href="/size/create">Add New Variant </a></li>
    <li class="{{ request()->is('size') ? 'active' : '' }}"><a href="/size">List Variants </a></li>
    <li class="{{ request()->is('product/create') ? 'active' : '' }}"><a href="/product/create">Add New Product </a></li>
    <li class="{{ request()->is('product') ? 'active' : '' }}"><a href="/product">List Product </a></li>
    <li class="{{ request()->is('final-product/create') ? 'active' : '' }}"><a href="/final-product/create">Assign
            Product with Variants </a></li>
    <li class="{{ request()->is('final-product') ? 'active' : '' }}"><a href="/final-product">Final Product List </a>
    </li>
</ul>
