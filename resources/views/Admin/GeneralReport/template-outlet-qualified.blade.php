<table>
    <tr>
        <td colspan="11" style="background-color: #f2f2f2;">
            <strong>OUTLET DENGAN PEMBELIAN AAC &ge; {{ $minQty }} M3 YANG SUDAH MENGISI SURVEI</strong>
        </td>
    </tr>
    <tr>
        <td colspan="11">Periode: {{ $periode }} | Jumlah outlet: {{ count($baris) }}</td>
    </tr>
    <tr>
        <td colspan="11"></td>
    </tr>

    <tr>
        <th>No</th>
        <th>Nama Outlet</th>
        <th>Nama Outlet Console</th>
        <th>Nomor Telp</th>
        <th>Total M3</th>
        {{-- <th>Jumlah Respondent</th> --}}
        <th>Periode</th>
        <th>Provinsi</th>
        <th>Kabupaten</th>
        <th>Area</th>
    </tr>

    @forelse ($baris as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $row['nama_outlet'] }}</td>
            <td>{{ $row['nama_outlet_console'] }}</td>
            <td>{{ $row['telepone_outlet'] }}</td>
            <td>{{ $row['total_qty'] }}</td>
            {{-- <td>{{ $row['jumlah_respondent'] }}</td> --}}
            <td>{{ $periode }}</td>
            <td>{{ $row['provinsi'] }}</td>
            <td>{{ $row['kabupaten'] }}</td>
            <td>{{ $row['area'] }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="11" style="text-align: center">Tidak ada outlet yang memenuhi syarat untuk periode ini.</td>
        </tr>
    @endforelse
</table>
