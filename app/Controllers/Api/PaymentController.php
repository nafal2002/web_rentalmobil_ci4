<?php

namespace App\Controllers\Api;

use App\Models\Payment_model;
use App\Models\Booking_model;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

class PaymentController extends ResourceController
{
    protected $modelName = 'App\Models\Payment_model';
    protected $format = 'json';
    protected $paymentModel;
    protected $bookingModel;

    public function __construct()
    {
        $this->paymentModel = new Payment_model();
        $this->bookingModel = new Booking_model();
    }

    // GET - Get all payments
    public function index()
    {
        $payments = $this->paymentModel->getAllPayments();
        return $this->respond($payments, ResponseInterface::HTTP_OK);
    }

    // GET - Get payment by ID
    public function show($id = null)
    {
        $payment = $this->paymentModel->find($id);
        if (!$payment) {
            return $this->failNotFound('Payment not found');
        }
        return $this->respond($payment);
    }

    // POST - Create payment
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!isset($data['booking_id'], $data['payment_amount'])) {
            return $this->fail('Missing required fields', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['payment_status'] = 'PENDING';

        $id = $this->paymentModel->insert($data);
        return $this->respondCreated(['payment_id' => $id, 'message' => 'Payment created successfully']);
    }

    // PUT - Update payment status
    public function updateStatus($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!isset($data['payment_status'])) {
            return $this->fail('Missing payment_status');
        }

        $this->paymentModel->updatePaymentStatus($id, $data['payment_status']);

        // If payment completed, update booking status
        if ($data['payment_status'] == 'COMPLETED') {
            $payment = $this->paymentModel->find($id);
            if ($payment) {
                $this->bookingModel->updateBookingStatus($payment['booking_id'], 'CONFIRMED');
            }
        }

        return $this->respond(['message' => 'Payment status updated']);
    }

    // GET - Get total revenue
    public function revenue()
    {
        $dateFrom = $this->request->getVar('from');
        $dateTo = $this->request->getVar('to');
        $total = $this->paymentModel->getTotalRevenue($dateFrom, $dateTo);
        return $this->respond(['total_revenue' => $total]);
    }
}
