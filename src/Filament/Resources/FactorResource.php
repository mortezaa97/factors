<?php

declare(strict_types=1);

namespace Mortezaa97\Factors\Filament\Resources;

use App\Enums\ModelType;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Deriszadeh\Moadian\Moadian;
use DateTime;
use Deriszadeh\Moadian\Services\SimpleGuidv4Service;
use Auth;
use Exception;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Mortezaa97\Factors\Filament\Resources\FactorResource\Pages\ListFactors;
use Mortezaa97\Factors\Filament\Resources\FactorResource\Pages\CreateFactor;
use Mortezaa97\Factors\Filament\Resources\FactorResource\Pages\EditFactor;
use App\Filament\Components\Form\CashTextInput;
use App\Filament\Components\Form\CreatedByHidden;
use App\Filament\Components\Form\CreditTextInput;
use App\Filament\Components\Form\CustomerSelect;
use App\Filament\Components\Form\DateTimeDateTimePicker;
use App\Filament\Components\Form\FinanceYearSelect;
use App\Filament\Components\Form\NoteTextarea;
use App\Filament\Components\Form\PatternSelect;
use App\Filament\Components\Form\ProductSelect;
use App\Filament\Components\Form\SettlementMethodSelect;
use App\Filament\Components\Form\SubjectSelect;
use App\Filament\Components\Form\TotalCountTextInput;
use App\Filament\Components\Form\TotalPriceTextInput;
use App\Filament\Components\Form\TypeSelect;
use App\Filament\Components\Form\UpdatedByHidden;
use App\Filament\Components\Table\CodeTextColumn;
use App\Filament\Components\Table\CreatedByTextColumn;
use App\Filament\Components\Table\DateTimeTextColumn;
use App\Filament\Components\Table\DeletedAtTextColumn;
use App\Filament\Components\Table\GatewayTextColumn;
use App\Filament\Components\Table\NationalCodeTextColumn;
use App\Filament\Components\Table\ParentTextColumn;
use App\Filament\Components\Table\PaymentTextColumn;
use App\Filament\Components\Table\SubjectTextColumn;
use App\Filament\Components\Table\TotalCountTextColumn;
use App\Filament\Components\Table\TypeTextColumn;
use App\Filament\Components\Table\UpdatedAtTextColumn;
use App\Filament\Components\Table\UpdatedByTextColumn;
use Mortezaa97\Factors\Models\Factor;
use Mortezaa97\Factors\Models\FactorHasItem;
use App\Models\History;
use App\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Filament\Navigation\NavigationItem;

class FactorResource extends Resource
{
    protected static ?string $model = Factor::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'فاکتور ها';

    protected static ?string $modelLabel = 'فاکتور';

    protected static ?string $pluralModelLabel = 'فاکتور ها';

    protected static string | \UnitEnum | null $navigationGroup = 'حسابداری';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                // \App\Filament\Components\Form\CodeTextInput::create()->required(),
                                Select::make('customer_id')
                                ->translateLabel()
                                ->options(User::all()->pluck('name', 'id'))
                                ->searchable()
                                ->columnSpan(4),
                                DateTimeDateTimePicker::create()->label('تاریخ صورتحساب')->required(),
                                TypeSelect::create(Factor::class)->required()
                                ->columnSpan(4)->default(1)
                                ->disabled(fn () => request()->query('type') !== null)
                                ->dehydrated(true),
                                Select::make('pattern')
                                ->label('الگوی صورتحساب')
                                ->translateLabel()
                                ->options(Factor::VALID_PATTERNS)
                                ->required()
                                ->default(1)
                                ->searchable()
                                ->columnSpan(4),
                                //                                SubjectSelect::create(Factor::class)->required()->default(1),
                                Select::make('finance_year')
                                    ->translateLabel()
                                    ->label('سال مالی')
                                    ->options(Factor::VALID_FINANCE_YEARS)
                                    ->searchable()
                                    ->columnSpan(4)
                            ])
                            ->columns(12)
                            ->columnSpan(12),
                    ])
                    ->columns(12)
                    ->columnSpan(8),
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Select::make('settlement_method')
                                ->translateLabel()
                                ->label('روش تسویه')
                                ->options(Factor::VALID_SETTLEMENT_METHODS)
                                ->searchable()
                                ->columnSpan(12)
                                ->default(1)
                                ->live(debounce: 500),
                                TextInput::make('credit')
                                ->translateLabel()
                                ->columnSpan(12)->hidden(fn (Get $get): bool =>  ! ($get('settlement_method') == 3))->suffix('تومان'),
                                TextInput::make('cash')
                                ->translateLabel()
                                ->columnSpan(12)->hidden(fn (Get $get): bool =>  ! ($get('settlement_method') == 3))->suffix('تومان'),
                                // Forms\Components\TextInput::make('subject_of_17')->translateLabel()->numeric()->default(0)->columnSpan(3),
                                // Forms\Components\TextInput::make('switch_number')->maxLength(255)->columnSpan(3),
                                // Forms\Components\TextInput::make('acceptor_number')->maxLength(255)->columnSpan(3),
                                // Forms\Components\TextInput::make('terminal_number')->maxLength(255)->columnSpan(3),
                                // Forms\Components\TextInput::make('ref_number')->maxLength(255)->columnSpan(3),
                                // Forms\Components\TextInput::make('cart_number')->maxLength(255)->columnSpan(3),
                                // \App\Filament\Components\Form\NationalCodeTextInput::create(),
                                // Forms\Components\DateTimePicker::make('pay_datetime')->columnSpan(3),
                                // Forms\Components\DateTimePicker::make('sync_at')->columnSpan(3),
                                // Forms\Components\Textarea::make('tax_ref_code')->columnSpan(3)->columnSpanFull(),
                                // Forms\Components\DateTimePicker::make('inquire_sync_at')->columnSpan(3),
                                Select::make('subject')
                                ->translateLabel()
                                ->options(Factor::VALID_SUBJECTS)
                                ->searchable()
                                ->columnSpan(12)->default(1)
                                ->label('موضوع صورتحساب'),
                                // Forms\Components\Toggle::make('is_buy')->required()->columnSpan(3),
                                // Forms\Components\Toggle::make('is_pre')->required()->columnSpan(3),
                                // Forms\Components\Toggle::make('is_return')->required()->columnSpan(3),
                                // \App\Filament\Components\Form\GatewayTextInput::create(),

                                // Forms\Components\TextInput::make('pmt')->translateLabel()->maxLength(255)->columnSpan(3),
                                // Forms\Components\TextInput::make('trn')->translateLabel()->numeric()->columnSpan(3),
                                // Forms\Components\TextInput::make('consfee')->translateLabel()->numeric()->columnSpan(3),
                                // Forms\Components\TextInput::make('spro')->translateLabel()->numeric()->columnSpan(3),
                                // Forms\Components\TextInput::make('bros')->translateLabel()->numeric()->columnSpan(3),
                                // Forms\Components\TextInput::make('crn')->translateLabel()->maxLength(255)->columnSpan(3),
                                // Forms\Components\TextInput::make('bank')->translateLabel()->maxLength(255)->columnSpan(3),
                                // Forms\Components\Toggle::make('is_online')->required()->columnSpan(3),
                                // Forms\Components\TextInput::make('payment_id')->maxLength(26)->columnSpan(3),
//                                \App\Filament\Components\Form\CreatedBySelect::create()->required(),
//                                \App\Filament\Components\Form\UpdatedBySelect::create(),
                                // Forms\Components\TextInput::make('parent_id')->maxLength(26)->columnSpan(3),
                                Textarea::make('note')
                                ->translateLabel()
                                ->label('توضیحات')
                                ->columnSpan(12),

                            ])
                            ->columns(12)
                            ->columnSpan(12),
                    ])
                    ->columns(12)
                    ->columnSpan(4),
                    Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Repeater::make('items')
                                    ->translateLabel()
                                    ->relationship()
                                    ->schema([
                                        Select::make('model_id')
                                            ->translateLabel()
                                            ->searchable()
                                            ->label('کالا')
                                            ->options(\Mortezaa97\Shop\Models\product::query()->pluck('name', 'id')->toArray())
                                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                                            ->preload()
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                self::updateValues($set, $get);
                                            })
                                            ->columnSpan(5),
                                        Hidden::make('model_type')->default(ModelType::PRODUCT->value),
                                        CreatedByHidden::create(),
                                        UpdatedByHidden::create(),
                                        TextInput::make('unit_price')
                                            ->columnSpan(3)
                                            ->default(1000)
                                            ->suffix('تومان')
                                            ->translateLabel()
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                self::updateValues($set, $get);
                                            }),
                                        TextInput::make('count')
                                            ->translateLabel()
                                            ->numeric()
                                            ->columnSpan(2)
                                            ->default(1)
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                self::updateValues($set, $get);
                                            }),
                                        TotalPriceTextInput::create()
                                        ->default(1000)
                                        ->suffix('تومان')
                                        ->translateLabel()
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(function (Set $set, Get $get) {
                                            self::updateValues($set, $get);
                                        })
                                        ->columnSpan(2),
                                        TextInput::make('discount')->columnSpan(3)
                                            ->default(0)
                                            ->suffix('تومان')
                                            ->translateLabel()
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                self::updateValues($set, $get);
                                            }),
                                        TextInput::make('vat_rate')
                                            ->columnSpan(3)
                                            ->default(10)
                                            ->suffix('%')
                                            ->translateLabel(),
                                        TextInput::make('vat')->columnSpan(3)
                                            ->translateLabel()
                                            ->default(0)
                                            ->live(debounce: 500)
                                            ->suffix('تومان')
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                self::updateValues($set, $get);
                                            }),
                                        TextInput::make('payable')->columnSpan(3)
                                            ->default(0)
                                            ->suffix('تومان')
                                            ->translateLabel(),
                                    ])
                                    ->live(debounce: 1000)
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::updateTotals($set, $get);
                                    })
                                    ->columns(12)
                                    ->addActionLabel('افزودن')
                                    ->addActionAlignment(Alignment::End)
                                    ->itemLabel(fn (array $state): ?string => $state['display_name'] ?? null)
                                    ->columnSpan(12),
                                TotalPriceTextInput::create()->columnSpan(3)->required(),
                                TextInput::make('total_vat')
                                    ->suffix('تومان')->translateLabel()->default(0)->numeric()->columnSpan(2),
                                TextInput::make('discount')
                                    ->suffix('تومان')->translateLabel()->default(0)->numeric()->columnSpan(2),
                                TotalCountTextInput::create()->columnSpan(2)->required()->default(1),
                                TextInput::make('payable')
                                    ->suffix('تومان')->default(1000)->translateLabel()->numeric()->columnSpan(3),
                                CreatedByHidden::create()->columnSpan(6),
                                UpdatedByHidden::create()->columnSpan(6),
                            ])
                            ->columns(12)
                            ->columnSpan(12),
                    ])
                    ->columns(12)
                    ->columnSpan(12),
            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                CodeTextColumn::create(),
                TextColumn::make('customer.name')->translateLabel(),
                TextColumn::make('total_price')->suffix('تومان ')->translateLabel()->numeric()->sortable(),
                TextColumn::make('payable')->suffix('تومان ')->translateLabel()->numeric()->sortable(),
                TextColumn::make('discount')->suffix('تومان ')->translateLabel()->numeric()->sortable(),
                TextColumn::make('total_vat')->suffix('تومان ')->translateLabel()->numeric()->sortable(),
                TextColumn::make('total_duties')->suffix('تومان ')->translateLabel()->numeric()->sortable(),
                TotalCountTextColumn::create(),
                TypeTextColumn::create(),
                TextColumn::make('pattern')->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)->numeric()->sortable(),
                SubjectTextColumn::create(),
                TextColumn::make('settlement_method')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->numeric()->sortable(),
                TextColumn::make('finance_year')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->searchable(),
                DateTimeTextColumn::create(),
                TextColumn::make('credit')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->numeric()->sortable(),
                TextColumn::make('cash')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->numeric()->sortable(),
                TextColumn::make('subject_of_17')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->numeric()->sortable(),
                TextColumn::make('switch_number')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->searchable(),
                TextColumn::make('acceptor_number')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->searchable(),
                TextColumn::make('terminal_number')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->searchable(),
                TextColumn::make('ref_number')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->searchable(),
                TextColumn::make('cart_number')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->searchable(),
                NationalCodeTextColumn::create(),
                TextColumn::make('pay_datetime')->dateTime()->translateLabel()->toggleable(isToggledHiddenByDefault: true)->sortable(),
                TextColumn::make('sync_at')->dateTime()->translateLabel()->toggleable(isToggledHiddenByDefault: true)->sortable(),
                TextColumn::make('discount')->suffix('تومان ')->numeric()->translateLabel()->toggleable(isToggledHiddenByDefault: true)->sortable(),
                TextColumn::make('payable')->suffix('تومان ')->numeric()->translateLabel()->toggleable(isToggledHiddenByDefault: true)->sortable(),
                TextColumn::make('inquire_sync_at')->dateTime()->translateLabel()->toggleable(isToggledHiddenByDefault: true)->sortable(),
                TextColumn::make('subject_code')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->searchable(),
                IconColumn::make('is_buy')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->boolean(),
                IconColumn::make('is_pre')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->boolean(),
                IconColumn::make('is_return')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->boolean(),
                GatewayTextColumn::create(),
                TextColumn::make('pmt')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->searchable(),
                TextColumn::make('trn')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->numeric()->sortable(),
                TextColumn::make('consfee')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->numeric()->sortable(),
                TextColumn::make('spro')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->numeric()->sortable(),
                TextColumn::make('bros')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->numeric()->sortable(),
                TextColumn::make('crn')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->searchable(),
                TextColumn::make('bank')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->searchable(),
                IconColumn::make('is_online')->translateLabel()->toggleable(isToggledHiddenByDefault: true)->boolean(),
                PaymentTextColumn::create(),
                TextColumn::make('company.name')->toggleable(isToggledHiddenByDefault: true)->translateLabel(),
                CreatedByTextColumn::create(),
                UpdatedByTextColumn::create(),
                DeletedAtTextColumn::create(),
                DateTimeTextColumn::create(),
                UpdatedAtTextColumn::create(),
                ParentTextColumn::create(),
            ])
            ->striped()
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('subject')
                    ->options(Factor::VALID_SUBJECTS),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->disabled(fn ($record) => $record->is_sent),
                    
                    Action::make('print_labels')
                        ->label('چاپ برچسب')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->url(fn (Factor $record): string => route('factors.labels', $record))
                        ->openUrlInNewTab(),
                    
                    Action::make('print_invoice')
                        ->label('چاپ فاکتور')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->url(fn (Factor $record): string => route('factors.invoice', $record))
                        ->openUrlInNewTab(),
                    
                    ReplicateAction::make()
                        ->label('ابطال فاکتور')
                        ->icon('heroicon-o-archive-box-x-mark')
                        ->disabled(fn ($record) => ! $record->is_inquired)
                        ->color('danger')
                        ->before(function (ReplicateAction $action, Factor $record) {
                            $record->load('items');
                            $record->parent_id = $record->getOriginal('id');
                            $record->subject = 3;
                        })
                        ->after(function (Model $replica): void {
                            $parent = Factor::where('id', $replica->parent_id)->with('items')->first();
                            foreach ($parent->items as $item) {
                                $item->factor_id = $replica->id;
                                FactorHasItem::create($item->toArray());
                            }
                        }),
                ])
                    ->iconButton()
                    ->label('عملیات'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFactors::route('/'),
            'create' => CreateFactor::route('/create'),
            'edit' => EditFactor::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make()
                ->label(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.*') && request()->query('type') == null)
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge())
                ->url(static::getNavigationUrl()),

            NavigationItem::make('new_sale')
                ->label('فروش جدید')
                ->group(static::getNavigationGroup())
                ->icon('heroicon-o-plus-circle')
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.create') && request()->query('type') == 1)
                ->url(static::getUrl('create', ['type' => 1])),
            
            NavigationItem::make('new_purchase')
                ->label('خرید جدید')
                ->group(static::getNavigationGroup())
                ->icon('heroicon-o-shopping-cart')
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.create') && request()->query('type') == 5)
                ->url(static::getUrl('create', ['type' => 5])),
        ];
    }

    public static function updateTotals(Set $set, Get $get): void
    {
        try {
            $items = $get('items');
            
            if (!$items) {
                return;
            }
            
            $products = collect($items)->filter(function ($item) {
                return ! empty($item['count']);
            });
            
            $subtotal = $products->reduce(function ($subtotal, $product) {
                return $subtotal + ($product['count'] * $product['unit_price']);
            }, 0);

            $discount = $products->reduce(function ($discount, $product) {
                return $discount + (int) $product['discount'];
            }, 0);

            $vat = $products->reduce(function ($vat, $product) {
                return $vat + (($product['count'] * $product['unit_price']) - (int) $product['discount']) / $product['vat_rate'];
            }, 0);

            $count = $products->reduce(function ($count, $product) {
                return $count + $product['count'];
            }, 0);

            $payable = $subtotal + $vat - $discount;
            if ($payable <= 0) {
                Notification::make()
                    ->title('مقدار تخفیف صحیح نیست')
                    ->body('مقدار تخفیف نمی تواند بیشتر از مبلغ کل فاکتور باشد')
                    ->danger()
                    ->color('danger')
                    ->send();
                // Optionally reset the field to null or a default value
                $set('discount', 0);
                $set('payable', $subtotal);
            } else {
                $set('discount', $discount);
                $set('payable', $payable);
            }
            $set('total_vat', $vat);
            $set('total_price', $subtotal);
            $set('total_count', $count);
        } catch (\Exception $e) {
            // Silently handle errors during rapid input
            return;
        }
    }

    public static function updateValues(Set $set, Get $get): void
    {
        try {
            $modelId = $get('model_id');
            
            if (!$modelId) {
                return;
            }
            
            $product = \Mortezaa97\Shop\Models\Product::find($modelId);
            
            if(!$product) {
                Notification::make()
                    ->title('محصول یافت نشد')
                    ->body('محصولی با این شناسه یافت نشد')
                    ->danger()
                    ->color('danger')
                    ->send();
                return;
            }
            
            $count = (int) ($get('count') ?? 1);
            $unit_price = (int) ($product->price ?? 0);
            $vat_rate = (int) ($product->vat_rate ?? 10);
            $discount = (int) ($get('discount') ?? 0);
            $vat_value = $vat_rate != 0 ? (($count * $product->price) - $discount) / $vat_rate : 0;
            
            $set('vat', $vat_value);
            $set('unit_price', $unit_price);
            $set('total_price', ($count * $product->price));
            $set('payable', ($count * $product->price) - $discount + $vat_value);
        } catch (\Exception $e) {
            // Silently handle errors during rapid input
            return;
        }
    }
}

