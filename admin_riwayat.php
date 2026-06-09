<?php
session_start();

// Proteksi halaman admin
if (!isset($_SESSION['login'])) {
    header("Location: admin_login.php");
    exit;
}
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

// Breakdown per metode pembayaran
$metode_data = [];
$res_metode = mysqli_query($conn, "SELECT metode_pembayaran, COUNT(*) as jml, SUM(total_bayar) as total 
                                   FROM transaksi GROUP BY metode_pembayaran");
while ($m = mysqli_fetch_assoc($res_metode)) {
    $metode_data[$m['metode_pembayaran']] = $m;
}

// ============================================================
// FILTER VARIABLES
// ============================================================
$filter_bulan  = $_GET['filter_bulan'] ?? '';
$filter_metode = $_GET['filter_metode'] ?? '';
$filter_bayar  = $_GET['filter_bayar'] ?? ''; 
$cari          = $_GET['cari'] ?? '';

// ============================================================
// QUERY UTAMA RIWAYAT (HANYA MENGAMBIL TRANSAKSI TERBARU PER ID PESANAN)
// ============================================================
$query = "SELECT 
            t.id_transaksi,
            t.id_pesanan,
            t.total_bayar,
            t.tanggal_bayar,
            t.metode_pembayaran,
            p.status_pesanan,
            p.status_pembayaran,
            pl.nama_pelanggan,
            GROUP_CONCAT(DISTINCT l.nama_layanan SEPARATOR ', ') as nama_layanan,
            (SELECT SUM(subtotal) FROM detail_pesanan WHERE id_pesanan = t.id_pesanan) as total_tagihan,
            (SELECT SUM(total_bayar) FROM transaksi WHERE id_pesanan = t.id_pesanan) as total_terbayar_akumulasi,
            (SELECT COUNT(*) FROM transaksi WHERE id_pesanan = t.id_pesanan) as jumlah_cicilan
          FROM transaksi t
          JOIN pesanan p ON t.id_pesanan = p.id_pesanan
          JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
          LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
          LEFT JOIN layanan l ON dp.id_layanan = l.id_layanan
          WHERE t.id_transaksi IN (
              SELECT MAX(id_transaksi) FROM transaksi GROUP BY id_pesanan
          )";

if (!empty($filter_bulan)) {
    $tahun = date('Y', strtotime($filter_bulan));
    $bulan = date('m', strtotime($filter_bulan));
    $query .= " AND MONTH(t.tanggal_bayar) = '$bulan' AND YEAR(t.tanggal_bayar) = '$tahun'";
}

if (!empty($filter_metode)) {
    $filter_metode_esc = mysqli_real_escape_string($conn, $filter_metode);
    $query .= " AND t.metode_pembayaran = '$filter_metode_esc'";
}

if (!empty($filter_bayar)) {
    $filter_bayar_esc = mysqli_real_escape_string($conn, $filter_bayar);
    $query .= " AND p.status_pembayaran = '$filter_bayar_esc'";
}

if (!empty($cari)) {
    $cari_esc = mysqli_real_escape_string($conn, $cari);
    $query .= " AND (pl.nama_pelanggan LIKE '%$cari_esc%' OR t.id_pesanan LIKE '%$cari_esc%')";
}

$query .= " GROUP BY t.id_transaksi, t.id_pesanan, t.total_bayar, t.tanggal_bayar, t.metode_pembayaran, p.status_pesanan, p.status_pembayaran, pl.nama_pelanggan";
$query .= " ORDER BY t.id_pesanan DESC";

$result = mysqli_query($conn, $query);

// Helper Status Pesanan
function badge_status_riwayat($status) {
    return match($status) {
        'belum_diambil' => ['bg-secondary', 'Belum Diambil'],
        'diproses'      => ['bg-warning text-dark', 'Diproses'],
        'selesai'       => ['bg-info text-dark', 'Selesai'],
        'diambil'       => ['bg-success', 'Diambil'],
        default         => ['bg-light text-dark', ucfirst($status)],
    };
}

// Helper Status Pembayaran
function badge_bayar_riwayat($status) {
    return match($status) {
        'belum_bayar' => ['bg-danger', 'Belum Bayar'],
        'dp'          => ['bg-warning text-dark', 'DP'],
        'lunas'       => ['bg-success', 'Lunas'],
        default       => ['bg-light text-dark', ucfirst($status)],
    };
}

include 'includes/navbar.php';
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-clock-history fs-2 text-primary me-3"></i>
            <h2 class="fw-bold mb-0">Riwayat Transaksi Keuangan (Per Pesanan)</h2>
        </div>
        <a href="admin_dashboard.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-primary text-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small opacity-75 fw-semibold">Total Omset (Uang Masuk)</span>
                    <i class="bi bi-wallet2 fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0">Rp <?= number_format($stats['total_pendapatan'] ?? 0, 0, ',', '.') ?></h3>
                <p class="small opacity-50 mb-0 mt-2"><?= $stats['total_trx'] ?> kali pencatatan dana</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-success text-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small opacity-75 fw-semibold">Metode CASH</span>
                    <i class="bi bi-cash-coin fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0">Rp <?= number_format($metode_data['cash']['total'] ?? 0, 0, ',', '.') ?></h3>
                <p class="small opacity-50 mb-0 mt-2"><?= $metode_data['cash']['jml'] ?? 0 ?> Transaksi</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-info text-dark h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small opacity-75 fw-semibold">Metode TRANSFER</span>
                    <i class="bi bi-bank fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0">Rp <?= number_format($metode_data['transfer']['total'] ?? 0, 0, ',', '.') ?></h3>
                <p class="small opacity-50 mb-0 mt-2"><?= $metode_data['transfer']['jml'] ?? 0 ?> Transaksi</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-warning text-dark h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small opacity-75 fw-semibold">Metode QRIS</span>
                    <i class="bi bi-qr-code-scan fs-4"></i>
                </div>
                <h3 class="fw-bold mb-0">Rp <?= number_format($metode_data['qris']['total'] ?? 0, 0, ',', '.') ?></h3>
                <p class="small opacity-50 mb-0 mt-2"><?= $metode_data['qris']['jml'] ?? 0 ?> Transaksi</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-3 rounded-4 mb-4 bg-white">
        <form method="GET" action="">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="cari" class="form-control border-0 bg-light py-2" 
                               placeholder="Cari Pelanggan / ID Pesanan..." value="<?= htmlspecialchars($cari) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="month" name="filter_bulan" class="form-control border-0 bg-light py-2" 
                           value="<?= htmlspecialchars($filter_bulan) ?>">
                </div>
                <div class="col-md-3">
                    <select name="filter_metode" class="form-select border-0 bg-light py-2">
                        <option value="">Semua Metode</option>
                        <option value="cash" <?= $filter_metode == 'cash' ? 'selected' : '' ?>>Cash / Tunai</option>
                        <option value="transfer" <?= $filter_metode == 'transfer' ? 'selected' : '' ?>>Transfer Bank</option>
                        <option value="qris" <?= $filter_metode == 'qris' ? 'selected' : '' ?>>QRIS</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="filter_bayar" class="form-select border-0 bg-light py-2">
                        <option value="">Semua Status Nota</option>
                        <option value="belum_bayar" <?= $filter_bayar == 'belum_bayar' ? 'selected' : '' ?>>Belum Bayar</option>
                        <option value="dp" <?= $filter_bayar == 'dp' ? 'selected' : '' ?>>DP (Cicil)</option>
                        <option value="lunas" <?= $filter_bayar == 'lunas' ? 'selected' : '' ?>>Lunas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr style="font-size: 14px;">
                        <th class="ps-4 py-3">ID Pesanan</th>
                        <th>Waktu Bayar Terakhir</th>
                        <th>Pelanggan</th>
                        <th>Layanan Laundry</th>
                        <th>Metode Terakhir</th>
                        <th class="text-end">Total Dibayar</th>
                        <th class="text-end text-danger">Sisa Tagihan</th>
                        <th class="text-center">Status Nota</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): 
                            [$cls_s, $lbl_s] = badge_status_riwayat($row['status_pesanan']);
                            [$cls_b, $lbl_b] = badge_bayar_riwayat($row['status_pembayaran']);
                            
                            $cls_m = match($row['metode_pembayaran']) {
                                'cash'     => 'bg-success-subtle text-success border border-success-subtle',
                                'transfer' => 'bg-info-subtle text-info border border-info-subtle',
                                'qris'     => 'bg-warning-subtle text-warning-dark border border-warning-subtle',
                                default    => 'bg-light text-dark'
                            };
                            $lbl_m = ucfirst($row['metode_pembayaran']);

                            $total_tagihan  = (float)($row['total_tagihan'] ?? 0);
                            $total_terbayar = (float)($row['total_terbayar_akumulasi'] ?? 0);
                            $sisa_bayar     = max(0, $total_tagihan - $total_terbayar);
                        ?>
                        <tr style="font-size: 14px; cursor: pointer;" onclick="window.location='admin_riwayat_detail.php?id_pesanan=<?= $row['id_pesanan'] ?>';">
                            <td class="ps-4 fw-bold text-primary">#<?= $row['id_pesanan'] ?></td>
                            <td class="text-nowrap text-secondary">
                                <?= date('d/m/Y H:i', strtotime($row['tanggal_bayar'])) ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_pelanggan']) ?></div>
                                <span class="badge bg-light text-dark border small" style="font-size: 10px;"><?= $row['jumlah_cicilan'] ?>x Bayar</span>
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($row['nama_layanan']) ?>">
                                    <?= htmlspecialchars($row['nama_layanan'] ?? '-') ?>
                                </span>
                            </td>
                            <td><span class="badge <?= $cls_m ?> rounded-pill px-3 py-1"><?= $lbl_m ?></span></td>
                            <td class="text-end fw-bold text-success">
                                Rp <?= number_format($total_terbayar, 0, ',', '.') ?>
                            </td>
                            <td class="text-end fw-bold <?= $sisa_bayar > 0 ? 'text-danger' : 'text-muted' ?>">
                                <?= $sisa_bayar > 0 ? 'Rp ' . number_format($sisa_bayar, 0, ',', '.') : '<span class="badge bg-success-subtle text-success rounded-pill fw-semibold"><i class="bi bi-check-all me-1"></i>Lunas</span>' ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $cls_b ?> rounded-pill px-3 py-1"><?= $lbl_b ?></span>
                            </td>
                            <td class="text-center pe-4" onclick="event.stopPropagation();">
                                <a href="admin_riwayat_detail.php?id_pesanan=<?= $row['id_pesanan'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-eye-fill me-1"></i> Riwayat
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                                Belum ada riwayat transaksi yang cocok dengan filter.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>