<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    public function createOrder(Order $order): array
    {
        Log::info('Paymob auth request started', [
            'order_id' => $order->id,
        ]);

        $authHttpResponse = Http::post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => config('paymob.api_key'),
        ]);

        Log::info('Paymob auth response received', [
            'order_id' => $order->id,
            'status' => $authHttpResponse->status(),
            'body' => $authHttpResponse->json(),
        ]);

        $authResponse = $authHttpResponse->throw()->json();

        $token = $authResponse['token'];
        $amountCents = (int) round(((float) $order->total) * 100);
        $merchantOrderId = 'order_' . $order->id . '_' . now()->timestamp;

        Log::info('Paymob ecommerce order request started', [
            'order_id' => $order->id,
            'merchant_order_id' => $merchantOrderId,
            'amount_cents' => $amountCents,
        ]);

        $orderHttpResponse = Http::withToken($token)
            ->post('https://accept.paymob.com/api/ecommerce/orders', [
                'auth_token' => $token,
                'delivery_needed' => false,
                'amount_cents' => $amountCents,
                'currency' => 'EGP',
                'merchant_order_id' => $merchantOrderId,
                'items' => [],
            ]);

        Log::info('Paymob ecommerce order response received', [
            'order_id' => $order->id,
            'status' => $orderHttpResponse->status(),
            'body' => $orderHttpResponse->json(),
        ]);

        $orderResponse = $orderHttpResponse->throw()->json();

        $paymobOrderId = $orderResponse['id'];

        Log::info('Paymob payment key request started', [
            'order_id' => $order->id,
            'paymob_order_id' => $paymobOrderId,
            'integration_id' => config('paymob.integration_id'),
            'iframe_id' => config('paymob.iframe_id'),
            'callback_url' => config('paymob.callback_url'),
            'response_url' => config('paymob.response_url'),
            'amount_cents' => $amountCents,
        ]);

        $paymentKeyHttpResponse = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', [
            'auth_token' => $token,
            'amount_cents' => $amountCents,
            'expiration' => 3600,
            'order_id' => $paymobOrderId,
            'billing_data' => [
                'first_name' => $order->user->name,
                'last_name' => '.',
                'email' => $order->user->email ?? 'NA',
                'phone_number' => $order->user->phone,
                'apartment' => 'NA',
                'floor' => 'NA',
                'street' => 'NA',
                'building' => 'NA',
                'city' => 'NA',
                'country' => 'NA',
                'shipping_method' => 'NA',
                'postal_code' => 'NA',
                'state' => 'NA',
            ],
            'currency' => 'EGP',
            'integration_id' => config('paymob.integration_id'),
            'notification_url' => config('paymob.callback_url'),
            'redirection_url' => config('paymob.response_url'),
        ]);

        Log::info('Paymob payment key response received', [
            'order_id' => $order->id,
            'paymob_order_id' => $paymobOrderId,
            'status' => $paymentKeyHttpResponse->status(),
            'body' => $paymentKeyHttpResponse->json(),
        ]);

        $paymentKeyResponse = $paymentKeyHttpResponse->throw()->json();

        $paymentKey = $paymentKeyResponse['token'];
        $paymentUrl = 'https://accept.paymob.com/api/acceptance/iframes/'
            . config('paymob.iframe_id')
            . '?payment_token=' . $paymentKey;

        Log::info('Paymob payment URL generated', [
            'order_id' => $order->id,
            'paymob_order_id' => $paymobOrderId,
            'payment_url' => $paymentUrl,
        ]);

        return [
            'paymob_order_id' => $paymobOrderId,
            'payment_key' => $paymentKey,
            'payment_url' => $paymentUrl,
        ];
    }

    public function verifyWebhook(array $data): bool
    {
        $hmac = $data['hmac'] ?? null;

        if (! $hmac) {
            return false;
        }

        $fields = [
            'obj.amount_cents',
            'obj.created_at',
            'obj.currency',
            'obj.error_occured',
            'obj.has_parent_transaction',
            'obj.id',
            'obj.integration_id',
            'obj.is_3d_secure',
            'obj.is_auth',
            'obj.is_capture',
            'obj.is_refunded',
            'obj.is_standalone_payment',
            'obj.is_voided',
            'obj.order.id',
            'obj.owner',
            'obj.pending',
            'obj.source_data.pan',
            'obj.source_data.sub_type',
            'obj.source_data.type',
            'obj.success',
        ];

        $concatenated = '';

        foreach ($fields as $field) {
            $value = Arr::get($data, $field, '');

            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $concatenated .= (string) $value;
        }

        $computed = hash_hmac('sha512', $concatenated, (string) config('paymob.hmac_secret'));

        Log::info('Paymob webhook verification attempted', [
            'paymob_order_id' => Arr::get($data, 'obj.order.id'),
            'transaction_id' => Arr::get($data, 'obj.id'),
            'computed_hmac' => $computed,
            'received_hmac' => $hmac,
            'success' => Arr::get($data, 'obj.success'),
        ]);

        return hash_equals($computed, (string) $hmac);
    }
}
