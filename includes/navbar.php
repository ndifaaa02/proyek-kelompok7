<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bintang Laundry - Admin</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/bootstrap-icons/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/style.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="d-flex min-vh-100">

        <div class="sidebar bg-white shadow-sm d-flex flex-column p-4 flex-shrink-0"
            style="width: 280px; min-height: 100vh; position: fixed; left: 0; top: 0; z-index: 1030;">

            <a class="text-decoration-none fw-bold d-flex align-items-center fs-4 mb-4 pb-3 border-bottom"
                href="index.php" style="color: #2d749a;">
                <i class="bi bi-star-fill text-warning me-2"></i> Bintang Laundry
            </a>

            <ul class="nav nav-pills flex-column mb-auto gap-2 w-100">
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-secondary py-3 px-3 rounded-3 d-flex align-items-center"
                        href="admin_dashboard.php">
                        <i class="bi bi-speedometer2 fs-5 me-3"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-secondary py-3 px-3 rounded-3 d-flex align-items-center"
                        href="admin_pesanan.php">
                        <i class="bi bi-cart-check fs-5 me-3"></i> Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-secondary py-3 px-3 rounded-3 d-flex align-items-center"
                        href="admin_harga.php">
                        <i class="bi bi-tags fs-5 me-3"></i> Kelola Harga
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-secondary py-3 px-3 rounded-3 d-flex align-items-center"
                        href="admin_riwayat.php">
                        <i class="bi bi-clock-history fs-5 me-3"></i> Riwayat Keuangan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-secondary py-3 px-3 rounded-3 d-flex align-items-center"
                        href="admin_pengeluaran.php">
                        <i class="bi bi-wallet2 fs-5 me-3"></i> Pengeluaran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-secondary py-3 px-3 rounded-3 d-flex align-items-center"
                        href="admin_laporan.php">
                        <i class="bi bi-graph-up-arrow fs-5 me-3"></i> Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-secondary py-3 px-3 rounded-3 d-flex align-items-center"
                        href="admin_akun.php">
                        <i class="bi bi-person-gear fs-5 me-3"></i> Kelola Akun
                    </a>
                </li>
            </ul>

            <?php if (isset($_SESSION['login'])): ?>
            <div class="mt-auto pt-3 border-top">
                <div class="d-flex align-items-center mb-3 px-2">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase me-2"
                        style="width: 40px; height: 40px; font-size: 14px;">
                        <?= substr($_SESSION['nama_pegawai'] ?? 'A', 0, 2) ?>
                    </div>
                    <div class="overflow-hidden">
                        <h6 class="mb-0 fw-bold text-dark text-truncate small">
                            <?= htmlspecialchars($_SESSION['nama_pegawai'] ?? 'Admin') ?></h6>
                        <span class="text-muted style" style="font-size: 11px;">Karyawan</span>
                    </div>
                </div>
                <a class="btn btn-outline-danger w-100 rounded-3 fw-bold d-flex align-items-center justify-content-center py-2"
                    href="logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Keluar Aplikasi
                </a>
            </div>
            <?php endif; ?>

        </div>

        <div id="main-content" class="flex-grow-1 p-4"
            style="margin-left: 280px; width: calc(100% - 280px); min-height: 100vh;">

            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const currentPage = window.location.pathname.split("/").pop();

                document.querySelectorAll(".sidebar .nav-link").forEach(link => {
                    const href = link.getAttribute("href");
                    if (href === currentPage) {
                        link.classList.add("active"); // tambah class active
                    }
                });
            });
            </script>