# Filament Shlink

[![Latest Version on Packagist](https://img.shields.io/packagist/v/adereksisusanto/filament-shlink.svg?style=flat-square)](https://packagist.org/packages/adereksisusanto/filament-shlink)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/adereksisusanto/filament-shlink/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/adereksisusanto/filament-shlink/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/adereksisusanto/filament-shlink/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/adereksisusanto/filament-shlink/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/adereksisusanto/filament-shlink.svg?style=flat-square)](https://packagist.org/packages/adereksisusanto/filament-shlink)

Filament panel integration for [Shlink](https://shlink.io), a self-hosted URL shortener. Manage short URLs and tags directly from your Filament panel.

## Installation

```bash
composer require adereksisusanto/filament-shlink
```

### Publish Config

```bash
php artisan vendor:publish --tag="filament-shlink-config"
```

### Register Plugin

```php
// AppServiceProvider.php or PanelProvider
use Adereksisusanto\FilamentShlink\FilamentShlinkPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(FilamentShlinkPlugin::make());
}
```

## Configuration

Set your Shlink server URL and API key in `.env`:

```env
SHLINK_SERVER_URL=https://your-shlink-server.com
SHLINK_API_KEY=your-api-key
```

Or configure them via **Shlink Settings** page in the Filament admin panel after registration.

Published config (`config/filament-shlink.php`):

```php
return [
    'server_url' => env('SHLINK_SERVER_URL', ''),
    'api_key' => env('SHLINK_API_KEY', ''),
];
```

## Features

- **Short URLs** — List, create, and edit short URLs
- **Tags** — List, rename, and delete tags
- **Settings** — Configure server connection from the admin panel

## Usage

Once registered, the plugin adds two menu items to your Filament panel:

- **Short URLs** — View all short URLs, create new ones, edit existing ones
- **Tags** — Manage tags (rename, delete)

> All data is fetched directly from the Shlink API — no local database tables are used.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Ade Reksi Susanto](https://github.com/adereksisusanto)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
