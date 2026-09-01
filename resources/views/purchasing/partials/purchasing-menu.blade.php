<ul class="dropdown-menu shadow-sm">
    @can('request-order.list')
    <li>
        <a class="dropdown-item @yield('menuOrder')" href="{{ route('request-order.index') }}" wire:navigate>
            <i class="bi bi-file-earmark-text me-2"></i>Request Order
        </a>
    </li>
    @endcan

    @can('request-payment.list')
    <li>
        <a class="dropdown-item @yield('menuPayment')" href="{{ route('request-payment.index') }}" wire:navigate>
            <i class="bi bi-cash-stack me-2"></i>Request Payment
        </a>
    </li>
    @endcan
</ul>