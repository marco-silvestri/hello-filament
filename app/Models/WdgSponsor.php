<?php

namespace App\Models;

use App\Scopes\ScopeVisible;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class WdgSponsor extends Model
{
    use HasFactory, ScopeVisible;

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

    protected function src():Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => json_decode($attributes['json_content'])->img
        );
    }

    protected function alt():Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => json_decode($attributes['json_content'])->alt
        );
    }

    public static function getAll():Collection
    {
        return WdgSponsor::visible()
            ->orderBy('cardinality')
            ->get();
    }
}
