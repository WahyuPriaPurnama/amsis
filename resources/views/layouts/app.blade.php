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
                <a class="navbar-brand" href="{{ url('/') }}" id="{{ Auth::guest() ? 'amsis-logo' : '' }}">
                    {{ config('app.name', 'AMSIS') }}
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                E-Slip
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item @yield('menuAMS')" href="/ams-malang">AMS Holding</a></li>
                                <li><a class="dropdown-item @yield('menuRMM')" href="/rmm-malang">RMM</a></li>
                                <li><a class="dropdown-item @yield('menuELN')" href="/eln-malang">ELN Malang</a></li>
                                <li><a class="dropdown-item @yield('menuELN2')" href="/eln-bwi">ELN Banyuwangi</a></li>
                                <li><a class="dropdown-item @yield('menuHAKA')" href="/haka-bwi">HAKA</a></li>
                                <li><a class="dropdown-item @yield('menuBOFI')" href="/bofi-bwi">BOFI</a></li>
                            </ul>
                        </li>



                        @auth
                            @canany(['employee.list', 'subsidiary.list', 'vehicle.list', 'asset.list'])
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown">HRD</a>
                                    @include('hrd.partials.hrd-menu')
                                </li>
                            @endcanany

                            @canany(['request-order.list', 'request-payment.list', 'master-supplier.list', 'receipts.list'])
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown">Pembelian</a>
                                    @include('purchasing.partials.purchasing-menu')
                                </li>
                            @endcanany
                        @endauth
                    </ul>

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
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown">
                                    <span class="text-muted me-1">{{ $greeting }},</span>
                                    <strong>{{ Auth::user()->name }}</strong>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end">
                                    @if (Auth::user()->hasRole('super-admin'))
                                        <a href="{{ route('roles.index') }}" class="dropdown-item">Roles & Permission</a>
                                        <a href="{{ route('users.index') }}" class="dropdown-item">User Management</a>
                                        <a href="{{ route('log.activity') }}" class="dropdown-item">Log Activity</a>
                                        <div class="dropdown-divider"></div>
                                    @endif

                                    @if (Auth::user()->employee_id)
                                        <a href="{{ route('employees.show', Auth::user()->employee_id) }}"
                                            class="dropdown-item">Profil</a>
                                        <a href="{{ route('password.edit') }}" class="dropdown-item">Ganti Password</a>
                                    @endif

                                    <a class="dropdown-item text-danger fw-bold" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <main class="py-4">
        @yield('content')
    </main>

    <footer class="bg-dark py-4 text-white mt-auto">
        <div class="container text-center">
            AMS Information System | © {{ date('Y') }} All rights reserved.
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DataTables Initialization
            const tableElement = $('#table');
            if (tableElement.length) {
                tableElement.DataTable();
            }

            // Guest Alert for Logo
            const logo = document.getElementById('amsis-logo');
            if (logo) {
                logo.addEventListener('click', function(e) {
                    e.preventDefault();
                    alert('Silakan login terlebih dahulu untuk mengakses halaman utama.');
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
