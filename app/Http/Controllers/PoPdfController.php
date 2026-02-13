<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class PoPdfController extends Controller
{
    public function export(PurchaseOrder $po)
    {
        $po->load([
            'items.prItem.product',
            'items.prItem.job',
            'purchaseRequest.department.site',
            'purchaseRequest.subDepartment'
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.po_export', [
            'po' => $po
        ]);

        // Set paper size and orientation to portrait
        $pdf->setPaper('a4', 'portrait');

        // Download PDF with safe filename
        $safeFilename = str_replace('/', '_', $po->po_number);
        return $pdf->download("PO_{$safeFilename}.pdf");
    }
}
