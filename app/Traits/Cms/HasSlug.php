<?php

namespace App\Traits\Cms;

use App\Models\Slug;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSlug {

    public function slug(): MorphOne
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }

    public function getHumanizedSluggableType()
    {
        return strtolower(str_replace('App\Models\\','',$this->slug->sluggable_type));
    }
}
