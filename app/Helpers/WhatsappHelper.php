<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class WhatsappHelper
{
    private $token;
    private $url;
    private $maxRetries;
    private $retryDelay;

    public function __construct()
    {
        $this->token = "SjUrckbTZaM4hCZ49RT3VvWUByg4WvFhaC9qnSaCX4iMZeercjpv0rh.VwnzPQkJ";
        $this->url = "https://pati.wablas.com/api/send-message";
        $this->maxRetries = 3;
        $this->retryDelay = 2; // seconds
    }

    /**
     * Format pesan survey sesuai template IM3 dengan logo dan clickable links
     */
    public function formatSurveyMessage($kodeUnik, $surveyLink)
    {
        // Pastikan link clickable
        $clickableSurveyLink = $this->formatClickableUrl($surveyLink);
        $clickableWebsiteLink = $this->formatClickableUrl('https://superiorprimasukses.co.id');

        $message = "*Survey Kepuasan Pelanggan*\n\n";
        $message .= "Terima kasih pelanggan setia bata ringan kami\n\n";
        $message .= "Tetap dukung kami dalam memberikan pelayanan terbaik\n";
        $message .= "📝 Isi survey dengan jujur sesuai dengan pengalaman dan saran yang diberikan sangat berarti bagi kami!\n\n";

        // Membuat kode unik dengan format yang bisa dicopy dengan mudah
        $message .= "🔑 *Kode Unik Anda:*\n";
        $message .= "`{$kodeUnik}`\n\n";

        $message .= "👇 *Mulai Survey Sekarang:*\n";
        // Format link tanpa spasi sebelum dan sesudah, dalam baris terpisah
        $message .= $clickableSurveyLink . "\n\n";

        // Menambahkan link website di bagian bawah dengan format yang proper
        $message .= "🌐 *Website Kami:*\n";
        $message .= $clickableWebsiteLink . "\n\n";

        $message .= "_Terima kasih atas partisipasi Anda!_";

        return $message;
    }

    /**
     * Kirim pesan WhatsApp dengan gambar menggunakan Wablas API
     */
    public function sendSurveyMessage($phoneNumber, $kodeUnik, $surveyLink)
    {
        try {
            // Format nomor telepon
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            // Format pesan
            $message = $this->formatSurveyMessage($kodeUnik, $surveyLink);

            // URL logo dari public/img
            $logoUrl = url('img/logo.png'); // Sesuaikan dengan nama file logo Anda

            // Data untuk API Wablas dengan gambar
            $data = [
                'phone' => $formattedPhone,
                'message' => $message,
                'image' => $logoUrl, // Menambahkan logo
            ];

            // Kirim menggunakan cURL
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
     * Alternative: Kirim pesan dengan logo sebagai media terpisah
     */
    public function sendSurveyMessageWithMedia($phoneNumber, $kodeUnik, $surveyLink)
    {
        try {
            // Format nomor telepon
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            // URL logo dari public/img - pastikan bisa diakses publik
            $logoUrl = url('img/logo.png'); // Sesuaikan dengan nama file logo Anda

            // Debug: Log URL logo
            Log::info('Logo URL', ['logo_url' => $logoUrl]);

            // CARA 1: Coba kirim sebagai satu pesan dengan image dan text
            $combinedData = [
                'phone' => $formattedPhone,
                'image' => $logoUrl,
                'message' => $this->formatSurveyMessageForImage($kodeUnik, $surveyLink)
            ];

            $result = $this->sendImageWithText($combinedData, $kodeUnik);

            if ($result['success']) {
                return $result;
            }

            // CARA 2: Jika cara 1 gagal, coba kirim terpisah
            Log::info('Trying alternative method - separate image and text');

            // Kirim logo terlebih dahulu
            $mediaData = [
                'phone' => $formattedPhone,
                'image' => $logoUrl,
                'caption' => '🏢 *Survey Kepuasan Pelanggan*'
            ];

            // Kirim media
            $mediaResult = $this->sendMedia($mediaData);

            // Log media result
            Log::info('Media sent', [
                'phone' => $formattedPhone,
                'media_result' => $mediaResult
            ]);

            // Delay 3 detik antara media dan text
            sleep(3);

            // Format pesan text dengan link yang benar-benar clickable
            $message = $this->formatClickableMessage($kodeUnik, $surveyLink);

            // Data untuk pesan text
            $textData = [
                'phone' => $formattedPhone,
                'message' => $message,
            ];

            // Kirim text message
            return $this->sendTextMessage($textData, $kodeUnik);
        } catch (\Exception $e) {
            Log::error('WhatsApp Blast with Media Error', [
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
     * Format pesan untuk dikirim bersama gambar
     */
    private function formatSurveyMessageForImage($kodeUnik, $surveyLink)
    {
        $message = "*🏢 Survey Kepuasan Pelanggan*\n\n";
        $message .= "Terima kasih pelanggan setia bata ringan kami\n\n";
        $message .= "Tetap dukung kami dalam memberikan pelayanan terbaik\n";
        $message .= "📝 Isi survey dengan jujur sesuai pengalaman Anda!\n\n";

        // Kode unik
        $message .= "🔑 *Kode Unik:* `{$kodeUnik}`\n\n";

        // Link survey - format sederhana untuk WhatsApp
        $message .= "👇 *KLIK LINK SURVEY:*\n";
        $message .= $surveyLink . "\n\n";

        // Website link
        $message .= "🌐 *Website:*\n";
        $message .= "https://superiorprimasukses.co.id\n\n";

        $message .= "_Terima kasih! 🙏_";

        return $message;
    }

    /**
     * Format pesan dengan link yang clickable
     */
    private function formatClickableMessage($kodeUnik, $surveyLink)
    {
        $message = "Terima kasih pelanggan setia bata ringan kami\n\n";
        $message .= "Tetap dukung kami dalam memberikan pelayanan terbaik\n";
        $message .= "📝 Isi survey dengan jujur sesuai pengalaman Anda!\n\n";

        // Kode unik dengan format monospace
        $message .= "🔑 *Kode Unik Anda:*\n";
        $message .= "`{$kodeUnik}`\n\n";

        $message .= "👇 *SURVEY LINK - KLIK DI BAWAH:*\n\n";
        // Link harus dalam baris terpisah tanpa formatting
        $message .= $surveyLink . "\n\n";

        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        $message .= "🌐 *WEBSITE KAMI:*\n\n";
        $message .= "https://superiorprimasukses.co.id\n\n";

        $message .= "_Terima kasih atas partisipasi Anda! 🙏_";

        return $message;
    }

    /**
     * Kirim gambar dengan text dalam satu request
     */
    private function sendImageWithText($data, $kodeUnik)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: " . $this->token,
        ]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        // Coba endpoint yang berbeda untuk image + message
        curl_setopt($curl, CURLOPT_URL, "https://tegal.wablas.com/api/send-image");
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        $response = json_decode($result, true);

        Log::info('Send Image with Text Response', [
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error
        ]);

        $success = $httpCode == 200 && !$error && isset($response['status']) && $response['status'] == true;

        return [
            'success' => $success,
            'http_code' => $httpCode,
            'response' => $response,
            'phone' => $data['phone'],
            'kode_unik' => $kodeUnik,
            'error' => $error ?: ($response['message'] ?? null)
        ];
    }

    /**
     * Helper method untuk mengirim media
     */
    private function sendMedia($data)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: " . $this->token,
        ]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, "https://tegal.wablas.com/api/send-image"); // endpoint untuk image
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result, true);
    }

    /**
     * Helper method untuk mengirim text message
     */
    private function sendTextMessage($data, $kodeUnik)
    {
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

        $response = json_decode($result, true);
        $success = $httpCode == 200 && !$error && isset($response['status']) && $response['status'] == true;

        return [
            'success' => $success,
            'http_code' => $httpCode,
            'response' => $response,
            'phone' => $data['phone'],
            'kode_unik' => $kodeUnik,
            'error' => $error ?: ($response['message'] ?? null)
        ];
    }

    /**
     * Validasi dan format URL agar clickable di WhatsApp
     */
    private function formatClickableUrl($url)
    {
        // Pastikan URL menggunakan https://
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }

        // Pastikan tidak ada spasi di awal atau akhir
        $url = trim($url);

        // Pastikan URL valid
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        return $url; // Return as is jika tidak bisa divalidasi
    }

    /**
     * Format nomor telepon ke format internasional - buat public untuk testing
     */
    public function formatPhoneNumber($phoneNumber)
    {
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);

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

            // Gunakan method dengan media untuk hasil yang lebih baik
            $result = $this->sendSurveyMessageWithMedia(
                $recipient['telepone_outlet'],
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
            usleep(2000000); // 2 detik delay karena mengirim 2 pesan (media + text)
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


// class WhatsappHelper
// {
//     private $token;
//     private $url;

//     public function __construct()
//     {
//         $this->token = "01TRuTo0G8tZoDu8vg19X0mgKVk43jD911E87EX18HXnsARPsVDRJ9A.NWWhGnrJ";
//         $this->url = "https://tegal.wablas.com/api/send-message";
//     }

//     /**
//      * Format pesan survey sesuai template IM3 dengan logo dan clickable links
//      */
//     public function formatSurveyMessage($kodeUnik, $surveyLink)
//     {
//         // Pastikan link clickable
//         $clickableSurveyLink = $this->formatClickableUrl($surveyLink);
//         $clickableWebsiteLink = $this->formatClickableUrl('https://superiorprimasukses.co.id');

//         $message = "*Survey Kepuasan Pelanggan*\n\n";
//         $message .= "Terima kasih pelanggan setia bata ringan kami\n\n";
//         $message .= "Tetap dukung kami dalam memberikan pelayanan terbaik\n";
//         $message .= "📝 Isi survey dengan jujur sesuai dengan pengalaman dan saran yang diberikan sangat berarti bagi kami!\n\n";

//         // Membuat kode unik dengan format yang bisa dicopy dengan mudah
//         $message .= "🔑 *Kode Unik Anda:*\n";
//         $message .= "`{$kodeUnik}`\n\n";

//         $message .= "👇 *Mulai Survey Sekarang:*\n";
//         // Format link tanpa spasi sebelum dan sesudah, dalam baris terpisah
//         $message .= $clickableSurveyLink . "\n\n";

//         // Menambahkan link website di bagian bawah dengan format yang proper
//         $message .= "🌐 *Website Kami:*\n";
//         $message .= $clickableWebsiteLink . "\n\n";

//         $message .= "_Terima kasih atas partisipasi Anda!_";

//         return $message;
//     }

//     /**
//      * Kirim pesan WhatsApp dengan gambar menggunakan Wablas API
//      */
//     public function sendSurveyMessage($phoneNumber, $kodeUnik, $surveyLink)
//     {
//         try {
//             // Format nomor telepon
//             $formattedPhone = $this->formatPhoneNumber($phoneNumber);

//             // Format pesan
//             $message = $this->formatSurveyMessage($kodeUnik, $surveyLink);

//             // URL logo dari public/img
//             $logoUrl = url('img/logo-superior-prima-sukses.jpg'); // Sesuaikan dengan nama file logo Anda

//             // Data untuk API Wablas dengan gambar
//             $data = [
//                 'phone' => $formattedPhone,
//                 'message' => $message,
//                 'image' => $logoUrl, // Menambahkan logo
//             ];

//             // Kirim menggunakan cURL
//             $curl = curl_init();

//             curl_setopt($curl, CURLOPT_HTTPHEADER, [
//                 "Authorization: " . $this->token,
//             ]);
//             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//             curl_setopt($curl, CURLOPT_URL, $this->url);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

//             $result = curl_exec($curl);
//             $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//             $error = curl_error($curl);
//             curl_close($curl);

//             // Parse response
//             $response = json_decode($result, true);

//             // Log response untuk debugging
//             Log::info('WhatsApp Blast Response', [
//                 'phone' => $formattedPhone,
//                 'kode_unik' => $kodeUnik,
//                 'http_code' => $httpCode,
//                 'response' => $response,
//                 'curl_error' => $error
//             ]);

//             $success = $httpCode == 200 && !$error && isset($response['status']) && $response['status'] == true;

//             return [
//                 'success' => $success,
//                 'http_code' => $httpCode,
//                 'response' => $response,
//                 'phone' => $formattedPhone,
//                 'kode_unik' => $kodeUnik,
//                 'error' => $error ?: ($response['message'] ?? null)
//             ];
//         } catch (\Exception $e) {
//             Log::error('WhatsApp Blast Error', [
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik,
//                 'error' => $e->getMessage()
//             ]);

//             return [
//                 'success' => false,
//                 'error' => $e->getMessage(),
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik
//             ];
//         }
//     }

//     /**
//      * Alternative: Kirim pesan dengan logo sebagai media terpisah
//      */
//     public function sendSurveyMessageWithMedia($phoneNumber, $kodeUnik, $surveyLink)
//     {
//         try {
//             // Format nomor telepon
//             $formattedPhone = $this->formatPhoneNumber($phoneNumber);

//             // URL logo dari public/img
//             $logoUrl = url('img/logo-superior-prima-sukses.jpg'); // Sesuaikan dengan nama file logo Anda

//             // Kirim logo terlebih dahulu
//             $mediaData = [
//                 'phone' => $formattedPhone,
//                 'image' => $logoUrl,
//                 'caption' => '*Survey Kepuasan Pelanggan*'
//             ];

//             // Kirim media
//             $this->sendMedia($mediaData);

//             // Delay sebentar sebelum kirim text
//             sleep(1);

//             // Format pesan tanpa judul (karena sudah ada di caption logo)
//             $message = "Terima kasih pelanggan setia bata ringan kami\n\n";
//             $message .= "Tetap dukung kami dalam memberikan pelayanan terbaik\n";
//             $message .= "📝 Isi survey dengan jujur sesuai dengan pengalaman dan saran yang diberikan sangat berarti bagi kami!\n\n";

//             // Kode unik dengan format yang mudah dicopy
//             $message .= "🔑 *Kode Unik Anda:*\n";
//             $message .= "`{$kodeUnik}`\n\n";

//             $message .= "👇 *Mulai Survey Sekarang:*\n";
//             // Pastikan link clickable dan dalam baris terpisah
//             $clickableSurveyLink = $this->formatClickableUrl($surveyLink);
//             $message .= $clickableSurveyLink . "\n\n";

//             // Link website
//             $message .= "🌐 *Website Kami:*\n";
//             $clickableWebsiteLink = $this->formatClickableUrl('https://superiorprimasukses.co.id');
//             $message .= $clickableWebsiteLink . "\n\n";

//             $message .= "_Terima kasih atas partisipasi Anda!_";

//             // Data untuk pesan text
//             $textData = [
//                 'phone' => $formattedPhone,
//                 'message' => $message,
//             ];

//             // Kirim text message
//             return $this->sendTextMessage($textData, $kodeUnik);
//         } catch (\Exception $e) {
//             Log::error('WhatsApp Blast with Media Error', [
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik,
//                 'error' => $e->getMessage()
//             ]);

//             return [
//                 'success' => false,
//                 'error' => $e->getMessage(),
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik
//             ];
//         }
//     }

//     /**
//      * Helper method untuk mengirim media
//      */
//     private function sendMedia($data)
//     {
//         $curl = curl_init();

//         curl_setopt($curl, CURLOPT_HTTPHEADER, [
//             "Authorization: " . $this->token,
//         ]);
//         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//         curl_setopt($curl, CURLOPT_URL, "https://tegal.wablas.com/api/send-image"); // endpoint untuk image
//         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

//         $result = curl_exec($curl);
//         curl_close($curl);

//         return json_decode($result, true);
//     }

//     /**
//      * Helper method untuk mengirim text message
//      */
//     private function sendTextMessage($data, $kodeUnik)
//     {
//         $curl = curl_init();

//         curl_setopt($curl, CURLOPT_HTTPHEADER, [
//             "Authorization: " . $this->token,
//         ]);
//         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//         curl_setopt($curl, CURLOPT_URL, $this->url);
//         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

//         $result = curl_exec($curl);
//         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//         $error = curl_error($curl);
//         curl_close($curl);

//         $response = json_decode($result, true);
//         $success = $httpCode == 200 && !$error && isset($response['status']) && $response['status'] == true;

//         return [
//             'success' => $success,
//             'http_code' => $httpCode,
//             'response' => $response,
//             'phone' => $data['phone'],
//             'kode_unik' => $kodeUnik,
//             'error' => $error ?: ($response['message'] ?? null)
//         ];
//     }

//     /**
//      * Validasi dan format URL agar clickable di WhatsApp
//      */
//     private function formatClickableUrl($url)
//     {
//         // Pastikan URL menggunakan https://
//         if (!preg_match('/^https?:\/\//', $url)) {
//             $url = 'https://' . $url;
//         }

//         // Pastikan tidak ada spasi di awal atau akhir
//         $url = trim($url);

//         // Pastikan URL valid
//         if (filter_var($url, FILTER_VALIDATE_URL)) {
//             return $url;
//         }

//         return $url; // Return as is jika tidak bisa divalidasi
//     }

//     /**
//      * Format nomor telepon ke format internasional
//      */
//     private function formatPhoneNumber($phoneNumber)
//     {
//         // Hapus semua karakter non-numeric
//         $phone = preg_replace('/[^0-9]/', '', $phoneNumber);

//         // Hapus leading zero dan tambahkan kode negara Indonesia
//         if (substr($phone, 0, 1) === '0') {
//             $phone = '62' . substr($phone, 1);
//         } elseif (substr($phone, 0, 2) !== '62') {
//             $phone = '62' . $phone;
//         }

//         return $phone;
//     }

//     /**
//      * Get API endpoint berdasarkan server
//      */
//     private function getApiEndpoint()
//     {
//         return $this->url;
//     }

//     /**
//      * Kirim blast ke multiple nomor
//      */
//     public function sendBulkSurvey($recipients, $surveyBaseUrl)
//     {
//         $results = [];
//         $successCount = 0;
//         $failCount = 0;

//         foreach ($recipients as $recipient) {
//             // Generate survey link dengan kode unik
//             $surveyLink = $surveyBaseUrl . '?code=' . $recipient['kode_unik'];

//             // Gunakan method dengan media untuk hasil yang lebih baik
//             $result = $this->sendSurveyMessageWithMedia(
//                 $recipient['no_telp_check'],
//                 $recipient['kode_unik'],
//                 $surveyLink
//             );

//             $results[] = $result;

//             if ($result['success']) {
//                 $successCount++;
//             } else {
//                 $failCount++;
//             }

//             // Delay untuk menghindari rate limiting
//             usleep(2000000); // 2 detik delay karena mengirim 2 pesan (media + text)
//         }

//         return [
//             'total' => count($recipients),
//             'success' => $successCount,
//             'failed' => $failCount,
//             'results' => $results
//         ];
//     }

//     /**
//      * Validasi token dan server
//      */
//     public function validateConnection()
//     {
//         try {
//             // Test dengan mengirim ke nomor kosong untuk validasi token
//             $testData = [
//                 'phone' => '628123456789',
//                 'message' => 'Test connection'
//             ];

//             $curl = curl_init();
//             curl_setopt($curl, CURLOPT_HTTPHEADER, [
//                 "Authorization: " . $this->token,
//             ]);
//             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($testData));
//             curl_setopt($curl, CURLOPT_URL, $this->url);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
//             curl_setopt($curl, CURLOPT_TIMEOUT, 10);

//             $result = curl_exec($curl);
//             $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//             curl_close($curl);

//             $response = json_decode($result, true);

//             return [
//                 'valid' => $httpCode == 200,
//                 'message' => $response['message'] ?? 'Connection test completed',
//                 'http_code' => $httpCode
//             ];
//         } catch (\Exception $e) {
//             return [
//                 'valid' => false,
//                 'message' => $e->getMessage()
//             ];
//         }
//     }
// }


// class WhatsappHelper
// {
//     private $token;
//     private $url;

//     public function __construct()
//     {
//         $this->token = "01TRuTo0G8tZoDu8vg19X0mgKVk43jD911E87EX18HXnsARPsVDRJ9A.NWWhGnrJ";
//         $this->url = "https://tegal.wablas.com/api/send-message";
//     }

//     /**
//      * Format pesan survey sesuai template IM3 dengan logo dan clickable links
//      */
//     public function formatSurveyMessage($kodeUnik, $surveyLink)
//     {
//         $message = "*🏢 Survey Kepuasan Pelanggan*\n\n";
//         $message .= "Terima kasih pelanggan setia bata ringan kami\n\n";
//         $message .= "Tetap dukung kami dalam memberikan pelayanan terbaik\n";
//         $message .= "📝 Isi survey dengan jujur sesuai dengan pengalaman dan saran yang diberikan sangat berarti bagi kami!\n\n";

//         // Kode unik dengan format monospace
//         $message .= "🔑 *Kode Unik Anda:*\n";
//         $message .= "```" . $kodeUnik . "```\n\n";

//         // Link survey dengan format yang pasti clickable
//         $message .= "👇 *KLIK LINK INI UNTUK SURVEY:*\n\n";
//         $message .= $surveyLink . "\n\n";

//         $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

//         // Website link
//         $message .= "🌐 *Info lebih lanjut kunjungi:*\n\n";
//         $message .= "https://superiorprimasukses.co.id\n\n";

//         $message .= "_Terima kasih atas partisipasi Anda! 🙏_";

//         return $message;
//     }

//     /**
//      * Kirim pesan WhatsApp dengan gambar menggunakan Wablas API
//      */
//     public function sendSurveyMessage($phoneNumber, $kodeUnik, $surveyLink)
//     {
//         try {
//             // Format nomor telepon
//             $formattedPhone = $this->formatPhoneNumber($phoneNumber);

//             // Format pesan
//             $message = $this->formatSurveyMessage($kodeUnik, $surveyLink);

//             // URL logo dari public/img
//             $logoUrl = url('img/logo-superior-prima-sukses.jpg'); // Sesuaikan dengan nama file logo Anda

//             // Data untuk API Wablas dengan gambar
//             $data = [
//                 'phone' => $formattedPhone,
//                 'message' => $message,
//                 'image' => $logoUrl, // Menambahkan logo
//             ];

//             // Kirim menggunakan cURL
//             $curl = curl_init();

//             curl_setopt($curl, CURLOPT_HTTPHEADER, [
//                 "Authorization: " . $this->token,
//             ]);
//             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//             curl_setopt($curl, CURLOPT_URL, $this->url);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

//             $result = curl_exec($curl);
//             $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//             $error = curl_error($curl);
//             curl_close($curl);

//             // Parse response
//             $response = json_decode($result, true);

//             // Log response untuk debugging
//             Log::info('WhatsApp Blast Response', [
//                 'phone' => $formattedPhone,
//                 'kode_unik' => $kodeUnik,
//                 'http_code' => $httpCode,
//                 'response' => $response,
//                 'curl_error' => $error
//             ]);

//             $success = $httpCode == 200 && !$error && isset($response['status']) && $response['status'] == true;

//             return [
//                 'success' => $success,
//                 'http_code' => $httpCode,
//                 'response' => $response,
//                 'phone' => $formattedPhone,
//                 'kode_unik' => $kodeUnik,
//                 'error' => $error ?: ($response['message'] ?? null)
//             ];
//         } catch (\Exception $e) {
//             Log::error('WhatsApp Blast Error', [
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik,
//                 'error' => $e->getMessage()
//             ]);

//             return [
//                 'success' => false,
//                 'error' => $e->getMessage(),
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik
//             ];
//         }
//     }

//     /**
//      * Alternative: Kirim pesan dengan logo sebagai media terpisah
//      */
//     public function sendSurveyMessageWithMedia($phoneNumber, $kodeUnik, $surveyLink)
//     {
//         try {
//             // Format nomor telepon
//             $formattedPhone = $this->formatPhoneNumber($phoneNumber);

//             // URL logo dari public/img
//             $logoUrl = url('img/logo.png'); // Sesuaikan dengan nama file logo Anda

//             // Kirim logo terlebih dahulu
//             $mediaData = [
//                 'phone' => $formattedPhone,
//                 'image' => $logoUrl,
//                 'caption' => '*Survey Kepuasan Pelanggan*'
//             ];

//             // Kirim media
//             $this->sendMedia($mediaData);

//             // Delay sebentar sebelum kirim text
//             sleep(1);

//             // Format pesan tanpa judul (karena sudah ada di caption logo)
//             $message = "Terima kasih pelanggan setia bata ringan kami\n\n";
//             $message .= "Tetap dukung kami dalam memberikan pelayanan terbaik\n";
//             $message .= "📝 Isi survey dengan jujur sesuai dengan pengalaman dan saran yang diberikan sangat berarti bagi kami!\n\n";

//             // Kode unik dengan format yang mudah dicopy
//             $message .= "🔑 *Kode Unik Anda:*\n";
//             $message .= "```{$kodeUnik}```\n\n";

//             $message .= "👇 *Klik link di bawah untuk mulai survey:*\n";
//             $message .= "{$surveyLink}\n\n";

//             // Link website
//             $message .= "🌐 *Kunjungi website kami:*\n";
//             $message .= "https://superiorprimasukses.co.id\n\n";

//             $message .= "_Terima kasih atas partisipasi Anda!_";

//             // Data untuk pesan text
//             $textData = [
//                 'phone' => $formattedPhone,
//                 'message' => $message,
//             ];

//             // Kirim text message
//             return $this->sendTextMessage($textData, $kodeUnik);
//         } catch (\Exception $e) {
//             Log::error('WhatsApp Blast with Media Error', [
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik,
//                 'error' => $e->getMessage()
//             ]);

//             return [
//                 'success' => false,
//                 'error' => $e->getMessage(),
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik
//             ];
//         }
//     }

//     /**
//      * Helper method untuk mengirim media
//      */
//     private function sendMedia($data)
//     {
//         $curl = curl_init();

//         curl_setopt($curl, CURLOPT_HTTPHEADER, [
//             "Authorization: " . $this->token,
//         ]);
//         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//         curl_setopt($curl, CURLOPT_URL, "https://tegal.wablas.com/api/send-image"); // endpoint untuk image
//         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

//         $result = curl_exec($curl);
//         curl_close($curl);

//         return json_decode($result, true);
//     }

//     /**
//      * Helper method untuk mengirim text message
//      */
//     private function sendTextMessage($data, $kodeUnik)
//     {
//         $curl = curl_init();

//         curl_setopt($curl, CURLOPT_HTTPHEADER, [
//             "Authorization: " . $this->token,
//         ]);
//         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//         curl_setopt($curl, CURLOPT_URL, $this->url);
//         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

//         $result = curl_exec($curl);
//         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//         $error = curl_error($curl);
//         curl_close($curl);

//         $response = json_decode($result, true);
//         $success = $httpCode == 200 && !$error && isset($response['status']) && $response['status'] == true;

//         return [
//             'success' => $success,
//             'http_code' => $httpCode,
//             'response' => $response,
//             'phone' => $data['phone'],
//             'kode_unik' => $kodeUnik,
//             'error' => $error ?: ($response['message'] ?? null)
//         ];
//     }

//     /**
//      * Format nomor telepon ke format internasional
//      */
//     private function formatPhoneNumber($phoneNumber)
//     {
//         // Hapus semua karakter non-numeric
//         $phone = preg_replace('/[^0-9]/', '', $phoneNumber);

//         // Hapus leading zero dan tambahkan kode negara Indonesia
//         if (substr($phone, 0, 1) === '0') {
//             $phone = '62' . substr($phone, 1);
//         } elseif (substr($phone, 0, 2) !== '62') {
//             $phone = '62' . $phone;
//         }

//         return $phone;
//     }

//     /**
//      * Get API endpoint berdasarkan server
//      */
//     private function getApiEndpoint()
//     {
//         return $this->url;
//     }

//     /**
//      * Kirim blast ke multiple nomor
//      */
//     public function sendBulkSurvey($recipients, $surveyBaseUrl)
//     {
//         $results = [];
//         $successCount = 0;
//         $failCount = 0;

//         foreach ($recipients as $recipient) {
//             // Generate survey link dengan kode unik
//             $surveyLink = $surveyBaseUrl . '?code=' . $recipient['kode_unik'];

//             // Gunakan method dengan media untuk hasil yang lebih baik
//             $result = $this->sendSurveyMessageWithMedia(
//                 $recipient['no_telp_check'],
//                 $recipient['kode_unik'],
//                 $surveyLink
//             );

//             $results[] = $result;

//             if ($result['success']) {
//                 $successCount++;
//             } else {
//                 $failCount++;
//             }

//             // Delay untuk menghindari rate limiting
//             usleep(2000000); // 2 detik delay karena mengirim 2 pesan (media + text)
//         }

//         return [
//             'total' => count($recipients),
//             'success' => $successCount,
//             'failed' => $failCount,
//             'results' => $results
//         ];
//     }

//     /**
//      * Validasi token dan server
//      */
//     public function validateConnection()
//     {
//         try {
//             // Test dengan mengirim ke nomor kosong untuk validasi token
//             $testData = [
//                 'phone' => '628123456789',
//                 'message' => 'Test connection'
//             ];

//             $curl = curl_init();
//             curl_setopt($curl, CURLOPT_HTTPHEADER, [
//                 "Authorization: " . $this->token,
//             ]);
//             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($testData));
//             curl_setopt($curl, CURLOPT_URL, $this->url);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
//             curl_setopt($curl, CURLOPT_TIMEOUT, 10);

//             $result = curl_exec($curl);
//             $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//             curl_close($curl);

//             $response = json_decode($result, true);

//             return [
//                 'valid' => $httpCode == 200,
//                 'message' => $response['message'] ?? 'Connection test completed',
//                 'http_code' => $httpCode
//             ];
//         } catch (\Exception $e) {
//             return [
//                 'valid' => false,
//                 'message' => $e->getMessage()
//             ];
//         }
//     }
// }


// class WhatsappHelper
// {
//     private $token;
//     private $url;

//     public function __construct()
//     {
//         $this->token = "01TRuTo0G8tZoDu8vg19X0mgKVk43jD911E87EX18HXnsARPsVDRJ9A.NWWhGnrJ";
//         $this->url = "https://tegal.wablas.com/api/send-message";
//     }

//     /**
//      * Format pesan survey sesuai template IM3
//      */
//     public function formatSurveyMessage($kodeUnik, $surveyLink)
//     {
//         $message = " *Survey Kepuasan Pelanggan*\n\n";
//         $message .= "Terima kasih pelanggan setia bata ringan kami\n\n";
//         $message .= "Tetap dukung kami dalam memberikan pelayanan terbaik\n";
//         $message .= "📝 Isi survey dengan jujur sesuai dengan pengalaman dan saran yang diberikan sangat berarti bagi kami!\n\n";
//         $message .= "Kode Unik: *{$kodeUnik}*\n\n";
//         $message .= "👇 Klik link di bawah untuk mulai survey:\n";
//         $message .= $surveyLink;

//         return $message;
//     }

//     /**
//      * Kirim pesan WhatsApp menggunakan Wablas API
//      */
//     public function sendSurveyMessage($phoneNumber, $kodeUnik, $surveyLink)
//     {
//         try {
//             // Format nomor telepon
//             $formattedPhone = $this->formatPhoneNumber($phoneNumber);

//             // Format pesan
//             $message = $this->formatSurveyMessage($kodeUnik, $surveyLink);

//             // Data untuk API Wablas
//             $data = [
//                 'phone' => $formattedPhone,
//                 'message' => $message,
//             ];

//             // Kirim menggunakan cURL seperti helper asli Anda
//             $curl = curl_init();

//             curl_setopt($curl, CURLOPT_HTTPHEADER, [
//                 "Authorization: " . $this->token,
//             ]);
//             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//             curl_setopt($curl, CURLOPT_URL, $this->url);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

//             $result = curl_exec($curl);
//             $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//             $error = curl_error($curl);
//             curl_close($curl);

//             // Parse response
//             $response = json_decode($result, true);

//             // Log response untuk debugging
//             Log::info('WhatsApp Blast Response', [
//                 'phone' => $formattedPhone,
//                 'kode_unik' => $kodeUnik,
//                 'http_code' => $httpCode,
//                 'response' => $response,
//                 'curl_error' => $error
//             ]);

//             $success = $httpCode == 200 && !$error && isset($response['status']) && $response['status'] == true;

//             return [
//                 'success' => $success,
//                 'http_code' => $httpCode,
//                 'response' => $response,
//                 'phone' => $formattedPhone,
//                 'kode_unik' => $kodeUnik,
//                 'error' => $error ?: ($response['message'] ?? null)
//             ];
//         } catch (\Exception $e) {
//             Log::error('WhatsApp Blast Error', [
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik,
//                 'error' => $e->getMessage()
//             ]);

//             return [
//                 'success' => false,
//                 'error' => $e->getMessage(),
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik
//             ];
//         }
//     }

//     /**
//      * Format nomor telepon ke format internasional
//      */
//     private function formatPhoneNumber($phoneNumber)
//     {
//         // Hapus semua karakter non-numeric
//         $phone = preg_replace('/[^0-9]/', '', $phoneNumber);

//         // Hapus leading zero dan tambahkan kode negara Indonesia
//         if (substr($phone, 0, 1) === '0') {
//             $phone = '62' . substr($phone, 1);
//         } elseif (substr($phone, 0, 2) !== '62') {
//             $phone = '62' . $phone;
//         }

//         return $phone;
//     }

//     /**
//      * Get API endpoint berdasarkan server
//      */
//     private function getApiEndpoint()
//     {
//         return $this->url;
//     }

//     /**
//      * Kirim blast ke multiple nomor
//      */
//     public function sendBulkSurvey($recipients, $surveyBaseUrl)
//     {
//         $results = [];
//         $successCount = 0;
//         $failCount = 0;

//         foreach ($recipients as $recipient) {
//             // Generate survey link dengan kode unik
//             $surveyLink = $surveyBaseUrl . '?code=' . $recipient['kode_unik'];

//             $result = $this->sendSurveyMessage(
//                 $recipient['no_telp_check'],
//                 $recipient['kode_unik'],
//                 $surveyLink
//             );

//             $results[] = $result;

//             if ($result['success']) {
//                 $successCount++;
//             } else {
//                 $failCount++;
//             }

//             // Delay untuk menghindari rate limiting
//             usleep(1000000); // 1 detik delay untuk Wablas
//         }

//         return [
//             'total' => count($recipients),
//             'success' => $successCount,
//             'failed' => $failCount,
//             'results' => $results
//         ];
//     }

//     /**
//      * Validasi token dan server
//      */
//     public function validateConnection()
//     {
//         try {
//             // Test dengan mengirim ke nomor kosong untuk validasi token
//             $testData = [
//                 'phone' => '628123456789',
//                 'message' => 'Test connection'
//             ];

//             $curl = curl_init();
//             curl_setopt($curl, CURLOPT_HTTPHEADER, [
//                 "Authorization: " . $this->token,
//             ]);
//             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($testData));
//             curl_setopt($curl, CURLOPT_URL, $this->url);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
//             curl_setopt($curl, CURLOPT_TIMEOUT, 10);

//             $result = curl_exec($curl);
//             $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//             curl_close($curl);

//             $response = json_decode($result, true);

//             return [
//                 'valid' => $httpCode == 200,
//                 'message' => $response['message'] ?? 'Connection test completed',
//                 'http_code' => $httpCode
//             ];
//         } catch (\Exception $e) {
//             return [
//                 'valid' => false,
//                 'message' => $e->getMessage()
//             ];
//         }
//     }
// }



// yang sebelumnya

//  private $token;
//     private $url;

//     public function __construct()
//     {
//         $this->token = "01TRuTo0G8tZoDu8vg19X0mgKVk43jD911E87EX18HXnsARPsVDRJ9A.NWWhGnrJ";
//         $this->url = "https://tegal.wablas.com/api/send-message";
//     }

//     /**
//      * Format pesan survey sesuai template IM3 dengan logo dan clickable links
//      */
//     public function formatSurveyMessage($kodeUnik, $surveyLink)
//     {
//         // Pastikan link clickable
//         $clickableSurveyLink = $this->formatClickableUrl($surveyLink);
//         $clickableWebsiteLink = $this->formatClickableUrl('https://superiorprimasukses.co.id');

//         $message = "*Survey Kepuasan Pelanggan*\n\n";
//         $message .= "Terima kasih pelanggan setia bata ringan kami\n\n";
//         $message .= "Tetap dukung kami dalam memberikan pelayanan terbaik\n";
//         $message .= "📝 Isi survey dengan jujur sesuai dengan pengalaman dan saran yang diberikan sangat berarti bagi kami!\n\n";

//         // Membuat kode unik dengan format yang bisa dicopy dengan mudah
//         $message .= "🔑 *Kode Unik Anda:*\n";
//         $message .= "`{$kodeUnik}`\n\n";

//         $message .= "👇 *Mulai Survey Sekarang:*\n";
//         // Format link tanpa spasi sebelum dan sesudah, dalam baris terpisah
//         $message .= $clickableSurveyLink . "\n\n";

//         // Menambahkan link website di bagian bawah dengan format yang proper
//         $message .= "🌐 *Website Kami:*\n";
//         $message .= $clickableWebsiteLink . "\n\n";

//         $message .= "_Terima kasih atas partisipasi Anda!_";

//         return $message;
//     }

//     /**
//      * Kirim pesan WhatsApp dengan gambar menggunakan Wablas API
//      */
//     public function sendSurveyMessage($phoneNumber, $kodeUnik, $surveyLink)
//     {
//         try {
//             // Format nomor telepon
//             $formattedPhone = $this->formatPhoneNumber($phoneNumber);

//             // Format pesan
//             $message = $this->formatSurveyMessage($kodeUnik, $surveyLink);

//             // URL logo dari public/img
//             $logoUrl = url('img/logo.png'); // Sesuaikan dengan nama file logo Anda

//             // Data untuk API Wablas dengan gambar
//             $data = [
//                 'phone' => $formattedPhone,
//                 'message' => $message,
//                 'image' => $logoUrl, // Menambahkan logo
//             ];

//             // Kirim menggunakan cURL
//             $curl = curl_init();

//             curl_setopt($curl, CURLOPT_HTTPHEADER, [
//                 "Authorization: " . $this->token,
//             ]);
//             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//             curl_setopt($curl, CURLOPT_URL, $this->url);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

//             $result = curl_exec($curl);
//             $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//             $error = curl_error($curl);
//             curl_close($curl);

//             // Parse response
//             $response = json_decode($result, true);

//             // Log response untuk debugging
//             Log::info('WhatsApp Blast Response', [
//                 'phone' => $formattedPhone,
//                 'kode_unik' => $kodeUnik,
//                 'http_code' => $httpCode,
//                 'response' => $response,
//                 'curl_error' => $error
//             ]);

//             $success = $httpCode == 200 && !$error && isset($response['status']) && $response['status'] == true;

//             return [
//                 'success' => $success,
//                 'http_code' => $httpCode,
//                 'response' => $response,
//                 'phone' => $formattedPhone,
//                 'kode_unik' => $kodeUnik,
//                 'error' => $error ?: ($response['message'] ?? null)
//             ];
//         } catch (\Exception $e) {
//             Log::error('WhatsApp Blast Error', [
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik,
//                 'error' => $e->getMessage()
//             ]);

//             return [
//                 'success' => false,
//                 'error' => $e->getMessage(),
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik
//             ];
//         }
//     }

//     /**
//      * Alternative: Kirim pesan dengan logo sebagai media terpisah
//      */
//     public function sendSurveyMessageWithMedia($phoneNumber, $kodeUnik, $surveyLink)
//     {
//         try {
//             // Format nomor telepon
//             $formattedPhone = $this->formatPhoneNumber($phoneNumber);

//             // URL logo dari public/img - pastikan bisa diakses publik
//             $logoUrl = url('img/logo.png'); // Sesuaikan dengan nama file logo Anda

//             // Debug: Log URL logo
//             Log::info('Logo URL', ['logo_url' => $logoUrl]);

//             // CARA 1: Coba kirim sebagai satu pesan dengan image dan text
//             $combinedData = [
//                 'phone' => $formattedPhone,
//                 'image' => $logoUrl,
//                 'message' => $this->formatSurveyMessageForImage($kodeUnik, $surveyLink)
//             ];

//             $result = $this->sendImageWithText($combinedData, $kodeUnik);

//             if ($result['success']) {
//                 return $result;
//             }

//             // CARA 2: Jika cara 1 gagal, coba kirim terpisah
//             Log::info('Trying alternative method - separate image and text');

//             // Kirim logo terlebih dahulu
//             $mediaData = [
//                 'phone' => $formattedPhone,
//                 'image' => $logoUrl,
//                 'caption' => '🏢 *Survey Kepuasan Pelanggan*'
//             ];

//             // Kirim media
//             $mediaResult = $this->sendMedia($mediaData);

//             // Log media result
//             Log::info('Media sent', [
//                 'phone' => $formattedPhone,
//                 'media_result' => $mediaResult
//             ]);

//             // Delay 3 detik antara media dan text
//             sleep(3);

//             // Format pesan text dengan link yang benar-benar clickable
//             $message = $this->formatClickableMessage($kodeUnik, $surveyLink);

//             // Data untuk pesan text
//             $textData = [
//                 'phone' => $formattedPhone,
//                 'message' => $message,
//             ];

//             // Kirim text message
//             return $this->sendTextMessage($textData, $kodeUnik);
//         } catch (\Exception $e) {
//             Log::error('WhatsApp Blast with Media Error', [
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik,
//                 'error' => $e->getMessage()
//             ]);

//             return [
//                 'success' => false,
//                 'error' => $e->getMessage(),
//                 'phone' => $phoneNumber,
//                 'kode_unik' => $kodeUnik
//             ];
//         }
//     }

//     /**
//      * Format pesan untuk dikirim bersama gambar
//      */
//     private function formatSurveyMessageForImage($kodeUnik, $surveyLink)
//     {
//         $message = "*🏢 Survey Kepuasan Pelanggan*\n\n";
//         $message .= "Terima kasih pelanggan setia bata ringan kami\n\n";
//         $message .= "Tetap dukung kami dalam memberikan pelayanan terbaik\n";
//         $message .= "📝 Isi survey dengan jujur sesuai pengalaman Anda!\n\n";

//         // Kode unik
//         $message .= "🔑 *Kode Unik:* `{$kodeUnik}`\n\n";

//         // Link survey - format sederhana untuk WhatsApp
//         $message .= "👇 *KLIK LINK SURVEY:*\n";
//         $message .= $surveyLink . "\n\n";

//         // Website link
//         $message .= "🌐 *Website:*\n";
//         $message .= "https://superiorprimasukses.co.id\n\n";

//         $message .= "_Terima kasih! 🙏_";

//         return $message;
//     }

//     /**
//      * Format pesan dengan link yang clickable
//      */
//     private function formatClickableMessage($kodeUnik, $surveyLink)
//     {
//         $message = "Terima kasih pelanggan setia bata ringan kami\n\n";
//         $message .= "Tetap dukung kami dalam memberikan pelayanan terbaik\n";
//         $message .= "📝 Isi survey dengan jujur sesuai pengalaman Anda!\n\n";

//         // Kode unik dengan format monospace
//         $message .= "🔑 *Kode Unik Anda:*\n";
//         $message .= "`{$kodeUnik}`\n\n";

//         $message .= "👇 *SURVEY LINK - KLIK DI BAWAH:*\n\n";
//         // Link harus dalam baris terpisah tanpa formatting
//         $message .= $surveyLink . "\n\n";

//         $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

//         $message .= "🌐 *WEBSITE KAMI:*\n\n";
//         $message .= "https://superiorprimasukses.co.id\n\n";

//         $message .= "_Terima kasih atas partisipasi Anda! 🙏_";

//         return $message;
//     }

//     /**
//      * Kirim gambar dengan text dalam satu request
//      */
//     private function sendImageWithText($data, $kodeUnik)
//     {
//         $curl = curl_init();

//         curl_setopt($curl, CURLOPT_HTTPHEADER, [
//             "Authorization: " . $this->token,
//         ]);
//         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//         // Coba endpoint yang berbeda untuk image + message
//         curl_setopt($curl, CURLOPT_URL, "https://tegal.wablas.com/api/send-image");
//         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

//         $result = curl_exec($curl);
//         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//         $error = curl_error($curl);
//         curl_close($curl);

//         $response = json_decode($result, true);

//         Log::info('Send Image with Text Response', [
//             'http_code' => $httpCode,
//             'response' => $response,
//             'error' => $error
//         ]);

//         $success = $httpCode == 200 && !$error && isset($response['status']) && $response['status'] == true;

//         return [
//             'success' => $success,
//             'http_code' => $httpCode,
//             'response' => $response,
//             'phone' => $data['phone'],
//             'kode_unik' => $kodeUnik,
//             'error' => $error ?: ($response['message'] ?? null)
//         ];
//     }

//     /**
//      * Helper method untuk mengirim media
//      */
//     private function sendMedia($data)
//     {
//         $curl = curl_init();

//         curl_setopt($curl, CURLOPT_HTTPHEADER, [
//             "Authorization: " . $this->token,
//         ]);
//         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//         curl_setopt($curl, CURLOPT_URL, "https://tegal.wablas.com/api/send-image"); // endpoint untuk image
//         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

//         $result = curl_exec($curl);
//         curl_close($curl);

//         return json_decode($result, true);
//     }

//     /**
//      * Helper method untuk mengirim text message
//      */
//     private function sendTextMessage($data, $kodeUnik)
//     {
//         $curl = curl_init();

//         curl_setopt($curl, CURLOPT_HTTPHEADER, [
//             "Authorization: " . $this->token,
//         ]);
//         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//         curl_setopt($curl, CURLOPT_URL, $this->url);
//         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

//         $result = curl_exec($curl);
//         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//         $error = curl_error($curl);
//         curl_close($curl);

//         $response = json_decode($result, true);
//         $success = $httpCode == 200 && !$error && isset($response['status']) && $response['status'] == true;

//         return [
//             'success' => $success,
//             'http_code' => $httpCode,
//             'response' => $response,
//             'phone' => $data['phone'],
//             'kode_unik' => $kodeUnik,
//             'error' => $error ?: ($response['message'] ?? null)
//         ];
//     }

//     /**
//      * Validasi dan format URL agar clickable di WhatsApp
//      */
//     private function formatClickableUrl($url)
//     {
//         // Pastikan URL menggunakan https://
//         if (!preg_match('/^https?:\/\//', $url)) {
//             $url = 'https://' . $url;
//         }

//         // Pastikan tidak ada spasi di awal atau akhir
//         $url = trim($url);

//         // Pastikan URL valid
//         if (filter_var($url, FILTER_VALIDATE_URL)) {
//             return $url;
//         }

//         return $url; // Return as is jika tidak bisa divalidasi
//     }

//     /**
//      * Format nomor telepon ke format internasional - buat public untuk testing
//      */
//     public function formatPhoneNumber($phoneNumber)
//     {
//         // Hapus semua karakter non-numeric
//         $phone = preg_replace('/[^0-9]/', '', $phoneNumber);

//         // Hapus leading zero dan tambahkan kode negara Indonesia
//         if (substr($phone, 0, 1) === '0') {
//             $phone = '62' . substr($phone, 1);
//         } elseif (substr($phone, 0, 2) !== '62') {
//             $phone = '62' . $phone;
//         }

//         return $phone;
//     }

//     /**
//      * Get API endpoint berdasarkan server
//      */
//     private function getApiEndpoint()
//     {
//         return $this->url;
//     }

//     /**
//      * Kirim blast ke multiple nomor
//      */
//     public function sendBulkSurvey($recipients, $surveyBaseUrl)
//     {
//         $results = [];
//         $successCount = 0;
//         $failCount = 0;

//         foreach ($recipients as $recipient) {
//             // Generate survey link dengan kode unik
//             $surveyLink = $surveyBaseUrl . '?code=' . $recipient['kode_unik'];

//             // Gunakan method dengan media untuk hasil yang lebih baik
//             $result = $this->sendSurveyMessageWithMedia(
//                 $recipient['no_telp_check_'],
//                 $recipient['kode_unik'],
//                 $surveyLink
//             );

//             $results[] = $result;

//             if ($result['success']) {
//                 $successCount++;
//             } else {
//                 $failCount++;
//             }

//             // Delay untuk menghindari rate limiting
//             usleep(2000000); // 2 detik delay karena mengirim 2 pesan (media + text)
//         }

//         return [
//             'total' => count($recipients),
//             'success' => $successCount,
//             'failed' => $failCount,
//             'results' => $results
//         ];
//     }

//     /**
//      * Validasi token dan server
//      */
//     public function validateConnection()
//     {
//         try {
//             // Test dengan mengirim ke nomor kosong untuk validasi token
//             $testData = [
//                 'phone' => '628123456789',
//                 'message' => 'Test connection'
//             ];

//             $curl = curl_init();
//             curl_setopt($curl, CURLOPT_HTTPHEADER, [
//                 "Authorization: " . $this->token,
//             ]);
//             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($testData));
//             curl_setopt($curl, CURLOPT_URL, $this->url);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
//             curl_setopt($curl, CURLOPT_TIMEOUT, 10);

//             $result = curl_exec($curl);
//             $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//             curl_close($curl);

//             $response = json_decode($result, true);

//             return [
//                 'valid' => $httpCode == 200,
//                 'message' => $response['message'] ?? 'Connection test completed',
//                 'http_code' => $httpCode
//             ];
//         } catch (\Exception $e) {
//             return [
//                 'valid' => false,
//                 'message' => $e->getMessage()
//             ];
//         }
//     }
