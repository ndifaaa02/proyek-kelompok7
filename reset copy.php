<?php
include 'header.php';
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
        
        $update = mysqli_query($conn, "UPDATE pegawai SET passwords='$hashed_password' WHERE email='$email'");

        if ($update) {
            echo "<script>alert('Password berhasil diperbarui!'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
    }
}
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card-signup">
        <div class="text-center mb-4">
            <h2 class="fw-bold mt-2">Buat Password Baru untuk: <?php echo htmlspecialchars($email); ?></h2> <br><br>
        </div>
        <form action="" method="POST">
            <div class="mb-3 mt-4">
            <label class="fw-bold mb-2">Masukkan Password Baru</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-envelope text-muted"></i>
                    <input type="password" class="form-control border-0 bg-transparent py-3" name="new_password" placeholder="Password Baru" required>
                </div>
            </div>
             <div class="mb-5">
            <label class="fw-bold mb-2">Konfirmasi Password Baru</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-envelope text-muted"></i>
                    <input type="password" class="form-control border-0 bg-transparent py-3" name="confirm_password" placeholder="Ulangi Password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-info w-100 py-3 fw-bold text-white rounded-3" style="background-color: #1a1a1a; border:none; color: white;" name="reset">Simpan Password</button>
        </form>
<?php include 'footer.php' ?>