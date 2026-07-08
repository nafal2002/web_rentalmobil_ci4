<?php

namespace App\Controllers;

use App\Models\Payment_model;
use App\Models\Booking_model;
use App\Utilities\PaymentGateway;

class PaymentManagement extends BaseController
{
    protected $paymentModel;
    protected $bookingModel;

    public function __construct()
    {
        $this->paymentModel = new Payment_model();
        $this->bookingModel = new Booking_model();
    }

    /**
     * List all payments
     */
    public function index()
    {
        $status = $this->request->getVar('status');
        $method = $this->request->getVar('method');

        $query = $this->paymentModel->getAllPayments();

        // Filter by status
        if ($status) {
            $query = array_filter($query, function ($p) use ($status) {
                return $p['payment_status'] === $status;
            });
        }

        // Filter by method
        if ($method) {
            $query = array_filter($query, function ($p) use ($method) {
                return $p['payment_method'] === $method;
            });
        }

        $data = [
            'payments' => $query,
            'title' => 'Payment Management'
        ];

        return view('backend/v_payment_management', $data);
    }

    /**
     * Create payment
     */
    public function create()
    {
        $bookingId = $this->request->getPost('booking_id');
        $method = $this->request->getPost('method');
        $amount = $this->request->getPost('amount');

        if (!$bookingId || !$method || !$amount) {
            return redirect()->back()->with('error', 'Missing required fields');
        }

        $paymentData = [
            'booking_id' => $bookingId,
            'payment_amount' => $amount,
            'payment_method' => $method,
            'payment_gateway' => 'midtrans',
            'payment_status' => 'PENDING',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->paymentModel->insert($paymentData);

        return redirect()->back()->with('success', 'Payment created');
    }

    /**
     * Process payment through gateway
     */
    public function process($paymentId)
    {
        $payment = $this->paymentModel->find($paymentId);
        if (!$payment) {
            return redirect()->back()->with('error', 'Payment not found');
        }

        $booking = $this->bookingModel->find($payment['booking_id']);
        $customer = $this->bookingModel->getBookingDetail($payment['booking_id']);

        $paymentData = [
            'booking_id' => $payment['booking_id'],
            'amount' => $payment['payment_amount'],
            'customer_name' => $customer['penyewa_nama'],
            'customer_email' => $customer['penyewa_email'],
            'customer_phone' => $customer['penyewa_no_telp']
        ];

        // Process through Midtrans
        $result = PaymentGateway::processMidtrans($paymentData);

        if ($result['status'] === 'success') {
            $this->paymentModel->update($paymentId, [
                'payment_reference' => $result['reference'],
                'payment_status' => 'COMPLETED'
            ]);
            $this->bookingModel->updateBookingStatus($payment['booking_id'], 'CONFIRMED');
            return redirect()->back()->with('success', 'Payment processed');
        }

        return redirect()->back()->with('error', 'Payment failed');
    }

    /**
     * Refund payment
     */
    public function refund($paymentId)
    {
        $reason = $this->request->getPost('reason');

        if (!$reason) {
            return redirect()->back()->with('error', 'Refund reason required');
        }

        $this->paymentModel->update($paymentId, [
            'payment_status' => 'REFUNDED',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Payment refunded');
    }
}
