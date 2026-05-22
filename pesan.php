<?php 
// Pastikan path ini sesuai dengan nama file kamu (cek VS Code)
include 'includes.php'; 

// Jika 'includes.php' sudah berisi koneksi, kita tidak perlu 'koneksi.php' lagi
if (!isset($conn)) {
    die("Koneksi gagal atau file koneksi tidak ditemukan!");
}

// Lanjut ke query data
$query = mysqli_query($conn, "SELECT * FROM layanan");
$layanan = [];
if ($query) {
    while($row = mysqli_fetch_assoc($query)){
        $layanan[$row['id_layanan']] = [
            'harga' => $row['harga_perkg'] ?? 0,
            'estimasi' => $row['estimasi_waktu'] ?? '-'
        ];
    }
}
?>

<div class="container py-4">
    </div>
<div class="card-layanan-small p-3 h-100">
    <h6 class="fw-bold mb-1">Cuci Kering</h6>
    <span class="badge-harga">Rp <?php echo number_format($layanan[1]['harga_perkg'] ?? 0, 0, ',', '.'); ?></span>
    <small class="text-primary"><i class="bi bi-clock"></i> <?php echo $layanan[1]['estimasi_waktu'] ?? '-'; ?></small>
</div>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="index.php" class="text-decoration-none me-3" style="color: #2d749a;"></a>
        <div>
            <h2 class="fw-bold mb-0">Pesan Layanan Laundry</h2>
            <p class="text-secondary small mb-0">Isi formulir di bawah untuk memesan dan konfirmasi</p>
        </div>
    </div>

    <form action="proses_pesanan.php" method="POST">
        <div class="card card-custom p-4 mb-4 border-0 shadow-sm">
            <h5 class="fw-bold mb-3">Informasi Pelanggan</h5>
            <p class="text-muted small">Data diri untuk pengiriman dan konfirmasi</p>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nama Lengkap *</label>
                    <input type="text" name="nama" class="form-control bg-light border-0" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nomor Telepon *</label>
                    <input type="text" name="telepon" class="form-control bg-light border-0" placeholder="08xxxxxxxxxx" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Alamat Lengkap *</label>
                    <textarea name="alamat" class="form-control bg-light border-0" rows="2" placeholder="Jalan, Nomor Rumah, RT/RW, Kelurahan, Kecamatan" required></textarea>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <?php foreach([1, 2, 3, 4] as $id): 
                $nama_layanan = ($id == 1) ? 'Cuci Kering' : (($id == 2) ? 'Cuci Setrika' : (($id == 3) ? 'Setrika Saja' : 'Express'));
                $deskripsi = ($id == 1) ? 'Pakaian di cuci dan dikeringkan' : (($id == 2) ? 'Cuci kering dan setrika rapi' : (($id == 3) ? 'Hanya layanan setrika' : 'Selesai dalam 24 jam'));
            ?>
            <div class="col-md-6">
                <div class="card-layanan-small p-3 h-100">
                    <h6 class="fw-bold mb-1"><?php echo $nama_layanan; ?></h6>
                    <p class="text-muted small mb-2"><?php echo $deskripsi; ?></p>
                    <span class="badge-harga d-block mb-1">Rp <?php echo number_format($layanan[$id]['harga'], 0, ',', '.'); ?></span>
                    <small class="text-primary fw-bold"><i class="bi bi-clock"></i> <?php echo $layanan[$id]['estimasi']; ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="card card-custom p-4 mb-4 border-0 shadow-sm mt-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Jenis Layanan *</label>
                    <select name="id_layanan" class="form-select bg-light border-0" required>
                        <option value="" disabled selected>Pilih jenis layanan</option>
                        <option value="1">Cuci Kering</option>
                        <option value="2">Cuci Setrika</option>
                        <option value="3">Setrika Saja</option>
                        <option value="4">Express</option>
                        <option value="5">Lainnya</option>

                    </select>
                <div class="col-12">
                    <label class="form-label fw-bold">Catatan Tambahan (Opsional)</label>
                    <textarea name="catatan" class="form-control bg-light border-0" rows="2" placeholder="Instruksi khusus atau catatan untuk pesanan Anda"></textarea>
                </div>
            </div>
        </div>


        <button type="submit" class="btn btn-pesan-final w-100 shadow-sm d-flex align-items-center justify-content-center">
            <i class="bi bi-cart-fill me-2"></i> Buat pesanan
        </button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>