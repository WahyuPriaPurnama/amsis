 <ul class="dropdown-menu">
     @can('employee.list')
         <li><a class="dropdown-item @yield('menuEmployees')" href="{{ route('employees.index') }}">Karyawan</a></li>
     @endcan
     @can('subsidiary.list')
         <li><a class="dropdown-item @yield('menuSubsidiaries')" href="{{ route('subsidiaries.index') }}">Perusahaan</a></li>
     @endcan
     @can('vehicle.list')
         <li><a class="dropdown-item @yield('menuVehicles')" href="{{ route('vehicles.index') }}">Kendaraan</a></li>
     @endcan
     @can('asset.list')
         <li><a href="{{ route('asset.index') }}" class="dropdown-item @yield('menuAsset')">Data Aset</a></li>
     @endcan
     {{-- <li><a class="dropdown-item @yield('menuScanlog')"
                                                href="{{ route('scanlog.index') }}">Scanlog</a></li>
                                        <li><a class="dropdown-item @yield('menuHarian')"
                                                href="{{ route('karyawan-harian.index') }}">Karyawan</a></li> --}}
 </ul>
