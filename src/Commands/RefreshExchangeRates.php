<?php

namespace Spatian\FireflyIIIXchange\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Spatian\FireflyIIIXchange\Contracts\ExchangeRateService;
use Spatian\FireflyIIIXchange\Helpers\Currency;
use Spatian\FireflyIIIXchange\Helpers\Str;
use Spatian\FireflyIIIXchange\Services\PackageService;
use ValueError;

class RefreshExchangeRates extends Command {
    private PackageService $package;
    private ExchangeRateService $service;
    private string $fireflyApi;
    private string $fireflyToken;
    private string $currency;

    protected $signature = 'xchange:refresh {--C|currency=}';
    protected $description = 'Fetches fresh exchange rates from the configured service and updates them in Firefly III';

    public function __construct() {
        parent::__construct();

        $this->package = App::make(PackageService::class);
        $this->service = App::make(ExchangeRateService::class);
        $this->fireflyApi = Str::chopEnd($this->package->config("firefly.api", ""), "/");
        $this->fireflyToken = $this->package->config("firefly.token") ?? "";
        $this->currency = $this->package->config("source_currency") ?? "";
    }

    public function handle() {
        if ($this->option('currency')) {
            $this->currency = $this->option('currency');
            $this->info("Currency option provided, using \"{$this->currency}\"");
        }

        if (Str::blank($this->currency)) {
            $this->warn("No source currency provided, exiting.");
            return;
        }

        if (!Currency::isValidCodeFormat($this->currency))
            throw new ValueError("Invalid currency code \"{$this->currency}\"");

        $fireflyCurrencies = array_map(
            fn($c) => $c["attributes"]["code"],
            $this->fetchFireflyCurrencies()
        );

        if (!in_array($this->currency, $fireflyCurrencies)) {
            $this->warn(
                "[{$this->currency}, ---]: Source currency not configured in Firefly, exiting ..."
            );
            return;
        }

        $this->info("Processing currency \"{$this->currency}\" ...");
        $rates = $this->service->all($this->currency);

        $today = now()->format("Y-m-d");

        $count = 0;
        foreach ($rates as $rate) {
            try {
                $this->line("\t{$rate}");

                if (!in_array($rate->dest, $fireflyCurrencies)) {
                    $this->warn(
                        "[{$rate->source}, {$rate->dest}]: Destination currency not configured in Firefly, skipping ..."
                    );
                    continue;
                }

                $response = Http::withToken($this->fireflyToken)->acceptJson()->post(
                    "{$this->fireflyApi}/api/v1/exchange-rates",
                    [
                        "date" => $today,
                        "from" => $rate->source,
                        "to" => $rate->dest,
                        "rate" => $rate->rate,
                    ]
                );
                /** @var \Illuminate\Http\Client\Response $response */
                $response->throw();
                $count++;
            } catch (\Throwable $th) {
                $this->error("[{$rate->source}, {$rate->dest}] Error: {$th->getMessage()}");
            }
        }

        $fetched = count($rates);
        $this->info("Done. Added {$count} out of {$fetched} fetched rates.");
    }

    private function fetchFireflyCurrencies(): array {
        $response = Http::withToken($this->fireflyToken)->acceptJson()->get(
            "{$this->fireflyApi}/api/v1/currencies"
        );
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $response->throw()->json();

        return $response["data"];
    }
}
