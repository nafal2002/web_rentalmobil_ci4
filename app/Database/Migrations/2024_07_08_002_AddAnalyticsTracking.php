<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAnalyticsTracking extends Migration
{
    public function up()
    {
        // Tabel untuk GPS Tracking
        $this->forge->addField([
            'tracking_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'booking_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'latitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
            ],
            'longitude' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
            ],
            'speed' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'timestamp' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('tracking_id', true);
        $this->forge->addForeignKey('booking_id', 'tabel_booking', 'booking_id', 'CASCADE', 'CASCADE');
        $this->forge->addIndex('booking_id');
        $this->forge->createTable('tabel_gps_tracking');

        // Tabel untuk Analytics/Statistik
        $this->forge->addField([
            'analytics_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'analytics_date' => [
                'type' => 'DATE',
            ],
            'total_bookings' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'total_revenue' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'total_customers' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'average_rating' => [
                'type' => 'DECIMAL',
                'constraint' => '3,2',
                'null' => true,
            ],
            'occupancy_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('analytics_id', true);
        $this->forge->addIndex('analytics_date');
        $this->forge->createTable('tabel_analytics');

        // Tabel untuk Customer Reviews/Rating
        $this->forge->addField([
            'review_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'booking_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'rating' => [
                'type' => 'INT',
                'constraint' => 5,
            ],
            'review_comment' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('review_id', true);
        $this->forge->addForeignKey('booking_id', 'tabel_booking', 'booking_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tabel_reviews');
    }

    public function down()
    {
        $this->forge->dropTable('tabel_gps_tracking');
        $this->forge->dropTable('tabel_analytics');
        $this->forge->dropTable('tabel_reviews');
    }
}
