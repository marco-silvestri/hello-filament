<?php

namespace App\Models\Cms;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\PostPlanningsFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostPlannings extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false;

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    protected  static function newFactory():Factory{
    return PostPlanningsFactory::new();
    }

}
