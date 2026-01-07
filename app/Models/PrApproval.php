<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'approver_id',
        'level',
        'role_name',
        'status',
        'approved_at',
        'remarks'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
