<ul class="dropdown-menu shadow-sm">
    @can('employee.list')
    <li>
        <a class="dropdown-item @yield('menuEmployees')" href="{{ route('employees.index') }}" wire:navigate>
            <i class="bi bi-people me-2"></i>Karyawan
        </a>
    </li>
    @endcan

    @can('subsidiary.list')
    <li>
        <a class="dropdown-item @yield('menuSubsidiaries')" href="{{ route('subsidiaries.index') }}" wire:navigate>
            <i class="bi bi-building me-2"></i>Perusahaan
        </a>
    </li>
    @endcan

    @can('vehicle.list')
    <li>
        <a class="dropdown-item @yield('menuVehicles')" href="{{ route('vehicles.index') }}" wire:navigate>
            <i class="bi bi-car-front me-2"></i>Kendaraan
        </a>
    </li>
    @endcan

    @can('asset.list')
    <li>
        <a class="dropdown-item @yield('menuAsset')" href="{{ route('asset.index') }}" wire:navigate>
            <i class="bi bi-box-seam me-2"></i>Data Aset
        </a>
    </li>
    @endcan
</ul>