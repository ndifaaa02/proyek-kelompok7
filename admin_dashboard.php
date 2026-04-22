<?php include 'includes/header.php';?>
<?php include 'includes.php';?>
<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: admin_login.php");
    exit;
}
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-5 text-start">
        <div>
            <h2 class="fw-bold mb-3">Dashboard Admin</h2>
            <p class="text-secondary mb-0">Selamat Datang, <b style="color: #2e8dc0ff;"><?= $_SESSION['nama_pegawai']; ?></b></p>
        </div>
        <a href="logout.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
            Logout <i class="bi bi-arrow-right ms-2"></i>
        </a>
    </div>

    <div class="row g-3 mb-5 text-start">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <p class="small text-secondary mb-1">Total Pesanan</p>
                <h2 class="fw-bold mb-1">15</h2>
                <p class="small text-muted mb-0">Semua pesanan</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <p class="small text-secondary mb-1">Pesanan Aktif</p>
                <h2 class="fw-bold mb-1">5</h2>
                <p class="small text-muted mb-0">Sedang diproses</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <p class="small text-secondary mb-1">Pesanan Selesai</p>
                <h2 class="fw-bold mb-1">10</h2>
                <p class="small text-muted mb-0">Total selesai</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <p class="small text-secondary mb-1">Total Pendapatan</p>
                <h2 class="fw-bold mb-1">Rp 410.100</h2>
                <p class="small text-muted mb-0">Dari semua pesanan</p>
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
            <a href="admin_laporan.php" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                    <i class="bi bi-graph-up text-primary fs-2 mb-3"></i>
                    <h5 class="fw-bold">Laporan Keuangan</h5>
                    <p class="small text-muted">Analisis laba/rugi dan grafik keuntungan bisnis</p>
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

<?php include 'includes/footer.php'; ?>