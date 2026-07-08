<?php

namespace App\Controllers\Api;

use App\Models\Booking_model;
use App\Models\Payment_model;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

class BookingController extends ResourceController
{
    protected $modelName = 'App\Models\Booking_model';
    protected $format = 'json';
    protected $bookingModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->bookingModel = new Booking_model();
        $this->paymentModel = new Payment_model();
    }

    // GET - Get all bookings
    public function index()
    {
        $bookings = $this->bookingModel->getBookingsWithDetails();
        return $this->respond($bookings, ResponseInterface::HTTP_OK);
    }

    // GET - Get booking by ID
    public function show($id = null)
    {
        $booking = $this->bookingModel->getBookingDetail($id);
        if (!$booking) {
            return $this->failNotFound('Booking not found');
        }
        return $this->respond($booking, ResponseInterface::HTTP_OK);
    }

    // POST - Create new booking
    public function create()
    {
        $data = $this->request->getJSON(true);

        // Validate required fields
        if (!$data || !isset($data['customer_id'], $data['car_id'], $data['booking_date_from'], $data['booking_date_to'])) {
            return $this->fail('Missing required fields', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['booking_status'] = 'PENDING';

        $id = $this->bookingModel->insert($data);
        return $this->respondCreated(['booking_id' => $id, 'message' => 'Booking created successfully']);
    }

    // PUT - Update booking
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (!$this->bookingModel->update($id, $data)) {
            return $this->fail('Failed to update booking');
        }

        return $this->respond(['message' => 'Booking updated successfully']);
    }

    // DELETE - Cancel booking
    public function delete($id = null)
    {
        $this->bookingModel->updateBookingStatus($id, 'CANCELLED');
        return $this->respond(['message' => 'Booking cancelled successfully']);
    }

    // GET - Get customer bookings
    public function customerBookings($customerId = null)
    {
        $bookings = $this->bookingModel->getCustomerBookings($customerId);
        return $this->respond($bookings);
    }
}
