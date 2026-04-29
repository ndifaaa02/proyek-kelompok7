<?php 
include 'includes.php'; 

// 1. Query untuk statistik (hanya menghitung yang sudah ada di tabel transaksi)
$stats_query = "SELECT COUNT(id_transaksi) as total_trx, SUM(total_bayar) as total_pendapatan FROM transaksi";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// 2. Query utama: Menampilkan riwayat pesanan yang SUDAH memiliki total_bayar
$query = "SELECT 
            p.id_pesanan, 
            p.tanggal_masuk, 
            p.tanggal_selesai, 
            pl.nama_pelanggan, 
            l.nama_layanan, 
            t.total_bayar, 
            p.status_pesanan 
          FROM pesanan p
          JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
          JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
          JOIN layanan l ON dp.id_layanan = l.id_layanan
          JOIN transaksi t ON p.id_pesanan = t.id_pesanan 
          ORDER BY p.id_pesanan DESC";
// Catatan: Penggunaan JOIN t (transaksi) memastikan hanya pesanan yang sudah dibayar yang tampil.

$result = mysqli_query($conn, $query);
?>

<?php include 'includes/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-5 text-start">
        <div class="d-flex align-items-center">
            <i class="bi bi-file-earmark-check fs-2 text-primary me-3"></i>
            <h2 class="fw-bold mb-0">Riwayat Pembayaran</h2>
        </div>
        <a href="admin_dashboard.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm p-4 rounded-4 text-start">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Total Transaksi: <?php echo $stats['total_trx'] ?? 0; ?></h4>
                <p class="text-muted mb-0">Pendapatan Terakumulasi: 
                    <span class="text-success fw-bold">Rp <?php echo number_format($stats['total_pendapatan'] ?? 0, 0, ',', '.'); ?></span>
                </p>
            </div>
            <button class="btn btn-primary-custom px-4 py-2 rounded-3">
                <i class="bi bi-download me-2"></i> Export CSV
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="text-muted small">
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Selesai</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                    </tr>
                </thead>
               <tbody class="small">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="fw-bold">#<?php echo $row['id_pesanan']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['tanggal_masuk'])); ?></td>
                            <td>
                                <?php 
                                    echo ($row['tanggal_selesai']) 
                                        ? date('d/m/Y', strtotime($row['tanggal_selesai'])) 
                                        : '<span class="badge bg-warning text-dark">Proses</span>'; 
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_layanan']); ?></td>
                            <td class="text-primary fw-bold">
                                Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?>
                            </td>
                            <td>
                                <?php 
                                    $status = strtolower($row['status_pesanan']);
                                    $badge_class = ($status == 'selesai') ? 'bg-success-light text-success' : 'bg-info-light text-info';
                                ?>
                                <span class="badge <?php echo $badge_class; ?> px-3 py-2" style="border-radius: 8px;">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                                Belum ada transaksi yang tercatat.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>