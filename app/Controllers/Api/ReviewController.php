<?php

namespace App\Controllers\Api;

use App\Models\Review_model;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

class ReviewController extends ResourceController
{
    protected $modelName = 'App\Models\Review_model';
    protected $format = 'json';
    protected $reviewModel;

    public function __construct()
    {
        $this->reviewModel = new Review_model();
    }

    // GET - Get reviews by booking
    public function index($bookingId = null)
    {
        if (!$bookingId) {
            return $this->fail('Booking ID required', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $reviews = $this->reviewModel->getBookingReviews($bookingId);
        return $this->respond($reviews);
    }

    // POST - Create review
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!isset($data['booking_id'], $data['rating'])) {
            return $this->fail('Missing required fields', ResponseInterface::HTTP_BAD_REQUEST);
        }

        if ($data['rating'] < 1 || $data['rating'] > 5) {
            return $this->fail('Rating must be between 1 and 5', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $this->reviewModel->insert($data);

        return $this->respondCreated(['review_id' => $id, 'message' => 'Review created successfully']);
    }

    // GET - Get average rating
    public function average()
    {
        $avgRating = $this->reviewModel->getAverageRating();
        return $this->respond(['average_rating' => $avgRating]);
    }
}
