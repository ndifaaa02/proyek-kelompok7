<?php include 'includes/header.php'; ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 text-start">
        <div class="d-flex align-items-center">
            <i class="bi bi-gear-fill fs-2 text-primary me-3"></i>
            <h2 class="fw-bold mb-0">Kelola harga</h2>
        </div>
        <a href="admin_dashboard.php"class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm p-5 rounded-4 mb-4 text-start">
        <h4 class="fw-bold mb-4" style="color: #2d749a;">Pengaturan Harga Layanan</h4>
        <form>
            <div class="mb-4">
                <label class="text-primary mb-2">Cuci kering</label>
                <div class="d-flex align-items-center">
                    <span class="me-3 fw-bold">Rp</span>
                    <input type="number" class="form-control border-0 bg-light p-3 rounded-3" value="5000">
                </div>
            </div>
            <div class="mb-4">
                <label class="text-primary mb-2">Cuci Setrika (per kg)</label>
                <div class="d-flex align-items-center">
                    <span class="me-3 fw-bold">Rp</span>
                    <input type="number" class="form-control border-0 bg-light p-3 rounded-3" value="7000">
                </div>
            </div>
            <div class="mb-4">
                <label class="text-primary mb-2">Setrika Saja (per kg)</label>
                <div class="d-flex align-items-center">
                    <span class="me-3 fw-bold">Rp</span>
                    <input type="number" class="form-control border-0 bg-light p-3 rounded-3" value="3000">
                </div>
            </div>
            <div class="mb-5">
                <label class="text-primary mb-2">Express (per kg)</label>
                <div class="d-flex align-items-center">
                    <span class="me-3 fw-bold">Rp</span>
                    <input type="number" class="form-control border-0 bg-light p-3 rounded-3" value="10000">
                </div>
            </div>
            <button class="btn btn-info w-100 py-3 fw-bold text-white shadow-sm" style="background-color: #b9e3e9; border:none; color: #2d749a !important;">
                <i class="bi bi-floppy me-2"></i> Simpan Perubahan
            </button>
        </form>
    </div>

    <div class="card border-0 shadow-sm p-5 rounded-4 text-start">
        <h4 class="fw-bold mb-4" style="color: #2d749a;">Preview Harga</h4>
        <div class="d-flex justify-content-between mb-3">
            <span class="text-primary">Cuci kering</span>
            <span class="fw-bold">Rp 5000</span>
        </div>
        <div class="d-flex justify-content-between mb-3">
            <span class="text-primary">Cuci Setrika</span>
            <span class="fw-bold">Rp 7000/kg</span>
        </div>
        <div class="d-flex justify-content-between mb-3">
            <span class="text-primary">Setrika Saja</span>
            <span class="fw-bold">Rp 3000/kg</span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-primary">Express</span>
            <span class="fw-bold">Rp 10000/kg</span>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>