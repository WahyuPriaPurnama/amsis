@extends('layouts.app')
@section('title', 'E-Slip HAKA')
@section('menuHAKA', 'active')
@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                E-Slip HAKA
            @endslot
            <div class="table-responsive">
                <table class="table table-hover display" id="table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>NAMA</th>
                            <th>E-Slip</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $slips = [
                                ['nama' => 'Achmad Soni', 'link' => 'https://gofile.me/7hje9/JC6S81IEO'],
                                ['nama' => 'Agus Affandi', 'link' => 'https://gofile.me/7hje9/ueeJFLUiN'],
                                ['nama' => 'Dhimas Perwira Setyawan', 'link' => 'https://gofile.me/7hje9/Xg4epG0ML'],
                                ['nama' => 'Firman Novianto', 'link' => 'https://gofile.me/7hje9/2al02JuXy'],
                                ['nama' => 'Hendrio', 'link' => 'https://gofile.me/7hje9/eX9QxYlrb'],
                                ['nama' => 'Heru Surahmad', 'link' => 'https://gofile.me/7hje9/o1mVnFn5C'],
                                ['nama' => 'Hirman Susandi', 'link' => 'https://gofile.me/7hje9/4ayS8tTYW'],
                                ['nama' => 'Imam Wahyudi', 'link' => 'https://gofile.me/7hje9/Tv4OZE2CP'],
                                ['nama' => 'Moh. Iwan', 'link' => 'https://gofile.me/7hje9/pHtGlwCGh'],
                                ['nama' => 'Mohammad Taufik', 'link' => 'https://gofile.me/7hje9/G1YPMO6SR'],
                                ['nama' => 'Mugi Lestari', 'link' => 'https://gofile.me/7hje9/kswlGBghN'],
                                ['nama' => 'Muntholib', 'link' => 'https://gofile.me/7hje9/rizIjLuBU'],
                                ['nama' => 'Novian Hadi', 'link' => 'https://gofile.me/7hje9/SrAhV0hy2'],
                                ['nama' => 'Nurul Hadi Syafaat', 'link' => 'https://gofile.me/7hje9/Qr6BWWEkH'],
                                ['nama' => 'Rachmadiyanto', 'link' => 'https://gofile.me/7hje9/sCqJ5JoIi'],
                                ['nama' => 'Siti Mariyani', 'link' => 'https://gofile.me/7hje9/0jlLYf9IA'],
                                ['nama' => 'Suparno', 'link' => 'https://gofile.me/7hje9/DG51ZNZA2'],
                                ['nama' => 'Syajidi', 'link' => 'https://gofile.me/7hje9/wVTiLQSHF'],
                                ['nama' => 'Taufik', 'link' => 'https://gofile.me/7hje9/yAcHVcdlS'],
                                ['nama' => 'Wage Trubus Istanto', 'link' => 'https://gofile.me/7hje9/BdULDlTUa'],
                            ];
                        @endphp



                    </tbody>
                </table>
            </div>
        @endcomponent
    </div>
@endsection
