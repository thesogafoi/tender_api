<?php

namespace App\Traits;

trait TypesTrait
{
    public static function types()
    {
        return self::TYPES;
    }

    public static function getValuesType($type)
    {
        if (in_array($type, self::types())) {
            return self::TYPEDEFINITION[$type];
        }
    }
}
