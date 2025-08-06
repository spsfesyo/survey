<?php

namespace App\Jobs;

use Throwable;
use App\Models\CheckBlast;
use Illuminate\Bus\Queueable;
use App\Helpers\WhatsappHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class SendWhatsappBlastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recordId;
    protected $linkSurvey;

    public $timeout = 120; // ⏱️ Timeout maksimum per job

    /**
     * Create a new job instance.
     */
    public function __construct($recordId, $linkSurvey)
    {
        $this->recordId = $recordId;
        $this->linkSurvey = $linkSurvey;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("🟡 [Job Start] WA Blast untuk record ID: {$this->recordId}");

        $record = CheckBlast::find($this->recordId);

        if (!$record) {
            Log::warning("⚠️ Record ID {$this->recordId} tidak ditemukan.");
            return;
        }

        if ($record->status_blast_wa === 'true') {
            Log::info("✅ Record ID {$this->recordId} sudah diblast. Skip.");
            return;
        }

        $helper = new WhatsappHelper();
        $wa = $record->no_telp_check;
        $kode = $record->kode_unik;
        $link = $this->linkSurvey . '?code=' . $kode;

        try {
            $sendResult = $helper->sendSurveyMessageWithMedia($wa, $kode, $link);

            if ($sendResult['success']) {
                $record->update([
                    'status_blast_wa' => 'true',
                    'blast_status' => 'sent',
                    'blast_sent_at' => now(),
                    'blast_error' => null,
                ]);

                Log::info("✅ WA berhasil dikirim ke: {$wa}");
            } else {
                $record->update([
                    'blast_status' => 'failed',
                    'blast_error' => $sendResult['error'] ?? 'Unknown error'
                ]);

                Log::error("❌ Gagal kirim WA ke: {$wa}. Error: " . ($sendResult['error'] ?? 'Unknown'));
            }
        } catch (\Exception $e) {
            $record->update([
                'blast_status' => 'failed',
                'blast_error' => $e->getMessage()
            ]);

            Log::error("🔥 Exception saat kirim WA ID {$this->recordId}: " . $e->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('🚨 WA Blast Job failed permanently', [
            'record_id' => $this->recordId,
            'error' => $exception->getMessage(),
        ]);
    }
}




// sebelumnyaa


// <?php

// namespace App\Jobs;

// use Throwable;
// use App\Models\CheckBlast;
// use Illuminate\Bus\Queueable;
// use App\Helpers\WhatsappHelper;
// use App\Models\MasterOutletSurvey;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;

// class SendWhatsappBlastJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     protected $recordId;
//     protected $linkSurvey;
//     public $timeout = 60;
//     /**
//      * Create a new job instance.
//      */
//     public function __construct($recordId, $linkSurvey)
//     {
//         $this->recordId = $recordId;
//         $this->linkSurvey = $linkSurvey;
//     }

//     /**
//      * Execute the job.
//      */
//     public function handle(): void
//     {
//         $record = CheckBlast::find($this->recordId);

//         if (!$record || $record->status_blast_wa === 'true') {
//             return; // Skip jika sudah diblast
//         }

//         $helper = new WhatsappHelper();
//         $wa = $record->no_telp_check;
//         $kode = $record->kode_unik;
//         $link = $this->linkSurvey . '?code=' . $kode;

//         $sendResult = $helper->sendSurveyMessageWithMedia($wa, $kode, $link);

//         if ($sendResult['success']) {
//             $record->update([
//                 'status_blast_wa' => 'true',
//                 'blast_status' => 'sent',
//                 'blast_sent_at' => now(),
//                 'blast_error' => null,
//             ]);
//             Log::info('WA sent successfully to: ' . $wa);
//         } else {
//             $record->update([
//                 'blast_status' => 'failed',
//                 'blast_error' => $sendResult['error'] ?? 'Unknown error'
//             ]);
//             Log::warning('Failed sending WA to: ' . $wa);
//         }
//     }

//     public function failed(Throwable $exception): void
//     {
//         Log::error('Blast WA Job failed', [
//             'record_id' => $this->recordId,
//             'error' => $exception->getMessage()
//         ]);
//     }
// }
