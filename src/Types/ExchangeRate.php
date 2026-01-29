<?php

namespace Spatian\FireflyIIIXchange\Types;

class ExchangeRate {
    public function __construct(
        public string $source, public string $dest, public float $rate
    ) {}

    public function __toString() {
        return " 1 {$this->source} -> {$this->rate} {$this->dest}";
    }
}
