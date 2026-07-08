<?php

namespace App\Controllers;

use App\Utilities\GPSTracking;
use App\Utilities\NotificationService;
use App\Models\Booking_model;

class GPSTrackingController extends BaseController
{
    protected $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new Booking_model();
    }

    /**
     * Get current location of active booking
     * GET /gps/current/{bookingId}
     */
    public function current($bookingId)
    {
        $location = GPSTracking::getCurrentLocation($bookingId);
        return $this->response->setJSON($location);
    }

    /**
     * Get location history for booking
     * GET /gps/history/{bookingId}
     */
    public function history($bookingId)
    {
        $history = GPSTracking::getLocationHistory($bookingId);
        return $this->response->setJSON($history);
    }

    /**
     * Record new GPS location
     * POST /gps/record
     */
    public function record()
    {
        $bookingId = $this->request->getPost('booking_id');
        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');
        $speed = $this->request->getPost('speed');

        if (!$bookingId || !$latitude || !$longitude) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing required parameters'
            ])->setStatusCode(400);
        }

        $result = GPSTracking::recordLocation($bookingId, $latitude, $longitude, $speed);
        return $this->response->setJSON($result);
    }

    /**
     * Get tracking map view
     */
    public function map($bookingId)
    {
        $booking = $this->bookingModel->getBookingDetail($bookingId);
        $history = GPSTracking::getLocationHistory($bookingId);

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found');
        }

        $data = [
            'booking' => $booking,
            'history' => $history['data'] ?? [],
            'title' => 'GPS Tracking - ' . $booking['mobil_no_polisi']
        ];

        return view('backend/v_gps_tracking', $data);
    }
}
