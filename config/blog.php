<?php

return [
   'footer'=>['company_name'=>'Quine srl',
              'company_address'=>'Via Spadolini, 7',
              'company_city'=>'Milano',
              'company_cap'=>'20141',
              'company_prov'=>'MI',
              'company_country'=>'Italia',
              'company_phone'=>' +39 02 881841',
              'company_fax'=>'+39 02 88184301',
              'company_piva'=>'002100157',
              'company_email'=>'info@quine.it',
              'company_editorial_email'=>'Redazione.Dimensionepulito@quine.it',
              'data_protection_officer_name'=>'Alessandro Bonelli',
              'data_protection_officer_email'=>'dpo@lswr.it'

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
];
