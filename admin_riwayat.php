<?php include 'includes/header.php'; ?>
<?php include 'includes.php';
$query = "SELECT p.id_pesanan, p.tanggal_masuk, p.tanggal_selesai, pl.nama_pelanggan, 
                 l.nama_layanan, t.total_bayar, p.status_pesanan 
          FROM PESANAN p
          JOIN PELANGGAN pl ON p.id_pelanggan = pl.id_pelanggan
          JOIN DETAIL_PESANAN dp ON p.id_pesanan = dp.id_pesanan
          JOIN LAYANAN l ON dp.id_layanan = l.id_layanan
          JOIN TRANSAKSI t ON p.id_pesanan = t.id_pesanan";
$result = mysqli_query($conn, $query);
?>

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
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="fw-bold"><?php echo $row['id_pesanan']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_masuk'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_selesai'])); ?></td>
                        <td><?php echo $row['nama_pelanggan']; ?></td>
                        <td><?php echo $row['nama_layanan']; ?></td>
                        <td class="text-primary fw-bold">Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                        <td>
                            <?php 
                                $status = $row['status_pesanan'];
                                $class = ($status == 'Selesai') ? 'bg-light text-muted' : 'bg-info-light text-info';
                            ?>
                            <span class="badge <?php echo $class; ?> px-3 py-2"><?php echo $status; ?></span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>`
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>