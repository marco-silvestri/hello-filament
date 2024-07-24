<?php

namespace App\Models\Cms;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sponsor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function contacts():HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
