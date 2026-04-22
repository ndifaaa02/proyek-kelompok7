<?php include 'includes/header.php'; ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-5 text-start">
        <div class="d-flex align-items-center">
            <i class="bi bi-file-earmark-text fs-2 text-primary me-3"></i>
            <h2 class="fw-bold mb-0">Riwayat Pesanan</h2>
        </div>
        <a href="admin_dashboard.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm p-4 rounded-4 text-start">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Total Transaksi: 14</h4>
                <p class="text-muted mb-0">Total Pendapatan: <span class="text-primary fw-bold">Rp 410.100</span></p>
            </div>
            <button class="btn btn-primary-custom px-4 py-2 rounded-3">
                <i class="bi bi-download me-2"></i> Export CVS
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="text-muted small">
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Keluar</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <tr>
                        <td class="fw-bold">ORD1A2B3C4D</td>
                        <td>05/01/2026</td>
                        <td>06/01/2026</td>
                        <td>Agus Setiawan</td>
                        <td>Cuci Setrika</td>
                        <td class="text-primary fw-bold">Rp 43.400</td>
                        <td><span class="badge bg-light text-muted px-3 py-2">Selesai</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">ORD4H8N2Q5D</td>
                        <td>20/02/2026</td>
                        <td>21/02/2026</td>
                        <td>Ahmad Rizki</td>
                        <td>Express</td>
                        <td class="text-primary fw-bold">Rp 25.000</td>
                        <td><span class="badge bg-info-light text-info px-3 py-2">Siap Diambil</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>