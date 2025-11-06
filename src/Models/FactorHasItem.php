<?php

namespace Mortezaa97\Factors\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    protected static function boot(){
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderByDesc('created_at');
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
}

