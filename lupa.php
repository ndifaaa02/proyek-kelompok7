<?php
include 'includes/header.php';
/** @var mysqli $conn */
include 'includes.php';

if (isset($_POST['check_email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = mysqli_query($conn, "SELECT * FROM pegawai WHERE username='$email'");

    if (mysqli_num_rows($query) > 0) {
        // Jika email ada, arahkan ke halaman ganti password dengan membawa parameter email
        header("Location: reset.php?email=" . $email);
    } else {
        echo "<script>alert('Email tidak terdaftar!');</script>";
    }
}
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 text-start">
        <div class="d-flex align-items-center">
            <i class="bi bi-person-lock fs-2 text-primary me-3"></i>
            <h2 class="fw-bold mb-0">Ganti Sandi</h2>
        </div>
        <a href="admin_dashboard.php" class="btn bg-white text-dark shadow-sm rounded-pill px-4 py-2 fw-bold border-0">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
    </div>
</div>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card border-0 shadow-sm p-5 rounded-4" style="width: 100%; max-width: 450px;">
        <div class="text-center mb-4">
            <i class="bi bi-star-fill text-warning" style="font-size: 3rem;"></i>
            <h2 class="fw-bold mt-2">Bintang Laundry</h2>
            <p class="text-secondary">Lupa Password Admin</p>
        </div>

        <form action="" method="post">
            <div class="mb-3">
                <label class="fw-bold mb-2">Email</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-envelope text-muted"></i>
                    <input type="email" class="form-control border-0 bg-transparent py-3" placeholder="admin@bintanglaundry.com" name="email" required>
                </div>
            </div>
            <button type="submit" class="btn btn-info w-100 py-3 fw-bold text-white rounded-3" style="background-color: #b9e3e9; border:none; color: #2d749a !important;" name="check_email">Reset Password</button>
        </form>
    </div>
</div>

<?php include 'includes/footer2.php' ?>