<ul class="dropdown-menu">
    @can('request-order.list')
        <li><a class="dropdown-item @yield('menuOrder')" href="{{ route('request-order.index') }}">Request Order</a></li>
    @endcan
    @can('request-payment.list')
        <li><a href="{{ route('request-payment.index') }}" class="dropdown-item @yield('menuPayment')">Request Payment</a></li>
    @endcan
    <li class="nav-item">
        <a class="nav-link fw-bold dropdown-item @yield('menuTracking')" href="{{ route('tracking.index') }}">
            Tracking Paket
            <span class="badge rounded-pill bg-danger ms-1" style="font-size: 0.50rem;">NEW</span>
        </a>
    </li>
</ul>
