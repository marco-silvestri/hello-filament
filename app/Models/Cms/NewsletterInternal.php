<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Cms\InternalNewsletterStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsletterInternal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status_changed_at' => 'datetime:Y-m-d',
        'status' => InternalNewsletterStatusEnum::class,
        'json_content' => 'array',
        'send_date' => 'datetime:Y-m-d H:i:s'
    ];
}
