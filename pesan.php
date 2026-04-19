<?php include 'includes/header.php'; ?>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="index.php" class="text-decoration-none me-3" style="color: #2d749a;">
            
        </a>
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

        <div class="card card-custom p-4 mb-4 border-0 shadow-sm">
            <h5 class="fw-bold mb-1">Pilih Layanan</h5>
            <p class="text-muted small mb-4">Pilih jenis layanan laundry yang diinginkan</p>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card-layanan-small p-3 h-100">
                        <h6 class="fw-bold mb-1">Cuci Kering</h6>
                        <p class="text-muted small mb-2">Pakaian di cuci dan dikeringkan</p>
                        <span class="badge-harga">Rp. 5.000/kg</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-layanan-small p-3 h-100">
                        <h6 class="fw-bold mb-1">Cuci Setrika</h6>
                        <p class="text-muted small mb-2">Cuci kering dan setrika rapi</p>
                        <span class="badge-harga">Rp. 7.000/kg</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-layanan-small p-3 h-100">
                        <h6 class="fw-bold mb-1">Setrika Saja</h6>
                        <p class="text-muted small mb-2">Hanya layanan setrika</p>
                        <span class="badge-harga">Rp. 3.000/kg</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-layanan-small p-3 h-100">
                        <h6 class="fw-bold mb-1">Express</h6>
                        <p class="text-muted small mb-2">Selesai dalam 24 jam</p>
                        <span class="badge-harga">Rp. 10.000/kg</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4 mb-4 border-0 shadow-sm">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Jenis Layanan *</label>
                    <select name="id_layanan" class="form-select bg-light border-0" required>
                        <option value="" disabled selected>Pilih jenis layanan</option>
                        <option value="1">Cuci Kering</option>
                        <option value="2">Cuci Setrika</option>
                        <option value="3">Setrika Saja</option>
                        <option value="4">Express</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Estimasi Berat *</label>
                    <input type="number" name="berat" class="form-control bg-light border-0" placeholder="Contoh: 5" required>
                </div>
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

</div> 
    </div> 

<?php include 'includes/footer.php'; ?>