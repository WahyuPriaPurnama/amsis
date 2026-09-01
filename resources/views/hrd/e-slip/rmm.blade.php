<div>
    <div class="container">
        @component('components.card')
        @slot('header')
        E-Slip RMM
        @endslot

        <div class="table-responsive">
            <table class="table table-hover" id="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>E-Slip</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $slips = [
                    ['nama' => 'Aji', 'link' => 'https://gofile.me/7hje9/TIrPS36zX'],
                    ['nama' => 'Angga', 'link' => 'https://gofile.me/7hje9/Wuhzx9Z4o'],
                    ['nama' => 'Bambang', 'link' => 'https://gofile.me/7hje9/r6DOKY5Cd'],
                    ['nama' => 'Bayu Roberto Karlos', 'link' => 'https://gofile.me/7hje9/xllyTK00r'],
                    ['nama' => 'Danu', 'link' => 'https://gofile.me/7hje9/KNZXh4udn'],
                    ['nama' => 'Dila', 'link' => 'https://gofile.me/7hje9/t8mNGF1OI'],
                    ['nama' => 'Heru', 'link' => 'https://gofile.me/7hje9/KLaBKGiAN'],
                    ['nama' => 'Ifa', 'link' => 'https://gofile.me/7hje9/sO7we4v6I'],
                    ['nama' => "Imam Syafi'i", 'link' => 'https://gofile.me/7hje9/abIMto1fD'],
                    ['nama' => 'Rama', 'link' => 'https://gofile.me/7hje9/QdqJJRMFk'],
                    ['nama' => 'Reza', 'link' => 'https://gofile.me/7hje9/Alm9b0d1a'],
                    ['nama' => 'Yudi', 'link' => 'https://gofile.me/7hje9/RodM6aIRh'],
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
</div>