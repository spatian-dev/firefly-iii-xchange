<?php

namespace Spatian\FireflyIIIXchange\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Spatian\FireflyIIIXchange\Contracts\ExchangeRateService;
use Spatian\FireflyIIIXchange\Helpers\Currency;
use Spatian\FireflyIIIXchange\Helpers\Number;
use Spatian\FireflyIIIXchange\Types\ExchangeRate;
use ValueError;

class ExchangeRateDotHost implements ExchangeRateService {
    private const API_BASE = 'http://api.exchangerate.host';

    private ?string $accessKey = null;
    private PackageService $package;

    public function __construct() {
        $this->package = App::make(PackageService::class);
        $this->accessKey = $this->package->config("exchangerate_host.key");
    }

    public function convert(string $source, array | string $dests): array {
        return $this->request($source, $dests);
    }

    public function all(string $source = "USD"): array { return $this->request($source); }

    private function liveRatesUrl(array $queryParts = []): string {
        return self::API_BASE .
            "/live?access_key={$this->accessKey}" .
            (count($queryParts) < 1 ? "" : '&' . implode("&", $queryParts));
    }

    private function request(?string $source = null, array | string | null $dests = null): array {
        $parts = [];

        if ($source !== null) {
            $parts[] = "source=$source";
        }

        if ($dests !== null) {
            if (!is_array($dests)) $dests = [$dests];
            if (count($dests) < 1)
                throw new InvalidArgumentException("Expected at least one destination currency");
            $parts[] = "currency=" . urlencode(implode(',', $dests));
        }

        $response = Http::acceptJson()->get($this->liveRatesUrl($parts));
        /** @var \Illuminate\Http\Client\Response $response */
        $json = $response->throw()->json();

        if ($response['success'] !== true)
            throw new RequestException($response);

        $result = [];
        foreach ($json["quotes"] as $pair => $rate) {
            $rate = Number::validateFloat($rate);
            if ($rate === false)
                throw new ValueError("Invalid rate \"$rate\" for pair \"$pair\"");
            [$source, $dest] = Currency::fromPair($pair);
            $result[] = new ExchangeRate($source, $dest, $rate);
        }
        return $result;
    }
}
