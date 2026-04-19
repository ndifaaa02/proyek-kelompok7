<?php include 'includes/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 text-start">
        <div class="d-flex align-items-center">
            <i class="bi bi-graph-up fs-2 text-primary me-3"></i>
            <h2 class="fw-bold mb-0">Laporan Keuangan</h2>
        </div>
        <a href="admin_dashboard.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <h5 class="text-primary fw-bold mb-4 text-start">Analisis Laba/Rugi</h5>

    <div class="row g-3 mb-5 text-start">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <p class="small text-muted mb-2">Total Pendapatan</p>
                <h3 class="fw-bold mb-1">Rp 410.000</h3>
                <p class="x-small text-muted mb-0">Dari semua transaksi</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <p class="small text-muted mb-2">Total Pengeluaran</p>
                <h3 class="fw-bold mb-1">Rp 164.040</h3>
                <p class="x-small text-muted mb-0">Biaya Operasional</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <p class="small text-muted mb-2">Laba Bersih</p>
                <h3 class="fw-bold mb-1">Rp 246.060</h3>
                <p class="x-small text-success mb-0">Margin: 60.0%</p>
            </div>
        </div>
    </div>

    <div class="row g-4 text-start">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <h6 class="fw-bold mb-4">Grafik Pendapatan Bulanan</h6>
                <canvas id="lineChart" style="max-height: 250px;"></canvas>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100 text-start">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0">Grafik Laba/Rugi Bulanan</h6>
                    <button class="btn btn-sm btn-light border small rounded-pill"><i class="bi bi-arrow-clockwise"></i> Update</button>
                </div>
                <canvas id="barChart" style="max-height: 250px;"></canvas>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <h6 class="fw-bold mb-4">Pendapatan per Layanan</h6>
                <div class="d-flex justify-content-center align-items-center">
                    <canvas id="pieChart" style="max-width: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 rounded-4 h-100">
                <h6 class="fw-bold mb-4">Detail Layanan</h6>
                <div class="list-group list-group-flush small">
                    <div class="list-group-item d-flex justify-content-between border-0 px-0">
                        <div><i class="bi bi-square-fill text-primary me-2"></i> Cuci Setrika <span class="text-muted ms-1">5 pesanan</span></div>
                        <span class="fw-bold">Rp 233.800</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between border-0 px-0">
                        <div><i class="bi bi-square-fill text-info me-2"></i> Cuci Kering <span class="text-muted ms-1">3 pesanan</span></div>
                        <span class="fw-bold">Rp 59.000</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between border-0 px-0">
                        <div><i class="bi bi-square-fill text-secondary me-2"></i> Express <span class="text-muted ms-1">3 pesanan</span></div>
                        <span class="fw-bold">Rp 81.000</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between border-0 px-0">
                        <div><i class="bi bi-square-fill text-light border me-2"></i> Setrika Saja <span class="text-muted ms-1">3 pesanan</span></div>
                        <span class="fw-bold">Rp 36.300</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Grafik Garis (Pendapatan Bulanan)
    const ctxLine = document.getElementById('lineChart');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['Jan 2026', 'Feb 2026'],
            datasets: [{
                label: 'Pendapatan',
                data: [150000, 260000],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: { plugins: { legend: { display: false } } }
    });

    // 2. Grafik Batang (Laba/Rugi Bulanan)
    const ctxBar = document.getElementById('barChart');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Jan 2026', 'Feb 2026'],
            datasets: [
                { label: 'Pendapatan', data: [150000, 260000], backgroundColor: '#42ba96' },
                { label: 'Pengeluaran', data: [80000, 84040], backgroundColor: '#df4759' },
                { label: 'Laba', data: [70000, 175960], backgroundColor: '#20c997' }
            ]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    // 3. Grafik Lingkaran (Pendapatan per Layanan)
    const ctxPie = document.getElementById('pieChart');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['Cuci Setrika', 'Cuci Kering', 'Express', 'Setrika Saja'],
            datasets: [{
                data: [57, 14, 20, 9],
                backgroundColor: ['#4e73df', '#36b9cc', '#858796', '#f8f9fc'],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: { plugins: { legend: { display: false } } }
    });
</script>

<?php include 'includes/footer.php'; ?>