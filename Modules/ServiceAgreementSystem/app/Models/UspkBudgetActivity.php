<?php

namespace Modules\ServiceAgreementSystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UspkBudgetActivity extends Model
{
    protected $fillable = [
        'sub_department_id',
        'block_id',
        'job_id',
        'budget_amount',
        'used_amount',
        'year',
        'description',
        'is_active',
    ];

    protected $casts = [
        'budget_amount' => 'decimal:2',
        'used_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function subDepartment(): BelongsTo
    {
        return $this->belongsTo(SubDepartment::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Sisa budget yang tersedia
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->budget_amount - $this->used_amount;
    }
}
