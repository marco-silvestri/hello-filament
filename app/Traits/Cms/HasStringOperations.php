<?php

namespace App\Traits\Cms;

use Illuminate\Support\Collection;

trait HasStringOperations
{
    protected function prepStringForQuery($rawSearchTerms, $addFuzzyness):array
    {
        $rawSearchTerms = $this->removeNonAlphaNumerics($rawSearchTerms);
        $rawSearchTerms = $this->filterStrings($rawSearchTerms);
        
        if ($addFuzzyness) {
            $rawSearchTerms = $this->addFuzzyness($rawSearchTerms);
        }

        return $rawSearchTerms
            ->values()
            ->toArray();
    }

    protected function deFuzz(string $keyword):string{
        return str_replace('%','',$keyword);
    }

    protected function addFuzzyness(Collection $rawSearchTerms): Collection
    {
        return $rawSearchTerms->map(fn ($el) => "%{$el}%");
    }

    protected function filterStrings(string $filterableString, ?array $filters = null): Collection
    {
        if ($filters === null) {
            $filters = [
                fn ($el) => $el != null && strlen($el) > 1,
            ];
        }
        $filterableString=explode(" ",$filterableString);
        $collection = collect($filterableString);

        foreach ($filters as $filter) {
            $collection = $collection->filter($filter);
        }

        return $collection->unique();
    }

    protected function removeNonAlphaNumerics(string $rawString, string $replaceWith = " "): string
    {
        return preg_replace("/[^a-zA-Z0-9À-ÿ]/u", $replaceWith, $rawString);
    }
}
