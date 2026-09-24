<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'AMSIS') }}</title>
    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">

    <!-- Vite Assets (CSS & JS utama) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- FontAwesome & DataTables Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="app">
        {{-- Navbar Header --}}
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}" id="{{ Auth::guest() ? 'amsis-logo' : '' }}" wire:navigate>
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
                                <li><a class="dropdown-item @yield('menuAMS')" href="/e-slip/ams" wire:navigate>AMS Holding</a></li>
                                <li><a class="dropdown-item @yield('menuRMM')" href="/e-slip/rmm" wire:navigate>RMM</a></li>
                                <li><a class="dropdown-item @yield('menuELN')" href="/e-slip/eln1" wire:navigate>ELN Malang</a></li>
                                <li><a class="dropdown-item @yield('menuELN2')" href="/e-slip/eln2" wire:navigate>ELN Banyuwangi</a></li>
                                <li><a class="dropdown-item @yield('menuHAKA')" href="/e-slip/haka" wire:navigate>HAKA</a></li>
                                <li><a class="dropdown-item @yield('menuBOFI')" href="/e-slip/bofi" wire:navigate>BOFI</a></li>
                            </ul>
                        </li>

                        @auth
                        @canany(['employee.list', 'subsidiary.list', 'vehicle.list', 'asset.list'])
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">HRD</a>
                            @include('hrd.partials.hrd-menu')
                        </li>
                        @endcanany

                        @canany(['request-order.list', 'request-payment.list', 'master-supplier.list', 'receipts.list'])
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Pembelian</a>
                            @include('purchasing.partials.purchasing-menu')
                        </li>
                        @endcanany
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        @guest
                        @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}" wire:navigate>{{ __('Login') }}</a>
                        </li>
                        @endif
                        @else
                        @php
                        $hour = now()->hour;
                        $greeting = match (true) {
                        $hour < 11=> 'Selamat Pagi',
                            $hour < 15=> 'Selamat Siang',
                                $hour < 18=> 'Selamat Sore',
                                    default => 'Selamat Malam',
                                    };
                                    @endphp
                                    <li class="nav-item dropdown">
                                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                            <span class="text-muted me-1">{{ $greeting }},</span>
                                            <strong>{{ Auth::user()->name }}</strong>
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-end">
                                            @if (Auth::user()->hasRole('super-admin'))
                                            <a href="{{ route('roles.index') }}" class="dropdown-item" wire:navigate>
                                                <i class="bi bi-shield-lock me-2"></i> Roles & Permission
                                            </a>
                                            <a href="{{ route('users.index') }}" class="dropdown-item" wire:navigate>
                                                <i class="bi bi-people me-2"></i> User Management
                                            </a>
                                            <a href="{{ route('log.activity') }}" class="dropdown-item" wire:navigate>
                                                <i class="bi bi-journal-text me-2"></i> Log Activity
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            @endif

                                            @if (Auth::user()->employee_id)
                                            <a href="{{ route('employees.show', Auth::user()->employee_id) }}" class="dropdown-item" wire:navigate>
                                                <i class="bi bi-person me-2"></i> Profil
                                            </a>
                                            <a href="{{ route('password.change') }}" class="dropdown-item" wire:navigate>
                                                <i class="bi bi-key me-2"></i> Ganti Password
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            @endif

                                            <a class="dropdown-item text-danger fw-bold" href="#" onclick="event.preventDefault(); handleLogout();">
                                                <i class="bi bi-box-arrow-right me-2"></i> Logout
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

    <!-- Main Content: Diberi padding vertical (py-3 di mobile, py-md-4 di desktop) -->
    <main class="flex-grow-1 py-3 py-md-4">
        @hasSection('content')
        @yield('content')
        @else
        {{ $slot ?? '' }}
        @endif
    </main>

    {{-- Footer --}}
    <footer class="bg-dark py-3 text-white mt-auto">
        <div class="container text-center">
            AMS Information System | © {{ date('Y') }} All rights reserved.
        </div>
    </footer>

    <!-- JS Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        function handleLogout() {
            const form = document.getElementById('logout-form');
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(() => {
                if (window.Livewire) {
                    Livewire.navigate('/login');
                } else {
                    window.location.href = '/login';
                }
            });
        }

        document.addEventListener('livewire:navigated', function() {
            const tableElement = $('#table');
            if (tableElement.length && !$.fn.DataTable.isDataTable('#table')) {
                tableElement.DataTable();
            }

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