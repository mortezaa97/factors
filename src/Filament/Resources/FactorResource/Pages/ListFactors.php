<?php

declare(strict_types=1);

namespace Mortezaa97\Factors\Filament\Resources\FactorResource\Pages;

use Filament\Actions\CreateAction;
use Mortezaa97\Factors\Filament\Resources\FactorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFactors extends ListRecords
{
    protected static string $resource = FactorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

