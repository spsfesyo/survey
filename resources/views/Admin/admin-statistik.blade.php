@extends('layouts.app')

@section('title', 'General Dashboard')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">


    <style>
        .thead-sticky th {
            background: #fff;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 2px solid #dee2e6;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .pie-chart-question {
            font-weight: 500;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
@endpush

@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Statistik</h1>
            </div>

            {{-- untuk pilihan Superior --}}

            <div class="section-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Tabel Hasil Data Respondent Merek Blesscon</h4>
                                <a href="{{ route('export.respondent', 1) }}" class="btn btn-icon icon-left btn-success">
                                    <i class="fas fa-download"></i> Excel
                                </a>
                            </div>

                            <div class="card-body text-center">
                                <div class="table-responsive" style="max-height: 500px; overflow: auto;">
                                    <table class="table table-bordered table-md" style="min-width: 1200px;">
                                        <thead class="thead-sticky bg-light">
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
                                            @foreach ($respondentsJenis1 as $respondent)
                                                <tr>
                                                    <td>{{ $loop->iteration + ($respondentsJenis1->currentPage() - 1) * $respondentsJenis1->perPage() }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($respondent->created_at)->format('d-m-Y') }}
                                                    </td>
                                                    <td>{{ $respondent->outletSurvey->nama_outlet ?? '-' }}</td>
                                                    <td>{{ $respondent->outletSurvey->sps_internal_name ?? '-' }}</td>

                                                    @foreach ($pertanyaanList as $pertanyaan)
                                                        @php
                                                            if (
                                                                $pertanyaan->master_tipe_pertanyaan_id == 5 &&
                                                                $pertanyaan->reference
                                                            ) {
                                                                switch ($pertanyaan->reference) {
                                                                    case 'provinsi_id':
                                                                        $jawaban =
                                                                            $respondent->provinsi->nama_provinsi ?? '-';
                                                                        break;

                                                                    case 'master_kabupaten_id':
                                                                        $jawaban =
                                                                            $respondent->kabupaten->nama_kabupaten ??
                                                                            '-';
                                                                        break;
                                                                    case 'jenis_pertanyaan_id':
                                                                        $jawaban =
                                                                            $respondent->JenisPertanyaan
                                                                                ->jenis_pertanyaan ?? '-';
                                                                        break;
                                                                    default:
                                                                        $jawaban =
                                                                            $respondent->{$pertanyaan->reference} ??
                                                                            '-';
                                                                }
                                                            } else {
                                                                $jawaban = $respondent->answers
                                                                    ->where('master_pertanyaan_id', $pertanyaan->id)
                                                                    ->map(function ($a) {
                                                                        $option = $a->options->options ?? null;
                                                                        if (
                                                                            strtolower($option) === 'other' ||
                                                                            strtolower($option) === 'lainnya'
                                                                        ) {
                                                                            return $a->lainnya ?? 'Other';
                                                                        }
                                                                        return $option ??
                                                                            ($a->jawaban_teks ?? $a->lainnya);
                                                                    })
                                                                    ->implode(', ');
                                                            }
                                                        @endphp
                                                        <td>{{ $jawaban }}</td>
                                                    @endforeach
                                                    <td>
                                                        @if ($respondent->foto_selfie)
                                                            <img src="{{ asset('storage/foto-respondent/' . $respondent->foto_selfie) }}"
                                                                alt="Foto Respondent"
                                                                style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <div class="d-inline-block">
                                    {{ $respondentsJenis1->appends(request()->except('tabel1'))->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


                {{-- untuk Pilihan yang Blesscon --}}

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Tabel Hasil Data Respondent Merek Superior</h4>
                                <a href="{{ route('export.respondent', 2) }}
                                "
                                    class="btn btn-icon icon-left btn-success">
                                    <i class="fas fa-download"></i> Excel
                                </a>
                            </div>

                            <div class="card-body text-center">
                                <div class="table-responsive" style="max-height: 500px; overflow: auto;">
                                    <table class="table table-bordered table-md" style="min-width: 1200px;">
                                        <thead class="thead-sticky bg-light">
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
                                            @foreach ($respondentsJenis2 as $respondent)
                                                <tr>
                                                    <td>{{ $loop->iteration + ($respondentsJenis2->currentPage() - 1) * $respondentsJenis2->perPage() }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($respondent->created_at)->format('d-m-Y') }}
                                                    </td>
                                                    <td>{{ $respondent->outletSurvey->nama_outlet ?? '-' }}</td>
                                                    <td>{{ $respondent->outletSurvey->sps_internal_name ?? '-' }}</td>
                                                    @foreach ($pertanyaanList as $pertanyaan)
                                                        @php
                                                            if (
                                                                $pertanyaan->master_tipe_pertanyaan_id == 5 &&
                                                                $pertanyaan->reference
                                                            ) {
                                                                switch ($pertanyaan->reference) {
                                                                    case 'provinsi_id':
                                                                        $jawaban =
                                                                            $respondent->provinsi->nama_provinsi ?? '-';
                                                                        break;
                                                                    case 'master_kabupaten_id':
                                                                        $jawaban =
                                                                            $respondent->kabupaten->nama_kabupaten ??
                                                                            '-';
                                                                        break;
                                                                    case 'jenis_pertanyaan_id':
                                                                        $jawaban =
                                                                            $respondent->JenisPertanyaan
                                                                                ->jenis_pertanyaan ?? '-';
                                                                        break;
                                                                    default:
                                                                        $jawaban =
                                                                            $respondent->{$pertanyaan->reference} ??
                                                                            '-';
                                                                }
                                                            } else {
                                                                $jawaban = $respondent->answers
                                                                    ->where('master_pertanyaan_id', $pertanyaan->id)
                                                                    ->map(function ($a) {
                                                                        $option = $a->options->options ?? null;
                                                                        if (
                                                                            strtolower($option) === 'other' ||
                                                                            strtolower($option) === 'lainnya'
                                                                        ) {
                                                                            return $a->lainnya ?? 'Other';
                                                                        }
                                                                        return $option ??
                                                                            ($a->jawaban_teks ?? $a->lainnya);
                                                                    })
                                                                    ->implode(', ');
                                                            }
                                                        @endphp
                                                        <td>{{ $jawaban }}</td>
                                                    @endforeach

                                                    <td>
                                                        @if ($respondent->foto_selfie)
                                                            <img src="{{ asset('storage/foto-respondent/' . $respondent->foto_selfie) }}"
                                                                alt="Foto Respondent" width="100"
                                                                style="border-radius: 8px;">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <div class="d-inline-block">
                                    {{ $respondentsJenis2->appends(request()->except('tabel2'))->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Diagram Lingkaran --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Diagram Lingkaran Data Respondent</h4>
                                <button type="button" class="btn btn-info" onclick="downloadAllCharts('pie')">
                                    <i class="fas fa-download"></i> Download Semua Diagram Lingkaran (.zip)
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($chartPaginated as $chart)
                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="mb-0">{{ $chart['label'] }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    <x-charts.pie :chartId="$chart['chartId']" :labels="$chart['labels']" :values="$chart['values']"
                                                        label="{{ $chart['label'] }}" />
                                                    @if (count($chart['labels']) === 1 && $chart['labels'][0] === 'Tidak ada data')
                                                        <p class="text-muted mt-2">Belum ada data jawaban untuk pertanyaan
                                                            ini.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>


                                <div class="d-flex justify-content-center">
                                    {{ $chartPaginated->appends(request()->except('chart'))->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Diagram batang --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Diagram Batang Data Respondent </h4>
                                <button type="button" class="btn btn-success" onclick="downloadAllCharts('bar')">
                                    <i class="fas fa-download"></i> Download Semua Diagram Batang (.zip)
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($barChartPaginated as $barChart)
                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="mb-0">{{ $barChart['label'] }}</h6>
                                                </div>
                                                <script>
                                                    console.log('Data Barchart untuk {{ $barChart['label'] }}:',
                                                        JSON.parse('@json($barChart)'));
                                                </script>
                                                <div class="card-body">
                                                    @if ($barChart['isEmpty'])
                                                        <p class="text-muted">Tidak ada data untuk ditampilkan</p>
                                                    @else
                                                        <x-charts.bar :chartId="'bar-chart-' . $barChart['questionId']" :labels="$barChart['labels']" :values="$barChart['values']"
                                                            :labelName="$barChart['label']" />
                                                    @endif
                                                    <div class="mt-2 text-center small">
                                                        Total Responden: {{ $barChart['totalResponses'] ?? 0 }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Pagination untuk Bar Chart --}}
                                <div class="d-flex justify-content-center">
                                    {{ $barChartPaginated->appends(request()->except('barchart'))->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <div id="chartDownloadArea" style="position: absolute; left: -9999px; top: 0;">
        @foreach ($chartDataAll as $chart)
            <div data-title="{{ $chart['label'] }}">
                <x-charts.pie :chartId="'hidden_pie_' . $chart['chartId']" :labels="$chart['labels']" :values="$chart['values']" label="{{ $chart['label'] }}" />
            </div>
        @endforeach

        @foreach ($barChartDataAll as $barChart)
            <div data-title="{{ $barChart['label'] }}">
                <x-charts.bar :chartId="'hidden_bar_' . $barChart['questionId']" :labels="$barChart['labels']" :values="$barChart['values']" :labelName="$barChart['label']" />
            </div>
        @endforeach
    </div>




@endsection

@push('scripts')
    <!-- ZIP Download Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>




    <script>
        function downloadAllCharts(type) {
            const zip = new JSZip();
            const canvases = document.querySelectorAll(`#chartDownloadArea canvas[id^="hidden_${type}_"]`);

            if (canvases.length === 0) {
                alert('Tidak ada chart untuk di-download!');
                return;
            }

            let processed = 0;

            canvases.forEach((canvas, index) => {
                const chartWrapper = canvas.closest('div[data-title]');
                const title = chartWrapper ? chartWrapper.getAttribute('data-title') : `Pertanyaan ${index + 1}`;

                // Buat canvas gabungan
                const combinedCanvas = document.createElement('canvas');
                const originalWidth = canvas.width;
                const originalHeight = canvas.height;
                const titleHeight = 60;

                combinedCanvas.width = originalWidth;
                combinedCanvas.height = originalHeight + titleHeight;

                const ctx = combinedCanvas.getContext('2d');

                // Background putih
                ctx.fillStyle = '#fff';
                ctx.fillRect(0, 0, combinedCanvas.width, combinedCanvas.height);

                // Tulis judul
                ctx.fillStyle = '#000';
                ctx.font = 'bold 20px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'top';

                // Bagi teks jadi 2 baris kalau terlalu panjang (optional, atau Anda mau saya bantu ini?)
                wrapText(ctx, title, originalWidth / 2, 10, originalWidth - 40, 24);

                // Gambar chart di bawah judul
                ctx.drawImage(canvas, 0, titleHeight);

                setTimeout(() => {
                    combinedCanvas.toBlob(blob => {
                        if (blob) {
                            zip.file(`chart_${type}_${index + 1}.jpg`, blob);
                        } else {
                            console.warn(`Chart ${index + 1} gagal dikonversi`);
                        }

                        processed++;
                        if (processed === canvases.length) {
                            zip.generateAsync({
                                type: "blob"
                            }).then(content => {
                                saveAs(content, `${type}_charts_with_titles.zip`);
                            });
                        }
                    }, 'image/jpeg', 0.95);
                }, 300);
            });
        }

        // Fungsi bantu untuk membungkus teks jika terlalu panjang
        function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
            const words = text.split(' ');
            let line = '';

            for (let n = 0; n < words.length; n++) {
                const testLine = line + words[n] + ' ';
                const metrics = ctx.measureText(testLine);
                const testWidth = metrics.width;

                if (testWidth > maxWidth && n > 0) {
                    ctx.fillText(line, x, y);
                    line = words[n] + ' ';
                    y += lineHeight;
                } else {
                    line = testLine;
                }
            }
            ctx.fillText(line, x, y);
        }
    </script>


    <!-- Template JS File -->
    <script src="{{ asset('js/scripts.js') }}"></script> {{-- penting! --}}
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush







{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script> --}}

{{-- <script>
        function downloadAllCharts(type) {
            const zip = new JSZip();
            const canvases = document.querySelectorAll(`#chartDownloadArea canvas[id^="hidden_${type}_"]`);

            if (canvases.length === 0) {
                alert('Tidak ada chart untuk di-download!');
                return;
            }

            let processed = 0;

            canvases.forEach((canvas, index) => {
                // Tunggu render (jika perlu delay kecil)
                setTimeout(() => {
                    canvas.toBlob(blob => {
                        if (blob) {
                            zip.file(`chart_${type}_${index + 1}.jpg`, blob);
                        } else {
                            console.warn(`Chart ${index + 1} gagal dikonversi`);
                        }

                        processed++;
                        if (processed === canvases.length) {
                            zip.generateAsync({
                                type: "blob"
                            }).then(content => {
                                saveAs(content, `${type}_charts.zip`);
                            });
                        }
                    }, 'image/jpeg', 0.95);
                }, 300); // memberi waktu render jika perlu
            });
        }
    </script> --}}





<!-- Custom Diagram Page Script -->
{{-- <script src="{{ asset('js/diagram-page.js') }}"></script> --}}
{{-- <script>
        async function downloadAllChartsAsZip() {
            const zip = new JSZip();
            const canvases = document.querySelectorAll('canvas[id^="pieChart-"], canvas[id^="pieChart"]');

            let counter = 1;
            for (let canvas of canvases) {
                const base64 = canvas.toDataURL('image/png');
                const blob = await (await fetch(base64)).blob();

                let label = canvas.closest('.card').querySelector('h6')?.innerText.trim() || `diagram_${counter}`;
                zip.file(`${label}.png`, blob);
                counter++;
            }

            zip.generateAsync({
                    type: "blob"
                })
                .then((content) => saveAs(content, "semua-diagram.zip"))
                .catch((err) => console.error('Error membuat zip:', err));
        }
    </script> --}}

{{-- <script>
        // Contoh ambil semua canvas chart dan kirim ke backend
        let charts = [];
        document.querySelectorAll('canvas').forEach((canvas) => {
            charts.push({
                dataUrl: canvas.toDataURL('image/jpeg')
            });
        });

        fetch('/download-pie-charts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    charts: charts
                })
            }).then(response => response.blob())
            .then(blob => {
                let url = window.URL.createObjectURL(blob);
                let a = document.createElement('a');
                a.href = url;
                a.download = "pie_charts.zip";
                document.body.appendChild(a);
                a.click();
                a.remove();
            }).catch(err => console.error('Download error:', err));
    </script> --}}

{{-- <script>
        function downloadAllCharts(type) {
            // Tampilkan loading indicator
            const btn = event.target;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Membuat ZIP...';
            btn.disabled = true;

            // Lakukan request download
            fetch(`/download-charts/${type}`)
                .then(response => {
                    if (response.ok) return response.blob();
                    throw new Error('Network response was not ok');
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `all_${type}_charts.zip`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mendownload: ' + error.message);
                })
                .finally(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                });
        }
    </script> --}}
