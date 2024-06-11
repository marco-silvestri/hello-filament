<?php

namespace App\Traits\Cms;

use App\Models\HomePageSetting;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasGroup{

    public function groups(): MorphMany
    {
        return $this->morphMany(HomePageSetting::class, 'groupable');
    }

}
