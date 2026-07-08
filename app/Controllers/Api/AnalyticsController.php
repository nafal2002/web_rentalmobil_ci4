<?php

namespace App\Controllers\Api;

use App\Models\Analytics_model;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

class AnalyticsController extends ResourceController
{
    protected $modelName = 'App\Models\Analytics_model';
    protected $format = 'json';
    protected $analyticsModel;

    public function __construct()
    {
        $this->analyticsModel = new Analytics_model();
    }

    // GET - Get analytics by date range
    public function index()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        if (!$from || !$to) {
            return $this->fail('Missing from and to date parameters', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $analytics = $this->analyticsModel->getAnalyticsByDateRange($from, $to);
        return $this->respond($analytics);
    }

    // GET - Get latest analytics
    public function latest()
    {
        $analytics = $this->analyticsModel->getLatestAnalytics();
        if (!$analytics) {
            return $this->failNotFound('No analytics data found');
        }
        return $this->respond($analytics);
    }

    // POST - Calculate daily analytics
    public function calculate()
    {
        $data = $this->request->getJSON(true);

        if (!isset($data['date'])) {
            return $this->fail('Missing date parameter', ResponseInterface::HTTP_BAD_REQUEST);
        }

        if ($this->analyticsModel->calculateDailyAnalytics($data['date'])) {
            return $this->respondCreated(['message' => 'Analytics calculated successfully']);
        }

        return $this->fail('Failed to calculate analytics');
    }
}
