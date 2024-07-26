<?php

namespace App\Traits\Cms;

trait HasJsonOperations
{
    use HasStringOperations;

    public static function extractMeaningfulContent(array $jsonData): string
    {
        $content = '';

        foreach ($jsonData as $block) {
            if ($block['type'] === 'paragraph') {
                $content .= $block['data']['content'] . ' ';
            }
        }

        $cleanedContent = strip_tags($content);

        return $cleanedContent;
    }
}
