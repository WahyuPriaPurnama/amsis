<ul class="dropdown-menu">
    @can('request-order.list')
        <li><a class="dropdown-item @yield('menuRO')" href="/request-order">Request Order</a></li>
    @endcan
</ul>
