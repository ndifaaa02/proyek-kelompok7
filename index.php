<?php
include 'includes/header.php';
/** @var mysqli $conn */
include 'includes.php';

$query = mysqli_query($conn, "SELECT * FROM layanan");
    $layanan = [];
    while($row = mysqli_fetch_assoc($query)){
        $layanan[$row['id_layanan']] = $row['harga_perkg'];
    }

// Mengambil data lengkap dari master layanan untuk daftar harga & dropdown
$query = mysqli_query($conn, "SELECT * FROM layanan ORDER BY id_layanan ASC");
$daftar_layanan = [];
$layanan_json = [];

while($row = mysqli_fetch_assoc($query)){
    $daftar_layanan[] = $row;
    // Menyimpan pasangan ID dan Harga untuk kebutuhan hitung otomatis di JavaScript jika diperlukan
    $layanan_json[$row['id_layanan']] = $row['harga_perkg'];
}

?>

<section class="text-center py-5">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3" style="color: #2d749a;">Laundry Berkualitas, Bersih & Wangi</h1>
        <p class="text-secondary mb-4">Percayakan cucian Anda pada Bintang Laundry. Layanan cepat, hasil maksimal, harga
            terjangkau</p>
        <a href="pesan.php" class="btn btn-primary-custom fw-bold">Pesan Sekarang</a>
    </div>
</section>

<section class="py-5">
    <div class="container text-center">
        <h3 class="fw-bold mb-5" style="color: #555;">Mengapa Pilih Kami?</h3>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-custom p-4 border-0">
                    <i class="bi bi-clock-history fs-1 text-danger mb-3"></i>
                    <h5 class="fw-bold">Cepat & Tepat Waktu</h5>
                    <p class="small text-muted">Proses laundry cepat dengan waktu pengerjaan yang dapat disesuaikan.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-4 border-0">
                    <i class="bi bi-stars fs-1 text-primary mb-3"></i>
                    <h5 class="fw-bold">Bersih Hasil Maksimal</h5>
                    <p class="small text-muted">Menggunakan deterjen berkualitas dan teknik pencucian profesional.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-4 border-0">
                    <i class="bi bi-shield-lock fs-1 text-success mb-3"></i>
                    <h5 class="fw-bold">Aman & Terpercaya</h5>
                    <p class="small text-muted">Pakaian Anda ditangani dengan hati-hati selama proses laundry.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h3 class="fw-bold text-center mb-5" style="color: #555;">Layanan Kami</h3>
        <div class="row g-3">
            <?php foreach ($daftar_layanan as $l): ?>
            <div class="col-md-4">
                <div class="p-3 rounded-4 border bg-light">
                    <span class="small fw-bold text-muted d-block text-uppercase" style="font-size:0.75rem;">Tarif
                        Kiloan</span>
                    <h5 class="fw-bold text-dark my-1"><?= htmlspecialchars($l['nama_layanan']) ?></h5>
                    <p class="text-muted small mb-2" style="font-size:0.8rem; min-height: 24px;">
                        <?= !empty($l['deskripsi']) ? htmlspecialchars($l['deskripsi']) : 'Layanan cuci higienis dan rapi.' ?>
                    </p>
                    <h4 class="fw-bold text-primary mb-0">Rp
                        <?= number_format($l['harga_perkg'], 0, ',', '.') ?><span
                            class="fs-6 fw-normal text-muted">/kg</span></h4>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="bg-white rounded-4 p-5 text-center shadow-sm">
            <h3 class="fw-bold mb-3" style="color: #2d749a;">Siap untuk Laundry?</h3>
            <p class="text-muted mb-4">Pesan sekarang dan nikmati kemudahan layanan laundry berkualitass</p>
            <a href="pesan.php" class="btn btn-primary-custom fw-bold">Buat Pesanan</a>
        </div>
    </div>
</section>

<?php include 'includes/footer2.php'; ?>