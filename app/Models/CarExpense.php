<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class CarExpense extends Model
{
    protected $fillable = ['car_id', 'concept', 'amount', 'expense_date', 'image_path'];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
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
