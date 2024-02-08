<?php

namespace App\Models;

use App\Enums\Cms\CommentStatusEnum;
use App\Traits\Cms\HasAuthor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes, HasAuthor;

    protected $guarded = ['id'];

    protected $casts = [
        'status_changed_at' => 'datetime:Y-m-d',
        'status' => CommentStatusEnum::class,
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }
}
