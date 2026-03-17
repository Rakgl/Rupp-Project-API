<?php

return [
    'api_url' => env('PAYWAY_API_URL', 'https://checkout-sandbox.ababank.com/api/v2'),
    'api_key' => env('PAYWAY_API_KEY', 'default_key'),
    'merchant_id' => env('PAYWAY_MERCHANT_ID', 'default_merchant'),
    'lifetime' => env('PAYWAY_LIFETIME', 15),
    'api_purchase' => '/payments/purchase',
    'api_transaction' => '/payments/check-transaction',
];
