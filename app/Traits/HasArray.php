<?php

namespace App\Traits;

trait HasArray{

    public static function toArray():array
    {
        $values = [];

        foreach (self::cases() as $case) {
            $values[$case->getLabel()] = $case->getValue();
        }

        return $values;
    }

    public static function getValues():array
    {
        return array_values(self::toArray());
    }

    public static function getLabels():array
    {
        return array_keys(self::toArray());
    }
}
