<?php

return [
    'layout' => [
        'has_profile_box' => false,
    ],
    'google_ads_key'=>env('GOOGLE_ADS_KEY',null),
    'has_quine_newsletter' => env('HAS_QUINE_NEWSLETTER', false),
    'quine_key' => [
        'base_address' => 'https://wscrm.lswr.it/api/Forms/',
        'newsletter_form_route' => 'detail',
        'newsletter_action' => 'detail',
        'id' => env('QUINE_ID'),
        'api_key' => env('QUINE_API_KEY'),
    ],
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
    'sharing' => [
        'services' => [
            'facebook' => [
                'uri' => 'https://www.facebook.com/sharer/sharer.php?u=',
                'text' => false,
                'icon' => 'bi-facebook',
                'color' => '#3b5998',
            ],
            'x' => [
                'uri' => 'https://x.com/intent/post?url=',
                'text' => true,
                'icon' => 'bi-twitter-x',
                'color' => '#000',
            ],
            'linkedin' => [
                'uri' => 'https://www.linkedin.com/shareArticle?mini=true&url=',
                'text' => false,
                'icon' => 'bi-linkedin',
                'color' => '#0e76a8',
            ],
            'pinterest' => [
                'uri' => 'https://pinterest.com/pin/create/button/?url=',
                'text' => false,
                'icon' => 'bi-pinterest',
                'color' => '#c8232c',
            ],
            'whatsapp' => [
                'uri' => 'https://wa.me/?text=',
                'text' => false,
                'icon' => 'bi-whatsapp',
                'color' => '#25d366',
            ],
        ],
    ]
];
