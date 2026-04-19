<?php 
include 'includes/header.php'; 
include 'includes.php'; 

$id_nota = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

// Query JOIN yang sudah diperbaiki kolomnya (kuantitas & harga_perkg)
$sql = "SELECT p.*, c.nama_pelanggan, c.no_hp, c.alamat, d.kuantitas as berat, l.nama_layanan, l.harga_perkg as harga
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

$biaya_admin = 1000;
// Menggunakan alias 'berat' dan 'harga' dari query di atas
$total_harga = $data['berat'] * $data['harga'];
$total_akhir = $total_harga + $biaya_admin;
?>

<div class="container py-5">
    <div class="card border-0 shadow-sm rounded-4 p-5 mx-auto" style="max-width: 800px; background-color: #f8f9fa;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Nota Transaksi</h2>
            <span class="badge bg-light text-success border border-success rounded-pill px-3 py-2">Lunas</span>
        </div>
        <p class="text-muted mb-4">Order ID: <span class="text-primary fw-bold">#ORD-<?= $data['id_pesanan']; ?></span></p>

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

        <div class="table-responsive bg-white rounded-3 border mb-4">
            <table class="table table-borderless mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="py-3 px-4">LAYANAN</th>
                        <th class="text-center">QTY</th>
                        <th class="text-center">HARGA</th>
                        <th class="text-end px-4">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-bottom">
                        <td class="py-3 px-4"><strong><?= $data['nama_layanan']; ?></strong></td>
                        <td class="text-center"><?= $data['berat']; ?> kg</td>
                        <td class="text-center">Rp <?= number_format($data['harga']); ?></td>
                        <td class="text-end px-4 fw-bold">Rp <?= number_format($total_harga); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="py-3 px-4 text-muted text-end">Biaya Admin</td>
                        <td class="text-end px-4 fw-bold">Rp <?= number_format($biaya_admin); ?></td>
                    </tr>
                </tbody>
                <tfoot class="bg-light border-top">
                    <tr>
                        <td colspan="3" class="py-3 px-4 fw-bold text-center">Total Akhir</td>
                        <td class="py-3 text-end px-4 fw-bold text-primary h4 mb-0">Rp <?= number_format($total_akhir); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-center p-4 rounded-4" style="border: 2px dashed #dee2e6;">
            <h5 class="fw-bold">Terima Kasih, <?= explode(' ', $data['nama_pelanggan'])[0]; ?>!</h5>
            <p class="text-muted small">Pesanan Anda sedang kami proses dengan sepenuh hati.</p>
            <div class="mt-3">
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-4">Unduh Nota</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>