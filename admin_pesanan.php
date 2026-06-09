<?php
session_start();

// Proteksi halaman admin
if (!isset($_SESSION['login'])) {
    header("Location: admin_login.php");
    exit;
}
include 'includes/navbar.php';
/** @var mysqli $conn */
include 'includes.php';


$pesan_sukses = '';
$pesan_error  = '';

// ============================================================
// PROSES UPDATE STATUS PESANAN, BERAT, & KAS MASUK (PEMBAYARAN)
// ============================================================
if (isset($_POST['update_status'])) {
    $id_pesanan        = (int) $_POST['id_pesanan'];
    $status_pesanan    = mysqli_real_escape_string($conn, $_POST['status_pesanan']);
    $jumlah_bayar_baru = isset($_POST['jumlah_bayar']) ? (int)$_POST['jumlah_bayar'] : 0;
    $berat_baru        = isset($_POST['berat']) ? (float)$_POST['berat'] : 0;
    $metode_pembayaran = isset($_POST['metode_pembayaran']) ? mysqli_real_escape_string($conn, $_POST['metode_pembayaran']) : 'cash';

    $valid_status = ['belum_diambil', 'diproses', 'selesai', 'diambil'];

    if (in_array($status_pesanan, $valid_status)) {

        // --- PROTEKSI BACKEND: VALIDASI UPLOAD BUKTI PEMBAYARAN ---
        // Jika metode bukan cash DAN ada input jumlah bayar baru, bukti wajib diupload
        $nama_file_bukti = null;

        if ($jumlah_bayar_baru > 0 && $metode_pembayaran !== 'cash') {
            // Cek apakah file di-upload
            if (empty($_FILES['bukti_pembayaran']['name'])) {
                $pesan_error = "Gagal! Bukti pembayaran wajib diupload untuk metode Transfer/QRIS.";
                goto skip_proses; // Lompat, tidak proses lebih lanjut
            }

            // Validasi tipe file (hanya gambar dan PDF)
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            $file_type      = $_FILES['bukti_pembayaran']['type'];
            $file_size      = $_FILES['bukti_pembayaran']['size'];
            $file_tmp       = $_FILES['bukti_pembayaran']['tmp_name'];
            $file_ext       = strtolower(pathinfo($_FILES['bukti_pembayaran']['name'], PATHINFO_EXTENSION));

            if (!in_array($file_type, $allowed_types)) {
                $pesan_error = "Gagal! Format file tidak valid. Hanya JPG, PNG, atau PDF yang diizinkan.";
                goto skip_proses;
            }

            // Batasi ukuran file maksimal 2MB
            if ($file_size > 2 * 1024 * 1024) {
                $pesan_error = "Gagal! Ukuran file terlalu besar. Maksimal 2MB.";
                goto skip_proses;
            }

            // Buat folder upload jika belum ada
            $upload_dir = 'uploads/bukti/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Generate nama file unik agar tidak tertimpa file lain
            $nama_file_bukti = 'bukti_' . $id_pesanan . '_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $nama_file_bukti;

            if (!move_uploaded_file($file_tmp, $upload_path)) {
                $pesan_error = "Gagal! File tidak berhasil diupload. Cek permission folder.";
                goto skip_proses;
            }
        }

        // --- 1. PROTEKSI BACKEND (CEK STATUS SEBELUMNYA) ---
        $query_proteksi = mysqli_query($conn, "SELECT status_pesanan, status_pembayaran FROM pesanan WHERE id_pesanan = $id_pesanan");
        $data_aktual = mysqli_fetch_assoc($query_proteksi);

        if ($data_aktual && $data_aktual['status_pesanan'] === 'diambil' && $data_aktual['status_pembayaran'] === 'lunas') {
            die("<div class='container mt-4'><div class='alert alert-danger fw-bold'><i class='bi bi-shield-slash-fill me-2'></i>Error: Transaksi ini sudah Selesai & Lunas. Data dikunci permanen!</div></div>");
        }

        mysqli_begin_transaction($conn);

        try {
            // A. UPDATE BERAT DAN SUB-TOTAL JIKA ADA PERUBAHAN BERAT (> 0)
            if ($berat_baru > 0) {
                $query_detail = mysqli_query($conn, "SELECT harga_layanan FROM detail_pesanan WHERE id_pesanan = $id_pesanan LIMIT 1");
                $data_detail  = mysqli_fetch_assoc($query_detail);

                if ($data_detail) {
                    $harga_layanan = $data_detail['harga_layanan'];
                    $subtotal_baru = $berat_baru * $harga_layanan;

                    mysqli_query($conn, "UPDATE detail_pesanan SET kuantitas = '$berat_baru', subtotal = '$subtotal_baru' WHERE id_pesanan = $id_pesanan");
                }
            }

            // B. AMBIL HITUNGAN SUB-TOTAL TERBARU
            $query_tagihan = mysqli_query($conn, "SELECT SUM(subtotal) as total FROM detail_pesanan WHERE id_pesanan = $id_pesanan");
            $data_tagihan  = mysqli_fetch_assoc($query_tagihan);
            $subtotal_akhir = $data_tagihan['total'] ?? 0;

            // C. PROSES AKUMULASI DARI TOTAL RIWAYAT TRANSAKSI YANG ADA (MENGGUNAKAN SUM)
            $cek_transaksi = mysqli_query($conn, "SELECT SUM(total_bayar) AS total_terbayar FROM transaksi WHERE id_pesanan = $id_pesanan");
            $data_transaksi = mysqli_fetch_assoc($cek_transaksi);
            $total_bayar_lama = $data_transaksi['total_terbayar'] ?? 0;

            // Total bayar sekarang = Riwayat akumulasi lama + Input pembayaran baru saat ini
            $total_bayar_akumulasi = $total_bayar_lama + $jumlah_bayar_baru;

            // Hitung sisa bayar secara real-time
            $sisa_bayar = $subtotal_akhir - $total_bayar_akumulasi;

            // D. LOGIKA OTOMATIS PENENTUAN STATUS PEMBAYARAN
            if ($total_bayar_akumulasi <= 0) {
                $status_pembayaran = 'belum_bayar';
            } elseif ($sisa_bayar <= 0) {
                $status_pembayaran = 'lunas';
                $sisa_bayar = 0;
            } else {
                $status_pembayaran = 'dp';
            }

            // E. INSERT BARIS TRANSAKSI BARU + NAMA FILE BUKTI JIKA ADA PEMBAYARAN
            if ($jumlah_bayar_baru > 0) {
                // Siapkan nilai kolom bukti_pembayaran (null jika cash / tidak upload)
                $val_bukti = $nama_file_bukti ? "'$nama_file_bukti'" : "NULL";

                mysqli_query($conn, "INSERT INTO transaksi (id_pesanan, total_bayar, tanggal_bayar, metode_pembayaran, bukti_pembayaran)
                                     VALUES ($id_pesanan, '$jumlah_bayar_baru', CURDATE(), '$metode_pembayaran', $val_bukti)");
            }

            // F. UPDATE STATUS PESANAN UTAMA & STATUS BAYAR OTOMATIS
            $set_selesai = ($status_pesanan === 'diambil') ? ", tanggal_selesai = CURDATE()" : "";
            mysqli_query($conn, "UPDATE pesanan SET status_pesanan = '$status_pesanan', status_pembayaran = '$status_pembayaran' $set_selesai WHERE id_pesanan = $id_pesanan");

            mysqli_commit($conn);
            $pesan_sukses = "Pembayaran berhasil diproses! Riwayat dicatat terpisah ke laporan keuangan.";

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $pesan_error = "Gagal memproses pembayaran: " . $e->getMessage();
        }
    }
}

// Label goto untuk lompat ke sini saat validasi file gagal
skip_proses:

// ============================================================
// PROSES HAPUS PESANAN (CASCADE MANUAL)
// ============================================================
if (isset($_POST['hapus_pesanan'])) {
    $id_pesanan = (int) $_POST['id_pesanan'];

    // --- 2. PROTEKSI BACKEND UNTUK PROSES HAPUS ---
    $query_proteksi_hapus = mysqli_query($conn, "SELECT status_pesanan, status_pembayaran FROM pesanan WHERE id_pesanan = $id_pesanan");
    $data_aktual_hapus = mysqli_fetch_assoc($query_proteksi_hapus);

    if ($data_aktual_hapus) {
        if ($data_aktual_hapus['status_pesanan'] === 'diambil' && $data_aktual_hapus['status_pembayaran'] === 'lunas') {
            die("<div class='container mt-4'><div class='alert alert-danger fw-bold'><i class='bi bi-shield-slash-fill me-2'></i>Error: Gagal menghapus! Pesanan ini sudah selesai dan lunas, riwayat tidak boleh dihapus kembali.</div></div>");
        }
    }
    // ----------------------------------------------

    mysqli_begin_transaction($conn);

    try {
        $query_detail = "DELETE FROM detail_pesanan WHERE id_pesanan = $id_pesanan";
        mysqli_query($conn, $query_detail);

        $query_transaksi = "DELETE FROM transaksi WHERE id_pesanan = $id_pesanan";
        mysqli_query($conn, $query_transaksi);

        $query_pesanan = "DELETE FROM pesanan WHERE id_pesanan = $id_pesanan";
        mysqli_query($conn, $query_pesanan);

        mysqli_commit($conn);
        $pesan_sukses = "Pesanan #$id_pesanan berhasil dihapus secara permanen.";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $pesan_error = "Gagal menghapus pesanan: " . mysqli_error($conn);
    }
}

// ============================================================
// FILTER, PENCARIAN & SORTIR
// ============================================================
$filter_bulan = $_GET['filter_bulan'] ?? '';
$cari         = $_GET['cari'] ?? '';
$filter_status = $_GET['filter_status'] ?? '';
$sortir       = $_GET['sortir'] ?? 'DESC';

// ============================================================
// QUERY UTAMA
// ============================================================
$query = "SELECT
            p.id_pesanan,
            p.tanggal_masuk,
            p.tanggal_selesai,
            p.status_pesanan,
            p.status_pembayaran,
            p.total_harga,
            p.catatan,
            pl.nama_pelanggan,
            pl.no_hp,
            pl.alamat,
            GROUP_CONCAT(DISTINCT l.nama_layanan ORDER BY l.id_layanan SEPARATOR ', ') as nama_layanan,
            GROUP_CONCAT(CONCAT(dp.kuantitas, ' kg') ORDER BY l.id_layanan SEPARATOR ', ') as kuantitas_list,
            SUM(dp.subtotal) as total_subtotal,
            MAX(dp.kuantitas) as berat_tunggal,
            (SELECT metode_pembayaran FROM transaksi WHERE id_pesanan = p.id_pesanan ORDER BY id_transaksi DESC LIMIT 1) as metode_pembayaran,
            (SELECT bukti_pembayaran FROM transaksi WHERE id_pesanan = p.id_pesanan AND bukti_pembayaran IS NOT NULL ORDER BY id_transaksi DESC LIMIT 1) as bukti_terakhir,
            trx.total_terbayar
          FROM pesanan p
          JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
          LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
          LEFT JOIN layanan l ON dp.id_layanan = l.id_layanan
          LEFT JOIN (
              SELECT id_pesanan, SUM(total_bayar) as total_terbayar
              FROM transaksi
              GROUP BY id_pesanan
          ) trx ON p.id_pesanan = trx.id_pesanan
          WHERE 1=1";

if (!empty($filter_bulan)) {
    $tahun = date('Y', strtotime($filter_bulan));
    $bulan = date('m', strtotime($filter_bulan));
    $query .= " AND MONTH(p.tanggal_masuk) = '$bulan' AND YEAR(p.tanggal_masuk) = '$tahun'";
}
if (!empty($cari)) {
    $cari_esc = mysqli_real_escape_string($conn, $cari);
    $query .= " AND (pl.nama_pelanggan LIKE '%$cari_esc%' OR p.id_pesanan LIKE '%$cari_esc%')";
}
if (!empty($filter_status)) {
    $filter_status_esc = mysqli_real_escape_string($conn, $filter_status);
    $query .= " AND p.status_pesanan = '$filter_status_esc'";
}

$query .= " GROUP BY p.id_pesanan, p.tanggal_masuk, p.tanggal_selesai, p.status_pesanan, p.status_pembayaran, p.total_harga, p.catatan, pl.nama_pelanggan, pl.no_hp, pl.alamat, trx.total_terbayar";
$query .= " ORDER BY p.tanggal_masuk $sortir, p.id_pesanan DESC";

$result = mysqli_query($conn, $query);

// ============================================================
// STATISTIK RINGKAS
// ============================================================
$stat = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT
        COUNT(*) as total,
        SUM(status_pesanan = 'diproses') as diproses,
        SUM(status_pesanan = 'belum_diambil') as belum_diambil,
        SUM(status_pesanan = 'selesai') as selesai,
        SUM(status_pesanan = 'diambil') as diambil
    FROM pesanan
"));

// Helper: badge warna status pesanan
function badge_status($status) {
    return match($status) {
        'belum_diambil' => ['bg-secondary', 'Belum Diambil'],
        'diproses'      => ['bg-warning text-dark', 'Diproses'],
        'selesai'       => ['bg-info text-dark', 'Selesai'],
        'diambil'       => ['bg-success', 'Diambil'],
        default         => ['bg-light text-dark', ucfirst($status)],
    };
}

// Helper: badge warna status pembayaran
function badge_bayar($status) {
    return match($status) {
        'belum_bayar' => ['bg-danger', 'Belum Bayar'],
        'dp'          => ['bg-warning text-dark', 'DP'],
        'lunas'       => ['bg-success', 'Lunas'],
        default       => ['bg-light text-dark', ucfirst($status)],
    };
}
?>


<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-box-seam fs-2 text-primary me-3"></i>
            <h2 class="fw-bold mb-0">Kelola Pesanan</h2>
        </div>
        <a href="admin_dashboard.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <?php if ($pesan_sukses): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3">
        <i class="bi bi-check-circle-fill me-2"></i><?= $pesan_sukses ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if ($pesan_error): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3">
        <i class="bi bi-exclamation-circle-fill me-2"></i><?= $pesan_error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <?php
        $stats_data = [
            ['label' => 'Total Pesanan',  'val' => $stat['total'],        'icon' => 'bi-inbox',          'color' => 'text-primary'],
            ['label' => 'Belum Diambil',  'val' => $stat['belum_diambil'],'icon' => 'bi-hourglass-split','color' => 'text-secondary'],
            ['label' => 'Diproses',       'val' => $stat['diproses'],     'icon' => 'bi-arrow-repeat',   'color' => 'text-warning'],
            ['label' => 'Selesai',        'val' => $stat['selesai'],      'icon' => 'bi-bag-check',      'color' => 'text-info'],
            ['label' => 'Diambil',        'val' => $stat['diambil'],      'icon' => 'bi-check-circle',   'color' => 'text-success'],
        ];
        foreach ($stats_data as $s): ?>
        <div class="col">
            <div class="card border-0 shadow-sm p-3 rounded-4 text-center h-100">
                <i class="bi <?= $s['icon'] ?> fs-4 <?= $s['color'] ?> mb-1"></i>
                <h4 class="fw-bold mb-0"><?= $s['val'] ?></h4>
                <p class="small text-muted mb-0"><?= $s['label'] ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <form method="GET" action="" class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                    <span class="input-group-text border-0 bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="cari" class="form-control border-0 py-3"
                        placeholder="Cari nama / ID pesanan..." value="<?= htmlspecialchars($cari) ?>">
                </div>
            </div>
            <div class="col-md-2">
                <input type="month" name="filter_bulan" class="form-control border-0 shadow-sm py-3 rounded-3"
                    value="<?= htmlspecialchars($filter_bulan) ?>">
            </div>
            <div class="col-md-3">
                <select name="filter_status" class="form-select border-0 shadow-sm py-3 rounded-3">
                    <option value="">Semua Status</option>
                    <option value="belum_diambil" <?= $filter_status == 'belum_diambil' ? 'selected' : '' ?>>Belum Diambil</option>
                    <option value="diproses" <?= $filter_status == 'diproses'      ? 'selected' : '' ?>>Diproses</option>
                    <option value="selesai" <?= $filter_status == 'selesai'       ? 'selected' : '' ?>>Selesai</option>
                    <option value="diambil" <?= $filter_status == 'diambil'       ? 'selected' : '' ?>>Diambil</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="sortir" class="form-select border-0 shadow-sm py-3 rounded-3">
                    <option value="DESC" <?= $sortir == 'DESC' ? 'selected' : '' ?>>Tanggal Terbaru</option>
                    <option value="ASC" <?= $sortir == 'ASC' ? 'selected' : '' ?>>Tanggal Terlama</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold">Filter</button>
            </div>
        </div>
    </form>

    <?php if (mysqli_num_rows($result) == 0): ?>
    <div class="text-center text-muted py-5">
        <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i> Tidak ada pesanan ditemukan.
    </div>
    <?php endif; ?>

    <?php while ($row = mysqli_fetch_assoc($result)):
        [$cls_status, $lbl_status]  = badge_status($row['status_pesanan']);
        [$cls_bayar,  $lbl_bayar]   = badge_bayar($row['status_pembayaran']);
        $total_tampil = $row['total_harga'] > 0 ? $row['total_harga'] : ($row['total_subtotal'] ?? 0);

        $subtotal       = isset($row['total_subtotal']) ? (float)$row['total_subtotal'] : 0.0;
        $total_terbayar = isset($row['total_terbayar']) ? (float)$row['total_terbayar'] : 0.0;
        $sisa_bayar     = $subtotal - $total_terbayar;

        $is_locked = ($row['status_pesanan'] === 'diambil' && $row['status_pembayaran'] === 'lunas');
        $id_form   = 'form_pesanan_' . $row['id_pesanan']; // ID unik tiap form
    ?>
    <div class="card border-0 shadow-sm p-4 rounded-4 mb-3">
        <div class="row align-items-start">

            <div class="col-md-5">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h5 class="fw-bold text-primary mb-0">Pesanan #<?= $row['id_pesanan'] ?></h5>
                    <span class="badge <?= $cls_status ?> rounded-pill"><?= $lbl_status ?></span>
                    <span class="badge <?= $cls_bayar  ?> rounded-pill"><?= $lbl_bayar  ?></span>
                </div>
                <p class="small text-muted mb-3">
                    Masuk: <?= date('d/m/Y', strtotime($row['tanggal_masuk'])) ?>
                    <?php if ($row['tanggal_selesai']): ?>
                    · Selesai: <?= date('d/m/Y', strtotime($row['tanggal_selesai'])) ?>
                    <?php endif; ?>
                </p>
                <div class="row g-1 small">
                    <div class="col-4 text-muted">Pelanggan</div>
                    <div class="col-8 fw-semibold"><?= htmlspecialchars($row['nama_pelanggan']) ?></div>
                    <div class="col-4 text-muted">No HP</div>
                    <div class="col-8"><?= htmlspecialchars($row['no_hp']) ?></div>
                    <div class="col-4 text-muted">Alamat</div>
                    <div class="col-8"><?= htmlspecialchars($row['alamat']) ?></div>
                    <div class="col-4 text-muted">Layanan</div>
                    <div class="col-8"><?= htmlspecialchars($row['nama_layanan'] ?? '-') ?></div>
                    <div class="col-4 text-muted">Catatan</div>
                    <div class="col-8"><?= htmlspecialchars($row['catatan'] ?? '-') ?></div>
                </div>

                <?php
                // =====================================================
                // TAMPILKAN BUKTI PEMBAYARAN TERAKHIR (JIKA ADA)
                // =====================================================
                if (!empty($row['bukti_terakhir'])):
                    $path_bukti = 'uploads/bukti/' . $row['bukti_terakhir'];
                    $ext_bukti  = strtolower(pathinfo($row['bukti_terakhir'], PATHINFO_EXTENSION));
                ?>
                <div class="mt-3 p-2 bg-light rounded-3 border">
                    <p class="small fw-bold text-muted mb-1">
                        <i class="bi bi-paperclip me-1"></i> Bukti Pembayaran Terakhir:
                    </p>
                    <?php if (in_array($ext_bukti, ['jpg', 'jpeg', 'png'])): ?>
                        <!-- Tampilkan thumbnail yang bisa diklik untuk lihat full -->
                        <a href="<?= $path_bukti ?>" target="_blank" title="Klik untuk lihat ukuran penuh">
                            <img src="<?= $path_bukti ?>" alt="Bukti Bayar"
                                 class="img-thumbnail rounded-3"
                                 style="max-height: 100px; object-fit: cover; cursor: zoom-in;">
                        </a>
                    <?php elseif ($ext_bukti === 'pdf'): ?>
                        <!-- Tampilkan link untuk file PDF -->
                        <a href="<?= $path_bukti ?>" target="_blank"
                           class="btn btn-sm btn-outline-danger rounded-3">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Lihat PDF
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div>

            <div class="col-md-7 d-flex flex-column justify-content-between h-100">

                <?php if ($is_locked): ?>
                <div class="alert alert-success d-flex align-items-center rounded-4 py-3 mb-3 border-0 shadow-sm">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div class="small fw-bold">Transaksi Selesai & Terkunci Permanen.</div>
                </div>
                <?php else: ?>

                <!-- =====================================================
                     FORM UPDATE — enctype WAJIB multipart/form-data
                     agar PHP bisa menerima file upload via $_FILES
                ====================================================== -->
                <form method="POST" action="" id="<?= $id_form ?>"
                      enctype="multipart/form-data">
                    <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan'] ?>">
                    <div class="bg-light p-3 rounded-4 mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <p class="small fw-bold text-muted mb-2">Berat (kg)</p>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold fs-1 text-dark text-nowrap me-5">
                                        <?= htmlspecialchars($row['kuantitas_list'] ?? '0') ?>
                                    </span>
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden ms-2" style="width: 140px;">
                                        <input type="number" name="berat"
                                               class="form-control bg-white border-0 shadow-sm input-berat-laundry"
                                               step="0.01" min="0" placeholder="Ubah..."
                                               data-id="<?= $row['id_pesanan'] ?>">
                                        <span class="input-group-text border-0 bg-white fw-bold text-muted w-45">
                                            <i class="bi bi-pencil-square"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <p class="small fw-bold text-muted mb-2">Update Status Pesanan</p>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <?php
                                    $options_status = [
                                        'belum_diambil' => ['label' => 'Belum Diambil', 'class' => 'btn-outline-secondary'],
                                        'diproses'      => ['label' => 'Diproses',      'class' => 'btn-outline-warning'],
                                        'selesai'       => ['label' => 'Selesai',       'class' => 'btn-outline-info'],
                                        'diambil'       => ['label' => 'Diambil',       'class' => 'btn-outline-success']
                                    ];
                                    foreach ($options_status as $val => $info):
                                        $isChecked = ($row['status_pesanan'] == $val) ? 'checked' : '';
                                        $unique_id = "status_" . $row['id_pesanan'] . "_" . $val;
                                    ?>
                                    <input type="radio" class="btn-check radio-status-laundry" name="status_pesanan"
                                        value="<?= $val ?>" id="<?= $unique_id ?>"
                                        data-id="<?= $row['id_pesanan'] ?>"
                                        <?= $isChecked ?> required>
                                    <label class="btn btn-sm <?= $info['class'] ?> rounded-pill px-3 py-1 fw-semibold"
                                        for="<?= $unique_id ?>">
                                        <?= $info['label'] ?>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <p class="small fw-bold text-muted mb-2">Masukkan Pembayaran</p>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text border-0 bg-white fw-bold text-muted">Rp.</span>
                                    <input type="number" name="jumlah_bayar" step="1000"
                                           class="form-control border-0 input-jumlah-bayar"
                                           placeholder="Masukkan disini..." min="0"
                                           data-form-id="<?= $id_form ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <p class="small fw-bold text-muted mb-2">Metode Pembayaran</p>
                                <select name="metode_pembayaran"
                                        class="form-select form-select-md border-0 shadow-sm select-metode"
                                        data-form-id="<?= $id_form ?>"
                                        data-pesanan-id="<?= $row['id_pesanan'] ?>">
                                    <?php foreach (['cash' => 'Cash', 'transfer' => 'Transfer', 'qris' => 'QRIS'] as $val => $lbl): ?>
                                    <option value="<?= $val ?>"
                                        <?= $row['metode_pembayaran'] == $val ? 'selected' : '' ?>>
                                        <?= $lbl ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- =====================================================
                                 AREA UPLOAD BUKTI PEMBAYARAN
                                 Muncul/hilang otomatis via JavaScript
                            ====================================================== -->
                            <div class="col-12" id="area-bukti-<?= $row['id_pesanan'] ?>"
                                 style="display: <?= ($row['metode_pembayaran'] !== 'cash') ? 'block' : 'none' ?>;">
                                <div class="p-3 rounded-3 border border-dashed"
                                     style="background-color: #f8f9ff; border-style: dashed !important;">
                                    <label class="form-label small fw-bold text-primary mb-2">
                                        <i class="bi bi-upload me-1"></i>
                                        Upload Bukti Pembayaran
                                        <span class="text-danger" id="label-wajib-<?= $row['id_pesanan'] ?>">*</span>
                                        <span class="text-muted fw-normal">(Wajib untuk Transfer/QRIS)</span>
                                    </label>
                                    <input type="file" name="bukti_pembayaran"
                                           id="input-bukti-<?= $row['id_pesanan'] ?>"
                                           class="form-control border-0 shadow-sm input-bukti"
                                           accept="image/jpeg,image/png,image/jpg,application/pdf"
                                           data-pesanan-id="<?= $row['id_pesanan'] ?>">
                                    <div class="form-text text-muted mt-1">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Format: JPG, PNG, PDF · Maks. 2MB
                                    </div>
                                    <!-- Preview thumbnail file yang dipilih (gambar saja) -->
                                    <div id="preview-bukti-<?= $row['id_pesanan'] ?>" class="mt-2"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <p class="small fw-bold text-muted mb-2"></p>
                                <button type="submit" name="update_status"
                                    class="btn btn-primary btn-md w-100 rounded-3 fw-bold">
                                    <i class="bi bi-check-lg me-1"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-4">
                        <p class="small text-muted mb-0">Sisa:</p>
                        <h3 class="fw-bold text-danger">Rp <?= number_format(max(0.0, $sisa_bayar), 0, ',', '.') ?></h3>
                    </div>
                    <div class="col-md-4">
                        <p class="small text-muted mb-0">Sudah Dibayar:</p>
                        <h3 class="fw-bold text-success">Rp <?= number_format($total_terbayar, 0, ',', '.') ?></h3>
                    </div>
                    <div class="col-md-4">
                        <p class="small text-muted mb-0">Total Harga:</p>
                        <h3 class="fw-bold text-primary mb-0">Rp <?= number_format($total_tampil, 0, ',', '.') ?></h3>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap text-end">
                    <a href="nota.php?id=<?= $row['id_pesanan'] ?>" target="_blank"
                        class="btn btn-outline-secondary btn-sm rounded-3 mt-2">
                        <i class="bi bi-printer me-1"></i> Cetak Nota
                    </a>
                    <?php if (!$is_locked): ?>
                    <form method="POST" action=""
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini secara permanen? Semua riwayat transaksi terkait juga akan ikut terhapus.');">
                        <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan'] ?>">
                        <button type="submit" name="hapus_pesanan" class="btn btn-outline-danger btn-sm rounded-3 mt-2">
                            <i class="bi bi-trash me-1"></i> Hapus Pesanan
                        </button>
                    </form>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>
    <?php endwhile; ?>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // ============================================================
    // 1. AUTO-TOGGLE AREA BUKTI SAAT METODE PEMBAYARAN DIGANTI
    // ============================================================
    document.querySelectorAll('.select-metode').forEach(function (select) {
        select.addEventListener('change', function () {
            const pesananId  = this.getAttribute('data-pesanan-id');
            const areaBukti  = document.getElementById('area-bukti-' + pesananId);
            const inputBukti = document.getElementById('input-bukti-' + pesananId);
            const metode     = this.value;

            if (metode === 'cash') {
                // Sembunyikan dan hilangkan required saat cash
                areaBukti.style.display  = 'none';
                inputBukti.removeAttribute('required');
                inputBukti.value = ''; // Reset pilihan file
                document.getElementById('preview-bukti-' + pesananId).innerHTML = '';
            } else {
                // Tampilkan dan tandai required untuk transfer/QRIS
                areaBukti.style.display = 'block';
                // required tidak dipasang di sini karena validasi utama ada di backend PHP
                // Ini hanya sebagai panduan visual ke user
            }
        });
    });

    // ============================================================
    // 2. PREVIEW THUMBNAIL FILE GAMBAR SEBELUM DIUPLOAD
    // ============================================================
    document.querySelectorAll('.input-bukti').forEach(function (inputFile) {
        inputFile.addEventListener('change', function () {
            const pesananId  = this.getAttribute('data-pesanan-id');
            const previewBox = document.getElementById('preview-bukti-' + pesananId);
            const file       = this.files[0];

            // Bersihkan preview lama
            previewBox.innerHTML = '';

            if (!file) return;

            // Cek ukuran file di sisi client (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                previewBox.innerHTML = `
                    <div class="alert alert-warning py-2 px-3 small mt-1 mb-0 rounded-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        File terlalu besar! Maksimal 2MB.
                    </div>`;
                this.value = ''; // Reset input
                return;
            }

            // Tampilkan thumbnail hanya untuk file gambar
            if (file.type.startsWith('image/')) {
                const reader  = new FileReader();
                reader.onload = function (e) {
                    previewBox.innerHTML = `
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <img src="${e.target.result}" alt="Preview"
                                 class="rounded-3 border shadow-sm"
                                 style="height: 80px; width: 80px; object-fit: cover;">
                            <div class="small text-muted">
                                <div class="fw-semibold text-dark">${file.name}</div>
                                <div>${(file.size / 1024).toFixed(1)} KB</div>
                                <div class="text-success"><i class="bi bi-check-circle me-1"></i>Siap diupload</div>
                            </div>
                        </div>`;
                };
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                // Tampilkan info nama file untuk PDF
                previewBox.innerHTML = `
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <i class="bi bi-file-earmark-pdf text-danger fs-3"></i>
                        <div class="small text-muted">
                            <div class="fw-semibold text-dark">${file.name}</div>
                            <div>${(file.size / 1024).toFixed(1)} KB</div>
                            <div class="text-success"><i class="bi bi-check-circle me-1"></i>Siap diupload</div>
                        </div>
                    </div>`;
            }
        });
    });

    // ============================================================
    // 3. AUTO-SET STATUS DIPROSES SAAT BERAT DIUBAH (EXISTING)
    // ============================================================
    document.querySelectorAll('.input-berat-laundry').forEach(function (input) {
        input.addEventListener('input', function () {
            const idPesanan  = this.getAttribute('data-id');
            const nilaiBerat = parseFloat(this.value);

            if (!isNaN(nilaiBerat) && nilaiBerat > 0) {
                const radioDiproses = document.getElementById(`status_${idPesanan}_diproses`);
                if (radioDiproses) radioDiproses.checked = true;
            }
        });
    });

});
</script>

<?php include 'includes/footer.php'; ?>