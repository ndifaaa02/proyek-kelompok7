<?php
session_start();

// Proteksi halaman admin
if (!isset($_SESSION['login'])) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/header.php';
/** @var mysqli $conn */
include 'includes.php';

$pesan_sukses = "";
$pesan_error = "";

// ============================================================
// PROSES A: TAMBAH LAYANAN BARU
// ============================================================
if (isset($_POST['tambah_layanan'])) {
    $nama_layanan  = mysqli_real_escape_string($conn, trim($_POST['nama_layanan']));
    $deskripsi     = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $harga_per_kg  = (int) $_POST['harga_per_kg'];

    $cek_nama = mysqli_query($conn, "SELECT * FROM layanan WHERE nama_layanan = '$nama_layanan'");
    
    if (mysqli_num_rows($cek_nama) > 0) {
        $pesan_error = "Gagal menambah! Nama layanan '$nama_layanan' sudah ada.";
    } else {
        // Urutan kolom disesuaikan: nama_layanan, deskripsi, baru harga_perkg
        $query_tambah = "INSERT INTO layanan (nama_layanan, deskripsi, harga_perkg, harga_layanan) VALUES ('$nama_layanan', '$deskripsi', $harga_per_kg, 0)";
        if (mysqli_query($conn, $query_tambah)) {
            $pesan_sukses = "Layanan baru berhasil ditambahkan!";
        } else {
            $pesan_error = "Terjadi kesalahan sistem saat menyimpan data.";
        }
    }
}

// ============================================================
// PROSES B: EDIT DATA LAYANAN
// ============================================================
if (isset($_POST['edit_layanan'])) {
    $id_layanan   = (int) $_POST['id_layanan'];
    $nama_layanan = mysqli_real_escape_string($conn, trim($_POST['nama_layanan']));
    $deskripsi     = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $harga_per_kg = (int) $_POST['harga_per_kg'];

    $cek_nama = mysqli_query($conn, "SELECT * FROM layanan WHERE nama_layanan = '$nama_layanan' AND id_layanan != $id_layanan");
    
    if (mysqli_num_rows($cek_nama) > 0) {
        $pesan_error = "Gagal memperbarui! Nama layanan '$nama_layanan' sudah digunakan oleh layanan lain.";
    } else {
        $query_update = "UPDATE layanan SET nama_layanan = '$nama_layanan', deskripsi = '$deskripsi', harga_perkg = $harga_per_kg WHERE id_layanan = $id_layanan";
        if (mysqli_query($conn, $query_update)) {
            $pesan_sukses = "Data layanan berhasil diperbarui!";
        } else {
            $pesan_error = "Gagal memperbarui data layanan.";
        }
    }
}

// ============================================================
// PROSES C: HAPUS LAYANAN
// ============================================================
if (isset($_POST['hapus_layanan'])) {
    $id_layanan = (int)$_POST['id_layanan'];

    $cek_relasi = mysqli_query($conn, "SELECT * FROM detail_pesanan WHERE id_layanan = $id_layanan LIMIT 1");
    if (mysqli_num_rows($cek_relasi) > 0) {
        $pesan_error = "Layanan tidak dapat dihapus karena sudah pernah digunakan dalam riwayat pesanan pelanggan!";
    } else {
        $query_hapus = "DELETE FROM layanan WHERE id_layanan = $id_layanan";
        if (mysqli_query($conn, $query_hapus)) {
            $pesan_sukses = "Layanan berhasil dihapus dari sistem.";
        } else {
            $pesan_error = "Gagal menghapus layanan.";
        }
    }
}

// Ambil data untuk tabel
$result = mysqli_query($conn, "SELECT * FROM layanan ORDER BY id_layanan DESC");
?>

<!-- Menambahkan CSS Kustom agar gaya visual persis seperti image_9244ae.png -->
<style>
body {
    background-color: #dbe7f3;
}

.card-custom {
    border-radius: 1rem;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    background-color: #ffffff;
}

.btn-custom-blue {
    background-color: #0d6efd;
    color: white;
    font-weight: 600;
    border-radius: 0.5rem;
}

.btn-custom-blue:hover {
    background-color: #0b5ed7;
    color: white;
}

.badge-kategori {
    background-color: #6c757d;
    color: white;
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
    border-radius: 10px;
}

.btn-action-outline {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    border: 1px solid;
    font-size: 0.85rem;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-action-edit {
    border-color: #0d6efd;
    color: #0d6efd;
}

.btn-action-edit:hover {
    background-color: #0d6efd;
    color: white;
}

.btn-action-delete {
    border-color: #dc3545;
    color: #dc3545;
}

.btn-action-delete:hover {
    background-color: #dc3545;
    color: white;
}

.form-control-custom {
    background-color: #f8f9fa;
    border: 1px solid #f1f3f5;
    border-radius: 0.5rem;
    padding: 0.6rem 0.75rem;
}

.form-control-custom:focus {
    background-color: #ffffff;
    border-color: #86b7fe;
    box-shadow: none;
}
</style>

<div class="container py-4 text-start">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Pengaturan Tarif & Layanan</h2>
            <p class="text-muted small mb-0">Manajemen variasi jenis jasa laundry beserta nominal harga per kilogram</p>
        </div>
        <a href="admin_dashboard.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <?php if ($pesan_sukses): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= $pesan_sukses ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($pesan_error): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i><?= $pesan_error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white sticky-top" style="top: 20px; z-index: 10;">
                <h5 class="fw-bold text-dark mb-3">Tambah Layanan Baru</h5>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Jasa / Layanan</label>
                        <input type="text" name="nama_layanan" class="form-control py-2 rounded-3" 
                               placeholder="Contoh: Cuci Kering Setrika" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Deskripsi Keterangan (Opsional)</label>
                        <textarea name="deskripsi" class="form-control rounded-3" rows="3" 
                                  placeholder="Contoh: Estimasi selesai 2-3 hari kerja."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Harga per Kilogram (kg)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border text-muted">Rp</span>
                            <input type="number" name="harga_per_kg" class="form-control py-2 rounded-3" 
                                   placeholder="Contoh: 7000" min="0" required>
                        </div>
                    </div>
                    <button type="submit" name="tambah_layanan" class="btn btn-primary btn-md w-100 rounded-3 fw-bold mt-2">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Layanan
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-start">
                        <thead class="table-light text-secondary border-bottom">
                            <tr>
                                <th class="py-3 ps-4" style="width: 10%">ID</th>
                                <th class="py-3" style="width: 40%">Jenis Jasa & Keterangan</th>
                                <th class="py-3" style="width: 25%">Tarif Kiloan</th>
                                <th class="py-3 pe-4 text-center" style="width: 25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) == 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i> Belum ada data layanan laundry.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr class="border-bottom">
                                        <td class="py-3 ps-4 fw-semibold text-muted">#<?= $row['id_layanan'] ?></td>
                                        <td class="py-3">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_layanan']) ?></div>
                                            <div class="text-muted small mt-0.5"><?= !empty($row['deskripsi']) ? htmlspecialchars($row['deskripsi']) : '<span class="text-muted opacity-50 italic small">Tidak ada keterangan deskripsi</span>'; ?></div>
                                        </td>
                                        <td class="py-3 fw-bold text-primary">Rp <?= number_format($row['harga_perkg'], 0, ',', '.') ?> <span class="small fw-normal text-muted">/kg</span></td>
                                        <td class="py-3 pe-4 text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn-action-outline btn-action-edit"
                                                        data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                        data-id="<?= $row['id_layanan'] ?>"
                                                        data-nama="<?= htmlspecialchars($row['nama_layanan']) ?>"
                                                        data-deskripsi="<?= htmlspecialchars($row['deskripsi'] ?? '') ?>"
                                                        data-harga="<?= $row['harga_perkg'] ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?');" class="m-0">
                                                    <input type="hidden" name="id_layanan" value="<?= $row['id_layanan'] ?>">
                                                    <button type="submit" name="hapus_layanan" class="btn-action-outline btn-action-delete ms-1">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 text-start">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Ubah Jasa Layanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body py-4">
                    <input type="hidden" name="id_layanan" id="edit-id">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Jasa / Layanan</label>
                        <input type="text" name="nama_layanan" id="edit-nama" class="form-control py-2 rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Deskripsi Keterangan</label>
                        <textarea name="deskripsi" id="edit-deskripsi" class="form-control rounded-3" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Harga per Kilogram (kg)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border text-muted">Rp</span>
                            <input type="number" name="harga_per_kg" id="edit-harga" class="form-control py-2 rounded-3" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="edit_layanan" class="btn btn-primary rounded-3 px-4 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-edit').forEach(button => {
    button.addEventListener('click', function() {
        const id_layanan   = this.getAttribute('data-id');
        const nama_layanan = this.getAttribute('data-nama');
        const deskripsi    = this.getAttribute('data-deskripsi');
        const harga_per_kg = this.getAttribute('data-harga');

        document.getElementById('edit-id').value = id_layanan;
        document.getElementById('edit-nama').value = nama_layanan;
        document.getElementById('edit-deskripsi').value = deskripsi;
        document.getElementById('edit-harga').value = harga_per_kg;
    });
});
</script>

<?php include 'includes/footer.php'; ?>