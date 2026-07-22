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
                ['nama' => 'Alfian Fenando', 'link' => 'https://gofile.me/7hje9/DkXkfafug'], //3110
                ['nama' => 'Alfian Sifaul Qolbi', 'link' => 'https://gofile.me/7hje9/6zkFDVtGg'],
                ['nama' => 'Andiko Prasetyo', 'link' => 'https://gofile.me/7hje9/gS375om4w'],
                ['nama' => 'Arif Ainur R', 'link' => 'https://gofile.me/7hje9/fXT61Xvy4'], //1010
                ['nama' => 'Beni F', 'link' => 'https://gofile.me/7hje9/ebkPdIvj8'], //1015
                ['nama' => 'Berlian Fitria', 'link' => 'https://gofile.me/7hje9/6QlRwVyXn'], //1008
                ['nama' => 'Brian Bima Wardhana', 'link' => 'https://gofile.me/7hje9/yD0vW3fpV'],
                ['nama' => 'Buang Rehati', 'link' => 'https://gofile.me/7hje9/vwH0Y2mgo'],
                ['nama' => 'Budi Cahyono', 'link' => 'https://gofile.me/7hje9/HJdKQXFVW'],
                ['nama' => 'Budiono', 'link' => 'https://gofile.me/7hje9/rOv8t5RhW'],
                ['nama' => 'Cahyo F.', 'link' => 'https://gofile.me/7hje9/vre7rdnzI'], //3710
                ['nama' => 'Candra Mauiliyanto', 'link' => 'https://gofile.me/7hje9/hEE6kPBwy'],
                ['nama' => 'Devi Ratnawati', 'link' => 'https://gofile.me/7hje9/i1Pmhhy7A'],
                ['nama' => 'Dian Purnomo', 'link' => 'https://gofile.me/7hje9/KTTrp8aAZ'],
                ['nama' => 'Dimas Alvian', 'link' => 'https://gofile.me/7hje9/SkWS70gGM'], //1012
                ['nama' => 'Dwi Siswanto', 'link' => 'https://gofile.me/7hje9/P6QWH4wWX'], //1035
                ['nama' => 'Edy Susanto', 'link' => 'https://gofile.me/7hje9/3uhWiTw3p'],
                ['nama' => 'Eko Hariyadi', 'link' => 'https://gofile.me/7hje9/0ZYaHVzTL'],
                ['nama' => 'Eko Marlindo', 'link' => 'https://gofile.me/7hje9/K9KL1KkYb'],
                ['nama' => 'Eko Rohandi', 'link' => 'https://gofile.me/7hje9/6cTS3EryZ'],
                ['nama' => 'Eko Winoto', 'link' => 'https://gofile.me/7hje9/JNpPYv9Rx'],
                ['nama' => 'Elmaniya', 'link' => 'https://gofile.me/7hje9/L9llnFZvi'],
                ['nama' => 'Endang R', 'link' => 'https://gofile.me/7hje9/Bh7ZV8Pfe'], //1028
                ['nama' => 'Endang Sugiartiningsih', 'link' => 'https://gofile.me/7hje9/bLk5bNfkk'],
                ['nama' => 'Fanni Faisal', 'link' => 'https://gofile.me/7hje9/0Tpe6h1JO'], //1011
                ['nama' => 'Faradya', 'link' => 'https://gofile.me/7hje9/9X2rJrJ2I'], //0720
                ['nama' => 'Faris R.', 'link' => 'https://gofile.me/7hje9/vyDWvZMTA'], //1008
                ['nama' => 'Fathur Rozi', 'link' => 'https://gofile.me/7hje9/KKrbmSEbD'],
                ['nama' => 'Fiki Adi', 'link' => 'https://gofile.me/7hje9/azlfHY6df'], //1342
                ['nama' => 'Firman A.', 'link' => 'https://gofile.me/7hje9/l55Jtr3mB'], //1036
                ['nama' => 'Fuad Faisal', 'link' => 'https://gofile.me/7hje9/3aDAGV01J'],
                ['nama' => 'Gebril Dandi', 'link' => 'https://gofile.me/7hje9/wukLfuuPB'], //1346
                ['nama' => 'Guntur A.', 'link' => 'https://gofile.me/7hje9/mazEwdW5K'], //1031
                ['nama' => 'Habib Ismail', 'link' => 'https://gofile.me/7hje9/DlihL4Wxm'],
                ['nama' => 'Hamdani', 'link' => 'https://gofile.me/7hje9/bbugcPUHy'],
                ['nama' => 'Hanif Putra', 'link' => 'https://gofile.me/7hje9/dXGSC53Zx'],
                ['nama' => 'Harianto', 'link' => 'https://gofile.me/7hje9/WkQOOkYgV'],
                ['nama' => 'Hendra Pratama', 'link' => 'https://gofile.me/7hje9/3WojrluUI'], //1014
                ['nama' => 'Hendrik A.', 'link' => 'https://gofile.me/7hje9/nUrIRSJQp'], //3810
                ['nama' => 'Hendriyo', 'link' => 'https://gofile.me/7hje9/0vLMs4WH9'],
                ['nama' => 'Heri Cahyono', 'link' => 'https://gofile.me/7hje9/AKeATZSgR'],
                ['nama' => 'I Made Sudarsana', 'link' => 'https://gofile.me/7hje9/i5tvt2wMn'],
                ['nama' => 'Ikbar Maulana', 'link' => 'https://gofile.me/7hje9/5G7CT49FL'], //3410
                ['nama' => 'Imam Sodikin', 'link' => 'https://gofile.me/7hje9/UE6H311py'],
                ['nama' => 'Indi Dwi Hayatin Sumarno', 'link' => 'https://gofile.me/7hje9/7LJaW4Qj9'],
                ['nama' => 'Indri Susanti', 'link' => 'https://gofile.me/7hje9/ou9hf9qBS'],
                ['nama' => 'Jhodi Dwi', 'link' => 'https://gofile.me/7hje9/GN7AyDXvU'],
                ['nama' => 'Khoirur Rizki', 'link' => 'https://gofile.me/7hje9/FZkltWefY'], //1043
                ['nama' => 'Kiki Maria Utama', 'link' => 'https://gofile.me/7hje9/safgAM8ev'],
                ['nama' => 'M. Andri Maulana', 'link' => 'https://gofile.me/7hje9/kgKqILJWI'],
                ['nama' => 'M. Riyan', 'link' => 'https://gofile.me/7hje9/mpSbZuNV6'], //4510
                ['nama' => 'M. Rizki S.', 'link' => 'https://gofile.me/7hje9/xqQv4bRAV'], //1032
                ['nama' => 'M. Rizky Pamungkas', 'link' => 'https://gofile.me/7hje9/GQrvricuT'],
                ['nama' => 'M. Yusuf', 'link' => 'https://gofile.me/7hje9/WJU8EQSFw'], //1343
                ['nama' => 'M. Zidan', 'link' => 'https://gofile.me/7hje9/vsyMjwL2Y'], //1010
                ['nama' => 'Mariyana', 'link' => 'https://gofile.me/7hje9/0rPIUAIzb'],
                ['nama' => 'Miranda P.', 'link' => 'https://gofile.me/7hje9/t3hnNcVfg'], //2026
                ['nama' => 'Mirza Agung', 'link' => 'https://gofile.me/7hje9/LJjn3UkNs'],
                ['nama' => 'Misran Tambir', 'link' => 'https://gofile.me/7hje9/Ke18DyQ86'],
                ['nama' => 'Moh. Dedi Riyanto', 'link' => 'https://gofile.me/7hje9/hauoHhz2s'],
                ['nama' => 'Moh. Lukman Nur Hakim', 'link' => 'https://gofile.me/7hje9/sPGzL8GOW'],
                ['nama' => 'Mugi Lestari', 'link' => 'https://gofile.me/7hje9/ZevQKqjCL'],
                ['nama' => 'Muhammad Fausiy', 'link' => 'https://gofile.me/7hje9/73902Ahg4'],
                ['nama' => 'Ni Ketut Sariningsih', 'link' => 'https://gofile.me/7hje9/BbYWdHkpN'],
                ['nama' => 'Qoiril', 'link' => 'https://gofile.me/7hje9/l0FHFTzQ5'], //1037
                ['nama' => 'R. Ulul', 'link' => 'https://gofile.me/7hje9/f9Ae3eXE0'], //1009
                ['nama' => 'Rachamdiyanto', 'link' => 'https://gofile.me/7hje9/96PF8fRu8'],
                ['nama' => 'Rahman Andi Pratama', 'link' => 'https://gofile.me/7hje9/Vc5y3XE6d'],
                ['nama' => 'Ramli', 'link' => 'https://gofile.me/7hje9/jo9JxSnzC'],
                ['nama' => 'Randi H.', 'link' => 'https://gofile.me/7hje9/DGVdiFfxH'], //1039
                ['nama' => 'Rehan', 'link' => 'https://gofile.me/7hje9/ZA24Q0eC4'], //1044
                ['nama' => 'Restu Hidayat', 'link' => 'https://gofile.me/7hje9/O8xdtPTH5'], //1005
                ['nama' => 'Retno Hariyani', 'link' => 'https://gofile.me/7hje9/JAx7lDr3v'],
                ['nama' => 'Reza Zakariya', 'link' => 'https://gofile.me/7hje9/NZiSohfVr'], //1034
                ['nama' => 'Rina J.', 'link' => 'https://gofile.me/7hje9/dSsy81pRG'], //1007
                ['nama' => 'Rohim', 'link' => 'https://gofile.me/7hje9/9mQC3jOUU'], //0934
                ['nama' => 'Rudi Mardiyanto', 'link' => 'https://gofile.me/7hje9/bS7R6J6EI'],
                ['nama' => 'Rudi Prastiyan', 'link' => 'https://gofile.me/7hje9/ui3LbGwEE'],
                ['nama' => 'Ryan Mulyadi', 'link' => 'https://gofile.me/7hje9/BaT5ZNuZM'], //1016
                ['nama' => 'Sabrina Ayu Lestari', 'link' => 'https://gofile.me/7hje9/F0MFR3kxQ'],
                ['nama' => 'Sahrul Khan', 'link' => 'https://gofile.me/7hje9/3mfE8ZgQP'],
                ['nama' => 'Samsul Aripin', 'link' => 'https://gofile.me/7hje9/TKliGEIIL'],
                ['nama' => 'Saut', 'link' => 'https://gofile.me/7hje9/z8ySeuq5D'], //1045
                ['nama' => 'Sofiatul Mufidah', 'link' => 'https://gofile.me/7hje9/kL19C9gWS'],
                ['nama' => 'Sofiatul Wardah', 'link' => 'https://gofile.me/7hje9/KEMGPnG9F'],
                ['nama' => 'Sriah', 'link' => 'https://gofile.me/7hje9/3z60T1oVT'], //1352
                ['nama' => 'Sukirno', 'link' => 'https://gofile.me/7hje9/CIYw9WYdw'],
                ['nama' => 'Sulistyo Kurniawan', 'link' => 'https://gofile.me/7hje9/fo4kHsknT'],
                ['nama' => 'Sulistyowati', 'link' => 'https://gofile.me/7hje9/CZi8Xduwc'],
                ['nama' => 'Suparjiono', 'link' => 'https://gofile.me/7hje9/Ti6RdAxBI'],
                ['nama' => 'Suriyono', 'link' => 'https://gofile.me/7hje9/rHc9OJSvg'],
                ['nama' => 'Suryono', 'link' => 'https://gofile.me/7hje9/iToy6TScu'],
                ['nama' => 'Susilo', 'link' => 'https://gofile.me/7hje9/dPsqlVqY0'],
                ['nama' => 'Syamsul Hadi', 'link' => 'https://gofile.me/7hje9/SgK9fQWE2'],
                ['nama' => 'Tatik Nurul', 'link' => 'https://gofile.me/7hje9/xMc4sPhMC'], //3510
                ['nama' => 'Umi Kalsum', 'link' => 'https://gofile.me/7hje9/3tfUwrRov'],
                ['nama' => 'Vidya Hayatun Nufus', 'link' => 'https://gofile.me/7hje9/DX9MXgGNc'],
                ['nama' => 'Vita Puji Lestari', 'link' => 'https://gofile.me/7hje9/eQAvSNWQu'],
                ['nama' => 'Wahyu Arif', 'link' => 'https://gofile.me/7hje9/OpqhIVqTL'], //2207
                ['nama' => 'Widarini', 'link' => 'https://gofile.me/7hje9/6ESKsRGwM'],
                ['nama' => 'Widya A.', 'link' => 'https://gofile.me/7hje9/cLM9yyJOj'], //1038
                ['nama' => 'Yogi W.', 'link' => 'https://gofile.me/7hje9/sdXDUpqfR'], //3610
                ['nama' => 'Yuni Ana Ayu Kamelya', 'link' => 'https://gofile.me/7hje9/6PfTDhBw3'],
                ['nama' => 'Zahra', 'link' => 'https://gofile.me/7hje9/0BRQqHUY4'], //4410
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