<?php

return [
    'layout' => [
        'has_profile_box' => false,
        'has_breadcrumbs' => false,
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
        'follow_footer_btn' => env('FOLLOW_FOOTER_BTN', false),
        'services' => [
            'facebook' => [
                'uri' => 'https://www.facebook.com/sharer/sharer.php?u=',
                'follow_url' => env('FOLLOW_FB','https://www.facebook.com'),
                'text' => false,
                'icon' => 'bi-facebook',
                'color' => '#3b5998',
            ],
            'x' => [
                'uri' => 'https://x.com/intent/post?url=',
                'follow_url' => env('FOLLOW_X','https://x.com/'),
                'text' => true,
                'icon' => 'bi-twitter-x',
                'color' => '#000',
            ],
            'linkedin' => [
                'uri' => 'https://www.linkedin.com/shareArticle?mini=true&url=',
                'follow_url' => env('FOLLOW_LINKEDIN','https://it.linkedin.com/'),
                'text' => false,
                'icon' => 'bi-linkedin',
                'color' => '#0e76a8',
            ],
            'pinterest' => [
                'uri' => 'https://pinterest.com/pin/create/button/?url=',
                'follow_url' => env('FOLLOW_PINTEREST',''),
                'text' => false,
                'icon' => 'bi-pinterest',
                'color' => '#c8232c',
            ],
            'youtube' => [
                'uri' => '',
                'follow_url' => env('FOLLOW_YOUTUBE','https://www.youtube.com/'),
                'text' => false,
                'icon' => 'bi-youtube',
                'color' => '#c8232c',
            ],
            'whatsapp' => [
                'uri' => 'https://wa.me/?text=',
                'follow_url' => '',
                'text' => false,
                'icon' => 'bi-whatsapp',
                'color' => '#25d366',
            ],
        ],
    ]
];
