<?php

namespace App\Models\Cms;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostSettings extends Model
{
    use HasFactory;

    public $table = "post_settings";
    protected $guarded = ['id'];
    public $timestamps = false;

    public function post():BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
