<table>
    <tr>
        <th>No</th>
        <th>Nama Outlet</th>
        <th>Nama Outlet Console</th>
        <th>Nomor Telp</th>
        <th>Kode Unik</th>
        <th>Periode</th>
        <th>Provinsi</th>
        <th>Kabupaten</th>
        <th>Area</th>
    </tr>

    @forelse ($data as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->nama_outlet }}</td>
            <td>{{ $item->nama_outlet_console }}</td>
            <td>{{ $item->telepone_outlet }}</td>
            <td>{{ $item->kode_unik }}</td>
            <td>{{ $item->periode }}</td>
            <td>{{ $item->provinsi }}</td>
            <td>{{ $item->kabupaten }}</td>
            <td>{{ $item->area }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="9" style="text-align: center">Tidak ada data untuk periode ini.</td>
        </tr>
    @endforelse
</table>
