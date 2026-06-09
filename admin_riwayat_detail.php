<?php 
/** @var mysqli $conn */
include 'includes.php'; 

$id_pesanan = isset($_GET['id_pesanan']) ? (int)$_GET['id_pesanan'] : 0;

if ($id_pesanan <= 0) {
    header("Location: admin_riwayat.php");
    exit;
}

// 1. AMBIL DATA PESANAN UTAMA & PELANGGAN
$query_pesanan = mysqli_query($conn, "
    SELECT p.*, pl.nama_pelanggan, pl.no_hp, pl.alamat,
           GROUP_CONCAT(DISTINCT l.nama_layanan SEPARATOR ', ') as nama_layanan
    FROM pesanan p
    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    LEFT JOIN layanan l ON dp.id_layanan = l.id_layanan
    WHERE p.id_pesanan = $id_pesanan
    GROUP BY p.id_pesanan
");
$data_p = mysqli_fetch_assoc($query_pesanan);

if (!$data_p) {
    die("<div class='container mt-4'><div class='alert alert-danger'>Data pesanan tidak ditemukan!</div></div>");
}

// 2. HITUNG TOTAL TAGIHAN ASLI & TOTAL AKUMULASI YANG SUDAH DIBAYAR
$query_hitung = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        (SELECT SUM(subtotal) FROM detail_pesanan WHERE id_pesanan = $id_pesanan) as total_tagihan,
        (SELECT SUM(total_bayar) FROM transaksi WHERE id_pesanan = $id_pesanan) as total_terbayar
"));

$total_tagihan  = (float)($query_hitung['total_tagihan'] ?? 0);
$total_terbayar = (float)($query_hitung['total_terbayar'] ?? 0);
$sisa_bayar     = max(0, $total_tagihan - $total_terbayar);

// 3. AMBIL SEMUA RIWAYAT TRANSAKSI / CICILAN UNTUK PESANAN INI
$result_transaksi = mysqli_query($conn, "
    SELECT * FROM transaksi 
    WHERE id_pesanan = $id_pesanan 
    ORDER BY id_transaksi ASC
");

include 'includes/navbar.php';
?>

<div class="container py-4">

    <div class="mb-3">
        <a href="admin_riwayat.php" class="btn btn-light rounded-pill px-3 shadow-sm border">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
                <h4 class="fw-bold text-dark mb-3">Detail Pelanggan & Laundry #<?= $id_pesanan ?></h4>
                <table class="table table-borderless align-middle mb-0 small">
                    <tr>
                        <td class="text-muted py-1" style="width: 140px;">Nama Pelanggan</td>
                        <td class="fw-bold py-1">: <?= htmlspecialchars($data_p['nama_pelanggan']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">No. HP / WhatsApp</td>
                        <td class="py-1">: <?= htmlspecialchars($data_p['no_hp']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Alamat</td>
                        <td class="py-1">: <?= htmlspecialchars($data_p['alamat']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Layanan Laundry</td>
                        <td class="fw-semibold text-primary py-1">: <?= htmlspecialchars($data_p['nama_layanan'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Tanggal Masuk</td>
                        <td class="py-1">: <?= date('d F Y', strtotime($data_p['tanggal_masuk'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
                <h4 class="fw-bold mb-3 opacity-75">Status Keuangan</h4>
                <div class="mb-2">
                    <span class="small opacity-50 d-block">Total Tagihan Laundry:</span>
                    <h4 class="fw-bold">Rp <?= number_format($total_tagihan, 0, ',', '.') ?></h4>
                </div>
                <div class="mb-2">
                    <span class="small opacity-50 d-block">Total Sudah Dibayar:</span>
                    <h4 class="fw-bold text-success">Rp <?= number_format($total_terbayar, 0, ',', '.') ?></h4>
                </div>
                <hr class="opacity-25">
                <div>
                    <span class="small opacity-50 d-block">Sisa Pembayaran:</span>
                    <?php if ($sisa_bayar > 0): ?>
                        <h3 class="fw-bold text-danger">Rp <?= number_format($sisa_bayar, 0, ',', '.') ?></h3>
                    <?php else: ?>
                        <h3 class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> LUNAS</h3>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <h5 class="fw-bold text-dark mb-4"><i class="bi bi-journal-text me-2 text-primary"></i>Riwayat Log Angsuran / Pembayaran Masuk</h5>
        
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr class="table-light">
                        <th class="ps-3" style="width: 70px;">No</th>
                        <th>ID Trx</th>
                        <th>Waktu Pembayaran</th>
                        <th>Metode Pembayaran</th>
                        <th class="text-end pe-3">Jumlah Uang Masuk</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if (mysqli_num_rows($result_transaksi) > 0):
                        while ($trx = mysqli_fetch_assoc($result_transaksi)): 
                            $cls_m = match($trx['metode_pembayaran']) {
                                'cash'     => 'bg-success-subtle text-success',
                                'transfer' => 'bg-info-subtle text-info',
                                'qris'     => 'bg-warning-subtle text-warning-dark',
                                default    => 'bg-light text-dark'
                            };
                    ?>
                        <tr>
                            <td class="ps-3 text-muted"><?= $no++ ?></td>
                            <td class="fw-mono text-secondary">#<?= $trx['id_transaksi'] ?></td>
                            <td><?= date('d/m/Y H:i:s', strtotime($trx['tanggal_bayar'])) ?></td>
                            <td><span class="badge <?= $cls_m ?> rounded-pill px-3 py-1"><?= ucfirst($trx['metode_pembayaran']) ?></span></td>
                            <td class="text-end fw-bold text-success pe-3">Rp <?= number_format($trx['total_bayar'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada rekaman pembayaran tunai/dp masuk.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>