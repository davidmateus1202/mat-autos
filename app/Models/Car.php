<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;

class Car extends Model
{
    protected $fillable = [
        'brand_id', 'model', 'year', 'vin', 'plate', 'color',
        'purchase_price', 'purchase_date', 'status',
        'sale_price', 'estimated_price', 'sale_date', 'sold_at',
        'user_id'
    ];

    protected static function booted()
    {
        static::addGlobalScope('user', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('user_id', auth()->id());
            }
        });

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->user_id = auth()->id();
            }
        });
    }

    protected $appends = ['total_cost', 'gross_profit', 'net_profit'];

    protected $casts = [
        'purchase_date' => 'date',
        'sale_date' => 'date',
        'sold_at' => 'datetime',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'estimated_price' => 'decimal:2',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(CarExpense::class);
    }

    public function sale(): HasOne
    {
        return $this->hasOne(CarSale::class);
    }

    public function financialMovements(): MorphMany
    {
        return $this->morphMany(FinancialMovement::class, 'movementable');
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class);
    }

    public function getTotalCostAttribute()
    {
        return $this->purchase_price + $this->expenses()->sum('amount');
    }

    public function getGrossProfitAttribute()
    {
        if (!$this->sale_price) return 0;
        return $this->sale_price - $this->purchase_price;
    }

    public function getNetProfitAttribute()
    {
        if (!$this->sale_price) return 0;
        return $this->sale_price - $this->total_cost;
    }
}
