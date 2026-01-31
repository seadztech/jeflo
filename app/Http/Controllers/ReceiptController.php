<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReceiptController extends Controller
{
    /**
     * Generate PDF receipt optimized for thermal printer
     */
    public function generate($id)
    {
        try {
            $sale = Sale::with([
                'saleItems.item',
                'transactions',
                'user.branch'
            ])->findOrFail($id);

            $company = Company::latest()->first();

            $html = view('receipts.salesReceipt', [
                'sale'     => $sale,
                'company'  => $company,
            ])->render();

            // Thermal printer paper size (58mm width)
            // Convert mm to points: 1mm = 2.83465 points
            $widthMm = 58;  // 58mm thermal paper width
            $heightMm = 297; // Common receipt paper roll length
            
            $widthPoints = $widthMm * 2.83465;
            $heightPoints = $heightMm * 2.83465;
            
            // Configure PDF for thermal printer
            $pdf = Pdf::loadHTML($html)
                ->setPaper([0, 0, $widthPoints, $heightPoints], 'portrait')
                ->setOption('margin-bottom', 0)
                ->setOption('margin-left', 0);

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="Receipt_' . $sale->id . '.pdf"');

        } catch (\Throwable $e) {
            Log::error('PDF Generation Failed', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'sale_id' => $id,
            ]);

            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Direct HTML view for printing (bypasses PDF issues)
     */
    public function print($id)
    {
        try {
            $sale = Sale::with([
                'saleItems.item',
                'transactions',
                'user.branch'
            ])->findOrFail($id);

            $company = Company::latest()->first();

            return view('receipts.salesReceipt', [
                'sale'     => $sale,
                'company'  => $company,
            ]);

        } catch (\Throwable $e) {
            Log::error('Print View Failed', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'sale_id' => $id,
            ]);

            return back()->with('error', 'Failed to load receipt: ' . $e->getMessage());
        }
    }
}