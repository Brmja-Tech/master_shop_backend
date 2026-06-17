<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaymobService
{
    private const BASE_URL = 'https://accept.paymob.com/api';

    public function createOrder(Order $order, array $billingData = []): array
    {
        $authHttpResponse = Http::post(self::BASE_URL . '/auth/tokens', [
            'api_key' => config('paymob.api_key'),
        ]);

        $authResponse = $authHttpResponse->throw()->json();

        $token = $authResponse['token'];
        $amountCents = (int) round(((float) $order->total) * 100);
        $merchantOrderId = 'order_' . $order->id . '_' . now()->timestamp;

        $orderHttpResponse = Http::withToken($token)
            ->post(self::BASE_URL . '/ecommerce/orders', [
                'auth_token' => $token,
                'delivery_needed' => false,
                'amount_cents' => $amountCents,
                'currency' => 'EGP',
                'merchant_order_id' => $merchantOrderId,
                'items' => [],
            ]);

        $orderResponse = $orderHttpResponse->throw()->json();

        $paymobOrderId = $orderResponse['id'];

        $paymentKeyHttpResponse = Http::post(self::BASE_URL . '/acceptance/payment_keys', [
            'auth_token' => $token,
            'amount_cents' => $amountCents,
            'expiration' => 3600,
            'order_id' => $paymobOrderId,
            'billing_data' => [
                'first_name' => $billingData['first_name'] ?? $order->user->name,
                'last_name' => $billingData['last_name'] ?? '.',
                'email' => $order->user->email ?? 'NA',
                'phone_number' => $billingData['phone'] ?? $order->user->phone,
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

        $paymentKeyResponse = $paymentKeyHttpResponse->throw()->json();

        $paymentKey = $paymentKeyResponse['token'];
        $paymentUrl = self::BASE_URL . '/acceptance/iframes/'
            . config('paymob.iframe_id')
            . '?payment_token=' . $paymentKey;

        return [
            'paymob_order_id' => $paymobOrderId,
            'payment_key' => $paymentKey,
            'payment_url' => $paymentUrl,
        ];
    }

    public function refundOrVoid(Order $order): array
    {
        if (! $order->paymob_transaction_id) {
            throw new RuntimeException('Missing Paymob transaction ID for this order.');
        }

        $token = $this->authenticate();
        $transactionId = (int) $order->paymob_transaction_id;
        $amountCents = (int) round(((float) $order->total) * 100);
        $isCaptured = $order->payment_status === PaymentStatus::Paid;

        $endpoint = $isCaptured
            ? config('paymob.refund_endpoint', self::BASE_URL . '/acceptance/void_refund/refund')
            : config('paymob.void_endpoint', self::BASE_URL . '/acceptance/void_refund/void');

        $payload = [
            'auth_token' => $token,
            'transaction_id' => $transactionId,
        ];

        if ($isCaptured) {
            $payload['amount_cents'] = $amountCents;
        }

        $response = Http::post($endpoint, $payload)->throw()->json();

        return [
            'action' => $isCaptured ? 'refund' : 'void',
            'response' => $response,
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

        return hash_equals($computed, (string) $hmac);
    }

    public function syncOrderPaymentFromWebhook(Order $order, array $payload): void
    {
        $updates = [];

        if (Arr::get($payload, 'obj.success') === true) {
            $updates['payment_status'] = PaymentStatus::Paid;
            $updates['paymob_transaction_id'] = (string) Arr::get($payload, 'obj.id');
        }

        if (Arr::get($payload, 'obj.is_refunded') === true || Arr::get($payload, 'obj.is_voided') === true) {
            $updates['payment_status'] = PaymentStatus::Refunded;
        }

        if ($updates !== []) {
            $order->update($updates);
        }
    }

    private function authenticate(): string
    {
        $authHttpResponse = Http::post(self::BASE_URL . '/auth/tokens', [
            'api_key' => config('paymob.api_key'),
        ]);

        $authResponse = $authHttpResponse->throw()->json();

        return (string) $authResponse['token'];
    }
}
