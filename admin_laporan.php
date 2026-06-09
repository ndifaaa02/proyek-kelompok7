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

$tahun_aktif = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// ==========================================
// 1. QUERY KARTU ANALISIS KEUANGAN
// ==========================================

// A. Total Pendapatan (kolom yang benar: total_bayar, tabel: transaksi)
$query_pendapatan = mysqli_query($conn, "SELECT SUM(total_bayar) as total_masuk FROM transaksi");
$data_pendapatan  = mysqli_fetch_assoc($query_pendapatan);
$total_pendapatan = $data_pendapatan['total_masuk'] ?? 0;

// B. Total Pengeluaran (kolom yang benar: jumlah, bukan nominal)
$query_pengeluaran = mysqli_query($conn, "SELECT SUM(jumlah) as total_keluar FROM pengeluaran");
$data_pengeluaran  = mysqli_fetch_assoc($query_pengeluaran);
$total_pengeluaran = $data_pengeluaran['total_keluar'] ?? 0;

// C. Laba Bersih
$laba_bersih = $total_pendapatan - $total_pengeluaran;
$margin_persen = $total_pendapatan > 0 ? round(($laba_bersih / $total_pendapatan) * 100, 1) : 0;


// ==========================================
// 2. QUERY GRAFIK BULANAN
// ==========================================

$pendapatan_bulanan  = array_fill(1, 12, 0);
$pengeluaran_bulanan = array_fill(1, 12, 0);

// FIX: kolom tanggal yang benar adalah tanggal_bayar (bukan tanggal_transaksi)
$query_chart_in = mysqli_query($conn, "SELECT MONTH(tanggal_bayar) as bulan, SUM(total_bayar) as total 
                                       FROM transaksi 
                                       WHERE YEAR(tanggal_bayar) = $tahun_aktif 
                                       GROUP BY MONTH(tanggal_bayar)");
while ($row = mysqli_fetch_assoc($query_chart_in)) {
    $pendapatan_bulanan[$row['bulan']] = (int)$row['total'];
}

// FIX: kolom jumlah (bukan nominal)
$query_chart_out = mysqli_query($conn, "SELECT MONTH(tanggal_pengeluaran) as bulan, SUM(jumlah) as total 
                                        FROM pengeluaran 
                                        WHERE YEAR(tanggal_pengeluaran) = $tahun_aktif 
                                        GROUP BY MONTH(tanggal_pengeluaran)");
while ($row = mysqli_fetch_assoc($query_chart_out)) {
    $pengeluaran_bulanan[$row['bulan']] = (int)$row['total'];
}

$laba_bulanan = [];
for ($i = 1; $i <= 12; $i++) {
    $laba_bulanan[$i] = $pendapatan_bulanan[$i] - $pengeluaran_bulanan[$i];
}


// ==========================================
// 3. QUERY PIE CHART - PENDAPATAN PER LAYANAN
// ==========================================

// Ambil semua layanan dari database secara dinamis
$layanan_list  = [];
$layanan_nama  = [];
$layanan_total = [];
$layanan_count = [];

$query_layanan = mysqli_query($conn, "SELECT id_layanan, nama_layanan FROM layanan ORDER BY id_layanan");
while ($row = mysqli_fetch_assoc($query_layanan)) {
    $layanan_list[$row['id_layanan']] = $row['nama_layanan'];
    $layanan_nama[]  = $row['nama_layanan'];
    $layanan_total[$row['id_layanan']] = 0;
    $layanan_count[$row['id_layanan']] = 0;
}

// Hitung total pendapatan & jumlah pesanan per layanan
$query_pie = mysqli_query($conn, "SELECT dp.id_layanan, 
                                         SUM(dp.subtotal) as total_layanan,
                                         COUNT(DISTINCT dp.id_pesanan) as jumlah_pesanan
                                  FROM detail_pesanan dp
                                  JOIN transaksi t ON dp.id_pesanan = t.id_pesanan
                                  GROUP BY dp.id_layanan");
while ($row = mysqli_fetch_assoc($query_pie)) {
    if (isset($layanan_total[$row['id_layanan']])) {
        $layanan_total[$row['id_layanan']] = (int)$row['total_layanan'];
        $layanan_count[$row['id_layanan']] = (int)$row['jumlah_pesanan'];
    }
}


// ==========================================
// 4. RIWAYAT PENGELUARAN TERBARU
// ==========================================

// FIX: kolom kategori sudah dihapus, sekarang JOIN ke tabel kategori_pengeluaran
$query_riwayat = mysqli_query($conn, "SELECT p.keterangan, p.jumlah, p.tanggal_pengeluaran, k.nama_kategori
                                      FROM pengeluaran p
                                      LEFT JOIN kategori_pengeluaran k ON p.id_kategori = k.id_kategori
                                      ORDER BY p.tanggal_pengeluaran DESC 
                                      LIMIT 5");


// ==========================================
// 5. KONVERSI KE JSON UNTUK CHART.JS
// ==========================================

$json_pendapatan  = json_encode(array_values($pendapatan_bulanan));
$json_pengeluaran = json_encode(array_values($pengeluaran_bulanan));
$json_laba        = json_encode(array_values($laba_bulanan));
$json_pie_data    = json_encode(array_values($layanan_total));
$json_pie_labels  = json_encode($layanan_nama);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-graph-up fs-2 text-primary me-3"></i>
            <h2 class="fw-bold mb-0">Laporan Keuangan</h2>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <!-- Filter Tahun -->
            <form method="GET" class="d-flex gap-2 align-items-center">
                <select name="tahun" class="form-select form-select-sm rounded-pill" style="width: 75px;" onchange="this.form.submit()">
                    <?php for ($y = date('Y'); $y >= 2024; $y--): ?>
                        <option value="<?= $y ?>" <?= $y == $tahun_aktif ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </form>
            <a href="admin_dashboard.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
                <i class="bi bi-arrow-left me-2"></i> Dashboard
            </a>
        </div>
    </div>

    <h5 class="text-primary fw-bold mb-4 text-start">Analisis Laba/Rugi</h5>

    <!-- Kartu Ringkasan -->
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <p class="small text-muted mb-2"><i class="bi bi-arrow-down-circle-fill text-success me-1"></i> Total Pendapatan</p>
                <h3 class="fw-bold mb-1 text-success">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                <p class="small text-muted mb-0">Dari semua transaksi masuk</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <p class="small text-muted mb-2"><i class="bi bi-arrow-up-circle-fill text-danger me-1"></i> Total Pengeluaran</p>
                <h3 class="fw-bold mb-1 text-danger">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h3>
                <p class="small text-muted mb-0">Biaya operasional & bahan baku</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100" style="background-color: #f4fbf7;">
                <p class="small text-muted mb-2"><i class="bi bi-cash-stack text-primary me-1"></i> Laba Bersih</p>
                <h3 class="fw-bold mb-1 <?= $laba_bersih >= 0 ? 'text-primary' : 'text-danger' ?>">
                    Rp <?= number_format($laba_bersih, 0, ',', '.') ?>
                </h3>
                <p class="small text-muted mb-0">Margin: <?= $margin_persen ?>%</p>
            </div>
        </div>
    </div>

    <!-- Grafik Baris 1 -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <h6 class="fw-bold mb-3">Tren Laba Bersih (<?= $tahun_aktif ?>)</h6>
                <canvas id="lineChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <h6 class="fw-bold mb-3">Perbandingan Bulanan</h6>
                <canvas id="barChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Grafik Baris 2 -->
    <div class="row g-4 mb-4">
        <!-- Pie Chart -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <h6 class="fw-bold mb-3">Pendapatan per Layanan</h6>
                <div class="d-flex justify-content-center align-items-center" style="height: 260px;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detail Layanan dari DB -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <h6 class="fw-bold mb-3">Detail Layanan</h6>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead class="text-muted small">
                            <tr>
                                <th>Layanan</th>
                                <th class="text-center">Pesanan</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $warna_badge = ['bg-primary', 'bg-info', 'bg-warning', 'bg-success', 'bg-danger'];
                            $i = 0;
                            foreach ($layanan_list as $id => $nama): 
                                $warna = $warna_badge[$i % count($warna_badge)];
                            ?>
                            <tr>
                                <td>
                                    <span class="badge <?= $warna ?> me-2">&nbsp;</span>
                                    <?= htmlspecialchars($nama) ?>
                                </td>
                                <td class="text-center"><?= $layanan_count[$id] ?> pesanan</td>
                                <td class="text-end fw-bold">Rp <?= number_format($layanan_total[$id], 0, ',', '.') ?></td>
                            </tr>
                            <?php $i++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pengeluaran Terbaru -->
    <div class="card border-0 shadow-sm p-4 rounded-4">
        <h6 class="fw-bold mb-3">Riwayat Pengeluaran Terbaru</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="text-muted small">
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Kategori</th>
                        <th class="text-end">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($query_riwayat)): ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($row['tanggal_pengeluaran'])) ?></td>
                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td>
                                        <?php if ($row['nama_kategori']): ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                        <td class="text-end text-danger fw-bold">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    const dataPendapatan  = <?= $json_pendapatan ?>;
    const dataPengeluaran = <?= $json_pengeluaran ?>;
    const dataLaba        = <?= $json_laba ?>;
    const dataPieLayanan  = <?= $json_pie_data ?>;
    const labelPie        = <?= $json_pie_labels ?>;
    const labelBulan      = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    // Cari bulan terakhir yang punya data (untuk slicing bar chart)
    let bulanAktif = 1;
    for (let i = 0; i < 12; i++) {
        if (dataPendapatan[i] > 0 || dataPengeluaran[i] > 0) bulanAktif = i + 1;
    }
    const sliceBulan = Math.max(bulanAktif, 2);

    // 1. Line Chart - Tren Laba Bersih
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: labelBulan,
            datasets: [{
                label: 'Laba Bersih',
                data: dataLaba,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: {
                y: {
                    ticks: {
                        callback: val => 'Rp ' + val.toLocaleString('id-ID')
                    }
                }
            }
        }
    });

    // 2. Bar Chart - Perbandingan Bulanan (hanya bulan yang ada datanya)
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: labelBulan.slice(0, sliceBulan),
            datasets: [
                { label: 'Pendapatan',  data: dataPendapatan.slice(0, sliceBulan),  backgroundColor: '#42ba96' },
                { label: 'Pengeluaran', data: dataPengeluaran.slice(0, sliceBulan), backgroundColor: '#df4759' },
                { label: 'Laba',        data: dataLaba.slice(0, sliceBulan),        backgroundColor: '#4e73df' }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    ticks: {
                        callback: val => 'Rp ' + val.toLocaleString('id-ID')
                    }
                }
            }
        }
    });

    // 3. Pie Chart - Pendapatan per Layanan (dinamis dari DB)
    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: labelPie,
            datasets: [{
                data: dataPieLayanan,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: ctx => ' Rp ' + ctx.parsed.toLocaleString('id-ID')
                    }
                }
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>