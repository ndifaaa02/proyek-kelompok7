<?php 
include 'includes/header.php'; 
/** @var mysqli $conn */
include 'includes.php'; 

$id_nota = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

// Query JOIN yang sudah diperbaiki kolomnya (kuantitas & harga_perkg)
// Query yang mengambil harga dari detail_pesanan (d), bukan layanan (l)
$sql = "SELECT p.*, c.nama_pelanggan, c.no_hp, c.alamat, 
               d.kuantitas as berat, 
               d.harga_layanan as harga,  -- MENGAMBIL HARGA SNAPSHOT
               l.nama_layanan
        FROM pesanan p
        JOIN pelanggan c ON p.id_pelanggan = c.id_pelanggan
        JOIN detail_pesanan d ON p.id_pesanan = d.id_pesanan
        JOIN layanan l ON d.id_layanan = l.id_layanan
        WHERE p.id_pesanan = '$id_nota'";

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<div class='container py-5'><h4>Data transaksi tidak ditemukan.</h4></div>";
    include 'includes/footer.php'; exit;
}

// Menggunakan alias 'berat' dan 'harga' dari query di atas
$total_harga = $data['berat'] * $data['harga'];
$total_akhir = $total_harga;
?>

<div class="container py-5">
    <div class="card border-0 shadow-sm rounded-4 p-5 mx-auto" style="max-width: 800px; background-color: #f8f9fa;">
        <p class="text-muted mb-4">Order ID: <span class="text-primary fw-bold">#ORD-<?= $data['id_pesanan']; ?></span>
        </p>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="bg-white p-3 rounded-3 border">
                    <p class="small text-muted text-uppercase mb-1">Pelanggan</p>
                    <h6 class="fw-bold mb-1"><?= $data['nama_pelanggan']; ?></h6>
                    <p class="small text-muted mb-0"><?= $data['no_hp']; ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bg-white p-3 rounded-3 border">
                    <p class="small text-muted text-uppercase mb-1">Waktu & Lokasi</p>
                    <h6 class="fw-bold mb-1"><?= date('d F Y', strtotime($data['tanggal_masuk'])); ?></h6>
                    <p class="small text-muted mb-0"><?= $data['alamat']; ?></p>
                </div>
            </div>
        </div>

        <div class="text-center p-4 rounded-4" style="border: 2px dashed #dee2e6;">
            <h5 class="fw-bold">Terima Kasih, <?= explode(' ', $data['nama_pelanggan'])[0]; ?>!</h5>
            <p class="text-muted small">Pesanan Anda akan kami proses dengan sepenuh hati.</p>
            <div class="mt-3">
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer2.php'; ?>