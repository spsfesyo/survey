<?php

namespace App\Http\Controllers;

use Exception;
use ZipArchive;
use Nette\Utils\Image;
use Illuminate\Http\Request;
use App\Models\MasterPertanyaan;
use App\Models\MasterRespondent;
use App\Exports\RespondentExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminStatistikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // untuk membersihkan string agar label chart tidak mengandung karakter \r dan \n
    private function cleanLabel($text)
    {
        // Hapus karakter \r dan \n
        $text = str_replace(["\r", "\n"], ' ', $text);
        // Ganti multiple whitespace dengan single space
        $text = preg_replace('/\s+/', ' ', $text);
        // Trim spasi di awal dan akhir
        return trim($text);
    }
    public function showStatistik()
    {

        // Pagination untuk tabel responden - menggunakan pageName 'tabel'
        $respondentsPaginated = MasterRespondent::with([
            'provinsi',
            'kota',
            'JenisPertanyaan',
            'answers.options'
        ])->paginate(5, ['*'], 'tabel');


        // fungsi untuk memisahkan blesscon dan superior

        // Ambil respondent dengan jenis_pertanyaan_id = 1 (misal Blesscon)
        $respondentsJenis1 = MasterRespondent::with([
            'provinsi',
            'kota',
            'JenisPertanyaan',
            'answers.options'
        ])
            ->where('jenis_pertanyaan_id', 1)
            ->orderBy('created_at', 'desc') // ⬅️ tambahkan ini
            ->paginate(5, ['*'], 'tabel1');

        // Ambil respondent dengan jenis_pertanyaan_id = 2 (misal Superior)
        $respondentsJenis2 = MasterRespondent::with([
            'provinsi',
            'kota',
            'JenisPertanyaan',
            'answers.options'
        ])
            ->where('jenis_pertanyaan_id', 2)
            ->orderBy('created_at', 'desc') // ⬅️ tambahkan ini
            ->paginate(5, ['*'], 'tabel2');

        // Ambil semua data untuk chart (tidak perlu pagination di sini)
        $respondentsAll = MasterRespondent::with([
            'provinsi',
            'kota',
            'JenisPertanyaan',
            'answers.options'
        ])->get();

        $pertanyaanList = MasterPertanyaan::orderBy('order')->get();

        $chartData = [];

        foreach ($pertanyaanList as $pertanyaan) {
            $answers = collect();

            try {
                if ($pertanyaan->master_tipe_pertanyaan_id == 5 && $pertanyaan->reference) {
                    // Untuk pertanyaan tipe 5 - data dari master responden
                    $answers = $respondentsAll->map(function ($responden) use ($pertanyaan) {
                        $reference = $pertanyaan->reference;
                        $value = null;

                        try {
                            switch ($reference) {
                                case 'provinsi_id':
                                    $value = $responden->provinsi->nama_provinsi ?? null;
                                    break;
                                case 'kota_id':
                                    $value = $responden->kota->kota ?? null;
                                    break;
                                case 'jenis_pertanyaan_id':
                                    $value = $responden->JenisPertanyaan->jenis_pertanyaan ?? null;
                                    break;
                                default:
                                    if (isset($responden->{$reference})) {
                                        $value = $responden->{$reference};
                                    }
                            }
                        } catch (Exception $e) {
                            Log::error("Error getting reference data for question {$pertanyaan->id}: " . $e->getMessage());
                            return null;
                        }

                        return $value;
                    })->filter(function ($value) {
                        return $value !== null && $value !== '' && $value !== 0;
                    });
                } else {
                    // Untuk pertanyaan non-tipe 5 - data dari answer_master
                    $answers = $respondentsAll->flatMap(function ($responden) use ($pertanyaan) {
                        return $responden->answers
                            ->where('master_pertanyaan_id', $pertanyaan->id)
                            ->map(function ($answer) {
                                $result = null;

                                if ($answer->options && !empty($answer->options->options)) {
                                    $option = trim($answer->options->options);
                                    $optionLower = strtolower($option);

                                    if (in_array($optionLower, ['other', 'lainnya'])) {
                                        $result = !empty(trim($answer->lainnya)) ? trim($answer->lainnya) : 'Lainnya';
                                    } else {
                                        $result = $option;
                                    }
                                } elseif (!empty(trim($answer->jawaban_teks))) {
                                    $result = trim($answer->jawaban_teks);
                                } elseif (!empty(trim($answer->lainnya))) {
                                    $result = trim($answer->lainnya);
                                }

                                return $result;
                            })
                            ->filter(function ($value) {
                                return $value !== null && $value !== '' && $value !== 'null';
                            });
                    });
                }

                $grouped = $answers->countBy();

                if ($grouped->isEmpty() || $grouped->sum() == 0) {
                    $chartData[] = [
                        'chartId' => 'chart_' . $pertanyaan->id,
                        'label' => $pertanyaan->pertanyaan,
                        'labels' => ['Tidak ada data'],
                        'values' => [0],
                        'hasData' => false,
                        'isEmpty' => true
                    ];
                } else {
                    $sortedGrouped = $grouped->sortDesc();

                    $chartData[] = [
                        'chartId' => 'chart_' . $pertanyaan->id,
                        'label' => $pertanyaan->pertanyaan,
                        'labels' => $sortedGrouped->keys()->toArray(),
                        'values' => $sortedGrouped->values()->toArray(),
                        'hasData' => true,
                        'isEmpty' => false,
                        'totalResponses' => $sortedGrouped->sum()
                    ];
                }
            } catch (Exception $e) {
                Log::error("Error processing question {$pertanyaan->id}: " . $e->getMessage());

                $chartData[] = [
                    'chartId' => 'chart_' . $pertanyaan->id,
                    'label' => $pertanyaan->pertanyaan,
                    'labels' => ['Error memuat data'],
                    'values' => [0],
                    'hasData' => false,
                    'isEmpty' => true,
                    'error' => true
                ];
            }
        }

        // Pagination untuk PIE chart - menggunakan pageName 'chart'
        $perPage = 4;
        $currentChartPage = request()->get('chart', 1);
        $chartDataCollection = collect($chartData);

        $paginatedChart = new LengthAwarePaginator(
            $chartDataCollection->forPage($currentChartPage, $perPage),
            $chartDataCollection->count(),
            $perPage,
            $currentChartPage,
            [
                'path' => request()->url(),
                'pageName' => 'chart',
            ]
        );

        // Debug log untuk pie chart
        foreach ($chartData as $index => $chart) {
            Log::info("PieChart {$index}: {$chart['label']} - Has Data: " . ($chart['hasData'] ? 'Yes' : 'No') . " - Total: " . array_sum($chart['values']));
        }

        // ===== BAR CHART DATA PROCESSING =====
        // Membuat data terpisah untuk bar chart dengan struktur yang sama
        $barChartData = [];

        foreach ($pertanyaanList as $pertanyaan) {
            $answers = collect();

            try {
                if ($pertanyaan->master_tipe_pertanyaan_id == 5 && $pertanyaan->reference) {
                    // Untuk pertanyaan tipe 5 - data dari master responden
                    $answers = $respondentsAll->map(function ($responden) use ($pertanyaan) {
                        $reference = $pertanyaan->reference;
                        $value = null;

                        try {
                            switch ($reference) {
                                case 'provinsi_id':
                                    $value = $responden->provinsi->nama_provinsi ?? null;
                                    break;
                                case 'kota_id':
                                    $value = $responden->kota->kota ?? null;
                                    break;
                                case 'jenis_pertanyaan_id':
                                    $value = $responden->JenisPertanyaan->jenis_pertanyaan ?? null;
                                    break;
                                default:
                                    if (isset($responden->{$reference})) {
                                        $value = $responden->{$reference};
                                    }
                            }
                        } catch (Exception $e) {
                            Log::error("BarChart Error getting reference data for question {$pertanyaan->id}: " . $e->getMessage());
                            return null;
                        }

                        return $value;
                    })->filter(function ($value) {
                        return $value !== null && $value !== '' && $value !== 0;
                    });
                } else {
                    // Untuk pertanyaan non-tipe 5 - data dari answer_master
                    $answers = $respondentsAll->flatMap(function ($responden) use ($pertanyaan) {
                        return $responden->answers
                            ->where('master_pertanyaan_id', $pertanyaan->id)
                            ->map(function ($answer) {
                                $result = null;

                                if ($answer->options && !empty($answer->options->options)) {
                                    $option = trim($answer->options->options);
                                    $optionLower = strtolower($option);

                                    if (in_array($optionLower, ['other', 'lainnya'])) {
                                        $result = !empty(trim($answer->lainnya)) ? trim($answer->lainnya) : 'Lainnya';
                                    } else {
                                        $result = $option;
                                    }
                                } elseif (!empty(trim($answer->jawaban_teks))) {
                                    $result = trim($answer->jawaban_teks);
                                } elseif (!empty(trim($answer->lainnya))) {
                                    $result = trim($answer->lainnya);
                                }

                                return $result;
                            })
                            ->filter(function ($value) {
                                return $value !== null && $value !== '' && $value !== 'null';
                            });
                    });
                }

                $grouped = $answers->countBy();

                if ($grouped->isEmpty() || $grouped->sum() == 0) {
                    $barChartData[] = [
                        'barChartId' => 'barchart_' . $pertanyaan->id,
                        'label' => trim(preg_replace('/\s+/', ' ', $pertanyaan->pertanyaan)),
                        'labels' => ['Tidak ada data'],
                        'values' => [0],
                        'hasData' => false,
                        'isEmpty' => true,
                        'questionId' => $pertanyaan->id
                    ];
                } else {
                    $sortedGrouped = $grouped->sortDesc();

                    $barChartData[] = [
                        'barChartId' => 'barchart_' . $pertanyaan->id,
                        'label' => trim(preg_replace('/\s+/', ' ', $pertanyaan->pertanyaan)),
                        'labels' => $sortedGrouped->keys()->toArray(),
                        'values' => $sortedGrouped->values()->toArray(),
                        'hasData' => true,
                        'isEmpty' => false,
                        'totalResponses' => $sortedGrouped->sum(),
                        'questionId' => $pertanyaan->id
                    ];
                }
            } catch (Exception $e) {
                Log::error("BarChart Error processing question {$pertanyaan->id}: " . $e->getMessage());

                $barChartData[] = [
                    'barChartId' => 'barchart_' . $pertanyaan->id,
                    'label' => trim(preg_replace('/\s+/', ' ', $pertanyaan->pertanyaan)),
                    'labels' => ['Error memuat data'],
                    'values' => [0],
                    'hasData' => false,
                    'isEmpty' => true,
                    'error' => true,
                    'questionId' => $pertanyaan->id
                ];
            }
        }


        // Pagination untuk BAR chart - menggunakan pageName 'barchart'
        $perPageBarChart = 4;
        $currentBarChartPage = request()->get('barchart', 1);
        $barChartDataCollection = collect($barChartData);

        $paginatedBarChart = new LengthAwarePaginator(
            $barChartDataCollection->forPage($currentBarChartPage, $perPageBarChart),
            $barChartDataCollection->count(),
            $perPageBarChart,
            $currentBarChartPage,
            [
                'path' => request()->url(),
                'pageName' => 'barchart',
            ]
        );

        // Debug log untuk bar chart
        foreach ($barChartData as $index => $barChart) {
            Log::info("BarChart {$index}: Q{$barChart['questionId']} - {$barChart['label']} - Has Data: " . ($barChart['hasData'] ? 'Yes' : 'No') . " - Total: " . array_sum($barChart['values']) . " - Labels: " . implode(', ', $barChart['labels']));
        }

        // dd([
        //     'respondents' => $respondentsPaginated,
        //     'pertanyaanList' => $pertanyaanList,
        //     'chartPaginated' => $paginatedChart,
        //     'barChartPaginated' => $paginatedBarChart
        // ]);

        return view('Admin.admin-statistik', [
            'respondents' => $respondentsPaginated,
            'pertanyaanList' => $pertanyaanList,
            'chartPaginated' => $paginatedChart,
            'barChartPaginated' => $paginatedBarChart,
            'chartDataAll' => $chartData,         // ALL pie chart data
            'barChartDataAll' => $barChartData,   // ALL bar chart data
            'respondentsJenis1' => $respondentsJenis1,
            'respondentsJenis2' => $respondentsJenis2
        ]);
    }



    // public function showTableKuisioner()
    // {

    //     $pertanyaanListKuisioner = MasterPertanyaan::orderBy('order')->get();

    //     // Ambil respondent dengan jenis_pertanyaan_id = 1 (misal Blesscon)
    //     $respondentsJenis1 = MasterRespondent::with([
    //         'provinsi',
    //         'kota',
    //         'JenisPertanyaan',
    //         'answers.options'
    //     ])
    //         ->where('jenis_pertanyaan_id', 1)
    //         ->paginate(5, ['*'], 'tabel1');

    //     // Ambil respondent dengan jenis_pertanyaan_id = 2 (misal Superior)
    //     $respondentsJenis2 = MasterRespondent::with([
    //         'provinsi',
    //         'kota',
    //         'JenisPertanyaan',
    //         'answers.options'
    //     ])
    //         ->where('jenis_pertanyaan_id', 2)
    //         ->paginate(5, ['*'], 'tabel2');

    //     return view('Admin.admin-statistik', compact(
    //         'pertanyaanListKuisioner',
    //         'respondentsJenis1',
    //         'respondentsJenis2'
    //     ));
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function exportExcel($jenisId)
    {
        $namaFile = $jenisId == 1 ? 'data-respondent-blesscon.xlsx' : 'data-respondent-superior.xlsx';
        return Excel::download(new RespondentExport($jenisId), $namaFile);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function downloadPieCharts(Request $request)
    // {
    //     $charts = $request->input('charts', []);
    //     if (empty($charts)) {
    //         return response()->json(['error' => 'No chart data provided'], 400);
    //     }

    //     $publicPath = storage_path('app/public');
    //     if (!is_dir($publicPath)) {
    //         mkdir($publicPath, 0755, true);
    //     }

    //     $tempFiles = [];
    //     foreach ($charts as $index => $chart) {
    //         if (empty($chart['dataUrl'])) {
    //             Log::error("Chart $index dataUrl is empty");
    //             continue;
    //         }

    //         $filename = 'chart_' . $index . '_' . uniqid() . '.jpg';
    //         $path = $publicPath . '/' . $filename;

    //         $data = $chart['dataUrl'];
    //         if (strpos($data, 'base64,') !== false) {
    //             list(, $data) = explode('base64,', $data);
    //         }

    //         $binaryData = base64_decode($data);
    //         if ($binaryData === false) {
    //             Log::error("Chart $index failed base64 decode");
    //             continue;
    //         }

    //         file_put_contents($path, $binaryData);
    //         if (filesize($path) === 0) {
    //             Log::error("Chart $index file size 0: $path");
    //             continue;
    //         }

    //         $tempFiles[] = $path;
    //     }

    //     if (empty($tempFiles)) {
    //         return response()->json(['error' => 'No valid chart images created'], 500);
    //     }

    //     $zipName = 'pie_charts_' . now()->format('Ymd_His') . '.zip';
    //     $zipPath = $publicPath . '/' . $zipName;

    //     $zip = new ZipArchive;
    //     if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
    //         return response()->json(['error' => 'Failed to create zip file'], 500);
    //     }

    //     foreach ($tempFiles as $file) {
    //         $zip->addFile($file, basename($file));
    //     }

    //     $zip->close();

    //     foreach ($tempFiles as $file) {
    //         unlink($file);
    //     }

    //     return response()->download($zipPath)->deleteFileAfterSend(true);
    // }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
