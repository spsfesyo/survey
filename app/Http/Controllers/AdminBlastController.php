<?php

namespace App\Http\Controllers;

use App\Models\CheckBlast;
use Illuminate\Http\Request;

use App\Helpers\WhatsappHelper;
use Illuminate\Support\Facades\DB;
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
            $linkSurvey = "https://survey.superiorprimasukses.co.id/";
            $data = CheckBlast::whereNotNull('no_telp_check')
                            ->whereNotNull('kode_unik')
                            ->get();

            $successCount = 0;
            $failCount = 0;

            foreach ($data as $item) {
                $surveyLinkWithCode = $linkSurvey . '?code=' . $item->kode_unik;

                $result = $this->whatsappHelper->sendSurveyMessage(
                    $item->no_telp_check,
                    $item->kode_unik,
                    $surveyLinkWithCode
                );

                if ($result['success']) {
                    $successCount++;
                    // Update status jika berhasil
                    $item->update([
                        'blast_status' => 'sent',
                        'blast_sent_at' => now(),
                        'blast_error' => null
                    ]);
                } else {
                    $failCount++;
                    // Update status jika gagal
                    $item->update([
                        'blast_status' => 'failed',
                        'blast_error' => $result['error'] ?? 'Unknown error'
                    ]);
                }

                // Delay 1 detik antar pengiriman
                sleep(1);
            }

            Log::info('Blast WA Completed', [
                'total' => $data->count(),
                'success' => $successCount,
                'failed' => $failCount
            ]);

            return redirect()->back()->with('success', "Pesan blast berhasil dikirim! Berhasil: {$successCount}, Gagal: {$failCount}");

        } catch (\Exception $e) {
            Log::error('Blast WA Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim blast: ' . $e->getMessage());
        }
    }
}
