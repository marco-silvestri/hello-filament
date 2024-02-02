<?php

namespace App\Models;

use App\Enums\HookEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Snippet extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
}
