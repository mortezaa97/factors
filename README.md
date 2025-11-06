# Factors Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mortezaa97/factors.svg?style=flat-square)](https://packagist.org/packages/mortezaa97/factors)
[![Total Downloads](https://img.shields.io/packagist/dt/mortezaa97/factors.svg?style=flat-square)](https://packagist.org/packages/mortezaa97/factors)

A comprehensive Filament plugin for managing invoices/factors and their items with support for multiple types, barcode scanning, label printing, and more.

## Features

- 🧾 **Complete Invoice Management**: Create, edit, and manage invoices with multiple types
- 📦 **Item Management**: Add products to invoices with automatic calculations
- 🔍 **Barcode Scanning**: Built-in barcode scanner for quick product addition (F1 key)
- 🏷️ **Label Printing**: Generate customizable product labels
- 📄 **Invoice Printing**: Professional invoice templates
- 🔐 **Authorization**: Built-in policies for secure access control
- 🌐 **API Ready**: RESTful API endpoints for all resources
- 🇮🇷 **Persian Support**: Full RTL and Persian language support

## Installation

### 1. Require the package via Composer

```bash
composer require mortezaa97/factors
```

### 2. Register the Plugin

In your `app/Providers/Filament/AdminPanelServiceProvider.php`:

```php
use Mortezaa97\Factors\FactorsPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FactorsPlugin::make(),
        ]);
}
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="factors-config"
```

Publish migrations:

```bash
php artisan vendor:publish --tag="factors-migrations"
```

Publish views (for label and invoice customization):

```bash
php artisan vendor:publish --tag="factors-views"
```

## Usage

See [PLUGIN_USAGE.md](PLUGIN_USAGE.md) for detailed usage instructions.

## Quick Example

```php
use Mortezaa97\Factors\Models\Factor;

// Create a factor
$factor = Factor::create([
    'customer_id' => $userId,
    'type' => 1, // Sale
    'date_time' => now(),
    'total_price' => 100000,
    'created_by' => auth()->id(),
]);

// Add items
$factor->items()->create([
    'model_type' => 'App\\Models\\Product',
    'model_id' => $productId,
    'count' => 2,
    'unit_price' => 50000,
]);
```

## Available Routes

- Filament Admin Panel: Automatic resource registration
- API: `/api/factors`, `/api/factor-items`
- Print: `/factors/{id}/labels`, `/factors/{id}/invoice`

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [mortezaa97](https://github.com/mortezaa97)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
