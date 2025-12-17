@extends('layouts.app')
@section('title', 'E-Slip ELN Banyuwangi')
@section('menuELN2', 'active')
@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                E-Slip ELN Banyuwangi
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
                                ['nama' => 'Affandi', 'link' => 'https://gofile.me/7hje9/JMtGcxGP9'],
                                ['nama' => 'Afriyadi Saputra', 'link' => 'https://gofile.me/7hje9/yiVcdbIpJ'],
                                ['nama' => 'Agus Salim', 'link' => 'https://gofile.me/7hje9/9uvp0d7Z7'],
                                ['nama' => 'Ainul Mubtadin', 'link' => 'https://gofile.me/7hje9/KbVfysBx2'],
                                ['nama' => 'Ainur Rofiq', 'link' => 'https://gofile.me/7hje9/YtpEaqzQx'],
                                ['nama' => 'Alfian Sifaul Qolbi', 'link' => 'https://gofile.me/7hje9/6zkFDVtGg'],
                                ['nama' => 'Andiko Prasetyo', 'link' => 'https://gofile.me/7hje9/gS375om4w'],
                                ['nama' => 'Brian Bima Wardhana', 'link' => 'https://gofile.me/7hje9/yD0vW3fpV'],
                                ['nama' => 'Buang Rehati', 'link' => 'https://gofile.me/7hje9/vwH0Y2mgo'],
                                ['nama' => 'Budi Cahyono', 'link' => 'https://gofile.me/7hje9/HJdKQXFVW'],
                                ['nama' => 'Budiono', 'link' => 'https://gofile.me/7hje9/rOv8t5RhW'],
                                ['nama' => 'Candra Mauiliyanto', 'link' => 'https://gofile.me/7hje9/hEE6kPBwy'],
                                ['nama' => 'Devi Ratnawati', 'link' => 'https://gofile.me/7hje9/i1Pmhhy7A'],
                                ['nama' => 'Dian Purnomo', 'link' => 'https://gofile.me/7hje9/KTTrp8aAZ'],
                                ['nama' => 'Edy Susanto', 'link' => 'https://gofile.me/7hje9/3uhWiTw3p'],
                                ['nama' => 'Eko Hariyadi', 'link' => 'https://gofile.me/7hje9/0ZYaHVzTL'],
                                ['nama' => 'Eko Marlindo', 'link' => 'https://gofile.me/7hje9/K9KL1KkYb'],
                                ['nama' => 'Eko Rohandi', 'link' => 'https://gofile.me/7hje9/6cTS3EryZ'],
                                ['nama' => 'Eko Winoto', 'link' => 'https://gofile.me/7hje9/JNpPYv9Rx'],
                                ['nama' => 'Elmaniya', 'link' => 'https://gofile.me/7hje9/L9llnFZvi'],
                                ['nama' => 'Endang Sugiartiningsih', 'link' => 'https://gofile.me/7hje9/bLk5bNfkk'],
                                ['nama' => 'Fathur Rozi', 'link' => 'https://gofile.me/7hje9/KKrbmSEbD'],
                                ['nama' => 'Fuad Faisal', 'link' => 'https://gofile.me/7hje9/3aDAGV01J'],
                                ['nama' => 'Habib Ismail', 'link' => 'https://gofile.me/7hje9/DlihL4Wxm'],
                                ['nama' => 'Hamdani', 'link' => 'https://gofile.me/7hje9/bbugcPUHy'],
                                ['nama' => 'Hanif Putra', 'link' => 'https://gofile.me/7hje9/dXGSC53Zx'],
                                ['nama' => 'Harianto', 'link' => 'https://gofile.me/7hje9/WkQOOkYgV'],
                                ['nama' => 'Hendriyo', 'link' => 'https://gofile.me/7hje9/0vLMs4WH9'],
                                ['nama' => 'Heri Cahyono', 'link' => 'https://gofile.me/7hje9/AKeATZSgR'],
                                ['nama' => 'Imam Sodikin', 'link' => 'https://gofile.me/7hje9/UE6H311py'],
                                ['nama' => 'I Made Sudarsana', 'link' => 'https://gofile.me/7hje9/i5tvt2wMn'],
                                ['nama' => 'Indi Dwi Hayatin Sumarno', 'link' => 'https://gofile.me/7hje9/7LJaW4Qj9'],
                                ['nama' => 'Indri Susanti', 'link' => 'https://gofile.me/7hje9/ou9hf9qBS'],
                                ['nama' => 'Kiki Maria Utama', 'link' => 'https://gofile.me/7hje9/safgAM8ev'],
                                ['nama' => 'M. Andri Maulana', 'link' => 'https://gofile.me/7hje9/kgKqILJWI'],
                                ['nama' => 'M. Rizky Pamungkas', 'link' => 'https://gofile.me/7hje9/GQrvricuT'],
                                ['nama' => 'Mariyana', 'link' => 'https://gofile.me/7hje9/0rPIUAIzb'],
                                ['nama' => 'Mirza Agung', 'link' => 'https://gofile.me/7hje9/LJjn3UkNs'],
                                ['nama' => 'Misran Tambir', 'link' => 'https://gofile.me/7hje9/Ke18DyQ86'],
                                ['nama' => 'Moh. Dedi Riyanto', 'link' => 'https://gofile.me/7hje9/hauoHhz2s'],
                                ['nama' => 'Moh. Lukman Nur Hakim', 'link' => 'https://gofile.me/7hje9/sPGzL8GOW'],
                                ['nama' => 'Mugi Lestari', 'link' => 'https://gofile.me/7hje9/ZevQKqjCL'],
                                ['nama' => 'Muhammad Fausiy', 'link' => 'https://gofile.me/7hje9/73902Ahg4'],
                                ['nama' => 'Ni Ketut Sariningsih', 'link' => 'https://gofile.me/7hje9/BbYWdHkpN'],
                                ['nama' => 'Rachamdiyanto', 'link' => 'https://gofile.me/7hje9/96PF8fRu8'],
                                ['nama' => 'Rahman Andi Pratama', 'link' => 'https://gofile.me/7hje9/Vc5y3XE6d'],
                                ['nama' => 'Ramli', 'link' => 'https://gofile.me/7hje9/jo9JxSnzC'],
                                ['nama' => 'Retno Hariyani', 'link' => 'https://gofile.me/7hje9/JAx7lDr3v'],
                                ['nama' => 'Rudi Mardiyanto', 'link' => 'https://gofile.me/7hje9/bS7R6J6EI'],
                                ['nama' => 'Rudi Prastiyan', 'link' => 'https://gofile.me/7hje9/ui3LbGwEE'],
                                ['nama' => 'Sabrina Ayu Lestari', 'link' => 'https://gofile.me/7hje9/F0MFR3kxQ'],
                                ['nama' => 'Samsul Aripin', 'link' => 'https://gofile.me/7hje9/TKliGEIIL'],
                                ['nama' => 'Sofiatul Mufidah', 'link' => 'https://gofile.me/7hje9/kL19C9gWS'],
                                ['nama' => 'Sofiatul Wardah', 'link' => 'https://gofile.me/7hje9/KEMGPnG9F'],
                                ['nama' => 'Sukirno', 'link' => 'https://gofile.me/7hje9/CIYw9WYdw'],
                                ['nama' => 'Sulistyo Kurniawan', 'link' => 'https://gofile.me/7hje9/fo4kHsknT'],
                                ['nama' => 'Sulistyowati', 'link' => 'https://gofile.me/7hje9/CZi8Xduwc'],
                                ['nama' => 'Suparjiono', 'link' => 'https://gofile.me/7hje9/Ti6RdAxBI'],
                                ['nama' => 'Suriyono', 'link' => 'https://gofile.me/7hje9/rHc9OJSvg'],
                                ['nama' => 'Suryono', 'link' => 'https://gofile.me/7hje9/iToy6TScu'],
                                ['nama' => 'Susilo', 'link' => 'https://gofile.me/7hje9/dPsqlVqY0'],
                                ['nama' => 'Sahrul Khan', 'link' => 'https://gofile.me/7hje9/3mfE8ZgQP'],
                                ['nama' => 'Syamsul Hadi', 'link' => 'https://gofile.me/7hje9/SgK9fQWE2'],
                                ['nama' => 'Umi Kalsum', 'link' => 'https://gofile.me/7hje9/3tfUwrRov'],
                                ['nama' => 'Vidya Hayatun Nufus', 'link' => 'https://gofile.me/7hje9/DX9MXgGNc'],
                                ['nama' => 'Vita Puji Lestari', 'link' => 'https://gofile.me/7hje9/eQAvSNWQu'],
                                ['nama' => 'Widarini', 'link' => 'https://gofile.me/7hje9/6ESKsRGwM'],
                                ['nama' => 'Yuni Ana Ayu Kamelya', 'link' => 'https://gofile.me/7hje9/6PfTDhBw3'],
                            ];
                        @endphp

                        @foreach ($slips as $slip)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $slip['nama'] }}</td>
                                <td>
                                    <a class="btn btn-primary" href="{{ $slip['link'] }}" target="_blank">
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
