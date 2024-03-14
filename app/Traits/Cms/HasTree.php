<?php

namespace App\Traits\Cms;

use Illuminate\Support\Collection;

trait HasTree {

    public static function buildHierarchyTree($entities, $parentId = null):Collection
    {
        $branch = [];

        foreach ($entities as $entity) {
            if ($entity->parent_id === $parentId) {
                $children = self::buildHierarchyTree($entities, $entity->id);
                if ($children) {
                    $entity->replies = $children;
                }
                $branch[] = $entity;
            }
        }

        return collect($branch)->sortBy('id');
    }
}
