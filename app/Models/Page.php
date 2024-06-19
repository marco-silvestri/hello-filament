<?php

namespace App\Models;

use App\Traits\Cms\HasSlug;
use App\Traits\Cms\HasHierarchy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory, SoftDeletes, HasSlug, HasHierarchy;


    protected $guarded = ['id'];

    protected $casts = [
        'json_content' => 'array'
    ];
}
