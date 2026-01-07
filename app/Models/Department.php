<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['site_id', 'name', 'code', 'budget'];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function approverConfigs(): HasMany
    {
        return $this->hasMany(ApproverConfig::class);
    }

    public function purchaseRequests(): HasMany
    {
        return $this->hasMany(PurchaseRequest::class);
    }
}
