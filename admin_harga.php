<?php include 'includes/header.php'; ?>
<?php include 'includes.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 text-start">
        <div class="d-flex align-items-center">
            <i class="bi bi-gear-fill fs-2 text-primary me-3"></i>
            <h2 class="fw-bold mb-0">Kelola harga</h2>
        </div>
        <a href="admin_dashboard.php"class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm p-5 rounded-4 mb-4 text-start">
        <h4 class="fw-bold mb-4" style="color: #2d749a;">Pengaturan Harga Layanan</h4>
        <form action="admin_harga.php" method="post">
            <div class="mb-4">
                <label class="text-primary mb-2">Cuci kering</label>
                <div class="d-flex align-items-center">
                    <span class="me-3 fw-bold">Rp</span>
                    <input type="number" class="form-control border-0 bg-light p-3 rounded-3" value="5000" name="harga[1]">
                </div>
            </div>
            <div class="mb-4">
                <label class="text-primary mb-2">Cuci Setrika (per kg)</label>
                <div class="d-flex align-items-center">
                    <span class="me-3 fw-bold">Rp</span>
                    <input type="number" class="form-control border-0 bg-light p-3 rounded-3" value="7000" name="harga[2]">
                </div>
            </div>
            <div class="mb-4">
                <label class="text-primary mb-2">Setrika Saja (per kg)</label>
                <div class="d-flex align-items-center">
                    <span class="me-3 fw-bold">Rp</span>
                    <input type="number" class="form-control border-0 bg-light p-3 rounded-3" value="3000" name="harga[3]">
                </div>
            </div>
            <div class="mb-5">
                <label class="text-primary mb-2">Express (per kg)</label>
                <div class="d-flex align-items-center">
                    <span class="me-3 fw-bold">Rp</span>
                    <input type="number" class="form-control border-0 bg-light p-3 rounded-3" value="10000" name="harga[4]">
                </div>
            </div>
            <button class="btn btn-info w-100 py-3 fw-bold text-white shadow-sm" name="update" style="background-color: #b9e3e9; border:none; color: #2d749a !important;">
                <i class="bi bi-floppy me-2"></i> Simpan Perubahan
            </button>
        </form>

        <?php

        if (isset($_POST['update'])) {
            $harga_array = $_POST['harga'];

            foreach ($harga_array as $id => $nilai_baru) {
    
                $sql = "UPDATE layanan SET harga_perkg = '$nilai_baru' WHERE id_layanan = '$id'";
                mysqli_query($conn, $sql);
            }
        }
        ?>

    </div>

    <?php
    $query = mysqli_query($conn, "SELECT * FROM layanan");
    $layanan = [];
    while($row = mysqli_fetch_assoc($query)){
        $layanan[$row['id_layanan']] = $row['harga_perkg'];
    }
    ?>

    <div class="card border-0 shadow-sm p-5 rounded-4 text-start">
        <h4 class="fw-bold mb-4" style="color: #2d749a;">Preview Harga</h4>
        <div class="d-flex justify-content-between mb-3">
            <span class="text-primary">Cuci kering</span>
            <span class="fw-bold">Rp <?php echo number_format($layanan[1], 0, ',', '.'); ?></span>
        </div>
        <div class="d-flex justify-content-between mb-3">
            <span class="text-primary">Cuci Setrika</span>
            <span class="fw-bold">Rp <?php echo number_format($layanan[2], 0, ',', '.'); ?></span>
        </div>
        <div class="d-flex justify-content-between mb-3">
            <span class="text-primary">Setrika Saja</span>
            <span class="fw-bold">Rp <?php echo number_format($layanan[3], 0, ',', '.'); ?></span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-primary">Express</span>
            <span class="fw-bold">Rp <?php echo number_format($layanan[4], 0, ',', '.'); ?></span>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>