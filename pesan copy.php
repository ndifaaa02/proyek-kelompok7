<?php
include 'includes/header.php';
/** @var mysqli $conn */
include 'includes.php'; 

// Mengambil data lengkap dari master layanan untuk daftar harga & dropdown
$query = mysqli_query($conn, "SELECT * FROM layanan ORDER BY id_layanan ASC");
$daftar_layanan = [];
$layanan_json = [];

while($row = mysqli_fetch_assoc($query)){
    $daftar_layanan[] = $row;
    // Menyimpan pasangan ID dan Harga untuk kebutuhan hitung otomatis di JavaScript jika diperlukan
    $layanan_json[$row['id_layanan']] = $row['harga_perkg'];
}
?>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
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
                    <input type="text" name="nama_pelanggan" class="form-control" placeholder="Nama lengkap Anda" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nomor WhatsApp *</label>
                    <input type="tel" name="no_hp" class="form-control" placeholder="Contoh: 08123456789" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Alamat Lengkap *</label>
                    <input type="text" name="alamat" class="form-control" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Catatan Khusus (Opsional)</label>
                    <input type="text" name="catatan" class="form-control" placeholder="Contoh: jangan campur pakaian luntur, setrika rapi">
                </div>
            </div>
        </div>

        <div class="card card-custom p-4 mb-4 border-0 shadow-sm">
            <h5 class="fw-bold mb-1">Daftar Harga Layanan</h5>
            <p class="text-secondary small mb-4">Berikut adalah tarif kiloan yang berlaku saat ini</p>
            
            <div class="row g-3">
                <?php foreach ($daftar_layanan as $l): ?>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 border bg-light">
                            <span class="small fw-bold text-muted d-block text-uppercase" style="font-size:0.75rem;">Tarif Kiloan</span>
                            <h5 class="fw-bold text-dark my-1"><?= htmlspecialchars($l['nama_layanan']) ?></h5>
                            <p class="text-muted small mb-2" style="font-size:0.8rem; min-height: 24px;">
                                <?= !empty($l['deskripsi']) ? htmlspecialchars($l['deskripsi']) : 'Layanan cuci higienis dan rapi.' ?>
                            </p>
                            <h4 class="fw-bold text-primary mb-0">Rp <?= number_format($l['harga_perkg'], 0, ',', '.') ?><span class="fs-6 fw-normal text-muted">/kg</span></h4>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card card-custom p-4 mb-4 border-0 shadow-sm">
            <h5 class="fw-bold mb-1">Detail Pakaian & Layanan</h5>
            <p class="text-secondary small mb-3">Pilih kategori jenis pengerjaan laundry Anda</p>

            <div id="container-form">
                <div class="item-form p-3 border rounded-4 bg-white mb-3 position-relative">
                    
                    <button type="button" class="btn btn-sm btn-light text-danger border position-absolute rounded-3 btn-hapus-form" 
                            style="top: 15px; right: 15px; z-index: 5;" onclick="hapusForm(this)">
                        <i class="bi bi-trash-fill"></i>
                    </button>

                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Pilih Jenis Layanan</label>
                            <select name="id_layanan[]" class="form-select border-0 bg-light shadow-sm py-2" required>
                                <option value="" disabled selected>-- Pilih Layanan --</option>
                                <?php foreach ($daftar_layanan as $l): ?>
                                    <option value="<?= $l['id_layanan'] ?>">
                                        <?= htmlspecialchars($l['nama_layanan']) ?> (Rp <?= number_format($l['harga_perkg'], 0, ',', '.') ?>/kg)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <p class="small fw-bold text-muted mb-2">Berat Aktual</p>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold fs-3 text-dark text-nowrap">Estimasi</span>
                                <input type="number" name="berat[]" class="form-control bg-light border-0 shadow-sm" 
                                       step="0.01" min="0" placeholder="Contoh: 3.5" required
                                       style="background-color: white !important; max-width: 140px;">
                                <span class="fw-bold text-muted fs-5">kg</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <button type="button" id="btn-tambah" class="btn bg-white shadow-sm rounded-pill px-4 py-2 border text-dark fw-bold" style="width: 49%;">
                    <i class="bi bi-plus-circle-fill me-1" style="color: #083954ff; font-size : 1.1rem"></i>Tambah Pesanan
                </button>
                <button type="submit" class="btn btn-pesan-final d-flex align-items-center justify-content-center text-white" style="width: 49%; background-color: #083954ff;">
                    <i class="bi bi-floppy2-fill me-1"></i>Pesan Sekarang
                </button>
            </div>
        </div>
    </form>
</div>

<script>
const container = document.getElementById('container-form');
const btnTambah = document.getElementById('btn-tambah');

btnTambah.addEventListener('click', function() {
    const formAsli = document.querySelector('.item-form');
    const formBaru = formAsli.cloneNode(true);

    // Reset semua nilai input/select di form duplikasi baru
    formBaru.querySelectorAll('input').php
    formBaru.querySelectorAll('input').forEach(input => {
        input.value = '';
    });
    formBaru.querySelectorAll('select').forEach(select => {
        select.selectedIndex = 0;
    });

    container.appendChild(formBaru);
});

function hapusForm(tombol) {
    const jumlahForm = document.querySelectorAll('.item-form').length;

    if (jumlahForm > 1) {
        tombol.parentElement.remove();
    } else {
        alert('Minimal harus memesan 1 layanan laundry!');
    }
}
</script>

<?php include 'includes/footer.php'; ?>