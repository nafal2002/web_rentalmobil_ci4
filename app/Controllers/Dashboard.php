<?php

namespace App\Controllers;

use App\Models\Booking_model;
use App\Models\Payment_model;
use App\Models\Analytics_model;

class Dashboard extends BaseController
{
    protected $bookingModel;
    protected $paymentModel;
    protected $analyticsModel;

    public function __construct()
    {
        $this->bookingModel = new Booking_model();
        $this->paymentModel = new Payment_model();
        $this->analyticsModel = new Analytics_model();
    }

    public function admin()
    {
        // Get dashboard statistics
        $totalBookings = count($this->bookingModel->findAll());
        $pendingBookings = count($this->bookingModel->getBookingsByStatus('PENDING'));
        $totalRevenue = $this->paymentModel->getTotalRevenue();
        $latestAnalytics = $this->analyticsModel->getLatestAnalytics();

        $data = [
            'total_bookings' => $totalBookings,
            'pending_bookings' => $pendingBookings,
            'total_revenue' => $totalRevenue,
            'latest_analytics' => $latestAnalytics,
            'recent_bookings' => array_slice($this->bookingModel->getBookingsWithDetails(), 0, 5)
        ];

        return view('backend/v_dashboard_admin', $data);
    }

    public function customer($customerId)
    {
        $bookings = $this->bookingModel->getCustomerBookings($customerId);
        $totalSpent = $this->paymentModel->getTotalRevenue();

        $data = [
            'bookings' => $bookings,
            'total_spent' => $totalSpent,
            'total_bookings' => count($bookings)
        ];

        return view('frontend/v_dashboard_customer', $data);
    }
}
