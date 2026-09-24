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

    @can('vendor.review')
    <li>
        <a class="dropdown-item {{ request()->routeIs('admin.vendors.review') ? 'active' : '' }}" href="{{ route('admin.vendors.review') }}" wire:navigate>
            <i class="bi bi-clipboard-check me-2"></i>Review Vendor
        </a>
    </li>
    @endcan
    @can('vendor.list')
    <li>
        <a class="dropdown-item {{ request()->routeIs('admin.vendors.approved') ? 'active' : '' }}" href="{{ route('admin.vendors.approved') }}" wire:navigate>
            <i class="bi bi-building-check me-2"></i> Daftar Vendor
        </a>
    </li>
    @endcan
</ul>