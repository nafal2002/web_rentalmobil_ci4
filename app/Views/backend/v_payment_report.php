<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 d-inline-block">Laporan Pembayaran</h1>
            <div class="float-right">
                <a href="<?php echo base_url('invoice/report?from=' . date('Y-m-01') . '&to=' . date('Y-m-d')); ?>" 
                   class="btn btn-primary" target="_blank">
                    <i class="fas fa-file-pdf"></i> Cetak Laporan
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filter Pembayaran</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status Pembayaran</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="PENDING" <?php echo ($status === 'PENDING') ? 'selected' : ''; ?>>Pending</option>
                        <option value="COMPLETED" <?php echo ($status === 'COMPLETED') ? 'selected' : ''; ?>>Selesai</option>
                        <option value="FAILED" <?php echo ($status === 'FAILED') ? 'selected' : ''; ?>>Gagal</option>
                        <option value="REFUNDED" <?php echo ($status === 'REFUNDED') ? 'selected' : ''; ?>>Dikembalikan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="from" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="from" name="from" value="<?php echo $from; ?>">
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="to" name="to" value="<?php echo $to; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Transaksi</h5>
                    <h3 class="text-primary"><?php echo number_format($total_payments); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Pendapatan</h5>
                    <h3 class="text-success">Rp <?php echo number_format($total_amount, 0, ',', '.'); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Rata-rata Pembayaran</h5>
                    <h3 class="text-info">Rp <?php echo ($total_payments > 0) ? number_format($total_amount / $total_payments, 0, ',', '.') : '0'; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Table -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Daftar Pembayaran</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($payments)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Metode</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <strong>INV-<?php echo str_pad($payment['payment_id'], 6, '0', STR_PAD_LEFT); ?></strong>
                            </td>
                            <td><?php echo $payment['penyewa_nama']; ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo $payment['payment_method']; ?></span>
                            </td>
                            <td class="text-end">
                                <strong>Rp <?php echo number_format($payment['payment_amount'], 0, ',', '.'); ?></strong>
                            </td>
                            <td>
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
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($payment['created_at'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo base_url('invoice/invoice/' . $payment['payment_id']); ?>" 
                                       class="btn btn-sm btn-primary" target="_blank" title="Cetak Invoice">
                                        <i class="fas fa-file-pdf"></i> Invoice
                                    </a>
                                    <a href="<?php echo base_url('invoice/receipt/' . $payment['payment_id']); ?>" 
                                       class="btn btn-sm btn-success" target="_blank" title="Cetak Struk">
                                        <i class="fas fa-receipt"></i> Struk
                                    </a>
                                    <a href="<?php echo base_url('payment-report/detail/' . $payment['payment_id']); ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i> Tidak ada data pembayaran yang ditemukan.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .table-responsive {
        overflow-x: auto;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
