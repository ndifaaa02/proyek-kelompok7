<?php 
/** @var mysqli $conn */
include 'includes.php'; 

// ============================================================
// STATISTIK RINGKAS
// ============================================================
$stats = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(id_transaksi) as total_trx, 
        SUM(total_bayar) as total_pendapatan,
        COUNT(DISTINCT metode_pembayaran) as jml_metode
    FROM transaksi
"));

// Breakdown per metode pembayaran (ENUM: cash, transfer, qris)
$metode_data = [];
$res_metode = mysqli_query($conn, "SELECT metode_pembayaran, COUNT(*) as jml, SUM(total_bayar) as total 
                                   FROM transaksi GROUP BY metode_pembayaran");
while ($m = mysqli_fetch_assoc($res_metode)) {
    $metode_data[$m['metode_pembayaran']] = $m;
}

// ============================================================
// FILTER
// ============================================================
$filter_bulan  = $_GET['filter_bulan'] ?? '';
$filter_metode = $_GET['filter_metode'] ?? '';
$cari          = $_GET['cari'] ?? '';

// ============================================================
// QUERY UTAMA RIWAYAT
// ============================================================
// FIX: tanggal_bayar sekarang TIMESTAMP — gunakan DATE() untuk format & filter
// FIX: metode_pembayaran sekarang ENUM (cash/transfer/qris)
// Layanan: GROUP_CONCAT karena 1 pesanan bisa multi-layanan
$query = "SELECT 
            t.id_transaksi,
            t.total_bayar,
            t.metode_pembayaran,
            DATE(t.tanggal_bayar) as tgl_bayar,
            p.id_pesanan,
            p.tanggal_masuk,
            p.tanggal_selesai,
            p.status_pesanan,
            p.status_pembayaran,
            pl.nama_pelanggan,
            GROUP_CONCAT(l.nama_layanan ORDER BY l.id_layanan SEPARATOR ', ') as nama_layanan
          FROM transaksi t
          JOIN pesanan p   ON t.id_pesanan = p.id_pesanan
          JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
          LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
          LEFT JOIN layanan l ON dp.id_layanan = l.id_layanan
          WHERE 1=1";

if (!empty($filter_bulan)) {
    $tahun = date('Y', strtotime($filter_bulan));
    $bulan = date('m', strtotime($filter_bulan));
    $query .= " AND MONTH(t.tanggal_bayar) = '$bulan' AND YEAR(t.tanggal_bayar) = '$tahun'";
}
if (!empty($filter_metode)) {
    $filter_metode_esc = mysqli_real_escape_string($conn, $filter_metode);
    $query .= " AND t.metode_pembayaran = '$filter_metode_esc'";
}
if (!empty($cari)) {
    $cari_esc = mysqli_real_escape_string($conn, $cari);
    $query .= " AND (pl.nama_pelanggan LIKE '%$cari_esc%' OR p.id_pesanan LIKE '%$cari_esc%')";
}

$query .= " GROUP BY t.id_transaksi ORDER BY t.tanggal_bayar DESC";
$result = mysqli_query($conn, $query);

// Helper badge status pesanan
function badge_status_r($status) {
    return match($status) {
        'belum_diambil' => ['bg-secondary', 'Belum Diambil'],
        'diproses'      => ['bg-warning text-dark', 'Diproses'],
        'diambil'       => ['bg-info text-dark', 'Diambil'],
        'selesai'       => ['bg-success', 'Selesai'],
        default         => ['bg-light text-dark', ucfirst($status)],
    };
}

// Helper badge metode bayar
function badge_metode($metode) {
    return match($metode) {
        'cash'     => ['bg-success', 'Cash'],
        'transfer' => ['bg-primary', 'Transfer'],
        'qris'     => ['bg-info text-dark', 'QRIS'],
        default    => ['bg-secondary', ucfirst($metode)],
    };
}
?>

<?php include 'includes/header.php'; ?>

<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-file-earmark-check fs-2 text-primary me-3"></i>
            <h2 class="fw-bold mb-0">Riwayat Pembayaran</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="?<?= http_build_query(array_merge($_GET, [])) ?>&export=csv" 
               class="btn btn-outline-success rounded-pill px-4 fw-bold">
                <i class="bi bi-download me-2"></i> Export CSV
            </a>
            <a href="admin_dashboard.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
                <i class="bi bi-arrow-left me-2"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Kartu Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <p class="small text-muted mb-1"><i class="bi bi-receipt text-primary me-1"></i> Total Transaksi</p>
                <h3 class="fw-bold text-primary mb-0"><?= $stats['total_trx'] ?? 0 ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <p class="small text-muted mb-1"><i class="bi bi-cash-stack text-success me-1"></i> Total Pendapatan</p>
                <h3 class="fw-bold text-success mb-0">Rp <?= number_format($stats['total_pendapatan'] ?? 0, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <p class="small text-muted mb-2"><i class="bi bi-credit-card me-1"></i> Per Metode</p>
                <div class="d-flex gap-2 flex-wrap">
                    <?php foreach (['cash' => 'Cash', 'transfer' => 'Transfer', 'qris' => 'QRIS'] as $key => $label): 
                        $jml = $metode_data[$key]['jml'] ?? 0;
                        [$cls] = badge_metode($key);
                    ?>
                        <span class="badge <?= $cls ?> rounded-pill px-3 py-2">
                            <?= $label ?>: <?= $jml ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" action="" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                    <span class="input-group-text border-0 bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="cari" class="form-control border-0 py-3"
                           placeholder="Cari nama pelanggan / ID..."
                           value="<?= htmlspecialchars($cari) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <input type="month" name="filter_bulan" class="form-control border-0 shadow-sm py-3 rounded-3"
                       value="<?= htmlspecialchars($filter_bulan) ?>">
            </div>
            <div class="col-md-3">
                <select name="filter_metode" class="form-select border-0 shadow-sm py-3 rounded-3">
                    <option value="">Semua Metode</option>
                    <option value="cash"     <?= $filter_metode == 'cash'     ? 'selected' : '' ?>>Cash</option>
                    <option value="transfer" <?= $filter_metode == 'transfer' ? 'selected' : '' ?>>Transfer</option>
                    <option value="qris"     <?= $filter_metode == 'qris'     ? 'selected' : '' ?>>QRIS</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold">Filter</button>
                <a href="admin_riwayat.php" class="btn btn-outline-secondary py-3 rounded-3">Reset</a>
            </div>
        </div>
    </form>

    <!-- Tabel Riwayat -->
    <div class="card border-0 shadow-sm p-4 rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small">
                    <tr>
                        <th>#Trx</th>
                        <th>Tgl Bayar</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Metode</th>
                        <th class="text-end">Total Bayar</th>
                        <th class="text-center">Status Pesanan</th>
                        <th class="text-center">Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)):
                            [$cls_s, $lbl_s] = badge_status_r($row['status_pesanan']);
                            [$cls_m, $lbl_m] = badge_metode($row['metode_pembayaran']);
                            $lbl_bayar = match($row['status_pembayaran']) {
                                'lunas'      => ['bg-success', 'Lunas'],
                                'dp'         => ['bg-warning text-dark', 'DP'],
                                'belum_bayar'=> ['bg-danger', 'Belum Bayar'],
                                default      => ['bg-secondary', '-'],
                            };
                        ?>
                        <tr>
                            <td class="fw-bold text-primary">#<?= $row['id_transaksi'] ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tgl_bayar'])) ?></td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($row['nama_pelanggan']) ?></div>
                                <div class="text-muted" style="font-size:11px">Pesanan #<?= $row['id_pesanan'] ?></div>
                            </td>
                            <td><?= htmlspecialchars($row['nama_layanan'] ?? '-') ?></td>
                            <td><span class="badge <?= $cls_m ?> rounded-pill"><?= $lbl_m ?></span></td>
                            <td class="text-end fw-bold text-success">
                                Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $cls_s ?> rounded-pill px-3"><?= $lbl_s ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $lbl_bayar[0] ?> rounded-pill px-3"><?= $lbl_bayar[1] ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                                Belum ada riwayat transaksi.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>