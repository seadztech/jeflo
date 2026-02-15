<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

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
        $printer = null;

        try {
            $sale = Sale::with([
                'saleItems.item',
                'transactions',
                'user.branch'
            ])->findOrFail($id);

            $company = Company::latest()->first();

            // CONNECT TO USB PRINTER (WINDOWS)
            $connector = new WindowsPrintConnector("ThermalPrinter"); // Your printer name
            $printer = new Printer($connector);

            // LOGO (top, minimal feed)
            $logoPath = public_path('logo.png');
            if (file_exists($logoPath)) {
                $logo = EscposImage::load($logoPath, false);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->bitImage($logo);
                $printer->feed(1);
            }

            // HEADER
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            if ($company?->name) {
                $printer->setEmphasis(true);
                $printer->text($company->name . "\n");
                $printer->setEmphasis(false);
            }
            if ($company?->address) $printer->text("Address: {$company->address}\n");
            if ($company?->phone) $printer->text("Tel: {$company->phone}\n");
            if ($company?->email) $printer->text("Email: {$company->email}\n");
            $printer->text("Branch: " . ($sale->user->branch->name ?? "Main Branch") . "\n");
            $printer->text("Receipt #: {$sale->id}\n");
            $printer->text("Date: {$sale->created_at->format('d/m/Y H:i')}\n");
            $printer->feed(1);

            // ITEMS TABLE (80mm width)
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text(str_pad("Item", 26) .
                           str_pad("Qty", 5, " ", STR_PAD_LEFT) .
                           str_pad("Price", 8, " ", STR_PAD_LEFT) .
                           str_pad("Total", 9, " ", STR_PAD_LEFT) . "\n");
            $printer->text(str_repeat("-", 48) . "\n");

            $total = 0;
            foreach ($sale->saleItems as $item) {
                $name = substr($item->item->name ?? "Item", 0, 26);
                $qty = $item->quantity;
                $price = $item->unit_price;
                $lineTotal = $qty * $price;
                $total += $lineTotal;

                $line = str_pad($name, 26) .
                        str_pad($qty, 5, " ", STR_PAD_LEFT) .
                        str_pad(number_format($price, 0), 8, " ", STR_PAD_LEFT) .
                        str_pad(number_format($lineTotal, 0), 9, " ", STR_PAD_LEFT) . "\n";

                $printer->text($line);
            }

            $printer->text(str_repeat("-", 48) . "\n");

            // TOTAL
            $printer->setEmphasis(true);
            $printer->text(str_pad("TOTAL:", 43, " ", STR_PAD_LEFT) . " " . number_format($total, 0) . "\n");
            $printer->setEmphasis(false);

            // PAYMENTS
            $printer->text("PAYMENTS:\n");
            $totalPaid = 0;
            foreach ($sale->transactions as $txn) {
                $type = ($txn->type === 'sms_mpesa' || $txn->type === 'mpesa') ? "Mpesa" : ucfirst($txn->type);
                $totalPaid += $txn->amount;
                $printer->text(str_pad($type, 25) . str_pad(number_format($txn->amount, 0), 23, " ", STR_PAD_LEFT) . "\n");
            }

            // BALANCE & SERVED BY
            $balance = $totalPaid - $total;
            $printer->setEmphasis(true);
            $printer->text(str_pad("BALANCE:", 43, " ", STR_PAD_LEFT) . " " . number_format($balance, 0) . "\n");
            $printer->setEmphasis(false);
            $printer->text("Served by: " . $sale->user->name . "\n");

            // FOOTER
            $printer->feed(1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you for your business!\n");
            $printer->text("System by Seadztech Technologies | 0790651941 | https://seadztech.co.ke\n");

            // Cut paper
            $printer->cut();

            return back()->with('success', 'Receipt printed successfully');

        } catch (\Throwable $e) {

            Log::error('ESC/POS print failed', [
                'sale_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Printing failed: ' . $e->getMessage()
            ], 500);

        } finally {
            if ($printer) {
                $printer->close();
            }
        }
    }
}