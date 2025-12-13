<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class FinancialAccount extends Model
{
    protected $fillable = ['name', 'account_number', 'bank_name', 'initial_balance', 'user_id'];

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
