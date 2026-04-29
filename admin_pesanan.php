<?php
include 'includes/header.php';
include 'includes.php'; // Pastikan koneksi database sudah benar

// 1. LOGIKA UPDATE STATUS PESANAN
if (isset($_POST['update_status'])) {
    $id_pesanan = mysqli_real_escape_string($conn, $_POST['id_pesanan']);
    $status_baru = mysqli_real_escape_string($conn, $_POST['status_pesanan']);
    
    $query_update = "UPDATE pesanan SET status_pesanan = '$status_baru' WHERE id_pesanan = '$id_pesanan'";
    
    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Status pesanan berhasil diperbarui!'); window.location='admin_pesanan.php';</script>";
    }
}

// 2. LOGIKA FILTER BULAN DAN PENCARIAN
$filter_bulan = $_GET['filter_bulan'] ?? '';
$cari = $_GET['cari'] ?? '';

// Query Mengambil data sesuai ralat:
// subtotal diambil langsung dari tabel detail_pesanan (dp.subtotal)
// dp.subtotal ini seharusnya sudah hasil dari (dp.kuantitas * dp.harga_layanan) saat proses INSERT
$query = "SELECT 
            p.id_pesanan, 
            p.tanggal_masuk, 
            p.status_pesanan, 
            pl.nama_pelanggan, 
            pl.no_hp, 
            pl.alamat, 
            l.nama_layanan, 
            dp.kuantitas, 
            dp.harga_layanan, 
            dp.subtotal
          FROM pesanan p
          JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
          LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
          LEFT JOIN layanan l ON dp.id_layanan = l.id_layanan
          WHERE 1=1";

if (!empty($filter_bulan)) {
    $tahun = date('Y', strtotime($filter_bulan));
    $bulan = date('m', strtotime($filter_bulan));
    $query .= " AND MONTH(p.tanggal_masuk) = '$bulan' AND YEAR(p.tanggal_masuk) = '$tahun'";
}

if (!empty($cari)) {
    $query .= " AND (pl.nama_pelanggan LIKE '%$cari%' OR p.id_pesanan LIKE '%$cari%')";
}

$query .= " ORDER BY p.tanggal_masuk DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 text-start">
        <div class="d-flex align-items-center">
            <i class="bi bi-box-seam fs-2 text-primary me-3"></i>
            <h2 class="fw-bold mb-0">Kelola Pesanan</h2>
        </div>
        <a href="admin_dashboard.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <form method="GET" action="" class="mb-4">
        <div class="row g-3">
            <div class="col-md-5">
                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                    <span class="input-group-text border-0 bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="cari" class="form-control border-0 py-3" placeholder="Cari pelanggan..." value="<?= htmlspecialchars($cari) ?>">
                </div>
            </div>
            <div class="col-md-4">
                <input type="month" name="filter_bulan" class="form-control border-0 shadow-sm py-3 rounded-3" value="<?= htmlspecialchars($filter_bulan) ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 shadow-sm fw-bold">Filter</button>
            </div>
        </div>
    </form>

    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
    <div class="card border-0 shadow-sm p-4 rounded-4 mb-4 text-start">
        <div class="row">
            <div class="col-md-7">
                <h5 class="fw-bold text-primary mb-1">Pesanan #<?= $row['id_pesanan']; ?></h5>
                <p class="small text-muted mb-4">Tanggal: <?= date('d/m/Y', strtotime($row['tanggal_masuk'])); ?></p>
                
                <div class="row g-2 small">
                    <div class="col-4 text-muted">Pelanggan</div>
                    <div class="col-8 fw-bold"><?= $row['nama_pelanggan']; ?></div>
                    <div class="col-4 text-muted">Layanan</div>
                    <div class="col-8"><?= $row['nama_layanan'] ?? '-'; ?></div>
                    <div class="col-4 text-muted">Harga (saat pesan)</div>
                    <div class="col-8">Rp <?= number_format($row['harga_layanan'], 0, ',', '.'); ?> /kg</div>
                    <div class="col-4 text-muted">Berat</div>
                    <div class="col-8"><?= $row['kuantitas']; ?> kg</div>
                </div>
            </div>

            <div class="col-md-5 text-end d-flex flex-column justify-content-between">
                <div class="bg-light p-3 rounded-4 text-start">
                    <form method="POST" action="">
                        <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan']; ?>">
                        <label class="small text-muted mb-2 fw-bold">Update Status</label>
                        <div class="d-flex gap-2">
                            <select name="status_pesanan" class="form-select border-0 shadow-sm">
                                <option value="Proses" <?= $row['status_pesanan'] == 'Proses' ? 'selected' : ''; ?>>Proses</option>
                                <option value="Selesai" <?= $row['status_pesanan'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary shadow-sm"><i class="bi bi-check"></i></button>
                        </div>
                    </form>
                </div>
                
                <div class="mt-4">
                    <p class="small text-muted mb-0">Total Harga (Subtotal)</p>
                    <h3 class="fw-bold text-primary">Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></h3>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include 'includes/footer.php'; ?>