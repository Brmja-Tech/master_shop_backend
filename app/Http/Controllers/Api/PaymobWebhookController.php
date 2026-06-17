<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymobService;
use Illuminate\Http\Request;

class PaymobWebhookController extends Controller
{
    public function __invoke(Request $request, PaymobService $paymobService)
    {
        if (! $paymobService->verifyWebhook($request->all())) {
            return response('unauthorized', 401);
        }

        $order = Order::where('paymob_order_id', $request->input('obj.order.id'))->first();

        if ($order) {
            $paymobService->syncOrderPaymentFromWebhook($order, $request->all());
        }

        return response('ok', 200);
    }
}
