<?php

namespace App\Http\Controllers;

use App\Models\CheckBlast;
use Illuminate\Http\Request;

use App\Helpers\WhatsappHelper;
use App\Jobs\SendWhatsappBlastJob;
use App\Models\MasterOutletSurvey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class AdminBlastController extends Controller
{
    protected $whatsappHelper;

    public function __construct()
    {
        $this->whatsappHelper = new WhatsappHelper();
    }

    /**
     * Method untuk blast WA seperti yang sudah Anda buat
     */

    public function index()
    {

        return view('Admin.admin-blast-wa');
    }



    public function BlastingWa(Request $request)
    {
        try {
            $batchSize = 1000;
            $linkSurvey = "https://survey.superiorprimasukses.co.id/";

            $dataToBlast = MasterOutletSurvey::where('status_blast_wa', 'false')
                ->whereNotNull('telepone_outlet')
                ->whereNotNull('kode_unik')
                ->limit($batchSize)
                ->get();

            if ($dataToBlast->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data baru untuk dikirim.');
            }

            $helper = new WhatsappHelper();
            $successCount = 0;
            $failCount = 0;

            foreach ($dataToBlast as $record) {
                $wa = $record->telepone_outlet;
                $kode = $record->kode_unik;
                $surveyLink = $linkSurvey . '?code=' . $kode;

                $sendResult = $helper->sendSurveyMessageWithMedia($wa, $kode, $surveyLink);

                if ($sendResult['success']) {
                    $record->update([
                        'status_blast_wa' => 'true',
                        'blast_status' => 'sent',
                        'blast_sent_at' => now(),
                        'blast_error' => null,
                    ]);
                    $successCount++;
                } else {
                    $record->update([
                        'blast_status' => 'failed',
                        'blast_error' => $sendResult['error'] ?? 'Unknown error'
                    ]);
                    $failCount++;
                }

                // Tambahkan jeda antar pesan untuk menghindari rate limit (misal 15 detik)
                sleep(15); // ⏱️
            }

            return redirect()->back()->with('success', "Blasting selesai: {$successCount} berhasil, {$failCount} gagal.");
        } catch (\Exception $e) {
            Log::error('Blasting WA Error', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses blasting: ' . $e->getMessage());
        }
    }





    // sebelumnya
    //     public function BlastingWa(Request $request)
    // {
    //     try {
    //         // Ambil maksimal 1000 data yang belum diblast
    //         $batchSize = 1000;
    //         $linkSurvey = "https://survey.superiorprimasukses.co.id/";

    //         $dataToBlast = CheckBlast::where('status_blast_wa', 'false')
    //             ->whereNotNull('no_telp_check')
    //             ->whereNotNull('kode_unik')
    //             ->limit($batchSize)
    //             ->get();

    //         if ($dataToBlast->isEmpty()) {
    //             return redirect()->back()->with('error', 'Tidak ada data baru untuk dikirim.');
    //         }

    //         // Awal waktu delay
    //         $startDelay = now();
    //         $delayIncrement = 15; // detik

    //         $jobs = [];
    //         foreach ($dataToBlast as $index => $record) {
    //             $delay = $startDelay->copy()->addSeconds($index * $delayIncrement);
    //             $jobs[] = (new SendWhatsappBlastJob($record->id, $linkSurvey))->delay($delay);
    //         }

    //         // Kirim batch job
    //         Bus::batch($jobs)
    //             ->name('Blasting WhatsApp')
    //             ->dispatch();

    //         return redirect()->back()->with('success', "Proses blasting telah dijadwalkan untuk {$dataToBlast->count()} nomor.");
    //     } catch (\Exception $e) {
    //         Log::error('Blasting WA Error', [
    //             'error' => $e->getMessage(),
    //         ]);

    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses blasting.');
    //     }
    // }






    // public function BlastingWa(Request $request)
    // {
    //     try {
    //         $linkSurvey = "https://survey.superiorprimasukses.co.id/";
    //         $data = CheckBlast::whereNotNull('no_telp_check')
    //             ->whereNotNull('kode_unik')
    //             ->get();

    //         $successCount = 0;
    //         $failCount = 0;

    //         foreach ($data as $item) {
    //             $surveyLinkWithCode = $linkSurvey . '?code=' . $item->kode_unik;

    //             // Gunakan method dengan media untuk mengirim logo + pesan
    //             $result = $this->whatsappHelper->sendSurveyMessageWithMedia(
    //                 $item->no_telp_check,
    //                 $item->kode_unik,
    //                 $surveyLinkWithCode
    //             );

    //             if ($result['success']) {
    //                 $successCount++;
    //                 // Update status jika berhasil
    //                 $item->update([
    //                     'blast_status' => 'sent',
    //                     'blast_sent_at' => now(),
    //                     'blast_error' => null
    //                 ]);
    //             } else {
    //                 $failCount++;
    //                 // Update status jika gagal
    //                 $item->update([
    //                     'blast_status' => 'failed',
    //                     'blast_error' => $result['error'] ?? 'Unknown error'
    //                 ]);
    //             }

    //             // Delay 3 detik antar pengiriman (karena mengirim 2 pesan: logo + text)
    //             sleep(3);
    //         }

    //         Log::info('Blast WA Completed', [
    //             'total' => $data->count(),
    //             'success' => $successCount,
    //             'failed' => $failCount
    //         ]);

    //         return redirect()->back()->with('success', "Pesan blast berhasil dikirim! Berhasil: {$successCount}, Gagal: {$failCount}");
    //     } catch (\Exception $e) {
    //         Log::error('Blast WA Error', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim blast: ' . $e->getMessage());
    //     }
    // }





    // public function BlastingWa(Request $request)
    // {
    //     try {
    //         $linkSurvey = "https://survey.superiorprimasukses.co.id/";
    //         $data = CheckBlast::whereNotNull('no_telp_check')
    //                         ->whereNotNull('kode_unik')
    //                         ->get();

    //         $successCount = 0;
    //         $failCount = 0;

    //         foreach ($data as $item) {
    //             $surveyLinkWithCode = $linkSurvey . '?code=' . $item->kode_unik;

    //             $result = $this->whatsappHelper->sendSurveyMessage(
    //                 $item->no_telp_check,
    //                 $item->kode_unik,
    //                 $surveyLinkWithCode
    //             );

    //             if ($result['success']) {
    //                 $successCount++;
    //                 // Update status jika berhasil
    //                 $item->update([
    //                     'blast_status' => 'sent',
    //                     'blast_sent_at' => now(),
    //                     'blast_error' => null
    //                 ]);
    //             } else {
    //                 $failCount++;
    //                 // Update status jika gagal
    //                 $item->update([
    //                     'blast_status' => 'failed',
    //                     'blast_error' => $result['error'] ?? 'Unknown error'
    //                 ]);
    //             }

    //             // Delay 1 detik antar pengiriman
    //             sleep(1);
    //         }

    //         Log::info('Blast WA Completed', [
    //             'total' => $data->count(),
    //             'success' => $successCount,
    //             'failed' => $failCount
    //         ]);

    //         return redirect()->back()->with('success', "Pesan blast berhasil dikirim! Berhasil: {$successCount}, Gagal: {$failCount}");

    //     } catch (\Exception $e) {
    //         Log::error('Blast WA Error', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim blast: ' . $e->getMessage());
    //     }
    // }





}

// sebelumnya


  //     try {
        //     $linkSurvey = "https://survey.superiorprimasukses.co.id/";
        //     $data = MasterOutletSurvey::whereNotNull('telepone_outlet')
        //         ->whereNotNull('kode_unik')
        //         ->get();

        //     $successCount = 0;
        //     $failCount = 0;
        //     $totalData = $data->count();

        //     // Estimasi waktu total
        //     $estimatedMinutes = ($totalData * 15) / 60;
        //     Log::info('Starting WhatsApp Blast', [
        //         'total_recipients' => $totalData,
        //         'delay_per_message' => '15 seconds',
        //         'estimated_duration' => round($estimatedMinutes, 1) . ' minutes'
        //     ]);

        //     foreach ($data as $index => $item) {
        //         $surveyLinkWithCode = $linkSurvey . '?code=' . $item->kode_unik;

        //         // Log progress setiap 10 pesan
        //         if (($index + 1) % 10 == 0 || $index == 0) {
        //             Log::info('Blast Progress', [
        //                 'current' => $index + 1,
        //                 'total' => $totalData,
        //                 'progress' => round((($index + 1) / $totalData) * 100, 1) . '%'
        //             ]);
        //         }

        //         // Gunakan method dengan media untuk mengirim logo + pesan
        //         $result = $this->whatsappHelper->sendSurveyMessageWithMedia(
        //             $item->telepone_outlet,
        //             $item->kode_unik,
        //             $surveyLinkWithCode
        //         );

        //         if ($result['success']) {
        //             $successCount++;
        //             // Update status jika berhasil
        //             $item->update([
        //                 'blast_status' => 'sent',
        //                 'blast_sent_at' => now(),
        //                 'blast_error' => null
        //             ]);

        //             Log::info('Message sent successfully', [
        //                 'phone' => $item->telepone_outlet,
        //                 'kode_unik' => $item->kode_unik,
        //                 'progress' => ($index + 1) . '/' . $totalData
        //             ]);
        //         } else {
        //             $failCount++;
        //             // Update status jika gagal
        //             $item->update([
        //                 'blast_status' => 'failed',
        //                 'blast_error' => $result['error'] ?? 'Unknown error'
        //             ]);

        //             Log::warning('Message failed to send', [
        //                 'phone' => $item->telepone_outlet,
        //                 'kode_unik' => $item->kode_unik,
        //                 'error' => $result['error'] ?? 'Unknown error'
        //             ]);
        //         }

        //         // Delay 15 detik antar pengiriman untuk menghindari spam detection
        //         // Kecuali untuk pesan terakhir
        //         if ($index < $totalData - 1) {
        //             Log::info('Waiting 15 seconds before next message...', [
        //                 'next_message' => $index + 2,
        //                 'remaining' => $totalData - ($index + 1)
        //             ]);
        //             sleep(15);
        //         }
        //     }

        //     Log::info('Blast WA Completed', [
        //         'total' => $totalData,
        //         'success' => $successCount,
        //         'failed' => $failCount,
        //         'success_rate' => $totalData > 0 ? round(($successCount / $totalData) * 100, 1) . '%' : '0%'
        //     ]);

        //     return redirect()->back()->with(
        //         'success',
        //         "Pesan blast selesai dikirim! Berhasil: {$successCount}, Gagal: {$failCount}. " .
        //             "Total waktu: " . round((($totalData * 15) / 60), 1) . " menit."
        //     );
        // } catch (\Exception $e) {
        //     Log::error('Blast WA Error', [
        //         'error' => $e->getMessage(),
        //         'trace' => $e->getTraceAsString()
        //     ]);

        //     return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim blast: ' . $e->getMessage());
        // }


        // sebelumnya
    //     public function BlastingWa(Request $request)
    // {
    //     try {
    //         // Ambil maksimal 1000 data yang belum diblast
    //         $batchSize = 1000;
    //         $linkSurvey = "https://survey.superiorprimasukses.co.id/";

    //         $dataToBlast = CheckBlast::where('status_blast_wa', 'false')
    //             ->whereNotNull('no_telp_check')
    //             ->whereNotNull('kode_unik')
    //             ->limit($batchSize)
    //             ->get();

    //         if ($dataToBlast->isEmpty()) {
    //             return redirect()->back()->with('error', 'Tidak ada data baru untuk dikirim.');
    //         }

    //         // Awal waktu delay
    //         $startDelay = now();
    //         $delayIncrement = 15; // detik

    //         $jobs = [];
    //         foreach ($dataToBlast as $index => $record) {
    //             $delay = $startDelay->copy()->addSeconds($index * $delayIncrement);
    //             $jobs[] = (new SendWhatsappBlastJob($record->id, $linkSurvey))->delay($delay);
    //         }

    //         // Kirim batch job
    //         Bus::batch($jobs)
    //             ->name('Blasting WhatsApp')
    //             ->dispatch();

    //         return redirect()->back()->with('success', "Proses blasting telah dijadwalkan untuk {$dataToBlast->count()} nomor.");
    //     } catch (\Exception $e) {
    //         Log::error('Blasting WA Error', [
    //             'error' => $e->getMessage(),
    //         ]);

    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses blasting.');
    //     }
    // }



    //  try {
    //         $linkSurvey = "https://survey.superiorprimasukses.co.id/";
    //         $batchSize = 1000;

    //         $dataToBlast = CheckBlast::where('status_blast_wa', 'false')
    //             ->whereNotNull('no_telp_check')
    //             ->whereNotNull('kode_unik')
    //             ->limit($batchSize)
    //             ->get();

    //         if ($dataToBlast->isEmpty()) {
    //             return redirect()->back()->with('error', 'Tidak ada data baru untuk dikirim.');
    //         }

    //         $helper = new WhatsappHelper(); // gunakan helper secara langsung
    //         $successCount = 0;
    //         $failCount = 0;

    //         foreach ($dataToBlast as $index => $record) {
    //             $wa = $record->no_telp_check;
    //             $kode = $record->kode_unik;
    //             $link = $linkSurvey . '?code=' . $kode;

    //             $result = $helper->sendSurveyMessageWithMedia($wa, $kode, $link);

    //             if ($result['success']) {
    //                 $record->update([
    //                     'status_blast_wa' => 'true',
    //                     'blast_status' => 'sent',
    //                     'blast_sent_at' => now(),
    //                     'blast_error' => null,
    //                 ]);
    //                 $successCount++;
    //             } else {
    //                 $record->update([
    //                     'blast_status' => 'failed',
    //                     'blast_error' => $result['error'] ?? 'Unknown error'
    //                 ]);
    //                 $failCount++;
    //             }

    //             // Delay 2 detik antara kiriman (biar nggak diblokir)
    //             sleep(2);
    //         }

    //         return redirect()->back()->with('success', "Blast selesai: Berhasil {$successCount}, Gagal {$failCount}");
    //     } catch (\Exception $e) {
    //         Log::error('Blasting WA Error', ['error' => $e->getMessage()]);
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat blasting: ' . $e->getMessage());
    //     }
