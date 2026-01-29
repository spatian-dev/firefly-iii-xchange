<?php

namespace Spatian\FireflyIIIXchange\Helpers;

use Illuminate\Support\Str as BaseStr;

class Str extends BaseStr {
    public static function blank(string $str): bool {
        return parent::of($str)->trim()->isEmpty();
    }
}
