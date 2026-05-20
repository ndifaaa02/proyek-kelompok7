<?php
include 'includes/header.php';
include 'includes.php';

// Pastikan ada parameter email di URL
if (!isset($_GET['email'])) {
    header("Location: lupa.php");
    exit;
}

$email = $_GET['email'];

if (isset($_POST['reset'])) {
    $new_pass     = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass) {
        // Hash password baru
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
        
        $update = mysqli_query($conn, "UPDATE pegawai SET password='$hashed_password' WHERE username='$email'");

        if ($update) {
            echo "<script>alert('Password berhasil diperbarui!'); window.location.href='admin_login.php';</script>";
        }
    } else {
        echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
    }
}
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card border-0 shadow-sm p-5 rounded-4" style="width: 100%; max-width: 450px;">
        <div class="text-center mb-4">
            <i class="bi bi-star-fill text-warning" style="font-size: 3rem;"></i>
            <h2 class="fw-bold mt-2">Bintang Laundry</h2>
            <p class="text-secondary">Reset Password Admin</p>
        </div>
        <form action="" method="post">
            <div class="mb-3">
                <label class="fw-bold mb-2">Password Baru</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-envelope text-muted"></i>
                    <input type="email" class="form-control border-0 bg-transparent py-3" placeholder="••••••••" name="new_password" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="fw-bold mb-2">Konfirmasi Password</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-envelope text-muted"></i>
                    <input type="email" class="form-control border-0 bg-transparent py-3" placeholder="••••••••" name="confirm_password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-info w-100 py-3 fw-bold text-white rounded-3" style="background-color: #b9e3e9; border:none; color: #2d749a !important;" name="reset">Reset Password</button>
        </form>
    </div>
</div>
<?php include 'includes/footer.php' ?>