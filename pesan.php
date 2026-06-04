<?php
include 'includes/header.php';
/** @var mysqli $conn */
include 'includes.php'; 

// Mengambil data harga per kilogram dari master layanan
$query = mysqli_query($conn, "SELECT * FROM layanan");
$layanan = [];
while($row = mysqli_fetch_assoc($query)){
    $layanan[$row['id_layanan']] = $row['harga_perkg'];
}
?>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Pesan Layanan Laundry</h2>
            <p class="text-secondary small mb-0">Isi formulir di bawah untuk memesan dan konfirmasi</p>
        </div>
    </div>

    <!-- Form diarahkan ke proses_pesanan_2.php -->
    <form action="proses_pesanan.php" method="POST">

        <!-- BAGIAN 1: INFORMASI PELANGGAN -->
        <div class="card card-custom p-4 mb-4 border-0 shadow-sm">
            <h5 class="fw-bold mb-3">Informasi Pelanggan</h5>
            <p class="text-muted small">Data diri untuk pengiriman dan konfirmasi</p>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nama Lengkap *</label>
                    <input type="text" name="nama" class="form-control bg-light border-0"
                        placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nomor Telepon *</label>
                    <!-- VALIDASI FRONTEND: Batas minimal 10 digit dan hanya menerima angka -->
                    <input type="tel" name="telepon" class="form-control bg-light border-0" placeholder="08xxxxxxxxxx"
                        minlength="10" pattern="[0-9]+" required>
                    <!-- Catatan Petunjuk User -->
                    <div class="form-text text-danger small mt-1">* Minimal 10 digit angka.</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Alamat Lengkap *</label>
                    <textarea name="alamat" class="form-control bg-light border-0" rows="2"
                        placeholder="Jalan, Nomor Rumah, RT/RW, Kelurahan, Kecamatan" required></textarea>
                </div>
            </div>
        </div>

        <!-- BAGIAN 2: DAFTAR HARGA LAYANAN -->
        <div class="card card-custom p-4 mb-4 border-0 shadow-sm">
            <h5 class="fw-bold mb-1">Pilih Layanan</h5>
            <p class="text-muted small mb-4">Pilih jenis layanan laundry yang diinginkan</p>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card-layanan-small p-3 h-100">
                        <h6 class="fw-bold mb-1">Cuci Kering</h6>
                        <p class="text-muted small mb-2">Pakaian di cuci dan dikeringkan</p>
                        <span class="badge-harga">Rp <?php echo number_format($layanan[1], 0, ',', '.'); ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-layanan-small p-3 h-100">
                        <h6 class="fw-bold mb-1">Cuci Setrika</h6>
                        <p class="text-muted small mb-2">Cuci kering dan setrika rapi</p>
                        <span class="badge-harga">Rp <?php echo number_format($layanan[2], 0, ',', '.'); ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-layanan-small p-3 h-100">
                        <h6 class="fw-bold mb-1">Setrika Saja</h6>
                        <p class="text-muted small mb-2">Hanya layanan setrika</p>
                        <span class="badge-harga">Rp <?php echo number_format($layanan[3], 0, ',', '.'); ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-layanan-small p-3 h-100">
                        <h6 class="fw-bold mb-1">Express</h6>
                        <p class="text-muted small mb-2">Selesai dalam 24 jam</p>
                        <span class="badge-harga">Rp <?php echo number_format($layanan[4], 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- BAGIAN 3: DETAIL SUBMIT PESANAN -->
        <div id="container-form">
            <div class="item-form">
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
                        <div class="col-12">
                            <label class="form-label fw-bold">Catatan Tambahan (Opsional)</label>
                            <textarea name="catatan" class="form-control bg-light border-0 mb-3" rows="4"
                                placeholder="Instruksi khusus atau catatan untuk pesanan Anda"></textarea>
                        </div>
                    </div>
                     <button type="button" class="btn btn-danger" onclick="hapusForm(this)">
                                <i class="bi bi-trash me-1"></i>Hapus Pesanan
                            </button>
                </div>
            </div>
        </div>


                <div class="d-flex justify-content-between mb-4">
                    <button type="button" class="btn btn-info d-flex align-items-center justify-content-center fw-bold" style="width: 49%; color: #083954ff; font-size: 1.1rem;" id="btn-tambah">
                        <i class="bi bi-plus-square me-1" style=" color: #083954ff; font-size : 1.1rem"></i>Tambah Pesanan
                    </button>
                    <!-- tombol Simpan sekarang di dalam <form> -->
                    <button type="submit" class="btn btn-pesan-final d-flex align-items-center justify-content-center" style="width: 49%;">
                        <i class="bi bi-floppy2-fill me-1"></i>Pesan Sekarang
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

    //Reset semua input, bukan cuma yang pertama
    formBaru.querySelectorAll('input').forEach(input => {
        alue = '';
        input.value = '';
    });
    formBaru.querySelectorAll('select').forEach(select => {
        select.selectedIndex = 0;
    });

    container.appendChild(formBaru);
});

//FIX: nama fungsi hapusForm (camelCase), this = tombol button itu sendiri
function hapusForm(tombol) {
    const jumlahForm = document.querySelectorAll('.item-form').length;

    if (jumlahForm > 1) {
        //parentElement dari button → card → item-form
        tombol.closest('.item-form').remove();
    } else {
        alert("Minimal harus ada satu form!");
    }
}
</script>
<?php include 'includes/footer.php'; ?>