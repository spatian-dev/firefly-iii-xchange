<?php

namespace Spatian\FireflyIIIXchange\Providers;

use Spatian\FireflyIIIXchange\Services\PackageService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use Spatian\FireflyIIIXchange\Commands\RefreshExchangeRates;
use Spatian\FireflyIIIXchange\Contracts\ExchangeRateService;
use Spatian\FireflyIIIXchange\Enums\AvailableExchangeRateServices;
use ValueError;

final class FireflyXchangeServiceProvider extends ServiceProvider {

    public function __construct(
        Application $app,
        private PackageService $package = new PackageService(),
    ) {
        parent::__construct($app);
    }

    public function register(): void {
        $this->mergeConfigFrom($this->package->configFile(), $this->package->name());
        $this->bindStartupServices();
    }

    public function boot(): void {
        if (!$this->app->runningInConsole()) return;
        $this->bindBootServices();
        $this->registerCommands();
        $this->registerPublishing();
    }

    private function bindStartupServices(): void {
        $this->app->singleton(PackageService::class, fn() => $this->package);
    }

    private function bindBootServices(): void {
        $service = AvailableExchangeRateServices::tryFrom($this->package->config("service", ""));
        if ($service !== null) {
            $serviceHandler = $this->package->config("{$service->value}.handler");
            if ($serviceHandler === null) {
                throw new ValueError(
                    "Invalid exchange rate service handler for \"{$service->value}\""
                );
            }
            $this->app->singleton(ExchangeRateService::class, fn() => new $serviceHandler);
        }
    }

    private function registerCommands(): void {
        AboutCommand::add(
            $this->package->displayName(),
            fn() => [
                'Version' => $this->package->version(),
                'Webpage' => $this->package->website(),
            ],
        );

        $this->commands([
            RefreshExchangeRates::class,
        ]);
    }

    private function registerPublishing(): void {
        $this->publishes(
            [
                $this->package->configFile() => config_path($this->package->name() . '.php'),
            ],
            'config'
        );
    }
}
