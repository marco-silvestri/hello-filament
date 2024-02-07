<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;


    protected $guarded = ['id'];

    protected $casts = [
        'blocks' => 'array'
    ];

    public function slug(): MorphOne
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }
}
