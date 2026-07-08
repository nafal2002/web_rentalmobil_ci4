<?php

namespace App\Controllers\Api;

use App\Models\Booking_model;
use App\Models\Payment_model;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class DashboardController extends Controller
{
    use ResponseTrait;

    protected $bookingModel;
    protected $paymentModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->bookingModel = new Booking_model();
        $this->paymentModel = new Payment_model();
    }

    /**
     * Get admin dashboard statistics
     * GET /api/v1/dashboard/admin
     */
    public function admin()
    {
        try {
            $db = \Config\Database::connect();

            // Total bookings
            $totalBookings = $db->table('tabel_booking')->countAllResults();

            // Active bookings (ACTIVE status)
            $activeBookings = $db->table('tabel_booking')
                ->where('booking_status', 'ACTIVE')
                ->countAllResults();

            // Pending bookings
            $pendingBookings = $db->table('tabel_booking')
                ->where('booking_status', 'PENDING')
                ->countAllResults();

            // Total revenue
            $totalRevenue = $db->table('tabel_payment')
                ->selectSum('payment_amount')
                ->where('payment_status', 'COMPLETED')
                ->get()->getRow()->payment_amount ?? 0;

            // Total customers
            $totalCustomers = $db->table('tabel_booking')
                ->distinct()
                ->countAllResults();

            // Average rating
            $avgRating = $db->table('tabel_reviews')
                ->selectAvg('rating')
                ->get()->getRow()->rating ?? 0;

            // Available cars
            $totalCars = $db->table('tabel_mobil')->countAllResults();

            // Recent bookings
            $recentBookings = $this->bookingModel->getBookingsWithDetails();
            $recentBookings = array_slice($recentBookings, 0, 5);

            $data = [
                'total_bookings' => $totalBookings,
                'active_bookings' => $activeBookings,
                'pending_bookings' => $pendingBookings,
                'total_revenue' => (float)$totalRevenue,
                'total_customers' => $totalCustomers,
                'average_rating' => (float)$avgRating,
                'total_cars' => $totalCars,
                'recent_bookings' => $recentBookings
            ];

            return $this->respond($data, 200);
        } catch (\Exception $e) {
            return $this->fail('Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get customer dashboard
     * GET /api/v1/dashboard/customer/{customerId}
     */
    public function customer($customerId = null)
    {
        try {
            if (!$customerId) {
                return $this->fail('Customer ID required', 400);
            }

            $db = \Config\Database::connect();

            // Customer info
            $customer = $db->table('tabel_penyewa')
                ->where('penyewa_id', $customerId)
                ->get()->getRowArray();

            if (!$customer) {
                return $this->failNotFound('Customer not found');
            }

            // Customer bookings
            $bookings = $this->bookingModel->getCustomerBookings($customerId);

            // Total spent
            $totalSpent = $db->table('tabel_payment')
                ->selectSum('payment_amount')
                ->join('tabel_booking', 'tabel_payment.booking_id = tabel_booking.booking_id')
                ->where('tabel_booking.customer_id', $customerId)
                ->where('tabel_payment.payment_status', 'COMPLETED')
                ->get()->getRow()->payment_amount ?? 0;

            // Completed bookings
            $completedBookings = $db->table('tabel_booking')
                ->where('customer_id', $customerId)
                ->where('booking_status', 'COMPLETED')
                ->countAllResults();

            $data = [
                'customer' => $customer,
                'total_bookings' => count($bookings),
                'completed_bookings' => $completedBookings,
                'total_spent' => (float)$totalSpent,
                'recent_bookings' => array_slice($bookings, 0, 5)
            ];

            return $this->respond($data, 200);
        } catch (\Exception $e) {
            return $this->fail('Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get revenue statistics
     * GET /api/v1/dashboard/revenue?period=monthly&year=2024
     */
    public function revenue()
    {
        try {
            $period = $this->request->getVar('period') ?? 'monthly'; // daily, weekly, monthly
            $year = $this->request->getVar('year') ?? date('Y');

            $db = \Config\Database::connect();

            if ($period === 'daily') {
                // Last 30 days
                $revenue = $db->table('tabel_payment')
                    ->select('DATE(created_at) as date, SUM(payment_amount) as amount')
                    ->where('payment_status', 'COMPLETED')
                    ->where('YEAR(created_at)', $year)
                    ->groupBy('DATE(created_at)')
                    ->orderBy('date', 'DESC')
                    ->limit(30)
                    ->get()->getResultArray();
            } elseif ($period === 'monthly') {
                // By month
                $revenue = $db->table('tabel_payment')
                    ->select('MONTH(created_at) as month, SUM(payment_amount) as amount')
                    ->where('payment_status', 'COMPLETED')
                    ->where('YEAR(created_at)', $year)
                    ->groupBy('MONTH(created_at)')
                    ->orderBy('month', 'ASC')
                    ->get()->getResultArray();
            } else {
                // Weekly
                $revenue = $db->table('tabel_payment')
                    ->select('WEEK(created_at) as week, SUM(payment_amount) as amount')
                    ->where('payment_status', 'COMPLETED')
                    ->where('YEAR(created_at)', $year)
                    ->groupBy('WEEK(created_at)')
                    ->orderBy('week', 'ASC')
                    ->get()->getResultArray();
            }

            return $this->respond([
                'period' => $period,
                'year' => $year,
                'data' => $revenue
            ], 200);
        } catch (\Exception $e) {
            return $this->fail('Error: ' . $e->getMessage(), 500);
        }
    }
}
