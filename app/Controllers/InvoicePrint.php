<?php

namespace App\Controllers;

use App\Models\Payment_model;
use App\Models\Booking_model;
use Mpdf\Mpdf as MpdfMpdf;

class InvoicePrint extends BaseController
{
    protected $paymentModel;
    protected $bookingModel;

    public function __construct()
    {
        $this->paymentModel = new Payment_model();
        $this->bookingModel = new Booking_model();
    }

    /**
     * Generate Invoice PDF
     * GET /invoice/print/{paymentId}
     */
    public function invoice($paymentId)
    {
        try {
            $payment = $this->paymentModel->find($paymentId);
            if (!$payment) {
                return redirect()->back()->with('error', 'Pembayaran tidak ditemukan');
            }

            $booking = $this->bookingModel->getBookingDetail($payment['booking_id']);
            if (!$booking) {
                return redirect()->back()->with('error', 'Booking tidak ditemukan');
            }

            $invoiceData = [
                'payment' => $payment,
                'booking' => $booking,
                'company_name' => 'SANAK RENTAL',
                'company_address' => 'Komp. Parupuk Raya Blok H. 6',
                'company_phone' => '+62-812-3456-789',
                'company_email' => 'info@sanakrental.com',
                'invoice_date' => date('d/m/Y', strtotime($payment['created_at'])),
                'invoice_number' => 'INV-' . str_pad($payment['payment_id'], 6, '0', STR_PAD_LEFT),
            ];

            $html = $this->generateInvoiceHTML($invoiceData);

            // Generate PDF
            $mpdf = new MpdfMpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_left' => 15,
                'margin_right' => 15,
            ]);

            $mpdf->WriteHTML($html);
            $mpdf->Output('Invoice-' . $invoiceData['invoice_number'] . '.pdf', 'I');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Generate Receipt PDF
     * GET /invoice/receipt/{paymentId}
     */
    public function receipt($paymentId)
    {
        try {
            $payment = $this->paymentModel->find($paymentId);
            if (!$payment) {
                return redirect()->back()->with('error', 'Pembayaran tidak ditemukan');
            }

            $booking = $this->bookingModel->getBookingDetail($payment['booking_id']);
            if (!$booking) {
                return redirect()->back()->with('error', 'Booking tidak ditemukan');
            }

            $receiptData = [
                'payment' => $payment,
                'booking' => $booking,
                'receipt_number' => 'RCP-' . str_pad($payment['payment_id'], 6, '0', STR_PAD_LEFT),
                'receipt_date' => date('d/m/Y H:i:s', strtotime($payment['created_at'])),
            ];

            $html = $this->generateReceiptHTML($receiptData);

            // Generate PDF (Thermal receipt size)
            $mpdf = new MpdfMpdf([
                'mode' => 'utf-8',
                'format' => [80, 200], // Thermal printer size
                'margin_top' => 3,
                'margin_bottom' => 3,
                'margin_left' => 3,
                'margin_right' => 3,
            ]);

            $mpdf->WriteHTML($html);
            $mpdf->Output('Receipt-' . $receiptData['receipt_number'] . '.pdf', 'I');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Generate Payment Report (Multiple invoices)
     * GET /invoice/report?from=2024-07-01&to=2024-07-31
     */
    public function report()
    {
        try {
            $dateFrom = $this->request->getVar('from');
            $dateTo = $this->request->getVar('to');

            if (!$dateFrom || !$dateTo) {
                return redirect()->back()->with('error', 'Tanggal harus diisi');
            }

            $db = \Config\Database::connect();
            $payments = $db->table('tabel_payment')
                ->select('tabel_payment.*, tabel_booking.*, tabel_penyewa.penyewa_nama, tabel_penyewa.penyewa_email')
                ->join('tabel_booking', 'tabel_payment.booking_id = tabel_booking.booking_id')
                ->join('tabel_penyewa', 'tabel_booking.customer_id = tabel_penyewa.penyewa_id')
                ->where('DATE(tabel_payment.created_at) >=', $dateFrom)
                ->where('DATE(tabel_payment.created_at) <=', $dateTo)
                ->where('tabel_payment.payment_status', 'COMPLETED')
                ->orderBy('tabel_payment.created_at', 'DESC')
                ->get()
                ->getResultArray();

            $reportData = [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'payments' => $payments,
                'total_amount' => array_sum(array_column($payments, 'payment_amount')),
                'total_count' => count($payments),
            ];

            $html = $this->generateReportHTML($reportData);

            $mpdf = new MpdfMpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'L',
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_left' => 15,
                'margin_right' => 15,
            ]);

            $mpdf->WriteHTML($html);
            $mpdf->Output('Payment-Report-' . $dateFrom . '-' . $dateTo . '.pdf', 'I');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Generate Invoice HTML
     */
    private function generateInvoiceHTML($data)
    {
        $payment = $data['payment'];
        $booking = $data['booking'];

        $html = '<html><head><meta charset="UTF-8">';
        $html .= '<style>';
        $html .= 'body { font-family: Arial, sans-serif; font-size: 11px; }';
        $html .= '.header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }';
        $html .= '.header h1 { margin: 0; font-size: 18px; }';
        $html .= '.header p { margin: 2px 0; font-size: 10px; }';
        $html .= '.invoice-details { margin: 15px 0; }';
        $html .= '.invoice-details table { width: 100%; border-collapse: collapse; }';
        $html .= '.invoice-details td { padding: 5px; border: 1px solid #ddd; }';
        $html .= '.label { font-weight: bold; width: 35%; }';
        $html .= '.amount { text-align: right; }';
        $html .= '.footer { text-align: center; margin-top: 20px; font-size: 10px; border-top: 1px solid #ddd; padding-top: 10px; }';
        $html .= '.total-row { font-weight: bold; background-color: #f0f0f0; }';
        $html .= 'table.summary { width: 100%; margin-top: 20px; }';
        $html .= 'table.summary td { padding: 8px; border: 1px solid #ddd; }';
        $html .= '</style>';
        $html .= '</head><body>';

        // Header
        $html .= '<div class="header">';
        $html .= '<h1>' . $data['company_name'] . '</h1>';
        $html .= '<p>' . $data['company_address'] . '</p>';
        $html .= '<p>Phone: ' . $data['company_phone'] . ' | Email: ' . $data['company_email'] . '</p>';
        $html .= '</div>';

        // Invoice Title
        $html .= '<h2 style="text-align: center; margin: 20px 0;">INVOICE</h2>';
        $html .= '<div style="margin-bottom: 15px;">';
        $html .= '<strong>No. Invoice:</strong> ' . $data['invoice_number'] . '<br>';
        $html .= '<strong>Tanggal:</strong> ' . $data['invoice_date'] . '<br>';
        $html .= '</div>';

        // Customer Details
        $html .= '<table class="summary" cellpadding="5">';
        $html .= '<tr>';
        $html .= '<td colspan="2"><strong>Informasi Pelanggan</strong></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Nama:</strong></td>';
        $html .= '<td>' . $booking['penyewa_nama'] . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Email:</strong></td>';
        $html .= '<td>' . $booking['penyewa_email'] . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>No. Telepon:</strong></td>';
        $html .= '<td>' . $booking['penyewa_no_telp'] . '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        // Booking Details
        $html .= '<table class="summary" cellpadding="5" style="margin-top: 10px;">';
        $html .= '<tr>';
        $html .= '<td colspan="2"><strong>Informasi Rental</strong></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Kendaraan:</strong></td>';
        $html .= '<td>' . $booking['merk_nama'] . ' ' . $booking['jenis_nama'] . ' (' . $booking['mobil_no_polisi'] . ')</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Tanggal Mulai:</strong></td>';
        $html .= '<td>' . date('d/m/Y H:i', strtotime($booking['booking_date_from'])) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Tanggal Selesai:</strong></td>';
        $html .= '<td>' . date('d/m/Y H:i', strtotime($booking['booking_date_to'])) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Lokasi Pickup:</strong></td>';
        $html .= '<td>' . $booking['pickup_location'] . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Lokasi Dropoff:</strong></td>';
        $html .= '<td>' . $booking['dropoff_location'] . '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        // Payment Details
        $html .= '<table class="summary" cellpadding="5" style="margin-top: 10px;">';
        $html .= '<tr>';
        $html .= '<td colspan="2"><strong>Informasi Pembayaran</strong></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Metode Pembayaran:</strong></td>';
        $html .= '<td>' . $payment['payment_method'] . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Gateway:</strong></td>';
        $html .= '<td>' . ($payment['payment_gateway'] ?? '-') . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>No. Referensi:</strong></td>';
        $html .= '<td>' . ($payment['payment_reference'] ?? '-') . '</td>';
        $html .= '</tr>';
        $html .= '<tr class="total-row">';
        $html .= '<td><strong>Total Pembayaran:</strong></td>';
        $html .= '<td align="right"><strong>Rp ' . number_format($payment['payment_amount'], 0, ',', '.') . '</strong></td>';
        $html .= '</tr>';
        $html .= '</table>';

        // Footer
        $html .= '<div class="footer">';
        $html .= '<p>Terima kasih telah menggunakan layanan SANAK RENTAL</p>';
        $html .= '<p>Dokumen ini adalah bukti resmi pembayaran</p>';
        $html .= '<p style="margin-top: 20px; font-size: 9px;">Dicetak pada: ' . date('d/m/Y H:i:s') . '</p>';
        $html .= '</div>';

        $html .= '</body></html>';
        return $html;
    }

    /**
     * Generate Receipt HTML (Thermal printer)
     */
    private function generateReceiptHTML($data)
    {
        $payment = $data['payment'];
        $booking = $data['booking'];

        $html = '<html><head><meta charset="UTF-8">';
        $html .= '<style>';
        $html .= 'body { font-family: Courier, monospace; font-size: 11px; margin: 0; padding: 0; }';
        $html .= '.receipt { width: 80mm; margin: 0 auto; }';
        $html .= '.header { text-align: center; margin-bottom: 10px; border-bottom: 1px solid #000; padding-bottom: 5px; }';
        $html .= '.header h3 { margin: 0; font-size: 14px; }';
        $html .= '.header p { margin: 2px 0; font-size: 9px; }';
        $html .= '.row { display: flex; justify-content: space-between; margin: 3px 0; }';
        $html .= '.label { flex: 1; }';
        $html .= '.value { flex: 1; text-align: right; }';
        $html .= '.divider { border-top: 1px dashed #000; margin: 8px 0; }';
        $html .= '.total { font-weight: bold; font-size: 13px; }';
        $html .= '.footer { text-align: center; margin-top: 10px; border-top: 1px solid #000; padding-top: 5px; font-size: 9px; }';
        $html .= '</style>';
        $html .= '</head><body>';

        $html .= '<div class="receipt">';
        // Header
        $html .= '<div class="header">';
        $html .= '<h3>SANAK RENTAL</h3>';
        $html .= '<p>Struk Pembayaran</p>';
        $html .= '</div>';

        // Receipt Details
        $html .= '<div class="row"><div class="label">No. Struk:</div><div class="value">' . $data['receipt_number'] . '</div></div>';
        $html .= '<div class="row"><div class="label">Tanggal:</div><div class="value">' . $data['receipt_date'] . '</div></div>';
        $html .= '<div class="divider"></div>';

        $html .= '<div class="row"><div class="label"><strong>Pelanggan:</strong></div></div>';
        $html .= '<div class="row"><div class="label">' . $booking['penyewa_nama'] . '</div></div>';
        $html .= '<div class="divider"></div>';

        $html .= '<div class="row"><div class="label"><strong>Kendaraan:</strong></div></div>';
        $html .= '<div class="row"><div class="label">' . $booking['mobil_no_polisi'] . '</div></div>';
        $html .= '<div class="row"><div class="label">' . $booking['merk_nama'] . ' ' . $booking['jenis_nama'] . '</div></div>';
        $html .= '<div class="divider"></div>';

        $html .= '<div class="row"><div class="label"><strong>Metode:</strong></div><div class="value">' . $payment['payment_method'] . '</div></div>';
        $html .= '<div class="row"><div class="label"><strong>Status:</strong></div><div class="value">' . $payment['payment_status'] . '</div></div>';
        $html .= '<div class="divider"></div>';

        $html .= '<div class="row total">';
        $html .= '<div class="label">TOTAL:</div>';
        $html .= '<div class="value">Rp ' . number_format($payment['payment_amount'], 0, ',', '.') . '</div>';
        $html .= '</div>';

        // Footer
        $html .= '<div class="footer">';
        $html .= '<p>Terima kasih atas pembayaran Anda</p>';
        $html .= '<p>Bukti pembayaran ini harus disimpan</p>';
        $html .= '</div>';

        $html .= '</div></body></html>';
        return $html;
    }

    /**
     * Generate Report HTML
     */
    private function generateReportHTML($data)
    {
        $html = '<html><head><meta charset="UTF-8">';
        $html .= '<style>';
        $html .= 'body { font-family: Arial, sans-serif; font-size: 10px; }';
        $html .= '.header { text-align: center; margin-bottom: 20px; }';
        $html .= '.header h1 { margin: 0; font-size: 16px; }';
        $html .= '.header p { margin: 5px 0; }';
        $html .= 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
        $html .= 'table th { background-color: #333; color: #fff; padding: 8px; text-align: left; border: 1px solid #ddd; }';
        $html .= 'table td { padding: 8px; border: 1px solid #ddd; }';
        $html .= 'table tr:nth-child(even) { background-color: #f9f9f9; }';
        $html .= '.text-right { text-align: right; }';
        $html .= '.summary { margin-top: 20px; }';
        $html .= '.summary-row { display: flex; justify-content: space-between; margin: 5px 0; font-size: 12px; }';
        $html .= '.summary-row strong { font-weight: bold; }';
        $html .= '.footer { text-align: center; margin-top: 30px; font-size: 9px; border-top: 1px solid #ddd; padding-top: 10px; }';
        $html .= '</style>';
        $html .= '</head><body>';

        // Header
        $html .= '<div class="header">';
        $html .= '<h1>LAPORAN PEMBAYARAN</h1>';
        $html .= '<p>Periode: ' . date('d/m/Y', strtotime($data['date_from'])) . ' - ' . date('d/m/Y', strtotime($data['date_to'])) . '</p>';
        $html .= '<p>SANAK RENTAL</p>';
        $html .= '</div>';

        // Table
        $html .= '<table>';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>No.</th>';
        $html .= '<th>Invoice</th>';
        $html .= '<th>Pelanggan</th>';
        $html .= '<th>Kendaraan</th>';
        $html .= '<th>Metode</th>';
        $html .= '<th>Jumlah</th>';
        $html .= '<th>Tanggal</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $no = 1;
        foreach ($data['payments'] as $payment) {
            $html .= '<tr>';
            $html .= '<td>' . $no . '</td>';
            $html .= '<td>INV-' . str_pad($payment['payment_id'], 6, '0', STR_PAD_LEFT) . '</td>';
            $html .= '<td>' . $payment['penyewa_nama'] . '</td>';
            $html .= '<td>' . $payment['mobil_no_polisi'] . '</td>';
            $html .= '<td>' . $payment['payment_method'] . '</td>';
            $html .= '<td class="text-right">Rp ' . number_format($payment['payment_amount'], 0, ',', '.') . '</td>';
            $html .= '<td>' . date('d/m/Y', strtotime($payment['created_at'])) . '</td>';
            $html .= '</tr>';
            $no++;
        }

        $html .= '</tbody>';
        $html .= '</table>';

        // Summary
        $html .= '<div class="summary">';
        $html .= '<div class="summary-row">';
        $html .= '<strong>Total Transaksi:</strong>';
        $html .= '<strong>' . $data['total_count'] . '</strong>';
        $html .= '</div>';
        $html .= '<div class="summary-row">';
        $html .= '<strong>Total Pendapatan:</strong>';
        $html .= '<strong>Rp ' . number_format($data['total_amount'], 0, ',', '.') . '</strong>';
        $html .= '</div>';
        $html .= '</div>';

        // Footer
        $html .= '<div class="footer">';
        $html .= '<p>Laporan ini dicetak pada: ' . date('d/m/Y H:i:s') . '</p>';
        $html .= '</div>';

        $html .= '</body></html>';
        return $html;
    }
}
