<?php

namespace Mortezaa97\Factors;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Mortezaa97\Factors\Filament\Resources\FactorResource;

class FactorsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'factors';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                FactorResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}

