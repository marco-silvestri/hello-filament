<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false;

    public function searches():HasMany
    {
        return $this->hasMany(Search::class);
    }
}
