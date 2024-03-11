<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WdgSponsor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'json_content' => 'array'
    ];


    protected function hasProblem(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $decoded = json_decode($attributes['json_content']);
                return isset($decoded->img);
            }
        );
    }
}
