<?php
// Livewire component: Receipt.php
namespace App\Livewire;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Mpdf\Mpdf;

class Receipt extends Component
{
    public $sale;

    public function mount($id)
    {
        $this->sale = Sale::with(['saleItems', 'transactions'])->findOrFail($id);
    }

    // public function generateReceipt()
    // {
    //     try {
    //         $sale = $this->sale;

    //         // Configure DOMPDF
    //         $options = new Options();
    //         $options->set([
    //             'defaultFont' => 'DejaVu Sans',
    //             'isRemoteEnabled' => true,
    //             'isHtml5ParserEnabled' => true,
    //             'isPhpEnabled' => true,
    //             'tempDir' => storage_path('app/dompdf/tmp'),
    //             'fontDir' => storage_path('fonts/'),
    //             'fontCache' => storage_path('fonts/'),
    //             'logOutputFile' => storage_path('logs/dompdf.log'),
    //             'defaultPaperSize' => [0, 0, 58 * 2.83465, 297 * 2.83465], // 58mm x 297mm in points
    //         ]);

    //         $dompdf = new Dompdf($options);

    //         // Generate HTML with proper encoding
    //         $html = view('receipts.salesReceipt', [
    //             'sale' => $sale,
    //             'logoPath' => public_path('logo.png'),
    //         ])->render();

    //         // Clean HTML output
    //         $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

    //         $dompdf->loadHtml($html);
    //         $dompdf->render();

    //         return response()->stream(fn() => print $dompdf->output(), 200, [
    //             'Content-Type' => 'application/pdf',
    //             'Content-Disposition' => 'inline; filename="Receipt_' . $sale->id . '.pdf"',
    //         ]);
    //     } catch (\Throwable $e) {
    //         Log::error('PDF Generation Failed', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);

    //         return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
    //     }
    // }

    // public function generateReceipt()
    // {
    //     try {
    //         $sale = $this->sale;

    //         // Generate HTML with proper encoding
    //         $html = view('receipts.salesReceipt', [
    //             'sale' => $sale,
    //             'logoPath' => public_path('logo.png')
    //         ])->render();

    //         // Create PDF instance
    //         $pdf = App::make('dompdf.wrapper');
    //         $pdf->loadHTML($html);

    //         // Set options
    //         $pdf->setOptions([
    //             'isRemoteEnabled' => true,
    //             'isHtml5ParserEnabled' => true,
    //             'defaultFont' => 'DejaVu Sans',
    //             'tempDir' => storage_path('app/dompdf/tmp'),
    //             'fontDir' => storage_path('fonts'),
    //             'fontCache' => storage_path('fonts'),
    //             'logOutputFile' => storage_path('logs/dompdf.log'),
    //         ]);

    //         // Set custom paper size (58mm width, 297mm height)
    //         $pdf->setPaper([0, 0, 58 * 2.83465, 297 * 2.83465]);

    //         // Return the PDF as a download
    //         return $pdf->stream("Receipt_{$sale->id}.pdf");

    //     } catch (\Throwable $e) {
    //         Log::error('PDF Generation Failed', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return back()->with('error', 'Failed to generate PDF: '.$e->getMessage());
    //     }
    // }

    public function generateReceipt()
    {
        try {
            $sale = $this->sale;

            // Generate HTML
            $html = view('receipts.salesReceipt', [
                'sale' => $sale,
                'logoPath' => public_path('logo.png'),
            ])->render();

            // Create PDF
            $pdf = Pdf::loadHTML($html)->setPaper([0, 0, 58 * 2.83465, 297 * 2.83465]); // 58mm x 297mm in points

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="Receipt_' . $sale->id . '.pdf"');
        } catch (\Throwable $e) {
            Log::error('PDF Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'sale_id' => $this->sale->id ?? null,
            ]);

            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.receipt');
    }
}
