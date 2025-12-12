<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialAccount extends Model
{
    protected $fillable = ['name', 'account_number', 'bank_name', 'initial_balance'];

    protected $casts = [
        'initial_balance' => 'decimal:2',
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(FinancialMovement::class);
    }

    public function getBalanceAttribute()
    {
        return $this->initial_balance + $this->movements()->sum('amount');
    }
}
