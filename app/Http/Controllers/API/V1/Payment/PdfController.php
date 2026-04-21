<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Models\Course\Enrollment;
use App\Models\PaymentHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use TCPDF;

class PdfController extends Controller
{
    public function generateReceipt(Request $request, int $paymentId)
    {
        $payment = PaymentHistory::with(['user', 'course'])->findOrFail($paymentId);

        if ($request->user() && $request->user()->id !== $payment->user_id) {
            if (! $request->user()->hasPermission('receipts.view')) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }
        }

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator('Eduvora');
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('Receipt - '.$payment->transaction_id);

        $pdf->AddPage();

        $html = $this->buildReceiptHtml($payment);
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'receipt_'.$payment->transaction_id.'.pdf';

        if ($request->get('download', false)) {
            $pdf->Output($filename, 'D');
        } else {
            $pdf->Output($filename, 'I');
        }

        return response()->json([
            'success' => true,
            'url' => route('payments.receipt.pdf', $paymentId),
        ]);
    }

    public function generateInvoice(Request $request, int $paymentId)
    {
        $payment = PaymentHistory::with(['user', 'course'])->findOrFail($paymentId);

        if ($request->user() && $request->user()->id !== $payment->user_id) {
            if (! $request->user()->hasPermission('invoices.view')) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }
        }

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator('Eduvora');
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('Invoice - '.$payment->transaction_id);

        $pdf->AddPage();

        $html = $this->buildInvoiceHtml($payment);
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'invoice_'.$payment->transaction_id.'.pdf';

        if ($request->get('download', false)) {
            $pdf->Output($filename, 'D');
        } else {
            $pdf->Output($filename, 'I');
        }

        return response()->json([
            'success' => true,
            'url' => route('payments.invoice.pdf', $paymentId),
        ]);
    }

    public function generateCertificate(Request $request, int $enrollmentId)
    {
        $enrollment = Enrollment::with(['user', 'course'])
            ->whereHas('certificate')
            ->findOrFail($enrollmentId);

        if (! $enrollment->completed) {
            return response()->json([
                'success' => false,
                'error' => 'Course not completed yet',
            ], 400);
        }

        $certificate = $enrollment->certificate;

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator('Eduvora');
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('Certificate - '.$certificate->certificate_number);

        $pdf->AddPage();

        $html = $this->buildCertificateHtml($enrollment);
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'certificate_'.$certificate->certificate_number.'.pdf';

        if ($request->get('download', false)) {
            $pdf->Output($filename, 'D');
        } else {
            $pdf->Output($filename, 'I');
        }

        return response()->json([
            'success' => true,
            'url' => route('certificates.pdf', $enrollmentId),
        ]);
    }

    public function bulkReceipts(Request $request)
    {
        $request->validate([
            'payment_ids' => 'required|array|min:1|max:50',
            'payment_ids.*' => 'integer|exists:payment_histories,id',
        ]);

        $payments = PaymentHistory::with(['user', 'course'])
            ->whereIn('id', $request->input('payment_ids'))
            ->where('status', 'completed')
            ->get();

        if ($payments->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => 'No completed payments found',
            ], 404);
        }

        $zipFileName = 'receipts_'.Carbon::now()->format('Y-m-d').'.zip';

        return response()->json([
            'success' => true,
            'count' => $payments->count(),
            'zip_file' => route('payments.receipts.bulk.zip', ['ids' => implode(',', $payments->pluck('id')->toArray())]),
        ]);
    }

    protected function buildReceiptHtml(PaymentHistory $payment): string
    {
        $companyName = config('app.name');
        $companyEmail = config('eduvora.email', 'support@eduvora.com');
        $companyAddress = config('eduvora.address', '123 Learning Street, Education City');

        return <<<HTML
        <style>
            .header { text-align: center; margin-bottom: 30px; }
            .company-name { font-size: 24px; font-weight: bold; color: #1a73e8; }
            .receipt-title { font-size: 18px; color: #333; margin-top: 10px; }
            .receipt-number { font-size: 14px; color: #666; margin-top: 5px; }
            .details { margin-top: 30px; }
            .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
            .detail-label { font-weight: bold; color: #666; }
            .detail-value { color: #333; }
            .total { margin-top: 20px; font-size: 18px; font-weight: bold; text-align: right; }
            .footer { margin-top: 50px; text-align: center; color: #999; font-size: 12px; }
            .status { display: inline-block; padding: 5px 15px; background: #4caf50; color: white; border-radius: 3px; }
        </style>
        
        <div class="header">
            <div class="company-name">{$companyName}</div>
            <div class="receipt-title">PAYMENT RECEIPT</div>
            <div class="receipt-number">Receipt #{$payment->transaction_id}</div>
        </div>
        
        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{$payment->created_at->format('F j, Y')}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Customer:</span>
                <span class="detail-value">{$payment->user->name}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{$payment->user->email}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Course:</span>
                <span class="detail-value">.($payment->course ? $payment->course->title : 'N/A').</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">{$payment->payment_method}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Transaction ID:</span>
                <span class="detail-value">{$payment->transaction_id}</span>
            </div>
        </div>
        
        <div class="total">
            Total Paid: {$payment->amount} {$payment->currency}
        </div>
        
        <div class="footer">
            <p>{$companyName}</p>
            <p>{$companyAddress}</p>
            <p>{$companyEmail}</p>
            <p>This is a computer-generated receipt and does not require a signature.</p>
        </div>
        HTML;
    }

    protected function buildInvoiceHtml(PaymentHistory $payment): string
    {
        $companyName = config('app.name');
        $companyEmail = config('eduvora.email', 'support@eduvora.com');
        $companyAddress = config('eduvora.address', '123 Learning Street, Education City');
        $invoiceNumber = 'INV-'.strtoupper($payment->transaction_id);

        return <<<HTML
        <style>
            .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
            .company-info { text-align: left; }
            .invoice-info { text-align: right; }
            .invoice-title { font-size: 24px; font-weight: bold; color: #1a73e8; }
            .bill-to { margin-top: 30px; }
            .bill-to-title { font-weight: bold; color: #666; margin-bottom: 10px; }
            .items-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
            .items-table th { background: #f5f5f5; padding: 12px; text-align: left; border-bottom: 2px solid #ddd; }
            .items-table td { padding: 12px; border-bottom: 1px solid #eee; }
            .items-table .amount { text-align: right; }
            .subtotal { margin-top: 20px; text-align: right; }
            .subtotal-row { display: flex; justify-content: flex-end; padding: 5px 0; }
            .total { font-size: 18px; font-weight: bold; margin-top: 10px; }
            .footer { margin-top: 50px; text-align: center; color: #999; font-size: 12px; }
        </style>
        
        <div class="header">
            <div class="company-info">
                <h2>{$companyName}</h2>
                <p>{$companyAddress}</p>
                <p>{$companyEmail}</p>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">INVOICE</div>
                <p><strong>Invoice #:</strong> {$invoiceNumber}</p>
                <p><strong>Date:</strong> {$payment->created_at->format('F j, Y')}</p>
                <p><strong>Due Date:</strong> {$payment->created_at->format('F j, Y')}</p>
            </div>
        </div>
        
        <div class="bill-to">
            <div class="bill-to-title">BILL TO:</div>
            <p><strong>{$payment->user->name}</strong></p>
            <p>{$payment->user->email}</p>
        </div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>.($payment->course ? $payment->course->title : 'Course Purchase').</td>
                    <td class="amount">{$payment->amount} {$payment->currency}</td>
                </tr>
            </tbody>
        </table>
        
        <div class="subtotal">
            <div class="subtotal-row">
                <span><strong>Subtotal:</strong> {$payment->amount} {$payment->currency}</span>
            </div>
            <div class="subtotal-row">
                <span><strong>Tax:</strong> 0.00 {$payment->currency}</span>
            </div>
            <div class="total">
                <span><strong>Total:</strong> {$payment->amount} {$payment->currency}</span>
            </div>
        </div>
        
        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>{$companyName} - {$companyEmail}</p>
        </div>
        HTML;
    }

    protected function buildCertificateHtml(Enrollment $enrollment): string
    {
        $user = $enrollment->user;
        $course = $enrollment->course;
        $certificate = $enrollment->certificate;

        return <<<HTML
        <style>
            .certificate { border: 10px solid #1a73e8; padding: 40px; text-align: center; height: 100%; }
            .title { font-size: 36px; color: #1a73e8; margin-bottom: 20px; }
            .subtitle { font-size: 18px; color: #666; margin-bottom: 10px; }
            .name { font-size: 32px; font-weight: bold; color: #333; margin: 30px 0; }
            .course-label { font-size: 14px; color: #666; }
            .course-title { font-size: 24px; font-weight: bold; color: #333; margin-top: 10px; }
            .date { font-size: 14px; color: #666; margin-top: 30px; }
            .cert-number { font-size: 12px; color: #999; margin-top: 20px; }
        </style>
        
        <div class="certificate">
            <div class="title">Certificate of Completion</div>
            <div class="subtitle">This is to certify that</div>
            <div class="name">{$user->name}</div>
            <div class="course-label">has successfully completed the course</div>
            <div class="course-title">{$course->title}</div>
            <div class="date">Issued on {$certificate->created_at->format('F j, Y')}</div>
            <div class="cert-number">Certificate ID: {$certificate->certificate_number}</div>
        </div>
        HTML;
    }
}
