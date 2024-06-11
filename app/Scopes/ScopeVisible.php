<?php

namespace App\Scopes;

trait ScopeVisible {

    public function scopeVisible($query)
    {
        return $query
            ->where('is_visible', true);
    }
}
