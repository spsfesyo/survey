    {{-- <table>


        <tr>
            <td colspan="2" style="background-color: #f2f2f2;">
                <strong>TOTAL KORESPONDEN/RESPONDENT: {{ $totalRespondent }}</strong>
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>

        @foreach ($answers->groupBy('pertanyaan') as $pertanyaan => $items)
            <!-- Judul Pertanyaan -->
            <tr>
                <td><strong>{{ $pertanyaan }}</strong></td>
                <td><strong>Jumlah Koresponden</strong></td>
            </tr>

            <tr>
                <td colspan="2"></td>
            </tr>

            <!-- Blesscon -->
            <tr>
                <td colspan="2"><strong>BLESSCON ({{ $totalBlesscon }})</strong></td>
            </tr>

            @foreach ($items->where('jenis_pertanyaan_id', 1) as $row)
                <tr>
                    <td style="text-align: center">{{ $row->option_text }}</td>
                    <td style="text-align: center">{{ $row->total }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="2"></td>
            </tr>

            <!-- Superior -->
            <tr>
                <td colspan="2"><strong>SUPERIOR ({{ $totalSuperior }})</strong></td>
            </tr>

            @foreach ($items->where('jenis_pertanyaan_id', 2) as $row)
                <tr>
                    <td>{{ $row->option_text }}</td>
                    <td>{{ $row->total }}</td>
                </tr>
            @endforeach

            <!-- Spasi antar pertanyaan -->
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
        @endforeach
    </table> --}}


    <table>
        <tr>
            <td colspan="2" style="background-color: #f2f2f2;">
                <strong>TOTAL KORESPONDEN/RESPONDENT: {{ $totalRespondent }}
                    @if (!empty($labelQty))
                        <span style="color: #c00;">({{ $labelQty }})</span>
                    @endif
                </strong>
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>

        @foreach ($questions as $pertanyaan => $kategoriData)
            <!-- Judul Pertanyaan -->
            <tr>
                <td><strong>{{ $pertanyaan }}</strong></td>
            </tr>
            <tr>
                <td colspan="{{ count($provinces) + 2 }}"></td>
            </tr>

            <!-- ================= BLESSCON ================= -->
            <tr>
                <td colspan="{{ count($provinces) + 2 }}"><strong>BLESSCON ({{ $totalBlesscon }})</strong></td>
            </tr>
            <tr>
                <td><strong>Jawaban</strong></td>
                @foreach ($provinces as $prov)
                    <td style="text-align: center"><strong>{{ $prov }}</strong></td>
                @endforeach
                <td style="text-align: center"><strong>Jumlah</strong></td>
            </tr>
            @foreach ($kategoriData['blesscon'] ?? [] as $opsi => $perProvinsi)
                <tr>
                    <td>{{ $opsi }}</td>
                    @foreach ($provinces as $prov)
                        <td style="text-align: center">{{ $perProvinsi[$prov] ?? 0 }}</td>
                    @endforeach
                    <td style="text-align: center"><strong>{{ $perProvinsi['total'] ?? 0 }}</strong></td>
                </tr>
            @endforeach

            <tr>
                <td colspan="{{ count($provinces) + 2 }}"></td>
            </tr>

            <!-- ================= SUPERIOR ================= -->
            <tr>
                <td colspan="{{ count($provinces) + 2 }}"><strong>SUPERIOR ({{ $totalSuperior }})</strong></td>
            </tr>
            <tr>
                <td><strong>Jawaban</strong></td>
                @foreach ($provinces as $prov)
                    <td style="text-align: center"><strong>{{ $prov }}</strong></td>
                @endforeach
                <td style="text-align: center"><strong>Jumlah</strong></td>
            </tr>
            @foreach ($kategoriData['superior'] ?? [] as $opsi => $perProvinsi)
                <tr>
                    <td>{{ $opsi }}</td>
                    @foreach ($provinces as $prov)
                        <td style="text-align: center">{{ $perProvinsi[$prov] ?? 0 }}</td>
                    @endforeach
                    <td style="text-align: center"><strong>{{ $perProvinsi['total'] ?? 0 }}</strong></td>
                </tr>
            @endforeach

            <!-- Spasi antar pertanyaan -->
            <tr>
                <td colspan="{{ count($provinces) + 2 }}"></td>
            </tr>
            <tr>
                <td colspan="{{ count($provinces) + 2 }}"></td>
            </tr>
        @endforeach
    </table>
