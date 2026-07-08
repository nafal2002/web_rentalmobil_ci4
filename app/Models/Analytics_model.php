<?php

namespace App\Models;

use CodeIgniter\Model;

class Analytics_model extends Model
{
    protected $table = 'tabel_analytics';
    protected $primaryKey = 'analytics_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'analytics_date', 'total_bookings', 'total_revenue', 'total_customers',
        'average_rating', 'occupancy_rate', 'created_at'
    ];

    // Get analytics data for date range
    public function getAnalyticsByDateRange($dateFrom, $dateTo)
    {
        return $this->where('analytics_date >=', $dateFrom)
            ->where('analytics_date <=', $dateTo)
            ->orderBy('analytics_date', 'ASC')
            ->findAll();
    }

    // Get latest analytics
    public function getLatestAnalytics()
    {
        return $this->orderBy('analytics_date', 'DESC')
            ->first();
    }

    // Calculate and save daily analytics
    public function calculateDailyAnalytics($date)
    {
        $db = \Config\Database::connect();

        // Count total bookings
        $totalBookings = $db->table('tabel_booking')
            ->where('DATE(created_at)', $date)
            ->countAllResults();

        // Calculate total revenue
        $totalRevenue = $db->table('tabel_payment')
            ->selectSum('payment_amount')
            ->where('DATE(created_at)', $date)
            ->where('payment_status', 'COMPLETED')
            ->get()->getRow()->payment_amount ?? 0;

        // Count unique customers
        $totalCustomers = $db->table('tabel_booking')
            ->distinct()
            ->where('DATE(created_at)', $date)
            ->countAllResults();

        // Calculate average rating
        $avgRating = $db->table('tabel_reviews')
            ->selectAvg('rating')
            ->where('DATE(created_at)', $date)
            ->get()->getRow()->rating ?? 0;

        $data = [
            'analytics_date' => $date,
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'total_customers' => $totalCustomers,
            'average_rating' => $avgRating,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }
}
