<?php

namespace Spatian\FireflyIIIXchange\Helpers;

use InvalidArgumentException;

class Currency {
    public static function isValidCodeFormat (string $code): bool {
        return preg_match('/^[A-Z]{3}$/', $code) === 1;
    }

    /**
     * Splits a currency code pair into its components
     *
     * @param string $pair Currency code pair
     * @return array<string> Currency code pair components
     */
    public static function fromPair (string $pair): array {
        if (mb_strlen($pair) !== 6)
            throw new InvalidArgumentException("Expected a string of 6 exactly characters");
        return mb_str_split($pair, 3);
    }
}
