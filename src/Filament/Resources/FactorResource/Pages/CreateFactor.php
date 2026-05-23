<?php

declare(strict_types=1);

namespace Mortezaa97\Factors\Filament\Resources\FactorResource\Pages;

use Mortezaa97\Factors\Filament\Resources\FactorResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Filament\Actions\Action;

class CreateFactor extends CreateRecord
{
    protected static string $resource = FactorResource::class;

    public function mount(): void
    {
        parent::mount();
        
        // Get type from query parameter and set it as default
        $type = request()->query('type');
        
        if ($type) {
            $this->data['type'] = (int) $type;
            $this->form->fill($this->data);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('scanBarcode')
                ->label('اسکن بارکد (F1)')
                ->icon('heroicon-o-qr-code')
                ->color('success')
                ->modalHeading('اسکن بارکد محصول')
                ->modalDescription('لطفا بارکد محصول را اسکن کنید یا وارد نمایید')
                ->modalSubmitActionLabel('افزودن')
                ->modalWidth('md')
                ->closeModalByClickingAway(false)
                ->schema([
                    TextInput::make('barcode')
                        ->label('بارکد')
                        ->placeholder('بارکد را اسکن کنید...')
                        ->required()
                        ->autofocus()
                        ->extraAttributes([
                            'x-on:keydown.enter.prevent' => '$wire.mountFormComponentAction(\'scanBarcode\', \'submit\')',
                        ])
                ])
                ->action(function (array $data, Action $action): void {
                    $this->addProductByBarcode($data['barcode']);
                    
                    // Reset the form and keep modal open for continuous scanning
                    $action->fillForm(['barcode' => '']);
                })
                ->extraAttributes([
                    'x-data' => '{}',
                    'x-on:keydown.f1.window.prevent' => '$wire.mountAction(\'scanBarcode\')',
                ]),
        ];
    }

    protected function addProductByBarcode(string $barcode): void
    {
        try {
            // Search for product by SKU or code
            $product = DB::table('products')
                ->where('sku', $barcode)
                ->orWhere('code', $barcode)
                ->first();

            if (!$product) {
                Notification::make()
                    ->title('محصول یافت نشد')
                    ->body('محصولی با بارکد ' . $barcode . ' یافت نشد')
                    ->danger()
                    ->send();
                return;
            }

            // Get current items from data property
            $currentItems = $this->data['items'] ?? [];
            
            // Remove empty items (items without model_id)
            $currentItems = array_values(array_filter($currentItems, function ($item) {
                return !empty($item['model_id']);
            }));

            // Check if product already exists in items
            $existingItemIndex = null;
            foreach ($currentItems as $index => $item) {
                if (isset($item['model_id']) && $item['model_id'] == $product->id) {
                    $existingItemIndex = $index;
                    break;
                }
            }

            if ($existingItemIndex !== null) {
                // Increment count if product already exists
                $currentItems[$existingItemIndex]['count'] = ($currentItems[$existingItemIndex]['count'] ?? 1) + 1;
                
                // Recalculate values
                $count = $currentItems[$existingItemIndex]['count'];
                $unitPrice = $currentItems[$existingItemIndex]['unit_price'] ?? $product->price;
                $discount = $currentItems[$existingItemIndex]['discount'] ?? 0;
                $vatRate = $currentItems[$existingItemIndex]['vat_rate'] ?? 10;
                
                $currentItems[$existingItemIndex]['total_price'] = $count * $unitPrice;
                $currentItems[$existingItemIndex]['vat'] = $vatRate != 0 ? (($count * $unitPrice) - $discount) / $vatRate : 0;
                $currentItems[$existingItemIndex]['payable'] = ($count * $unitPrice) - $discount + $currentItems[$existingItemIndex]['vat'];
            } else {
                // Add new item
                $vatValue = $product->price / 10;
                $newItem = [
                    'model_id' => $product->id,
                    'model_type' => 'Mortezaa97\\Shop\\Models\\Product',
                    'unit_price' => $product->price,
                    'count' => 1,
                    'total_price' => $product->price,
                    'discount' => 0,
                    'vat_rate' => 10,
                    'vat' => $vatValue,
                    'payable' => $product->price + $vatValue,
                ];
                $currentItems[] = $newItem;
            }

            // Update the data property and sync with form state
            $this->data['items'] = $currentItems;
            
            // Force form re-hydration to trigger callbacks
            $this->form->fill($this->data);
            
            Notification::make()
                ->title('محصول اضافه شد')
                ->body($product->name . ' به فاکتور اضافه شد')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('خطا در افزودن محصول')
                ->body('خطا: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}

