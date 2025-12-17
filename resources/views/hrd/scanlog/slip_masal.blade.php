<head>
    <title>Slip Gaji {{$slips->first()->nama}}</title>
    <style>
        .table-container{
            width: 33%;
            height: 33%;
            position: relative;
        }
        .table-watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 180px;
    height: auto;
    transform: translate(-50%, -50%);
    opacity: 0.08;
    z-index: 0;
  }

        table,th,td{
            
            font-size: 90%;
        }
        tr:nth-child(even) {
            background-color: #e9e9e9;
        }
      #jam {
            width: 50px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="table-container">

        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/logo/bofi.png'))) }}"  class="table-watermark">
        <br>
        @php 
    $slip=$slips->first() 
    @endphp
   <h5>
       {{$slip->harian->nama}}<br>
       {{$slip->harian->bagian}}<br>
       {{$start}} s/d {{$end}}
    </h5>
    <table>
        <tr>
            <th>TGL</th>
            <th id="jam">JAM</th>
            <th>UPAH</th>
        </tr>
        @foreach($slips as $slip)
        <tr>
            <td>
                {{ \Carbon\Carbon::parse($slip->tgl)->isoFormat('ddd, D/MM/YY') }}
            </td>
            <td>
                {{$slip->dk}}
            </td>
            <td>
                {{$slip->tgaji}}
            </td>
            @endforeach
        </tr>
    </table>
    <strong>Total Gaji: Rp. {{number_format($totalGaji,0,',','.')}}</strong>
</div>

</body>