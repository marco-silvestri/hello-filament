<?php

return [
    'newsletter' => 'internal',
    'newsletter_table' => 'newsletters',
    'newsletter_status_enum' => \App\Enums\Cms\InternalNewsletterStatusEnum::class,
    'newsletter_default_status_enum' => \App\Enums\Cms\InternalNewsletterStatusEnum::DRAFT->getValue(),
    'internal_newsletter_api' => [
        'user' => env('NEWSLETTER_API_USER', 'admin'),
        'password' => env('NEWSLETTER_API_PASSWORD', 'admin'),
    ],
    'internal_newsletter_addresses' => [
        'preview' => explode(',', env('NEWSLETTER_PREVIEW_ADDRESSES', 'admin')),
        'alert' => explode(',',env('NEWSLETTER_ALERT_ADDRESSES', 'admin')),
        'from' => [
            'alias' => env('NEWSLETTER_FROM_ALIAS', 'admin'),
            'address' => env('NEWSLETTER_FROM_ADDRESS', 'admin'),
        ],
        'reply' => env('NEWSLETTER_REPLY_ADDRESS', 'admin'),
    ],
    'post_caching' => [
        'enabled' => false,
        'ttl' => 60 * 60 * 2, // 2 hours
    ],
    'default_alt' => env('APP_NAME', 'Blog News'),
];
