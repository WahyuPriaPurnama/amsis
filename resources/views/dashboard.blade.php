@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="container">
        @if (session('feature_changes'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h5>🔔Info Peningkatan Fitur:</h5>
                <ul>
                    @foreach (session('feature_changes') as $date => $note)
                        <li><strong>{{ $date }}:</strong> {{ $note }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

        @endif

        @component('components.card')
            @slot('header')
                <div class="d-flex justify-content-between">
                    JUMLAH KARYAWAN
                    @php
                        $hour = \Carbon\Carbon::now()->hour;
                    @endphp

                    @if ($hour < 11)
                        <div>Selamat Pagi</div>
                    @elseif ($hour < 15)
                        <div>Selamat Siang</div>
                    @elseif ($hour < 18)
                        <div>Selamat Sore</div>
                    @else
                        <div>Selamat Malam</div>
                    @endif
                </div>
            @endslot

            <div>
                <livewire:dashboard.employee-chart :ams="$ams" :eln1="$eln1" :eln2="$eln2" :bofi="$bofi"
                    :hk="$hk" :rmm="$rmm" />
            </div>
        @endcomponent
    </div>
@endsection
