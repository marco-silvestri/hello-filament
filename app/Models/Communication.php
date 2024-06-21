<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Communication extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function post() :BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function contacts() :BelongsToMany
    {
        return $this->belongsToMany(Contact::class);
    }
}
