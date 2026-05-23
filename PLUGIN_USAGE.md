# Factors Plugin Usage

## Installation

The Factors package is a Filament plugin that manages invoices/factors and their items.

## Quick Start

### 1. Register the Plugin in your Filament Panel

In your `app/Providers/Filament/AdminPanelServiceProvider.php`, register the plugin:

```php
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Mortezaa97\Factors\FactorsPlugin;

class AdminPanelServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->plugins([
                FactorsPlugin::make(),
            ])
            // ... other configurations
    }
}
```

### 2. Run Migrations

The migrations are automatically loaded by the service provider:

```bash
php artisan migrate
```

### 3. Clear Caches

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

## Features

### Models
- **Factor**: Main invoice/factor model with relationships to customers and items
- **FactorHasItem**: Items belonging to a factor (polymorphic relationship)

### API Endpoints

The package automatically registers these routes:

```
GET    /api/factors                 - List all factors
POST   /api/factors                 - Create a new factor
GET    /api/factors/{id}            - Show a specific factor
PUT    /api/factors/{id}            - Update a factor
DELETE /api/factors/{id}            - Delete a factor

GET    /api/factor-items            - List all factor items
POST   /api/factor-items            - Create a new factor item
GET    /api/factor-items/{id}       - Show a specific factor item
PUT    /api/factor-items/{id}       - Update a factor item
DELETE /api/factor-items/{id}       - Delete a factor item

GET    /factors/{factor}/labels     - Generate printable labels
GET    /factors/{factor}/invoice    - Generate printable invoice
```

### Filament Resources

The plugin registers:
- **FactorResource**: Full CRUD interface for factors
  - List Factors page
  - Create Factor page (with barcode scanning support)
  - Edit Factor page
  - Custom navigation items for "New Sale" and "New Purchase"

### Factor Types

```php
const VALID_TYPES = [
    1 => 'صورتحساب نوع اول',      // Type 1 Invoice
    2 => 'صورتحساب نوع دوم',      // Type 2 Invoice
    3 => 'صورتحساب نوع سوم',      // Type 3 Invoice
    4 => 'صورتحساب کاغذی',        // Paper Invoice
    5 => 'خرید',                   // Purchase
];
```

### Policies

The package includes policies for authorization:
- `FactorPolicy`: Controls access to factor operations
- `FactorHasItemPolicy`: Controls access to factor item operations

## Usage in Code

### Creating a Factor

```php
use Mortezaa97\Factors\Models\Factor;

$factor = Factor::create([
    'customer_id' => $userId,
    'type' => 1,
    'pattern' => 1,
    'subject' => 1,
    'settlement_method' => 1,
    'finance_year' => '1404',
    'date_time' => now(),
    'total_price' => 100000,
    'total_count' => 1,
    'total_vat' => 10000,
    'payable' => 110000,
    'created_by' => auth()->id(),
    'updated_by' => auth()->id(),
]);
```

### Adding Items to a Factor

```php
use Mortezaa97\Factors\Models\FactorHasItem;

$factor->items()->create([
    'model_type' => 'Mortezaa97\\Shop\\Models\\Product',
    'model_id' => $productId,
    'count' => 2,
    'unit_price' => 50000,
    'total_price' => 100000,
    'discount' => 0,
    'vat' => 10000,
    'payable' => 110000,
    'created_by' => auth()->id(),
    'updated_by' => auth()->id(),
]);
```

### Loading Factor with Relationships

```php
$factor = Factor::with(['customer', 'items.model', 'createdBy', 'updatedBy'])
    ->find($id);
```

## Inventory & Other Package Integrations

The package now dispatches domain events whenever items change, so other packages (like `inventories`) can listen and react without hard coupling.

| Event | Fired when | Payload |
| --- | --- | --- |
| `Mortezaa97\Factors\Events\FactorItemCreated` | A new factor item is stored | `FactorHasItem $item` |
| `Mortezaa97\Factors\Events\FactorItemUpdated` | An existing item changes (count, product, etc.) | `FactorHasItem $item`, `array $originalAttributes` |
| `Mortezaa97\Factors\Events\FactorItemDeleted` | An item is soft-deleted/removed | `FactorHasItem $item` |

Listening is the same as any Laravel event:

```php
use Illuminate\Support\Facades\Event;
use Mortezaa97\Factors\Events\FactorItemCreated;

Event::listen(FactorItemCreated::class, function (FactorItemCreated $event) {
    // $event->item is fully hydrated (with factor relation)
});
```

These hooks keep the Factors package agnostic while allowing consumers to keep inventories, accounting, or analytics in sync.

## Customization

### Publishing Config

```bash
php artisan vendor:publish --tag="factors-config"
```

### Publishing Migrations

```bash
php artisan vendor:publish --tag="factors-migrations"
```

### Publishing Views

```bash
php artisan vendor:publish --tag="factors-views"
```

## Barcode Scanning

The Create and Edit Factor pages include barcode scanning functionality (F1 key):
- Press F1 to open the barcode scanner
- Scan or enter product barcode
- Product is automatically added to the factor
- If product already exists, quantity is incremented

## Label and Invoice Printing

Generate printable labels and invoices:

```php
// In your controller or route
return redirect()->route('factors.labels', $factor);
return redirect()->route('factors.invoice', $factor);
```

## Requirements

- PHP ^8.0
- Laravel ^10.0
- Filament ^3.0
- Shop package (for product relationships)

