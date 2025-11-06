<?php

namespace Mortezaa97\Factors\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factor extends Model
{
    use HasFactory;
    use SoftDeletes;

    const VALID_TYPES = [
        1 => 'صورتحساب نوع اول',
        2 => 'صورتحساب نوع دوم',
        3 => 'صورتحساب نوع سوم',
        4 => 'صورتحساب کاغذی',
        5 => 'خرید',
    ];

    const VALID_SUBJECTS = [
        1 => 'اصلی',
        2 => 'اصلاحی',
        3 => 'ابطالی',
        4 => 'برگشت از فروش',
    ];

    const VALID_FINANCE_YEARS = [
        1 => '1404',
    ];

    const VALID_SETTLEMENT_METHODS = [
        1 => 'نقدی',
        2 => 'نسیه',
        3 => 'نقدی/نسیه',
    ];

    const VALID_PATTERNS = [
        1 => 'فروش',
        2 => 'ارز',
        3 => 'طلا،جواهر و پلاتین',
        4 => 'پیمانکاری',
        // 5 => 'قبوض خدماتی',
        // 6 => 'بلیط هواپیما',
        // 7 => 'صادرات',
        // 8 => 'بارنامه',
        // 9 => 'بورس اوراق بهادار مبتنی بر کالا',
        // 10 => 'خدمات بیمه ای',
    ];
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    protected $appends = [];
    protected $with = [];

    protected static function boot(){
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderByDesc('created_at');
        });
        static::creating(function ($item) {
            $item->code = 10000 + static::count();
        });
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
    public function items(): HasMany
    {
        return $this->hasMany(FactorHasItem::class);
    }
    
    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}

