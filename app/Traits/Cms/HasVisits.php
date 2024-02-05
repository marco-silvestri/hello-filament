<?php

namespace App\Traits\Cms;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasVisits{

    public function visits(): MorphToMany
    {
        return $this->morphToMany(Visit::class, 'visitable');
    }

}
