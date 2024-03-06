<?php

namespace App\Models;

use Awcodes\Curator\Models\Media as ModelsMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Media extends Model
{
    protected $guarded = ['id'];

    public function getImagesList()
    {
        return $this->query()->select(DB::raw('title, path as value'))->get()->toArray();
    }

    protected function fullPath(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) =>
            asset('storage/'. $attributes['path'])
        );
    }
}
