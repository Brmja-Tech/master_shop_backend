<?php

return [
    'api_key' => env('PAYMOB_API_KEY'),
    'integration_id' => env('PAYMOB_INTEGRATION_ID'),
    'iframe_id' => env('PAYMOB_IFRAME_ID'),
    'hmac_secret' => env('PAYMOB_HMAC_SECRET'),
    'callback_url' => env('PAYMOB_CALLBACK_URL', 'https://mastershop.betamoneta.com/api/paymob/callback'),
    'response_url' => env('PAYMOB_RESPONSE_URL', 'https://mastershop.betamoneta.com/api/paymob/response'),
    'refund_endpoint' => env('PAYMOB_REFUND_ENDPOINT', 'https://accept.paymob.com/api/acceptance/void_refund/refund'),
    'void_endpoint' => env('PAYMOB_VOID_ENDPOINT', 'https://accept.paymob.com/api/acceptance/void_refund/void'),
];
