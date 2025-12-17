<ul class="dropdown-menu">
    @can('request-order.list')
        <li><a class="dropdown-item @yield('menuRO')" href="/request-order">Request Order</a></li>
    @endcan
    @can('request-payment.list')
        <li><a href="#" class="dropdown-item">Request Payment</a></li>
    @endcan
</ul>
