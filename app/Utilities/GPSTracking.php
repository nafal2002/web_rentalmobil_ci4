<?php

namespace App\Utilities;

class GPSTracking
{
    /**
     * Record GPS location for tracking
     */
    public static function recordLocation($bookingId, $latitude, $longitude, $speed = null)
    {
        try {
            $db = \Config\Database::connect();
            $data = [
                'booking_id' => $bookingId,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'speed' => $speed,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $db->table('tabel_gps_tracking')->insert($data);
            return ['status' => 'recorded'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Get location history for a booking
     */
    public static function getLocationHistory($bookingId)
    {
        try {
            $db = \Config\Database::connect();
            $locations = $db->table('tabel_gps_tracking')
                ->where('booking_id', $bookingId)
                ->orderBy('timestamp', 'ASC')
                ->get()
                ->getResultArray();

            return ['status' => 'success', 'data' => $locations];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Get current location for booking
     */
    public static function getCurrentLocation($bookingId)
    {
        try {
            $db = \Config\Database::connect();
            $location = $db->table('tabel_gps_tracking')
                ->where('booking_id', $bookingId)
                ->orderBy('timestamp', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            return ['status' => 'success', 'data' => $location];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c; // Distance in km

        return $distance;
    }
}
