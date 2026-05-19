<?php

namespace Mortezaa97\Factors\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mortezaa97\Factors\Events\FactorItemCreated;
use Mortezaa97\Factors\Events\FactorItemDeleted;
use Mortezaa97\Factors\Events\FactorItemUpdated;

class FactorHasItem extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    protected $appends = ['total_price', 'vat_rate', 'payable'];
    protected $with = [];

    protected array $inventoryOriginalAttributes = [];

    protected static function boot(){
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderByDesc('created_at');
        });

        static::created(function (self $item) {
            $item->loadMissing('factor');
            FactorItemCreated::dispatch($item);
        });

        static::updating(function (self $item) {
            $item->inventoryOriginalAttributes = $item->getOriginal();
        });

        static::updated(function (self $item) {
            $item->loadMissing('factor');
            FactorItemUpdated::dispatch($item, $item->inventoryOriginalAttributes);
            $item->inventoryOriginalAttributes = [];
        });

        static::deleted(function (self $item) {
            $item->loadMissing('factor');
            FactorItemDeleted::dispatch($item);
        });
    }


    public function getTotalPriceAttribute()
    {
        return $this->unit_price * $this->count;
    }

    public function getPayableAttribute()
    {
        return ($this->unit_price * $this->count) - $this->discount + $this->vat;
    }

    public function getVatRateAttribute()
    {
        return 10;
    }
    /*
    * Relations
    */
    public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function factor(): BelongsTo
    {
        return $this->belongsTo(Factor::class);
    }
}

