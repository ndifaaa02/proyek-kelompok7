<?php
session_start();

// 1. Proteksi Halaman Admin
if (!isset($_SESSION['login'])) {
    header("Location: admin_login.php");
    exit;
}

include 'includes/navbar.php';
/** @var mysqli $conn */
include 'includes.php';

// Set zona waktu lokal
date_default_timezone_set('Asia/Jakarta');
$hari_ini = date('Y-m-d');

// Inisialisasi notifikasi alert
$pesan_sukses = "";
$pesan_error = "";

// ============================================================
// PROSES A: TAMBAH DATA PENGELUARAN
// ============================================================
if (isset($_POST['tambah_pengeluaran'])) {
    $tanggals    = $_POST['tanggal_pengeluaran'];
    $keterangans = $_POST['keterangan'];
    $kategoris   = $_POST['id_kategori'];
    $jumlahs     = $_POST['jumlah'];

    $ada_error = false;

    foreach ($tanggals as $i => $tanggal_input) {
        $keterangan  = mysqli_real_escape_string($conn, trim($keterangans[$i]));
        $id_kategori = !empty($kategoris[$i]) ? (int)$kategoris[$i] : "NULL";
        $jumlah_dana = (int) $jumlahs[$i];

        if ($tanggal_input > $hari_ini) {
            $pesan_error = "Gagal! Tanggal pada form ke-" . ($i + 1) . " tidak boleh melebihi hari ini.";
            $ada_error = true;
            break;
        }

        $query_tambah = "INSERT INTO pengeluaran 
                            (tanggal_pengeluaran, keterangan, jumlah, id_kategori) 
                         VALUES 
                            ('$tanggal_input', '$keterangan', $jumlah_dana, $id_kategori)";

        if (!mysqli_query($conn, $query_tambah)) {
            $pesan_error = "Gagal menyimpan data ke-" . ($i + 1) . ": " . mysqli_error($conn);
            $ada_error = true;
            break;
        }
    }

    if (!$ada_error) {
        $jumlah_tersimpan = count($tanggals);
        $pesan_sukses = "$jumlah_tersimpan pengeluaran berhasil ditambahkan!";
    }
}

// ============================================================
// PROSES B: EDIT DATA PENGELUARAN
// ============================================================
if (isset($_POST['edit_pengeluaran'])) {
    $id_pengeluaran      = (int)$_POST['id_pengeluaran'];
    $tanggal_pengeluaran = mysqli_real_escape_string($conn, $_POST['tanggal_pengeluaran']);
    $keterangan          = mysqli_real_escape_string($conn, trim($_POST['keterangan']));
    $id_kategori         = !empty($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : "NULL";
    $jumlah              = (int)$_POST['jumlah'];

    if ($tanggal_pengeluaran > $hari_ini) {
        $pesan_error = "Gagal memperbarui! Tanggal pengeluaran tidak boleh melebihi hari ini.";
    } else {
        $query_update = "UPDATE pengeluaran SET 
                            tanggal_pengeluaran = '$tanggal_pengeluaran', 
                            keterangan = '$keterangan', 
                            jumlah = $jumlah, 
                            id_kategori = $id_kategori 
                         WHERE id_pengeluaran = $id_pengeluaran";
        
        if (mysqli_query($conn, $query_update)) {
            $pesan_sukses = "Data pengeluaran berhasil diperbarui!";
        } else {
            $pesan_error = "Gagal memperbarui data pengeluaran: " . mysqli_error($conn);
        }
    }
}

// ============================================================
// PROSES C: HAPUS DATA PENGELUARAN
// ============================================================
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    if (mysqli_query($conn, "DELETE FROM pengeluaran WHERE id_pengeluaran = $id_hapus")) {
        $pesan_sukses = "Pengeluaran berhasil dihapus.";
    } else {
        $pesan_error = "Gagal menghapus data.";
    }
}

// ============================================================
// PROSES D: HITUNG TOTAL PENGELUARAN (Kotak Kiri Bawah)
// ============================================================
$query_total = mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM pengeluaran");
$data_total  = mysqli_fetch_assoc($query_total);
$total_pengeluaran = $data_total['total'] ?? 0;

// E: Ambil opsi kategori ke bentuk array PHP agar dropdown dinamis tidak kosong saat di-clone JavaScript
$result_kategori = mysqli_query($conn, "SELECT * FROM kategori_pengeluaran");
$list_kategori = [];
while ($kat = mysqli_fetch_assoc($result_kategori)) {
    $list_kategori[] = $kat;
}

// F: Ambil data gabungan pengeluaran untuk ditampilkan di tabel utama
$query_tabel = "SELECT p.*, k.nama_kategori 
                FROM pengeluaran p 
                LEFT JOIN kategori_pengeluaran k ON p.id_kategori = k.id_kategori 
                ORDER BY p.tanggal_pengeluaran DESC, p.id_pengeluaran DESC";
$result_pengeluaran = mysqli_query($conn, $query_tabel);
?>

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
        <div class="d-flex align-items-center">
            <h2 class="fw-bold m-0" style="color: #1e293b;">
                <i class="bi bi-folder2-open fs-3" style="color: #0d6efd;"></i> Kelola Pengeluaran
            </h2>
        </div>
        <a href="admin_dashboard.php"
            class="btn btn-white bg-white rounded-pill px-4 py-2 shadow-sm fw-semibold text-dark text-decoration-none border-0">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <?php if (!empty($pesan_sukses)): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm d-flex align-items-center py-3 mb-4"
        role="alert" style="background-color: #d1e7dd; color: #0f5132;">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div><?= $pesan_sukses; ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (!empty($pesan_error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm d-flex align-items-center py-3 mb-4"
        role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div><?= $pesan_error; ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="mb-4">
        <div class="card border-0 p-3" style="background-color: #fde8e8; border-radius: 1rem;">
            <div class="small fw-semibold text-secondary mb-1">
                <span class="text-danger me-1">▲</span> Total Pengeluaran
            </div>
            <h2 class="fw-bold text-danger m-0">Rp <?= number_format($total_pengeluaran, 0, ',', '.'); ?></h2>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <form method="POST" action="">
                <div id="container-form">
                    <div class="item-form">
                        <div class="card card-custom p-4 mb-4">
                            <h5 class="fw-bold text-primary mb-4 d-flex align-items-center">
                                <i class="bi bi-plus-circle me-2"></i> Tambah Pengeluaran
                            </h5>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Tanggal</label>
                                <input type="date" name="tanggal_pengeluaran[]" class="form-control form-control-custom"
                                    max="<?= $hari_ini; ?>" value="<?= $hari_ini; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Keterangan</label>
                                <input type="text" name="keterangan[]" class="form-control form-control-custom"
                                    placeholder="Contoh: Beli deterjen" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Kategori</label>
                                <select name="id_kategori[]" class="form-select form-control-custom">
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach($list_kategori as $kat): ?>
                                    <option value="<?= $kat['id_kategori']; ?>">
                                        <?= htmlspecialchars($kat['nama_kategori']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary">Jumlah (Rp)</label>
                                <input type="number" name="jumlah[]" class="form-control form-control-custom"
                                    placeholder="Contoh: 50000" required>
                            </div>

                            <button type="button" class="btn btn-danger" onclick="hapusForm(this)">
                                <i class="bi bi-trash me-1"></i>Hapus Form
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <button type="button" class="btn btn-info text-white fw-semibold" id="btn-tambah" style="width: 49%;">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Form
                    </button>
                    <button type="submit" name="tambah_pengeluaran" class="btn btn-custom-blue" style="width: 49%;">
                        <i class="bi bi-floppy2-fill me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>

        <script>
        const container = document.getElementById('container-form');
        const btnTambah = document.getElementById('btn-tambah');

        btnTambah.addEventListener('click', function() {
            const formAsli = document.querySelector('.item-form');
            const formBaru = formAsli.cloneNode(true);

            //Reset semua input pada form baru hasil kloning
            formBaru.querySelectorAll('input').forEach(input => {
                if (input.type === 'date') {
                    input.value = '<?= $hari_ini; ?>';
                } else {
                    input.value = '';
                }
            });
            formBaru.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0;
            });

            container.appendChild(formBaru);
        });

        function hapusForm(tombol) {
            const jumlahForm = document.querySelectorAll('.item-form').length;

            if (jumlahForm > 1) {
                tombol.closest('.item-form').remove();
            } else {
                alert("Minimal harus ada satu form!");
            }
        }
        </script>

        <div class="col-md-8">
            <div class="card card-custom p-4">
                <h5 class="fw-bold text-dark mb-4">Daftar Pengeluaran</h5>
                <div class="table-responsive">
                    <table class="table align-middle text-start mb-0"
                        style="border-collapse: separate; border-spacing: 0 10px;">
                        <thead>
                            <tr class="text-secondary small fw-bold border-bottom">
                                <th style="width: 5%;">#</th>
                                <th style="width: 22%;">Tanggal</th>
                                <th style="width: 28%;">Keterangan</th>
                                <th style="width: 20%;">Kategori</th>
                                <th style="width: 15%;">Jumlah</th>
                                <th class="text-center" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result_pengeluaran) > 0): ?>
                            <?php 
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result_pengeluaran)): 
                                ?>
                            <tr class="border-bottom">
                                <td class="text-muted small"><?= $no++; ?></td>
                                <td class="text-dark fw-medium">
                                    <?= date('d M Y', strtotime($row['tanggal_pengeluaran'])); ?></td>
                                <td class="text-muted"><?= htmlspecialchars($row['keterangan']); ?></td>
                                <td>
                                    <?php if(!empty($row['nama_kategori'])): ?>
                                    <span class="badge-kategori"><?= htmlspecialchars($row['nama_kategori']); ?></span>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-danger fw-bold">Rp <?= number_format($row['jumlah'], 0, ',', '.'); ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button"
                                            class="btn-action-outline btn-action-edit"
                                            data-bs-toggle="modal" data-bs-target="#modalEdit"
                                            data-id="<?= $row['id_pengeluaran']; ?>"
                                            data-keterangan="<?= htmlspecialchars($row['keterangan']); ?>"
                                            data-jumlah="<?= $row['jumlah']; ?>" 
                                            data-kategori="<?= $row['id_kategori']; ?>"
                                            data-tanggal="<?= $row['tanggal_pengeluaran']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="admin_pengeluaran.php?hapus=<?= $row['id_pengeluaran']; ?>"
                                            class="btn-action-outline btn-action-delete"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"> 
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data pengeluaran.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 1rem;">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold" id="modalEditLabel">Edit Data Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body py-3">
                    <input type="hidden" name="id_pengeluaran" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tanggal</label>
                        <input type="date" name="tanggal_pengeluaran" id="edit-tanggal" class="form-control"
                            max="<?= $hari_ini; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keterangan</label>
                        <input type="text" name="keterangan" id="edit-keterangan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kategori</label>
                        <select name="id_kategori" id="edit-kategori" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach($list_kategori as $kat): ?>
                            <option value="<?= $kat['id_kategori']; ?>">
                                <?= htmlspecialchars($kat['nama_kategori']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jumlah</label>
                        <input type="number" name="jumlah" id="edit-jumlah" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="edit_pengeluaran" class="btn btn-primary rounded-3">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-action-edit').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('edit-id').value = this.getAttribute('data-id');
        document.getElementById('edit-keterangan').value = this.getAttribute('data-keterangan');
        document.getElementById('edit-jumlah').value = this.getAttribute('data-jumlah');
        document.getElementById('edit-tanggal').value = this.getAttribute('data-tanggal');
        
        const kategoriId = this.getAttribute('data-kategori');
        document.getElementById('edit-kategori').value = kategoriId ? kategoriId : "";
    });
});
</script>

<?php include 'includes/footer.php'; ?>