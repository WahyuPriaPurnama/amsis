@extends('layouts.app')

@section('title', 'E-Slip AMS Holding')
@section('menuAMS', 'active')

@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                E-Slip AMS Holding
            @endslot

            <div class="table-responsive">
                <table class="table table-hover display" id="table">
                    <thead>
                        <tr>
                            <th>NO.</th>
                            <th>NAMA</th>
                            <th>E-SLIP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $eslips = [
                                ['nama' => 'A Lofie Vila Sarie', 'link' => 'https://gofile.me/7hje9/mjxpp0J94'],
                                ['nama' => 'Abbiyu Laily Putri', 'link' => 'https://gofile.me/7hje9/X9G1ia030'],
                                ['nama' => 'Ade Ayu Santi', 'link' => 'https://gofile.me/7hje9/MkNcD9EgW'],
                                ['nama' => 'Alifyya Faiza', 'link' => 'https://gofile.me/7hje9/InnFtW30J'],
                                ['nama' => 'Daffa Naufarel Zaidan', 'link' => 'https://gofile.me/7hje9/NmLiHWNpr'],
                                ['nama' => 'Denny Setyawan', 'link' => 'https://gofile.me/7hje9/Nl8gUH80R'],
                                ['nama' => 'Desinta Anakusuma', 'link' => 'https://gofile.me/7hje9/5L1tzk9TF'],
                                ['nama' => 'Desi Kumalasari', 'link' => 'https://gofile.me/7hje9/1HeLc0TeD'],
                                ['nama' => 'Fajar Agro Wibowo', 'link' => 'https://gofile.me/7hje9/vcFilCuWN'],
                                ['nama' => 'Firsandy', 'link' => 'https://gofile.me/7hje9/36JOdvbO4'],
                                ['nama' => 'Hana Jelita Insani Pramo', 'link' => 'https://gofile.me/7hje9/BIHjvlIXn'],
                                ['nama' => 'Harianto', 'link' => 'https://gofile.me/7hje9/gp6lBJaJw'],
                                ['nama' => 'Hilda Astri Damayanti', 'link' => 'https://gofile.me/7hje9/9VWYL3xOU'],
                                ['nama' => 'Hisyam Al Farisi', 'link' => 'https://gofile.me/7hje9/nW0xb0pGA'],
                                ['nama' => 'Linggam Wardani Putri', 'link' => 'https://gofile.me/7hje9/xeUVQ13HM'],
                                ['nama' => 'M. Zidan', 'link' => 'https://gofile.me/7hje9/yuGE9YMIp'],
                                ['nama' => 'Michelline', 'link' => 'https://gofile.me/7hje9/EjAeVlEzh'],
                                ['nama' => 'Muhammad Bobsaid', 'link' => 'https://gofile.me/7hje9/TsLDeHAcr'],
                                ['nama' => 'Nabilah', 'link' => 'https://gofile.me/7hje9/9vVuqFoBY'],
                                ['nama' => 'Nadia Sofia Permatasari', 'link' => 'https://gofile.me/7hje9/1KZwUAoaS'],
                                ['nama' => 'Didik', 'link' => 'https://gofile.me/7hje9/bAJ5utbbk'],
                                ['nama' => 'Samsul Hidayat', 'link' => 'https://gofile.me/7hje9/lk9rq5mnx'],
                                ['nama' => 'Suwandi', 'link' => 'https://gofile.me/7hje9/9AmrWc20u'],
                                ['nama' => 'Toha', 'link' => 'https://gofile.me/7hje9/3Q7PRXnXS'],
                                ['nama' => 'Trisna Amrina', 'link' => 'https://gofile.me/7hje9/IuxZIusrR'],
                                ['nama' => 'Wahyu Pria Purnama', 'link' => 'https://gofile.me/7hje9/yKmYAhERF'],
                                ['nama' => 'Wahyu Vinika Sari', 'link' => 'https://gofile.me/7hje9/I7VRtTCBe'],
                            ];
                        @endphp

                        @foreach ($eslips as $eslip)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $eslip['nama'] }}</td>
                                <td>
                                    <a class="btn btn-primary" href="{{ $eslip['link'] }}" target="_blank">
                                        <i class="bi bi-file-earmark"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endcomponent
    </div>
@endsection
