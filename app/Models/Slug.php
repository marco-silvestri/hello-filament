<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Slug extends Model
{
    use HasFactory;


    protected $guarded = ['id'];

    public function sluggable(): MorphTo
    {
        return $this->morphTo();
    }
}
