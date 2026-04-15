    <table>
    @foreach($answers->groupBy('pertanyaan') as $pertanyaan => $items)

        <!-- Judul Pertanyaan -->
        <tr>
            <td ><strong>{{ $pertanyaan }}</strong></td>
            <td ><strong>Jumlah Koresponden</strong></td>
        </tr>

        <tr><td colspan="2"></td></tr>

        <!-- Blesscon -->
        <tr>
            <td colspan="2"><strong>BLESSCON ({{ $totalBlesscon }})</strong></td>
        </tr>

        @foreach($items->where('jenis_pertanyaan_id', 1) as $row)
            <tr>
                <td style="text-align: center">{{ $row->option_text }}</td>
                <td style="text-align: center">{{ $row->total }}</td>
            </tr>
        @endforeach

        <tr><td colspan="2"></td></tr>

        <!-- Superior -->
        <tr>
            <td colspan="2"><strong>SUPERIOR ({{ $totalSuperior }})</strong></td>
        </tr>

        @foreach($items->where('jenis_pertanyaan_id', 2) as $row)
            <tr>
                <td>{{ $row->option_text }}</td>
                <td>{{ $row->total }}</td>
            </tr>
        @endforeach

        <!-- Spasi antar pertanyaan -->
        <tr><td colspan="2"></td></tr>
        <tr><td colspan="2"></td></tr>

    @endforeach
</table>
