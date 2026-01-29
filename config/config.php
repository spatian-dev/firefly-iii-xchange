<?php

return [
    "firefly" => [
        "api" => env('XCHANGE_FIREFLY_API', config("app.url")),
        "token" => env('XCHANGE_FIREFLY_TOKEN'),
    ],
    "service" => env('XCHANGE_SERVICE'),
    "source_currency" => env('XCHANGE_CURRENCY', "USD"),

    "exchangerate_host" => [
        "handler" => Spatian\FireflyIIIXchange\Services\ExchangeRateDotHost::class,
        "key" => env('XCHANGE_KEY')
    ],
];
