<?php include 'includes/header.php'; ?>
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

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                <span class="input-group-text border-0 bg-white"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-0 py-3" placeholder="Cari ID pesanan atau nama pelanggan...">
            </div>
        </div>
        <div class="col-md-4">
            <select class="form-select border-0 shadow-sm py-3 rounded-3">
                <option selected>Semua Status</option>
                <option>Proses</option>
                <option>Selesai</option>
            </select>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-4 rounded-4 mb-4 text-start">
        <div class="row">
            <div class="col-md-7">
                <h5 class="fw-bold text-primary mb-1">Pesanan #ORD1A5UQSH</h5>
                <p class="small text-muted mb-4">5 Januari 2026 pukul 09.00</p>
                
                <div class="row g-2 small">
                    <div class="col-4 text-muted">Pelanggan</div>
                    <div class="col-8 fw-bold">Agus Setiawan</div>
                    <div class="col-4 text-muted">No. HP</div>
                    <div class="col-8">08981638519037</div>
                    <div class="col-4 text-muted">Alamat</div>
                    <div class="col-8">jl.Kebon jeruk No,07, Indramayu</div>
                    <div class="col-4 text-muted mt-3">Jenis Layanan</div>
                    <div class="col-8 mt-3 fw-bold">Cuci Setrika</div>
                    <div class="col-4 text-muted">Estimasi Berat</div>
                    <div class="col-8">6 kg</div>
                    <div class="col-4 text-muted">Berat Aktual</div>
                    <div class="col-8">6.2 kg</div>
                </div>
            </div>
            <div class="col-md-5 text-end d-flex flex-column justify-content-between">
                <div class="bg-light p-4 rounded-4 text-start">
                    <label class="small text-muted mb-2">Update Status Pesanan</label>
                    <select class="form-select border-0 py-2 mb-2">
                        <option selected>Selesai</option>
                        <option>Sedang Diproses</option>
                        <option>Siap Diambil</option>
                    </select>
                    <p class="x-small text-muted mb-0">Pilih status menjadi pesanan</p>
                </div>
                <div class="mt-4">
                    <p class="small text-muted mb-0">Total Harga</p>
                    <h3 class="fw-bold text-primary">Rp 43.000</h3>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>