<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrItem extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_request_id', 'product_id', 'job_id', 'item_name', 'specification', 'remarks', 'quantity', 'unit', 'price_estimation', 'subtotal', 'manual_category', 'url_link'];

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get final quantity considering HO adjustments
     * Returns the quantity from the highest-level HO approver who adjusted it,
     * or the original quantity if no HO adjustments were made
     */
    public function getFinalQuantity()
    {
        $pr = $this->purchaseRequest;
        // Get all approved HO approvals with adjusted quantities for this item
        $hoApprovals = PrApproval::where('purchase_request_id', $pr->id)
            ->where('status', 'Approved')
            ->whereNotNull('adjusted_quantities')
            ->orderBy('level', 'desc') // Highest level first
            ->get();

        foreach ($hoApprovals as $approval) {
            $adjustedQty = $approval->getAdjustedQuantityForItem($this->id);
            if ($adjustedQty !== null) {
                return $adjustedQty;
            }
        }

        // No HO adjustments, return original quantity
        return $this->quantity;
    }
}
