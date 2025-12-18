<ul class="dropdown-menu">
    @can('request-order.list')
        <li><a class="dropdown-item @yield('menuOrder')" href="{{ route('request-order.index') }}">Request Order</a></li>
    @endcan
    @can('request-payment.list')
        <li><a href="{{ route('request-payment.index') }}" class="dropdown-item @yield('menuPayment')">Request Payment</a></li>
    @endcan
</ul>
