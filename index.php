<?php include 'includes/header.php'; ?>
<?php include 'includes.php'; ?>

<section class="text-center py-5">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3" style="color: #2d749a;">Laundry Berkualitas, Bersih & Wangi</h1>
        <p class="text-secondary mb-4">Percayakan cucian Anda pada Bintang Laundry. Layanan cepat, hasil maksimal, harga terjangkau</p>
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
        <div class="row g-4">
            <?php
            $layanan = [
                ["Cuci Kering", "Layanan cuci dan kering pakaian dengan standar kebersihan terbaik.", "5000"],
                ["Cuci Setrika", "Pakaian dicuci bersih dan disetrika rapi siap pakai.", "7000"],
                ["Setrika Saja", "Layanan khusus untuk pakaian yang sudah bersih.", "3000"],
                ["Express", "Layanan kilat kebutuhan mendesak selesai dalam 24 jam.", "10000"]
            ];
            foreach ($layanan as $item) : ?>
                <div class="col-md-6">
                    <div class="card card-custom p-4 border-0">
                        <h5 class="fw-bold"><?= $item[0] ?></h5>
                        <p class="text-muted small"><?= $item[1] ?></p>
                        <p class="price-text mb-0">Mulai dari Rp <?= number_format($item[2], 0, ',', '.') ?>/kg</p>
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
            <p class="text-muted mb-4">Pesan sekarang dan nikmati kemudahan layanan laundry berkualitas</p>
            <a href="pesan.php" class="btn btn-primary-custom fw-bold">Buat Pesanan</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>