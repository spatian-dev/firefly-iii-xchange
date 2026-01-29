<?php

namespace Spatian\FireflyIIIXchange\Services;

use Illuminate\Support\Facades\App;
use Nette\NotImplementedException;
use Spatian\FireflyIIIXchange\Contracts\ExchangeRateService;

class DummyExchangeRatesService implements ExchangeRateService {
    private PackageService $package;

    public function __construct() {
        $this->package = App::make(PackageService::class);
    }

    public function convert(string $source, array | string $dests): array {
        return $this->noop();
    }

    public function all(string $source = "USD"): array {
        return $this->noop();
    }

    private function noop(): array {
        throw new NotImplementedException("{$this->package->displayName()} is disabled.");
    }
}
