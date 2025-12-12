<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class BankLoanPayment extends Model
{
    protected $fillable = [
        'bank_loan_id', 'amount', 'principal_amount', 'interest_amount', 'payment_date'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
    ];

    public function bankLoan(): BelongsTo
    {
        return $this->belongsTo(BankLoan::class);
    }

    public function financialMovement(): MorphOne
    {
        return $this->morphOne(FinancialMovement::class, 'movementable');
    }
}
