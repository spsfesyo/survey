<table>
    <thead>
        <tr>
            <td>No</td>
            <th>Tanggal</th>
            {{-- <th>Nama Respondent</th>
            <th>Provinsi Lokasi Toko</th>
            <th>Kota / Kabupaten Lokasi Toko</th>
            <th>Email Responden</th>
            <th>Alamat Toko</th>
            <th>Nama Toko</th>
            <th>Nomor Telepon</th>
            <th>Selama ini Anda membeli merek apa dari Produk Bata Ringan kami?</th> --}}

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
                {{-- <td>{{ $respondent->nama_respondent }}</td>
                <td>{{ $respondent->provinsi->nama_provinsi ?? '-' }}</td>
                <td>{{ $respondent->kota->kota ?? '-' }}</td>
                <td>{{ $respondent->email_respondent }}</td>
                <td>{{ $respondent->alamat_toko_respondent }}</td>
                <td>{{ $respondent->nama_toko_respondent }}</td>
                <td style="mso-number-format:'\@';"> {{ $respondent->telepone_respondent }}</td>
                <td>{{ $respondent->JenisPertanyaan->jenis_pertanyaan ?? '-' }}</td> --}}

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
</table>
