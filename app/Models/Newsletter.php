<?php

namespace App\Models;

use App\Enums\Cms\InternalNewsletterStatusEnum;
use App\Models\Cms\NewsletterInternal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Newsletter extends NewsletterInternal
{
    use HasFactory, SoftDeletes;
}
