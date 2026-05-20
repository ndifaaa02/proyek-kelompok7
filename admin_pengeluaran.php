<?php
include 'includes/header.php';
?>
<div class="container py-4">
    <div id="container-form">
        <div class="d-flex justify-content-between align-items-center mb-4 text-start">
            <div class="d-flex align-items-center">
                <i class="bi bi-wallet2 fs-2 text-primary me-3"></i>
                <h2 class="fw-bold mb-0">Kelola Pengeluaran</h2>
            </div>
            <a href="admin_dashboard.php"class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
                <i class="bi bi-arrow-left me-2"></i> Dashboard
            </a>
        </div>
    
    <div class="item-form" style="margin-bottom: 10px;">
        <div class="card border-0 shadow-sm p-5 rounded-4 mb-4 text-start">
            <h4 class="fw-bold text-primary mb-4">Update Biaya Pengeluaran</h4>
            <form action="" method="post">
                    <div class="mb-4">
                        <label class="fw-bold mb-2" style="color: #2d749a;">Keterangan</label>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control border-0 bg-light p-3 rounded-3" name="data[]" placeholder="Masukkan Keterangan">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2" style="color: #2d749a;">Kategori</label>
                        <div class="d-flex align-items-center">
                            <select name="status_pesanan" class="form-select border-0 shadow-sm">
                                <option value="operasional">Operasional</option>
                                <option value="dll">Dll</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary shadow-sm"><i class="bi bi-check"></i></button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2" style="color: #2d749a;">Total Biaya</label>
                        <div class="d-flex align-items-center">
                            <span class="me-3 fw-bold" style="color: #2d749a;">Rp</span>
                            <input type="number" class="form-control border-0 bg-light p-3 rounded-3" name="data[]" placeholder="Total Biaya">
                        </div>
                    </div>
            </form>
                 <button type="button" class="btn btn-info w-100 py-3 fw-bold text-white shadow-sm" style="background-color: #b9e3e9; border:none; color: #2d749a !important;" onclick="hapusForm(this)">Hapus</button>
        </div>
    </div>

    </div>
                <button type="button" class="btn btn-info w-100 py-3 fw-bold text-white shadow-sm mt-2" style="background-color: #b9e3e9; border:none; color: #2d749a !important;" id="btn-tambah">Tambah Form</button>
</div>

<script>
    // Mengambil referensi elemen dari HTML
const container = document.getElementById('container-form');
const btnTambah = document.getElementById('btn-tambah');

// Fungsi untuk menambah form
btnTambah.addEventListener('click', function() {
    // 1. Ambil elemen form pertama sebagai template
    const formAsli = document.querySelector('.item-form');
    
    // 2. Clone/Duplikasi elemen tersebut (true artinya menyalin semua isinya)
    const formBaru = formAsli.cloneNode(true);
    
    // 3. Reset nilai input pada form baru agar tidak ikut tersalin isinya
    formBaru.querySelector('input').value = '';
    
    // 4. Masukkan form baru ke dalam container
    container.appendChild(formBaru);
});

// Fungsi untuk menghapus form tertentu
function hapusForm(tombol) {
    // Cek dulu, jangan hapus jika itu satu-satunya form yang tersisa
    const jumlahForm = document.querySelectorAll('.item-form').length;
    
    if (jumlahForm > 1) {
        // Hapus elemen induk (div .item-form) dari tombol yang diklik
        tombol.parentElement.remove();
    } else {
        alert("Minimal harus ada satu form!");
    }
}
</script>
<?php
include 'includes/footer.php';
?>