<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FinancialMovement extends Model
{
    protected $fillable = [
        'financial_account_id', 'type', 'amount', 'description', 'movement_date',
        'movementable_id', 'movementable_type'
    ];

    protected $casts = [
        'movement_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function financialAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    public function movementable(): MorphTo
    {
        return $this->morphTo();
    }
}
