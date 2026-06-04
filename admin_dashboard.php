<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/header.php';
/** @var mysqli $conn */
include 'includes.php';

$stats_query = "SELECT COUNT(id_pesanan) as jumlah_pesanan FROM pesanan";

// ============================================================
// LOGIKA AMBIL DATA STATISTIK DARI DATABASE
// ============================================================

// 1. Ambil Total Semua Pesanan
$query_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan");
$data_total  = mysqli_fetch_assoc($query_total);
$total_pesanan = $data_total['total'] ?? 0;

// 2. Ambil Pesanan Diproses
$query_proses = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE status_pesanan = 'diproses'");
$data_proses  = mysqli_fetch_assoc($query_proses);
$pesanan_proses = $data_proses['total'] ?? 0;

// 3. Ambil Pesanan Selesai
$query_selesai = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE status_pesanan = 'selesai'");
$data_selesai  = mysqli_fetch_assoc($query_selesai);
$pesanan_selesai = $data_selesai['total'] ?? 0;

// 4. Ambil Total Pendapatan dari Tabel Transaksi
$query_pendapatan = mysqli_query($conn, "SELECT SUM(total_bayar) as total_masuk FROM transaksi");
$data_pendapatan  = mysqli_fetch_assoc($query_pendapatan);
$total_pendapatan = $data_pendapatan['total_masuk'] ?? 0;

?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-5 text-start">
        <div>
            <h2 class="fw-bold mb-3">Dashboard Admin</h2>
            <p class="text-secondary mb-0">Selamat Datang, <b
                    style="color: #2e8dc0ff;"><?= htmlspecialchars($_SESSION['nama_pegawai']); ?></b></p>
        </div>
        <a href="logout.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
            Logout <i class="bi bi-arrow-right ms-2"></i>
        </a>
    </div>

    <div class="row g-3 mb-5 text-start">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <p class="small text-secondary mb-1">Total Pesanan</p>
                <h2 class="fw-bold mb-1"><?= $total_pesanan; ?></h2>
                <p class="small text-muted mb-0">Semua riwayat pesanan</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <p class="small text-secondary mb-1">Pesanan Diproses</p>
                <h2 class="fw-bold mb-1"><?= $pesanan_proses; ?></h2>
                <p class="small text-muted mb-0">Sedang dikerjakan</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <p class="small text-secondary mb-1">Pesanan Selesai</p>
                <h2 class="fw-bold mb-1"><?= $pesanan_selesai; ?></h2>
                <p class="small text-muted mb-0">Cucian siap diambil</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <p class="small text-secondary mb-1">Total Pendapatan</p>
                <h2 class="fw-bold mb-1">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></h2>
                <p class="small text-muted mb-0">Dari semua transaksi</p>
            </div>
        </div>
    </div>

    <div class="row g-4 text-start">
        <div class="col-md-4">
            <a href="admin_pesanan.php" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                    <i class="bi bi-box text-primary fs-2 mb-3"></i>
                    <h5 class="fw-bold">Kelola Pesanan</h5>
                    <p class="small text-muted">Lihat dan update status pesanan dari pelanggan</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="admin_harga.php" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                    <i class="bi bi-gear text-primary fs-2 mb-3"></i>
                    <h5 class="fw-bold">Kelola Harga</h5>
                    <p class="small text-muted">Atur harga layanan dan paket laundry terbaru</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="admin_riwayat.php" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                    <i class="bi bi-file-text text-primary fs-2 mb-3"></i>
                    <h5 class="fw-bold">Riwayat Transaksi</h5>
                    <p class="small text-muted">Lihat semua daftar transaksi yang telah selesai</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="admin_pengeluaran.php" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                    <i class="bi bi-wallet2 text-primary fs-2 mb-3"></i>
                    <h5 class="fw-bold">Kelola Pengeluaran</h5>
                    <p class="small text-muted">Update dan lihat catatan pengeluaran</p>
                </div>
            </a>
        </div>


        <div class="col-md-4">
            <a href="admin_laporan.php" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                    <i class="bi bi-graph-up text-primary fs-2 mb-3"></i>
                    <h5 class="fw-bold">Laporan Keuangan</h5>
                    <p class="small text-muted">Analisis laba/rugi dan grafik keuntungan bisnis</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="admin_akun.php" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                    <i class="bi bi-person-gear text-primary fs-2 mb-3"></i>
                    <h5 class="fw-bold">Kelola Akun</h5>
                    <p class="small text-muted">Mengelola akun karyawan</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="index.php" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                    <i class="bi bi-star text-primary fs-2 mb-3"></i>
                    <h5 class="fw-bold">Lihat Website</h5>
                    <p class="small text-muted">Kembali ke halaman utama untuk melihat tampilan user</p>
                </div>
            </a>
        </div>

    </div>
</div>

<script>
// Memaksa halaman memuat ulang dari server jika diakses lewat tombol 'Back'
(function() {
    window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    };
})();
</script>

<?php include 'includes/footer.php'; ?>