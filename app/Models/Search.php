<?php

namespace App\Models;

use App\Models\Term;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Search extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false;

    public function users():BelongsToMany{
        return $this->belongsToMany(User::class);
    }

    public function term():BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
