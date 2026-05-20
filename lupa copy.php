<?php
include 'header.php';
include 'includes.php';

if (isset($_POST['check_email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($query) > 0) {
        // Jika email ada, arahkan ke halaman ganti password dengan membawa parameter email
        header("Location: reset.php?email=" . $email);
    } else {
        echo "<script>alert('Email tidak terdaftar!');</script>";
    }
}
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card-signup">
        <div class="text-center mb-4">
            <h2 class="fw-bold mt-2">Lupa Password</h2> <br><br>
        </div>
        <form action="" method="POST">
            <div class="mb-5 mt-4">
            <label class="fw-bold mb-2">Email</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-envelope text-muted"></i>
                    <input type="email" class="form-control border-0 bg-transparent py-3" name="email" placeholder="Masukkan Email Anda" required>
                </div>
            </div>
                    <button type="submit" class="btn btn-info w-100 py-3 fw-bold text-white rounded-3" style="background-color: #1a1a1a; border:none; color: white;" name="check_email">Lanjut</button>
        </form>

<?php include 'footer.php' ?>