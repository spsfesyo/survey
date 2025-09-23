<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Survey Konsumen</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.5; }
        h2 { margin-top: 20px; border-bottom: 1px solid #000; padding-bottom: 3px; }
        ul { margin: 0; padding-left: 20px; }
        .question { margin: 10px 0; }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Form Survey Konsumen</h1>

    {{-- Bagian A: Umum --}}
    <h2>Bagian A. Umum</h2>
    @foreach($formUtama as $q)
        <div class="question">
            <strong>{{ $q->order }}. {{ $q->pertanyaan }}</strong>
            @if(in_array($q->master_tipe_pertanyaan_id, [1,2]) && $q->options->count())
                <ul>
                    @foreach($q->options as $opt)
                        <li>
                            @if($q->master_tipe_pertanyaan_id == 1)
                                [ ] {{ $opt->options }}
                            @else
                                ( ) {{ $opt->options }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p>__________________________</p>
            @endif
        </div>
    @endforeach

    {{-- Bagian B: Harga --}}
    <h2>Bagian B. Harga</h2>
    @foreach($formHarga as $q)
        <div class="question">
            <strong>{{ $q->order }}. {{ $q->pertanyaan }}</strong>
            @if(in_array($q->master_tipe_pertanyaan_id, [1,2]) && $q->options->count())
                <ul>
                    @foreach($q->options as $opt)
                        <li>
                            @if($q->master_tipe_pertanyaan_id == 1)
                                [ ] {{ $opt->options }}
                            @else
                                ( ) {{ $opt->options }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p>__________________________</p>
            @endif
        </div>
    @endforeach

    {{-- Bagian C: Kualitas Produk --}}
    <h2>Bagian C. Kualitas Produk</h2>
    @foreach($formKualitas as $q)
        <div class="question">
            <strong>{{ $q->order }}. {{ $q->pertanyaan }}</strong>
            @if(in_array($q->master_tipe_pertanyaan_id, [1,2]) && $q->options->count())
                <ul>
                    @foreach($q->options as $opt)
                        <li>
                            @if($q->master_tipe_pertanyaan_id == 1)
                                [ ] {{ $opt->options }}
                            @else
                                ( ) {{ $opt->options }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p>__________________________</p>
            @endif
        </div>
    @endforeach

    {{-- Bagian D: Pengiriman Produk --}}
    <h2>Bagian D. Pengiriman Produk</h2>
    @foreach($formPengiriman as $q)
        <div class="question">
            <strong>{{ $q->order }}. {{ $q->pertanyaan }}</strong>
            @if(in_array($q->master_tipe_pertanyaan_id, [1,2]) && $q->options->count())
                <ul>
                    @foreach($q->options as $opt)
                        <li>
                            @if($q->master_tipe_pertanyaan_id == 1)
                                [ ] {{ $opt->options }}
                            @else
                                ( ) {{ $opt->options }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p>__________________________</p>
            @endif
        </div>
    @endforeach

    {{-- Bagian E: Pelayanan / Service Produk --}}
    <h2>Bagian E. Pelayanan / Service Produk</h2>
    @foreach($formPelayanan as $q)
        <div class="question">
            <strong>{{ $q->order }}. {{ $q->pertanyaan }}</strong>
            @if(in_array($q->master_tipe_pertanyaan_id, [1,2]) && $q->options->count())
                <ul>
                    @foreach($q->options as $opt)
                        <li>
                            @if($q->master_tipe_pertanyaan_id == 1)
                                [ ] {{ $opt->options }}
                            @else
                                ( ) {{ $opt->options }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p>__________________________</p>
            @endif
        </div>
    @endforeach

    <br><br>
    <p><em>Terima kasih atas partisipasi Anda 🙏</em></p>
</body>
</html>
