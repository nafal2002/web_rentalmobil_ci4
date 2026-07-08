<?php

namespace App\Models;

use CodeIgniter\Model;

class Payment_model extends Model
{
    protected $table = 'tabel_payment';
    protected $primaryKey = 'payment_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'booking_id', 'payment_amount', 'payment_method', 'payment_status',
        'payment_reference', 'payment_gateway', 'created_at', 'updated_at'
    ];

    // Get all payments
    public function getAllPayments()
    {
        return $this->db->table($this->table)
            ->select('tabel_payment.*, tabel_booking.booking_date_from, tabel_penyewa.penyewa_nama, tabel_penyewa.penyewa_email')
            ->join('tabel_booking', 'tabel_payment.booking_id = tabel_booking.booking_id')
            ->join('tabel_penyewa', 'tabel_booking.customer_id = tabel_penyewa.penyewa_id')
            ->orderBy('tabel_payment.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Get payment by booking ID
    public function getPaymentByBooking($bookingId)
    {
        return $this->where('booking_id', $bookingId)
            ->first();
    }

    // Get payments by status
    public function getPaymentsByStatus($status)
    {
        return $this->where('payment_status', $status)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    // Create payment record
    public function createPayment($data)
    {
        return $this->insert($data);
    }

    // Update payment status
    public function updatePaymentStatus($paymentId, $status)
    {
        return $this->update($paymentId, [
            'payment_status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    // Get total revenue
    public function getTotalRevenue($dateFrom = null, $dateTo = null)
    {
        $query = $this->db->table($this->table)
            ->selectSum('payment_amount')
            ->where('payment_status', 'COMPLETED');

        if ($dateFrom) {
            $query->where('created_at >=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at <=', $dateTo);
        }

        return $query->get()->getRow()->payment_amount ?? 0;
    }
}
