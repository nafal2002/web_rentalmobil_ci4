<?php

namespace App\Controllers;

use App\Models\Payment_model;
use App\Models\Booking_model;

class PaymentReport extends BaseController
{
    protected $paymentModel;
    protected $bookingModel;

    public function __construct()
    {
        $this->paymentModel = new Payment_model();
        $this->bookingModel = new Booking_model();
    }

    /**
     * Payment list with print options
     */
    public function index()
    {
        $status = $this->request->getVar('status');
        $dateFrom = $this->request->getVar('from');
        $dateTo = $this->request->getVar('to');

        $query = $this->paymentModel->getAllPayments();

        // Filter by status
        if ($status) {
            $query = array_filter($query, function ($p) use ($status) {
                return $p['payment_status'] === $status;
            });
        }

        // Filter by date range
        if ($dateFrom && $dateTo) {
            $query = array_filter($query, function ($p) use ($dateFrom, $dateTo) {
                $date = date('Y-m-d', strtotime($p['created_at']));
                return $date >= $dateFrom && $date <= $dateTo;
            });
        }

        // Sort by date descending
        usort($query, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        $data = [
            'payments' => $query,
            'status' => $status,
            'from' => $dateFrom,
            'to' => $dateTo,
            'total_payments' => count($query),
            'total_amount' => array_sum(array_column($query, 'payment_amount')),
            'title' => 'Laporan Pembayaran'
        ];

        return view('backend/v_payment_report', $data);
    }

    /**
     * View payment detail
     */
    public function detail($paymentId)
    {
        $payment = $this->paymentModel->find($paymentId);
        if (!$payment) {
            return redirect()->back()->with('error', 'Pembayaran tidak ditemukan');
        }

        $booking = $this->bookingModel->getBookingDetail($payment['booking_id']);

        $data = [
            'payment' => $payment,
            'booking' => $booking,
            'title' => 'Detail Pembayaran'
        ];

        return view('backend/v_payment_detail', $data);
    }
}
