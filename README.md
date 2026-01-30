# 💱 Firefly III Xchange
**An exchange rates API services integration for Firefly III**

Firefly III Xchange is a Laravel package that enhances [Firefly III](https://github.com/firefly-iii/firefly-iii) with automated exchange rate support. It connects to external exchange rate providers and makes up-to-date currency data available to a Firefly III installation.

### 🚀 Features
- 📊 Fetches current exchange rates from configurable API services
- 💸 Seamless integration with Firefly III’s currency system
- 📦 Easy setup in a Firefly III instance
- ⚙️ Simple yet flexible configuration for selecting rate providers

## 📦 Installation
Installation is easy. Simply navigate to your self-hosted Firefly III installation folder and run the following command
```sh
composer require spatian-dev/firefly-iii-xchange
```
> 🛠️ For now, only self-hosted instances are supported. We're looking to add support for containerized deployments. Contributions are welcome, please visit the [Contributing](#contributing) section below.

## ⚙️ Configuration
After installation, configuration is easy via the `.env` file.

> 🧑🏻‍💻 **For Advanced Users And Developers**\
> Although all settings right now are configurable directly in your `.env` file,
you can also publish the package’s settings
> ```sh
> php artisan vendor:publish --provider="Spatian\FireflyIIIXchange\Providers\FireflyXchangeServiceProvider" --tag=config
> ```

### Firefly III Personal Access Token
In order to update the exchange rates in your Firefly III administration, a personal access token (PAT) must be configured. 
```sh
XCHANGE_FIREFLY_TOKEN=<your-token-here>
```
You can create and manage PATs in your Firefly III administration panel. Consult the [Firefly III documentation](https://docs.firefly-iii.org/how-to/firefly-iii/features/api/#personal-access-tokens) for more details.

### Default Exchange Source Currency
Configure the source currency for which the exchange rates will be fetched here.
```sh
XCHANGE_CURRENCY=USD
```
This is can be overriden when running the `xchange:refresh` command. See the [Usage](#usage) section below.

### External Exchange Rates Service
```sh
XCHANGE_SERVICE=<external_api>
```
If this variable is left empty, Firefly III Xchange will be disabled and will not update exchange rates.
Currently supported services are listed below.

> ⚠️ **Disclaimer:** Please note that the authors of this package maintain no relationship or affiliation with any of the services listed above.

#### Exchangerate.host
[Exchangerate.host](https://exchangerate.host) is a paid service with a free tier.
- Use `XCHANGE_SERVICE=exchangerate_host` to enable this service.
- Configure your API key for this service using `XCHANGE_SERVICE=<your-api-key>`

## Usage

### Manually Running The Command
To exchange rates using your configured service and currency, simply run
```sh
php artisan xchange:refresh
```

You can override the default source currency for which exchange rates are fetched. For example, to use `CAD` as a source currency, run the following command
```sh
php artisan xchange:refresh -C CAD
```

### Automating Updates Using Cron
Cron is a very powerful time-based scheduler that can be used to automate exchange rates updates.
To do so, edit your file
```sh
crontab -e
```
Then add as many lines as necessary to schedule updates. These lines will look something like this
```
* * * * * php <path-to-firefly-iii>/artisan xchange:refresh
```
Make sure your replace `<path-to-firefly-iii>` with the actual absolute path to your Firefly III installation. The asterisk at the beginning are where the schedule can be configured, see [Cron on Wikipedia](https://en.wikipedia.org/wiki/Cron) and the [Cron article on Firefly III's documentation](https://docs.firefly-iii.org/how-to/firefly-iii/advanced/cron/) for more details.

For example, the following cron lines will fetch CAD exchange rates every day at 1:00 AM, and USD exchange rates every day at 2:00 AM
```
0 1 * * * php <path-to-firefly-iii>/artisan xchange:refresh -C CAD
0 2 * * * php <path-to-firefly-iii>/artisan xchange:refresh -C USD
```

## 📃 License
Firefly III Xchange is free and open-source software released under the Apache 2.0 license. See [LICENSE](LICENSE) for more information.

## 🧑🏻‍💻 Contributing
We are currently looking to:
- Expand the available external exchange rate services with more options
- Add support for containerized deployments

If you'd like to contribute, please consult the [contribution guide](CONTRIBUTING.md).
