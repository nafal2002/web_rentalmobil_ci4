<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentSystem extends Migration
{
    public function up()
    {
        // Tabel untuk Payment/Pembayaran
        $this->forge->addField([
            'payment_id' => [
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
            'payment_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['TRANSFER', 'CARD', 'E_WALLET', 'CASH'],
                'default' => 'TRANSFER',
            ],
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['PENDING', 'COMPLETED', 'FAILED', 'REFUNDED'],
                'default' => 'PENDING',
            ],
            'payment_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'payment_gateway' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('payment_id', true);
        $this->forge->addForeignKey('booking_id', 'tabel_sewa', 'sewa_no_nota', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tabel_payment');

        // Tabel untuk Booking dengan status lebih detail
        $this->forge->addField([
            'booking_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'car_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'booking_date_from' => [
                'type' => 'DATETIME',
            ],
            'booking_date_to' => [
                'type' => 'DATETIME',
            ],
            'booking_status' => [
                'type' => 'ENUM',
                'constraint' => ['PENDING', 'CONFIRMED', 'ACTIVE', 'COMPLETED', 'CANCELLED'],
                'default' => 'PENDING',
            ],
            'booking_total_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'booking_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'driver_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'pickup_location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'dropoff_location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'gps_latitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
                'null' => true,
            ],
            'gps_longitude' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('booking_id', true);
        $this->forge->addForeignKey('customer_id', 'tabel_penyewa', 'penyewa_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('car_id', 'tabel_mobil', 'mobil_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('driver_id', 'tabel_driver', 'driver_id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('tabel_booking');
    }

    public function down()
    {
        $this->forge->dropTable('tabel_payment');
        $this->forge->dropTable('tabel_booking');
    }
}
