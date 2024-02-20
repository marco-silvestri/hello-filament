<?php

return [
    'newsletter' => 'internal',
    'newsletter_table' => 'newsletters',
    'newsletter_status_enum' => \App\Enums\Cms\InternalNewsletterStatusEnum::class,
    'newsletter_default_status_enum' => \App\Enums\Cms\InternalNewsletterStatusEnum::DRAFT->getValue(),
];
