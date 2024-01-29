<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait HasWpData
{

    protected function collectWpJson($source, $key): Collection
    {
        $rawJson = file_get_contents($source);
        $rawJson = preg_replace('/[[:cntrl:]]/', '', $rawJson);
        $collectedJSon = collect(json_decode($rawJson)->{$key});

        return $collectedJSon;
    }
}
