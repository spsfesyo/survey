{{-- <table>
    <thead>
        <tr>
            <td>No</td>
            <th>Tanggal</th>


            @foreach ($pertanyaanList as $pertanyaan)
                <th>{{ $pertanyaan->pertanyaan }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($respondents as $respondent)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($respondent->created_at)->format('d-m-Y') }}</td>


                @foreach ($pertanyaanList as $pertanyaan)
                @php
                    // Cek apakah tipe pertanyaan adalah 5 (mengambil dari kolom lain di respondent)
                    if (
                        $pertanyaan->master_tipe_pertanyaan_id == 5 &&
                        $pertanyaan->reference
                    ) {
                        switch ($pertanyaan->reference) {
                            case 'provinsi_id':
                                $jawaban =
                                    $respondent->provinsi->nama_provinsi ?? '-';
                                break;
                            case 'kota_id':
                                $jawaban = $respondent->kota->kota ?? '-';
                                break;
                            case 'jenis_pertanyaan_id':
                                $jawaban =
                                    $respondent->JenisPertanyaan
                                        ->jenis_pertanyaan ?? '-';
                                break;
                            default:
                                $jawaban =
                                    $respondent->{$pertanyaan->reference} ?? '-';
                                break;
                        }
                    } else {
                        // Ambil jawaban dari relasi answers
                        $jawaban = $respondent->answers
                            ->where('master_pertanyaan_id', $pertanyaan->id)
                            ->map(function ($a) {
                                $option = $a->options->options ?? null;

                                // Jika jawaban adalah "Other" / "Lainnya", gunakan input teks-nya
                                if (
                                    strtolower($option) === 'other' ||
                                    strtolower($option) === 'lainnya'
                                ) {
                                    return $a->lainnya ?? 'Other';
                                }

                                // Jika tidak ada relasi option, gunakan jawaban_teks atau lainnya
                                return $option ?? ($a->jawaban_teks ?? $a->lainnya);
                            })
                            ->implode(', ');
                    }
                @endphp
                <td>{{ $jawaban }}</td>
            @endforeach
            </tr>
        @endforeach
    </tbody>
</table> --}}


<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Outlet</th>
            <th>Nama Outlet Console</th>
            @foreach ($pertanyaanList as $pertanyaan)
                <th>{{ $pertanyaan->pertanyaan }}</th>
            @endforeach
            <th>Foto Respondent</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($respondents as $respondent)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($respondent->created_at)->format('d-m-Y') }}</td>
                <td>{{ $respondent->outletSurvey->nama_outlet ?? '-' }}</td>
                <td>{{ $respondent->outletSurvey->sps_internal_name ?? '-' }}</td>
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
                {{-- kosong karena sudah diatur di Drawing() --}}
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
