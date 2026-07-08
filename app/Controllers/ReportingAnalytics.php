<?php

namespace App\Controllers;

use App\Models\Analytics_model;

class ReportingAnalytics extends BaseController
{
    protected $analyticsModel;

    public function __construct()
    {
        $this->analyticsModel = new Analytics_model();
    }

    /**
     * Daily report
     */
    public function daily()
    {
        $date = $this->request->getVar('date') ?? date('Y-m-d');

        $analytics = $this->analyticsModel->db->table('tabel_analytics')
            ->where('analytics_date', $date)
            ->first();

        if (!$analytics) {
            // Calculate if not exists
            $this->analyticsModel->calculateDailyAnalytics($date);
            $analytics = $this->analyticsModel->db->table('tabel_analytics')
                ->where('analytics_date', $date)
                ->first();
        }

        $data = [
            'date' => $date,
            'analytics' => $analytics,
            'title' => 'Daily Report'
        ];

        return view('backend/v_report_daily', $data);
    }

    /**
     * Monthly report
     */
    public function monthly()
    {
        $year = $this->request->getVar('year') ?? date('Y');
        $month = $this->request->getVar('month') ?? date('m');

        $db = \Config\Database::connect();
        $analytics = $db->table('tabel_analytics')
            ->where('YEAR(analytics_date)', $year)
            ->where('MONTH(analytics_date)', $month)
            ->get()->getResultArray();

        $data = [
            'year' => $year,
            'month' => $month,
            'analytics' => $analytics,
            'title' => 'Monthly Report'
        ];

        return view('backend/v_report_monthly', $data);
    }

    /**
     * Custom date range report
     */
    public function customRange()
    {
        $dateFrom = $this->request->getVar('from');
        $dateTo = $this->request->getVar('to');

        if ($dateFrom && $dateTo) {
            $analytics = $this->analyticsModel->getAnalyticsByDateRange($dateFrom, $dateTo);
        } else {
            $analytics = [];
        }

        $data = [
            'from' => $dateFrom,
            'to' => $dateTo,
            'analytics' => $analytics,
            'title' => 'Custom Range Report'
        ];

        return view('backend/v_report_custom', $data);
    }

    /**
     * Export report to PDF
     */
    public function exportPdf()
    {
        $dateFrom = $this->request->getVar('from');
        $dateTo = $this->request->getVar('to');

        $analytics = $this->analyticsModel->getAnalyticsByDateRange($dateFrom, $dateTo);

        // Generate PDF logic here
        // Using mPDF or similar library

        return view('backend/v_report_pdf', ['analytics' => $analytics]);
    }

    /**
     * Export report to Excel
     */
    public function exportExcel()
    {
        $dateFrom = $this->request->getVar('from');
        $dateTo = $this->request->getVar('to');

        $analytics = $this->analyticsModel->getAnalyticsByDateRange($dateFrom, $dateTo);

        // Generate Excel logic here
        // Using PhpSpreadsheet or similar library

        return view('backend/v_report_excel', ['analytics' => $analytics]);
    }
}
