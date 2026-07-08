<?php

namespace App\Controllers;

use App\Models\Booking_model;
use App\Models\Payment_model;

class BookingManagement extends BaseController
{
    protected $bookingModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->bookingModel = new Booking_model();
        $this->paymentModel = new Payment_model();
    }

    /**
     * List all bookings with filters
     */
    public function index()
    {
        $status = $this->request->getVar('status');
        $sortBy = $this->request->getVar('sort') ?? 'created_at';
        $order = $this->request->getVar('order') ?? 'DESC';

        if ($status) {
            $bookings = $this->bookingModel->getBookingsByStatus($status);
        } else {
            $bookings = $this->bookingModel->getBookingsWithDetails();
        }

        $data = [
            'bookings' => $bookings,
            'title' => 'Booking Management'
        ];

        return view('backend/v_booking_management', $data);
    }

    /**
     * View booking detail
     */
    public function detail($bookingId)
    {
        $booking = $this->bookingModel->getBookingDetail($bookingId);
        $payment = $this->paymentModel->getPaymentByBooking($bookingId);

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found');
        }

        $data = [
            'booking' => $booking,
            'payment' => $payment,
            'title' => 'Booking Detail'
        ];

        return view('backend/v_booking_detail', $data);
    }

    /**
     * Update booking status
     */
    public function updateStatus($bookingId)
    {
        $status = $this->request->getPost('status');

        if (!$status) {
            return redirect()->back()->with('error', 'Status required');
        }

        $this->bookingModel->updateBookingStatus($bookingId, $status);

        return redirect()->back()->with('success', 'Booking status updated');
    }

    /**
     * Assign driver to booking
     */
    public function assignDriver($bookingId)
    {
        $driverId = $this->request->getPost('driver_id');

        if (!$driverId) {
            return redirect()->back()->with('error', 'Driver required');
        }

        $this->bookingModel->update($bookingId, ['driver_id' => $driverId]);

        return redirect()->back()->with('success', 'Driver assigned');
    }

    /**
     * Cancel booking
     */
    public function cancel($bookingId)
    {
        $this->bookingModel->updateBookingStatus($bookingId, 'CANCELLED');

        return redirect()->back()->with('success', 'Booking cancelled');
    }
}
