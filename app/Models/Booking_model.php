<?php

namespace App\Models;

use CodeIgniter\Model;

class Booking_model extends Model
{
    protected $table = 'tabel_booking';
    protected $primaryKey = 'booking_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'customer_id', 'car_id', 'booking_date_from', 'booking_date_to',
        'booking_status', 'booking_total_price', 'booking_notes', 'driver_id',
        'pickup_location', 'dropoff_location', 'gps_latitude', 'gps_longitude',
        'created_at', 'updated_at'
    ];

    // Get all bookings with customer and car details
    public function getBookingsWithDetails()
    {
        return $this->db->table($this->table)
            ->select('tabel_booking.*, tabel_penyewa.penyewa_nama, tabel_penyewa.penyewa_email, '
                . 'tabel_penyewa.penyewa_no_telp, tabel_mobil.mobil_no_polisi, tabel_mobil.mobil_harga_sewa, '
                . 'tabel_merk.merk_nama, tabel_jenis.jenis_nama')
            ->join('tabel_penyewa', 'tabel_booking.customer_id = tabel_penyewa.penyewa_id')
            ->join('tabel_mobil', 'tabel_booking.car_id = tabel_mobil.mobil_id')
            ->join('tabel_merk', 'tabel_mobil.mobil_id_merk = tabel_merk.merk_id')
            ->join('tabel_jenis', 'tabel_mobil.mobil_id_jenis = tabel_jenis.jenis_id')
            ->get()
            ->getResultArray();
    }

    // Get booking by ID with full details
    public function getBookingDetail($bookingId)
    {
        return $this->db->table($this->table)
            ->select('tabel_booking.*, tabel_penyewa.penyewa_nama, tabel_penyewa.penyewa_email, '
                . 'tabel_penyewa.penyewa_no_telp, tabel_mobil.mobil_no_polisi, tabel_mobil.mobil_harga_sewa, '
                . 'tabel_merk.merk_nama, tabel_jenis.jenis_nama, tabel_driver.driver_nama')
            ->join('tabel_penyewa', 'tabel_booking.customer_id = tabel_penyewa.penyewa_id')
            ->join('tabel_mobil', 'tabel_booking.car_id = tabel_mobil.mobil_id')
            ->join('tabel_merk', 'tabel_mobil.mobil_id_merk = tabel_merk.merk_id')
            ->join('tabel_jenis', 'tabel_mobil.mobil_id_jenis = tabel_jenis.jenis_id')
            ->join('tabel_driver', 'tabel_booking.driver_id = tabel_driver.driver_id', 'left')
            ->where('tabel_booking.booking_id', $bookingId)
            ->get()
            ->getRowArray();
    }

    // Get bookings by customer
    public function getCustomerBookings($customerId)
    {
        return $this->db->table($this->table)
            ->select('tabel_booking.*, tabel_mobil.mobil_no_polisi, tabel_merk.merk_nama')
            ->join('tabel_mobil', 'tabel_booking.car_id = tabel_mobil.mobil_id')
            ->join('tabel_merk', 'tabel_mobil.mobil_id_merk = tabel_merk.merk_id')
            ->where('tabel_booking.customer_id', $customerId)
            ->orderBy('tabel_booking.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Get bookings by status
    public function getBookingsByStatus($status)
    {
        return $this->where('booking_status', $status)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    // Create new booking
    public function createBooking($data)
    {
        return $this->insert($data);
    }

    // Update booking status
    public function updateBookingStatus($bookingId, $status)
    {
        return $this->update($bookingId, [
            'booking_status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
