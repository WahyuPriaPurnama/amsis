<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AMSIS') }} | @yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                @auth
                    @php
                        $user = auth()->user();
                        $isEmployee = $user->hasRole('employee');
                        $employeeId = $user->employee_id ?? null;
                    @endphp
                    @if ($isEmployee && $employeeId)
                        <a class="navbar-brand" href="{{ route('employees.show', $employeeId) }}">
                            {{ config('app.name', 'AMSIS') }}
                        </a>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown">
                                        E-Slip
                                    </a>
                                    @include('partials.navbar-eslip')
                                </li>
                            </ul>
                        </div>
                    @else
                        <a class="navbar-brand" href="{{ url('/') }}">
                            {{ config('app.name', 'AMSIS') }}
                        </a>
                    @endif
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    @if (!$isEmployee)
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown">
                                        HRD
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item @yield('menuEmployees')"
                                                href="{{ route('employees.index') }}">Karyawan</a></li>
                                        <li><a class="dropdown-item @yield('menuSubsidiaries')"
                                                href="{{ route('subsidiaries.index') }}">Perusahaan</a></li>
                                        <li><a class="dropdown-item @yield('menuVehicles')"
                                                href="{{ route('vehicles.index') }}">Kendaraan</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown">
                                        Payroll
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item @yield('menuScanlog')"
                                                href="{{ route('scanlog.index') }}">Scanlog</a></li>
                                        <li><a class="dropdown-item @yield('menuHarian')"
                                                href="{{ route('karyawan-harian.index') }}">Karyawan</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown">
                                        E-Slip
                                    </a>
                                    @include('partials.navbar-eslip')
                                </li>
                            </ul>
                        </div>
                    @endif
                @endauth
                @guest
                    <a class="navbar-brand" href="{{ url('/') }}" id="amsis-logo">
                        {{ config('app.name', 'AMSIS') }}
                    </a>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    E-Slip
                                </a>
                                @include('partials.navbar-eslip')
                            </li>
                        </ul>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const logo = document.getElementById('amsis-logo');
                            if (logo) {
                                logo.addEventListener('click', function(e) {
                                    e.preventDefault(); // Mencegah navigasi
                                    alert('Silakan login terlebih dahulu untuk mengakses halaman ini.');
                                });
                            }
                        });
                    </script>
                @endguest
                <ul class="navbar-nav ms-auto">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif
                    @else
                        @php
                            $hour = now()->hour;
                            $greeting = match (true) {
                                $hour < 11 => 'Selamat Pagi',
                                $hour < 15 => 'Selamat Siang',
                                $hour < 18 => 'Selamat Sore',
                                default => 'Selamat Malam',
                            };
                        @endphp
                        <div class="d-flex align-items-center">
                            {{ $greeting }},
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                    @if (Auth::user()->hasRole('super-admin'))
                                    <a href="{{route('roles.index')}}" class="dropdown-item">Roles & Permission</a>
                                        <a class="dropdown-item" href="{{ route('users.index') }}">User
                                            Management</a>
                                        <a class="dropdown-item" href="{{ route('log.activity') }}">Log
                                            Activity</a>
                                    @elseif(Auth::user()->hasRole('employee'))
                                        <a href="{{ route('employees.show', $employeeId) }}"
                                            class="dropdown-item">Profil</a>
                                        <a href="{{ route('password.edit') }}" class="dropdown-item">Ganti
                                            Password</a>
                                    @endif
                                    <a class="dropdown-item text-danger fw-bold" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                </div>
                            </li>
                        </div>
                    @endguest
                </ul>
            </div>
        </nav>
    </div>
    <main class="py-4">
        @yield('content')
    </main>
    <footer class="bg-dark py-4 text-white  mt-auto">
        <div class="container text-center">
            AMS Information System | © {{ date('Y') }} All rights reserved.
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const tableElement = $('#table');
            if (tableElement.length) {
                tableElement.DataTable();
            }
        });

        // Optional: reinitialize if table is dynamically replaced via AJAX
        document.addEventListener("table:reload", () => {
            const tableElement = $('#table');
            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
            }
            tableElement.DataTable();
        });
    </script>
</body>

</html>
