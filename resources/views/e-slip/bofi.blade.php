@extends('layouts.app')
@section('title', 'E-Slip BOFI')
@section('menuBOFI', 'active')
@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                E-Slip BOFI
            @endslot
            <div class="table-responsive">
                <table class="table table-hover display" id="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NAMA</th>
                            <th>E-Slip</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $eslips = [
                                ['nama' => 'Abdul Haris', 'link' => 'https://gofile.me/7hje9/gi78Yy0so'],
                                ['nama' => 'Alfian Juhri', 'link' => 'https://gofile.me/7hje9/5Rl5mCqRZ'],
                                ['nama' => 'Anang Yuli Andoko', 'link' => 'https://gofile.me/7hje9/uoh9biIZI'],
                                ['nama' => 'Aprilia Nurjanah', 'link' => 'https://gofile.me/7hje9/zRwXhPA2I'],
                                ['nama' => 'Arthur Archilles', 'link' => 'https://gofile.me/7hje9/UFMKGcUaj'],
                                ['nama' => 'Dwi Novitasari', 'link' => 'https://gofile.me/7hje9/n5fSxPjfK'],
                                ['nama' => 'Edi Cahyono', 'link' => 'https://gofile.me/7hje9/a851JVz5l'],
                                ['nama' => 'Fabe Ansyah Budiatma', 'link' => 'https://gofile.me/7hje9/jcXcVteaN'],
                                ['nama' => 'Fazza Faizatul I', 'link' => 'https://gofile.me/7hje9/zPn9mPQNh'],
                                ['nama' => 'Hengki Dwi Purnomo', 'link' => 'https://gofile.me/7hje9/Io8Ghev0P'],
                                ['nama' => 'Kusnari', 'link' => 'https://gofile.me/7hje9/nkoTw58he'],
                                ['nama' => 'Maura Harniestania', 'link' => 'https://gofile.me/7hje9/Sx2kRgDj0'],
                                ['nama' => 'Megaria', 'link' => 'https://gofile.me/7hje9/bC5RuubmV'],
                                ['nama' => 'Moh Yasin', 'link' => 'https://gofile.me/7hje9/A6Sd9B2PY'],
                                ['nama' => 'Muhammad Farid Pratama', 'link' => 'https://gofile.me/7hje9/pTzvA3B9n'],
                                ['nama' => 'Muhammad Nurodin', 'link' => 'https://gofile.me/7hje9/hb3Yo7cbG'],
                                ['nama' => 'Nanda Dwi Nova', 'link' => 'https://gofile.me/7hje9/wQMtuikN5'],
                                ['nama' => 'Nazwar Arif', 'link' => 'https://gofile.me/7hje9/mu4IvxcoP'],
                                ['nama' => 'Nurkolis', 'link' => 'https://gofile.me/7hje9/IdqxRT3CQ'],
                                ['nama' => 'Regita Dwi Aprilia', 'link' => 'https://gofile.me/7hje9/d6fwSOMS7'],
                                ['nama' => 'Sugiani', 'link' => 'https://gofile.me/7hje9/PdjQ2BBxo'],
                                ['nama' => 'Sugianto', 'link' => 'https://gofile.me/7hje9/3KASfUDu8'],
                                ['nama' => 'Sugiyono Eko Susanto', 'link' => 'https://gofile.me/7hje9/UFMAgoaIi'],
                                ['nama' => 'Sunarko', 'link' => 'https://gofile.me/7hje9/gTFa1BRVu'],
                                ['nama' => 'Suyadi', 'link' => 'https://gofile.me/7hje9/7iNJ49K7Y'],
                                ['nama' => 'Syakira', 'link' => 'https://gofile.me/7hje9/zNHor2vrc'],
                                ['nama' => 'Tri Harsono', 'link' => 'https://gofile.me/7hje9/z5LxaVBnz'],
                                ['nama' => 'Tri Widyastuti', 'link' => 'https://gofile.me/7hje9/lBxj5lt8E'],
                                ['nama' => 'Wijianto', 'link' => 'https://gofile.me/7hje9/ERx7XiCOJ'],
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
