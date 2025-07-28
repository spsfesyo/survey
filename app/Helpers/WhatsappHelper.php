<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappHelper
{
    private $token;
    private $url;

    public function __construct()
    {
        $this->token = "01TRuTo0G8tZoDu8vg19X0mgKVk43jD911E87EX18HXnsARPsVDRJ9A.NWWhGnrJ";
        $this->url = "https://tegal.wablas.com/api/send-message";
    }

    /**
     * Format pesan survey sesuai template IM3
     */
    public function formatSurveyMessage($kodeUnik, $surveyLink)
    {
        $message = " *Survey Kepuasan Pelanggan*\n\n";
        $message .= "Terima kasih pelanggan setia bata ringan kami\n\n";
        $message .= "Tetap dukung kami dalam memberikan pelayanan terbaik\n";
        $message .= "📝 Isi survey dengan jujur sesuai dengan pengalaman dan saran yang diberikan sangat berarti bagi kami!\n\n";
        $message .= "Kode Unik: *{$kodeUnik}*\n\n";
        $message .= "👇 Klik link di bawah untuk mulai survey:\n";
        $message .= $surveyLink;

        return $message;
    }

    /**
     * Kirim pesan WhatsApp menggunakan Wablas API
     */
    public function sendSurveyMessage($phoneNumber, $kodeUnik, $surveyLink)
    {
        try {
            // Format nomor telepon
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            // Format pesan
            $message = $this->formatSurveyMessage($kodeUnik, $surveyLink);

            // Data untuk API Wablas
            $data = [
                'phone' => $formattedPhone,
                'message' => $message,
            ];

            // Kirim menggunakan cURL seperti helper asli Anda
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Authorization: " . $this->token,
            ]);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_URL, $this->url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            $result = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            curl_close($curl);

            // Parse response
            $response = json_decode($result, true);

            // Log response untuk debugging
            Log::info('WhatsApp Blast Response', [
                'phone' => $formattedPhone,
                'kode_unik' => $kodeUnik,
                'http_code' => $httpCode,
                'response' => $response,
                'curl_error' => $error
            ]);

            $success = $httpCode == 200 && !$error && isset($response['status']) && $response['status'] == true;

            return [
                'success' => $success,
                'http_code' => $httpCode,
                'response' => $response,
                'phone' => $formattedPhone,
                'kode_unik' => $kodeUnik,
                'error' => $error ?: ($response['message'] ?? null)
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp Blast Error', [
                'phone' => $phoneNumber,
                'kode_unik' => $kodeUnik,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'phone' => $phoneNumber,
                'kode_unik' => $kodeUnik
            ];
        }
    }

    /**
     * Format nomor telepon ke format internasional
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Hapus semua karakter non-numeric
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Hapus leading zero dan tambahkan kode negara Indonesia
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Get API endpoint berdasarkan server
     */
    private function getApiEndpoint()
    {
        return $this->url;
    }

    /**
     * Kirim blast ke multiple nomor
     */
    public function sendBulkSurvey($recipients, $surveyBaseUrl)
    {
        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($recipients as $recipient) {
            // Generate survey link dengan kode unik
            $surveyLink = $surveyBaseUrl . '?code=' . $recipient['kode_unik'];

            $result = $this->sendSurveyMessage(
                $recipient['no_telp_check'],
                $recipient['kode_unik'],
                $surveyLink
            );

            $results[] = $result;

            if ($result['success']) {
                $successCount++;
            } else {
                $failCount++;
            }

            // Delay untuk menghindari rate limiting
            usleep(1000000); // 1 detik delay untuk Wablas
        }

        return [
            'total' => count($recipients),
            'success' => $successCount,
            'failed' => $failCount,
            'results' => $results
        ];
    }

    /**
     * Validasi token dan server
     */
    public function validateConnection()
    {
        try {
            // Test dengan mengirim ke nomor kosong untuk validasi token
            $testData = [
                'phone' => '628123456789',
                'message' => 'Test connection'
            ];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Authorization: " . $this->token,
            ]);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($testData));
            curl_setopt($curl, CURLOPT_URL, $this->url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);

            $result = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $response = json_decode($result, true);

            return [
                'valid' => $httpCode == 200,
                'message' => $response['message'] ?? 'Connection test completed',
                'http_code' => $httpCode
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
