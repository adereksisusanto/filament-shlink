# Filament Shlink

[![Latest Version on Packagist](https://img.shields.io/packagist/v/adereksisusanto/filament-shlink.svg?style=flat-square)](https://packagist.org/packages/adereksisusanto/filament-shlink)
[![tests](https://github.com/adereksisusanto/filament-shlink/actions/workflows/tests.yml/badge.svg)](https://github.com/adereksisusanto/filament-shlink/actions/workflows/tests.yml)
[![fix-code-style](https://github.com/adereksisusanto/filament-shlink/actions/workflows/fix-code-style.yml/badge.svg)](https://github.com/adereksisusanto/filament-shlink/actions/workflows/fix-code-style.yml)
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

### Modal Mode

By default, create and edit actions navigate to dedicated pages. To use modal forms instead:

```php
FilamentShlinkPlugin::make()->modal(true)
```

Configure modal appearance with named parameters using enums:

```php
use Adereksisusanto\FilamentShlink\Enums\ModalType;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\SlideOverPosition;
use Filament\Support\Enums\Width;

FilamentShlinkPlugin::make()->modal(
    enabled: true,
    type: ModalType::SlideOver,
    position: SlideOverPosition::End,   // slide-over position
    width: Width::FourExtraLarge,        // Tailwind max-width
    alignment: Alignment::Center,        // content alignment
)
```

Example — slide-over panel from the right:

```php
use Adereksisusanto\FilamentShlink\Enums\ModalType;
use Filament\Support\Enums\Width;

FilamentShlinkPlugin::make()->modal(
    enabled: true,
    type: ModalType::SlideOver,
    width: Width::TwoExtraLarge,
)
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
- **Dashboard Widget** — Visits overview widget showing total visits, short URLs, and tags
- **Settings** — Configure server connection from the admin panel

## Usage

Once registered, the plugin adds the following to your Filament panel:

- **Dashboard Widget** — `VisitsOverviewWidget` displays total visits, short URL count, and tag count (appears on the Short URLs page)
- **Short URLs** — View all short URLs, create new ones, edit existing ones
- **Tags** — Manage tags (rename, delete)

> All data is fetched directly from the Shlink API — no local database tables are used.

## Requirements

- PHP ^8.3
- Filament ^5.0
- Laravel ^11.0|^12.0

## Testing

```bash
composer test
```

The test suite covers:

- **Models** — ShortUrl, Tag, and TagStats creation from Shlink SDK DTOs
- **Service** — Configuration state, connection setup via `setConfig()`
- **Widget** — Visits overview widget handles configured and unconfigured states
- **Service Provider** — Singleton registration, config/translation/view loading
- **Architecture** — Debug functions (`dd`, `dump`, `ray`) are not used anywhere in the source

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
