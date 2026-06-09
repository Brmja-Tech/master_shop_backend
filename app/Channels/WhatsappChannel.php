<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappChannel
{
    /**
     * إعدادات beon.chat
     */
    protected string $apiBase = 'https://v3.api.beon.chat/api/v3';
    protected string $token   = '2iwDKEktifxE8TRgX1RnH46ckt8ST6pE37PUpty8AOVDgUSBhXwPd0ngcJmq';

    /**
     * يرسل OTP عبر beon.chat باستخدام form-data.
     *
     * يدعم طريقتين لاستخراج الداتا من النوتيفكيشن:
     * - toBeon($notifiable)  => المفضّل
     * - toWhatsapp($notifiable) => توافقًا مع الكود القديم
     *
     * توقُّع الداتا من النوتيفكيشن:
     *   [
     *     'phone' => '+2010...',   // إجباري
     *     'code'  => '8807',       // إجباري
     *     'name'  => 'fisal',      // اختياري
     *     'type'  => 'sms',        // اختياري (sms | whatsapp)
     *     'lang'  => 'ar',         // اختياري
     *   ]
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toBeon') && !method_exists($notification, 'toWhatsapp')) {
            // لا توجد داتا للإرسال
            return;
        }

        // أعطي أولوية لـ toBeon ولو مش موجودة أستخدم toWhatsapp (توافقًا مع القديم)
        $method = method_exists($notification, 'toBeon') ? 'toBeon' : 'toWhatsapp';
        $message = \call_user_func([$notification, $method], $notifiable);

        if (!is_array($message) || !isset($message['phone'], $message['code'])) {
            Log::error('رسالة OTP غير صالحة (مطلوب phone & code).', ['message' => $message]);
            return;
        }

        // تجهيز الحقول المطلوبة لـ beon.chat
        $phone = $this->normalizeE164($message['phone']);
        $name  = $message['name'] ?? ($notifiable->name ?? 'besohola');
        $code  = (string) $message['code'];

        $url = rtrim($this->apiBase, '/') . '/messages/otp';

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->withHeaders([
                    'beon-token' => $this->token,
                    'Accept'     => 'application/json',
                ])
                ->post($url, [
                    'phoneNumber' => $phone,
                    'name'        => $name,
                    'type'        => $message['type'] ?? 'sms',
                    'lang'        => $message['lang'] ?? 'ar',
                    'custom_code' => $code,
                ]);

            Log::info('✅ Beon API Response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->failed()) {
                Log::error('❌ فشل إرسال OTP عبر beon.chat', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return;
            }

            Log::info('✅ تم إرسال OTP بنجاح', [
                'phone' => $phone,
                'name'  => $name,
            ]);
        } catch (\Throwable $e) {
            Log::error('📛 Beon OTP Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * تطبيع الرقم لصيغة E.164:
     * 01273860271 -> +201273860271
     * 1273860271 -> +201273860271
     */
    protected function normalizeE164(string $phone): string
    {
        $phone = trim($phone);

        // إزالة أي مسافات أو شرطات
        $phone = preg_replace('/[\s\-().]/', '', $phone);

        // إذا بدأت بـ 0، غيّرها لـ 20 (مصر)
        if (strpos($phone, '0') === 0) {
            $phone = '2' . $phone;
        }

        // إذا ما فيش +، أضيفها
        if (strpos($phone, '+') !== 0) {
            $phone = '+' . $phone;
        }

        return $phone;
    }
}
