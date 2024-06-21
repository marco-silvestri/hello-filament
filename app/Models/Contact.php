<?php

namespace App\Models;

use App\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function communications() :BelongsToMany
    {
        return $this->belongsToMany(Communication::class);
    }

}
