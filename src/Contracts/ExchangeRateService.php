<?php

namespace Spatian\FireflyIIIXchange\Contracts;

use Spatian\FireflyIIIXchange\Types\ExchangeRate;

interface ExchangeRateService {
    // function convert(string $source, array | string $dests): ExchangeRate;

    /**
     * Gets the latest rates of convertion for the source currency
     * into one or more destination currency
     *
     * @param string $source Source currency
     * @param array<string>|string $dest Source currency
     *
     * @return array<ExchangeRate>
     */
    function convert(string $source, array | string $dests): array;

    /**
     * Gets the latest rates of convertion for the source currency
     * into all available currency
     *
     * @param string|null $source Source currency.
     *
     * @return array<ExchangeRate>
     */
    function all(string $source): array;
}
