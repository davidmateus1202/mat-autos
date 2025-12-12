<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class CarSale extends Model
{
    protected $fillable = ['car_id', 'sale_price', 'sale_date', 'customer_name', 'customer_document'];

    protected $casts = [
        'sale_date' => 'date',
        'sale_price' => 'decimal:2',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function financialMovement(): MorphOne
    {
        return $this->morphOne(FinancialMovement::class, 'movementable');
    }
}
