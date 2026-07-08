<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3">Detail Pembayaran</h1>
            <a href="<?php echo base_url('payment-report'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Payment Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">No. Invoice:</label>
                            <p>INV-<?php echo str_pad($payment['payment_id'], 6, '0', STR_PAD_LEFT); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Pembayaran:</label>
                            <p><?php echo date('d/m/Y H:i:s', strtotime($payment['created_at'])); ?></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Metode Pembayaran:</label>
                            <p>
                                <span class="badge bg-info"><?php echo $payment['payment_method']; ?></span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Status:</label>
                            <p>
                                <?php 
                                    $statusClass = '';
                                    if ($payment['payment_status'] === 'COMPLETED') $statusClass = 'success';
                                    else if ($payment['payment_status'] === 'PENDING') $statusClass = 'warning';
                                    else if ($payment['payment_status'] === 'FAILED') $statusClass = 'danger';
                                    else if ($payment['payment_status'] === 'REFUNDED') $statusClass = 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $statusClass; ?>">
                                    <?php echo $payment['payment_status']; ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Gateway:</label>
                            <p><?php echo $payment['payment_gateway'] ?? '-'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">No. Referensi:</label>
                            <p><?php echo $payment['payment_reference'] ?? '-'; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Informasi Rental</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Kendaraan:</label>
                            <p><?php echo $booking['merk_nama'] . ' ' . $booking['jenis_nama']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">No. Polisi:</label>
                            <p><?php echo $booking['mobil_no_polisi']; ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Mulai:</label>
                            <p><?php echo date('d/m/Y H:i', strtotime($booking['booking_date_from'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Tanggal Selesai:</label>
                            <p><?php echo date('d/m/Y H:i', strtotime($booking['booking_date_to'])); ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Lokasi Pickup:</label>
                            <p><?php echo $booking['pickup_location']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Lokasi Dropoff:</label>
                            <p><?php echo $booking['dropoff_location']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Amount Card -->
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Jumlah Pembayaran</h5>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-success mb-0">
                        Rp <?php echo number_format($payment['payment_amount'], 0, ',', '.'); ?>
                    </h2>
                </div>
            </div>

            <!-- Customer Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Data Pelanggan</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong><?php echo $booking['penyewa_nama']; ?></strong>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-envelope"></i>
                        <small><?php echo $booking['penyewa_email']; ?></small>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-phone"></i>
                        <small><?php echo $booking['penyewa_no_telp']; ?></small>
                    </p>
                </div>
            </div>

            <!-- Print Options Card -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Opsi Cetak</h5>
                </div>
                <div class="card-body">
                    <a href="<?php echo base_url('invoice/invoice/' . $payment['payment_id']); ?>" 
                       class="btn btn-primary w-100 mb-2" target="_blank">
                        <i class="fas fa-file-pdf"></i> Cetak Invoice
                    </a>
                    <a href="<?php echo base_url('invoice/receipt/' . $payment['payment_id']); ?>" 
                       class="btn btn-success w-100" target="_blank">
                        <i class="fas fa-receipt"></i> Cetak Struk
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-bold {
        font-weight: 700;
    }
</style>
