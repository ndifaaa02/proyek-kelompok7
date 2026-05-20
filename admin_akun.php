<?php
include 'includes/header.php';
include 'includes.php'; // Pastikan koneksi database sudah benar
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 text-start">
        <div class="d-flex align-items-center">
            <i class="bi bi-person-gear fs-2 text-primary me-3"></i>
            <h2 class="fw-bold mb-0">Kelola Akun</h2>
        </div>
        <a href="admin_dashboard.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <div class="row g-4 text-start">
        <div class="col-md-4">
            <a href="admin_signup.php" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                    <i class="bi bi-person-add text-primary fs-2 mb-3"></i>
                    <h5 class="fw-bold">Tambah Akun</h5>
                    <p class="small text-muted">Untuk menambah akun karyawan</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="lupa.php" class="text-decoration-none text-dark">
                <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                    <i class="bi bi-person-lock text-primary fs-2 mb-3"></i>
                    <h5 class="fw-bold">Ganti Sandi</h5>
                    <p class="small text-muted">Untuk ganti akun karyawan</p>
                </div>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>