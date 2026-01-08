<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrItem extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_request_id', 'product_id', 'item_name', 'specification', 'quantity', 'unit', 'price_estimation', 'subtotal', 'manual_category'];

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
        $hoApprovals = $pr->approvals()
            ->where('status', 'approved')
            ->whereNotNull('adjusted_quantities')
            ->orderBy('level', 'desc') // Highest level first
            ->get();

        foreach ($hoApprovals as $approval) {
            $adjustedQty = $approval->getAdjustedQuantityForItem($this->id);
            if ($adjustedQty !== null && $adjustedQty > 0) {
                return $adjustedQty;
            }
        }

        // No HO adjustments, return original quantity
        return $this->quantity;
    }
}
