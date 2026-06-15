<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymobController extends Controller
{
    public function callback(Request $request, PaymobService $paymobService)
    {
        Log::info('Paymob callback received', [
            'payload' => $request->all(),
        ]);

        if (! $paymobService->verifyWebhook($request->all())) {
            return response('unauthorized', 401);
        }

        $order = Order::where('paymob_order_id', $request->input('obj.order.id'))->first();

        if ($order && $request->input('obj.success') === true) {
            $order->update([
                'payment_status' => PaymentStatus::Paid,
                'paymob_transaction_id' => (string) $request->input('obj.id'),
            ]);
        }

        return response('ok', 200);
    }

    public function response(Request $request)
    {
        Log::info('Paymob response received', [
            'payload' => $request->all(),
        ]);

        $success = filter_var($request->input('success'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $pending = filter_var($request->input('pending'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $transactionId = $request->input('id') ?? $request->input('txn_response_code');
        $orderId = $request->input('order') ?? $request->input('merchant_order_id') ?? $request->input('order_id');

        $title = 'Payment Status';
        $heading = 'Payment status received';
        $message = 'We received your payment response successfully.';
        $accent = '#1d4ed8';

        if ($success === true) {
            $title = 'Payment Successful';
            $heading = 'Payment completed successfully';
            $message = 'Your payment was confirmed successfully.';
            $accent = '#15803d';
        } elseif ($pending === true) {
            $title = 'Payment Pending';
            $heading = 'Payment is still pending';
            $message = 'Your payment is under processing. Please check again shortly.';
            $accent = '#b45309';
        } elseif ($success === false) {
            $title = 'Payment Failed';
            $heading = 'Payment was not completed';
            $message = 'Your payment was declined or failed. Please try again.';
            $accent = '#b91c1c';
        }

        $transactionHtml = $transactionId
            ? '<p><strong>Transaction ID:</strong> ' . e((string) $transactionId) . '</p>'
            : '';
        $orderHtml = $orderId
            ? '<p><strong>Order Reference:</strong> ' . e((string) $orderId) . '</p>'
            : '';

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #fff7ed, #fee2e2);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1f2937;
        }
        .card {
            width: min(92vw, 520px);
            background: #ffffff;
            border-radius: 20px;
            padding: 32px 28px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
            text-align: center;
        }
        .badge {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            margin: 0 auto 20px;
            background: {$accent};
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
        }
        h1 {
            margin: 0 0 12px;
            font-size: 28px;
        }
        p {
            margin: 8px 0;
            line-height: 1.6;
        }
        .meta {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">i</div>
        <h1>{$heading}</h1>
        <p>{$message}</p>
        <div class="meta">
            {$orderHtml}
            {$transactionHtml}
        </div>
    </div>
</body>
</html>
HTML;

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
