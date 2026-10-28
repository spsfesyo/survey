@php
    // 5 kolom statis di depan (No, Nama Outlet SAP, Console, Total M3, Area) + 1 kolom foto
    $totalKolom = 6 + $pertanyaanList->count();
@endphp

<table>
    {{-- HEADER ATAS --}}
    <tr>
        <td colspan="{{ $totalKolom }}" style="background-color: #f2f2f2;">
            <strong>OUTLET DENGAN PEMBELIAN AAC &ge; {{ $minQty }} M3 YANG SUDAH MENGISI SURVEI</strong>
        </td>
    </tr>
    <tr>
        <td colspan="{{ $totalKolom }}">
            Periode: {{ $periode }} | Jumlah outlet: {{ count($baris) }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ $totalKolom }}"></td>
    </tr>

    {{-- HEADER KOLOM --}}
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Outlet</th>
            <th>Nama Outlet Console</th>
            <th>Total M3</th>
            {{-- <th>Area</th> --}}
            <th>Periode</th>

            {{-- Kolom pertanyaan dinamis (sudah termasuk Nama Customer, Nama Toko,
                 Provinsi, Kabupaten, Alamat, Telp/WA, Merek yang dibeli, dst.) --}}
            @foreach ($pertanyaanList as $pertanyaan)
                <th>{{ $pertanyaan->pertanyaan }}</th>
            @endforeach

            <th>Foto Respondent</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($baris as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row['nama_outlet'] }}</td>
                <td>{{ $row['nama_outlet_console'] }}</td>
                <td>{{ $row['total_qty'] }}</td>
                <td>{{ $periode}}</td>


                {{-- Jawaban per pertanyaan (sudah diproses di controller) --}}
                @foreach ($pertanyaanList as $pertanyaan)
                    <td>{{ $row['jawaban'][$pertanyaan->id] ?? '-' }}</td>
                @endforeach

                <td>{{ $row['foto_url'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $totalKolom }}" style="text-align: center">
                    Tidak ada outlet yang memenuhi syarat untuk periode ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
