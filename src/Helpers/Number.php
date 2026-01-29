<?php

namespace Spatian\FireflyIIIXchange\Helpers;

class Number {
    public static function validateFloat (string|int|float $value): float | false {
        $type = gettype($value);
        return match ($type) {
            "integer" => $value,
            "double" => $value,
            "string" => filter_var($value, FILTER_VALIDATE_FLOAT),
        };
    }
}
