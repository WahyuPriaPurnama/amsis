@extends('layouts.app')
@section('title', 'E-Slip ELN Malang')
@section('menuELN', 'active')
@section('content')
    <div class="container">
        @component('components.card')
            @slot('header')
                E-Slip ELN Malang
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
                                ['nama' => 'Adimas Setiawan', 'link' => 'https://gofile.me/7hje9/HVvL4deZ1'],
                                ['nama' => 'Ahmad Falakhitsaniya', 'link' => 'https://gofile.me/7hje9/iguxcBzcB'],
                                ['nama' => 'Alfian Fernando', 'link' => 'https://gofile.me/7hje9/9vDhxbhYY'],
                                ['nama' => 'Alfian Nur', 'link' => 'https://gofile.me/7hje9/IsAcaqsse'],
                                ['nama' => 'Alvana Noor Fariza', 'link' => 'https://gofile.me/7hje9/tJUbMuxck'],
                                ['nama' => 'Andika R.', 'link' => 'https://gofile.me/7hje9/zd12BTb3e'],
                                ['nama' => 'Ani Widhi Asih', 'link' => 'https://gofile.me/7hje9/VLmcHvO75'],
                                ['nama' => 'Ardiansyah', 'link' => 'https://gofile.me/7hje9/IuBUfmi1x'],
                                ['nama' => 'Arif Ainur Rofiq', 'link' => 'https://gofile.me/7hje9/W8OKNR9QM'],
                                ['nama' => 'Ary Puja Triswantoro', 'link' => 'https://gofile.me/7hje9/FaLk2laac'],
                                ['nama' => 'Aris Kurniawan', 'link' => 'https://gofile.me/7hje9/sq6hyQKlg'],
                                ['nama' => 'Benny Febriansyah', 'link' => 'https://gofile.me/7hje9/UqIZz3jIb'],
                                ['nama' => 'Berlian Fitria', 'link' => 'https://gofile.me/7hje9/MgZ4SZDaA'],
                                ['nama' => 'Davis Ariansyah', 'link' => 'https://gofile.me/7hje9/trnEGbalz'],
                                ['nama' => 'Deddy Candra Wijaya', 'link' => 'https://gofile.me/7hje9/A21KdDcFw'],
                                ['nama' => 'Diki Ari Julianto', 'link' => 'https://gofile.me/7hje9/ma6UyxKtC'],
                                ['nama' => 'Dimas Alvian', 'link' => 'https://gofile.me/7hje9/RklNhd3Pw'],
                                ['nama' => 'Endang R.', 'link' => 'https://gofile.me/7hje9/oPrmvMHKK'],
                                ['nama' => 'Fiki Nopan', 'link' => 'https://gofile.me/7hje9/pIMcC9Q3H'],
                                ['nama' => 'Firdaus Fitraryansyah', 'link' => 'https://gofile.me/7hje9/VGxaP0e5j'],
                                ['nama' => 'Firdaus Sauqi', 'link' => 'https://gofile.me/7hje9/caUjLcSd1'],
                                ['nama' => 'Firdha Ayu Lestari', 'link' => 'https://gofile.me/7hje9/xr7EKddcN'],
                                ['nama' => 'Frinda Ahmad Ghofur', 'link' => 'https://gofile.me/7hje9/AGwBkXtzK'],
                                ['nama' => 'Haryanto', 'link' => 'https://gofile.me/7hje9/ZCnp8Wn3M'],
                                ['nama' => 'Hendra Pratama', 'link' => 'https://gofile.me/7hje9/GarbPcOHP'],
                                ['nama' => 'Ipung Septian Raharjo', 'link' => 'https://gofile.me/7hje9/k4wgRFAGL'],
                                ['nama' => 'Jhody Dwi Bastian', 'link' => 'https://gofile.me/7hje9/tjW2wEOOA'],
                                ['nama' => 'Kharisma Nanda', 'link' => 'https://gofile.me/7hje9/5xwPdm0Nl'],
                                ['nama' => 'Lingga Pratama', 'link' => 'https://gofile.me/7hje9/LrrTlJak2'],
                                ['nama' => 'Lutfi Abdi Hafid', 'link' => 'https://gofile.me/7hje9/ACBHqAA7Z'],
                                ['nama' => 'M. Alvin', 'link' => 'https://gofile.me/7hje9/2pfPXWRPw'],
                                ['nama' => 'M. Arif', 'link' => 'https://gofile.me/7hje9/LO3DpN3lE'],
                                ['nama' => 'M. Ghofur', 'link' => 'https://gofile.me/7hje9/NV7yiRR6D'],
                                ['nama' => 'M. Igo', 'link' => 'https://gofile.me/7hje9/hlxwEd9Ej'],
                                ['nama' => 'M. Sofyan Setiyo W.', 'link' => 'https://gofile.me/7hje9/LfVEgr3tJ'],
                                ['nama' => 'Mahyudi', 'link' => 'https://gofile.me/7hje9/kbZdN90Mf'],
                                ['nama' => 'Mirza Ahmad', 'link' => 'https://gofile.me/7hje9/tAOtH9JDV'],
                                ['nama' => 'Moh. Yusuf', 'link' => 'https://gofile.me/7hje9/FXe9sJmm6'],
                                ['nama' => 'Novita Anggraini', 'link' => 'https://gofile.me/7hje9/TNgvHcJgL'],
                                ['nama' => 'Ragil', 'link' => 'https://gofile.me/7hje9/uKE8FgWPg'],
                                ['nama' => 'Restu Hidayat', 'link' => 'https://gofile.me/7hje9/ZutWtfEIl'],
                                ['nama' => 'Rico Syaifudin', 'link' => 'https://gofile.me/7hje9/u8mhCyxAt'],
                                ['nama' => 'Ridho Ardiansyah', 'link' => 'https://gofile.me/7hje9/5ByhadnfT'],
                                ['nama' => 'Rudi Nanda', 'link' => 'https://gofile.me/7hje9/kAjagsCql'],
                                ['nama' => 'Ryan Mulyadi', 'link' => 'https://gofile.me/7hje9/D6uOpmg3W'],
                                ['nama' => 'Septian Candra', 'link' => 'https://gofile.me/7hje9/QoL4ixekq'],
                                ['nama' => 'Sugeng Karyono', 'link' => 'https://gofile.me/7hje9/dOTyWZtEi'],
                                ['nama' => 'Suharto', 'link' => 'https://gofile.me/7hje9/tEJQXHURD'],
                                ['nama' => 'Viki Wahyu', 'link' => 'https://gofile.me/7hje9/Th1vm508f'],
                                ['nama' => 'Yusqi F.', 'link' => 'https://gofile.me/7hje9/WiCHB9uW4'],
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
