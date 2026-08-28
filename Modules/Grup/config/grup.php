<?php

return [
    // Satu-satunya tujuan outbound. Nilai ini hanya boleh datang dari konfigurasi
    // deployment, tidak pernah dari request pengguna atau metadata cabang.
    'hub_url' => env('GRUP_HUB_URL', env('SAAS_CENTRAL_HUB_URL', 'https://hub.simgos.id')),
    'hub_token' => env('GRUP_HUB_TOKEN', ''),
    'hub_hmac_secret' => env('GRUP_HUB_HMAC_SECRET', ''),
    'instance_id' => env('GRUP_INSTANCE_ID'),
    'timeout' => (int) env('GRUP_HUB_TIMEOUT', 15),
    'connect_timeout' => (int) env('GRUP_HUB_CONNECT_TIMEOUT', 5),
    'timestamp_tolerance' => (int) env('GRUP_HUB_TIMESTAMP_TOLERANCE', 300),
    'max_clinical_rows' => (int) env('GRUP_MAX_CLINICAL_ROWS', 100),
    'reverb' => [
        'enabled' => (bool) env('GRUP_REVERB_ENABLED', false),
        'scheme' => env('GRUP_REVERB_SCHEME', 'wss'),
        'host' => env('GRUP_REVERB_HOST'),
        'port' => (int) env('GRUP_REVERB_PORT', 443),
        'app_key' => env('GRUP_REVERB_APP_KEY'),
        'channel_prefix' => env('GRUP_REVERB_CHANNEL_PREFIX', 'private-grup.instance.'),
    ],
];
