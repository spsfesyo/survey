{{-- <table>
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
</table> --}}



<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Outlet</th>
            <th>Nama Outlet Console</th>
             {{-- <th>Area</th> --}}
            {{-- <th>Kode Unik</th> --}}
            <th>Periode</th>


            {{-- Kolom pertanyaan dinamis --}}
            @foreach ($pertanyaanList as $pertanyaan)
                <th>{{ $pertanyaan->pertanyaan }}</th>
            @endforeach

            <th>Foto Respondent</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $index => $respondent)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($respondent->created_at)->format('d-m-Y') }}</td>
                <td>{{ $respondent->outletSurvey->nama_outlet ?? '-' }}</td>
                <td>{{ $respondent->outletSurvey->sps_internal_name ?? '-' }}</td>
                {{-- <td>{{ $respondent->area->nama_area ?? '-' }}</td> --}}
                {{-- <td>{{ $respondent->outletSurvey->telepone_outlet ?? '-' }}</td> --}}
                {{-- <td>{{ $respondent->outletSurvey->kode_unik ?? '-' }}</td> --}}
                <td>
                    {{ $respondent->periode->tanggal_mulai ?? '-' }} -
                    {{ $respondent->periode->tanggal_selesai ?? '-' }}
                </td>
                <td>{{ $respondent->provinsi->nama_provinsi ?? '-' }}</td>
                <td>{{ $respondent->kabupaten->nama_kabupaten ?? '-' }}</td>


                {{-- Jawaban per pertanyaan --}}
                @foreach ($pertanyaanList as $pertanyaan)
                    @php
                        if ($pertanyaan->master_tipe_pertanyaan_id == 5 && $pertanyaan->reference) {
                            switch ($pertanyaan->reference) {
                                case 'provinsi_id':
                                    $jawaban = $respondent->provinsi->nama_provinsi ?? '-';
                                    break;
                                case 'master_kabupaten_id':
                                    $jawaban = $respondent->kabupaten->nama_kabupaten ?? '-';
                                    break;
                                case 'jenis_pertanyaan_id':
                                    $jawaban = $respondent->JenisPertanyaan->jenis_pertanyaan ?? '-';
                                    break;
                                default:
                                    $jawaban = $respondent->{$pertanyaan->reference} ?? '-';
                            }
                        } else {
                            $jawaban = $respondent->answers
                                ->where('master_pertanyaan_id', $pertanyaan->id)
                                ->map(function ($a) {
                                    $option = $a->options->options ?? null;
                                    if (strtolower($option) === 'other' || strtolower($option) === 'lainnya') {
                                        return $a->lainnya ?? 'Other';
                                    }
                                    return $option ?? ($a->jawaban_teks ?? $a->lainnya);
                                })
                                ->implode(', ');
                        }
                    @endphp
                    <td>{{ $jawaban }}</td>
                @endforeach

                {{-- Foto: tampilkan URL (bukan gambar) --}}
                <td>
                    @if ($respondent->foto_selfie)
                        {{ asset($respondent->foto_selfie) }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ 11 + $pertanyaanList->count() }}" style="text-align: center">
                    Tidak ada data untuk periode ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
