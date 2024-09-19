<?php

namespace App\Traits\Cms;

trait HasPostsCaching {

    protected function getTtl():int
    {
        $postCachingEnabled = config('cms.post_caching.enabled');
        $ttl = config('cms.post_caching.ttl');

        if($postCachingEnabled
            && gettype($ttl) === 'integer')
        {
            return $ttl;
        }

        return 0;
    }

    protected function getFlexibleTtl():array
    {
        $postCachingEnabled = config('cms.post_caching.enabled');

        if($postCachingEnabled)
        {
            return [600, 1200];
        }

        return [1,2];
    }
}
