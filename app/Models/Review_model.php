<?php

namespace App\Models;

use CodeIgniter\Model;

class Review_model extends Model
{
    protected $table = 'tabel_reviews';
    protected $primaryKey = 'review_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['booking_id', 'rating', 'review_comment', 'created_at'];

    // Get all reviews for a booking
    public function getBookingReviews($bookingId)
    {
        return $this->where('booking_id', $bookingId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    // Get average rating
    public function getAverageRating()
    {
        return $this->selectAvg('rating')
            ->first();
    }

    // Create review
    public function createReview($data)
    {
        return $this->insert($data);
    }
}
