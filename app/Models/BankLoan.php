<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Builder;

class BankLoan extends Model
{
    protected $fillable = [
        'financial_account_id', 'bank_name', 'amount', 'interest_rate',
        'start_date', 'term_months', 'status', 'disbursement_date',
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

    protected $casts = [
        'start_date' => 'date',
        'disbursement_date' => 'date',
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
    ];

    public function financialAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BankLoanPayment::class);
    }

    public function disbursements(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(FinancialMovement::class, 'movementable')
                    ->where('type', 'loan_disbursement');
    }

    public function getTotalDisbursedAttribute()
    {
        return $this->disbursements()->sum('amount');
    }

    public function getTotalPrincipalPaidAttribute()
    {
        return $this->payments()->sum('principal_amount');
    }

    public function getCurrentDebtAttribute()
    {
        return $this->total_disbursed - $this->total_principal_paid;
    }

    public function getAvailableCreditAttribute()
    {
        return $this->amount - $this->current_debt;
    }

    public function getRemainingBalanceAttribute()
    {
        // Deprecated, alias for current debt for backward compatibility if needed
        return $this->current_debt;
    }
}
