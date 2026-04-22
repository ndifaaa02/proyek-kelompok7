<?php include 'includes/header.php'; ?>
<?php include 'includes.php'; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card-signup">
        <div class="text-center mb-4">
            <i class="bi bi-star-fill text-warning" style="font-size: 3rem;"></i>
            <h2 class="fw-bold mt-2">Bintang Laundry</h2>
            <p class="text-secondary">Sign Up Admin Dashboard</p>
        </div>
        <form action="admin_signup.php" method="post">
            <div class="mb-3">
                <label class="fw-bold mb-2">Email</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-envelope text-muted"></i>
                    <input type="email" class="form-control border-0 bg-transparent py-3" placeholder="admin@bintanglaundry.com" name="email" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="fw-bold mb-2">Username</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-person text-muted"></i>
                    <input type="text" class="form-control border-0 bg-transparent py-3" placeholder="Nama Pegawai" name="nama" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="fw-bold mb-2">Password</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-lock text-muted"></i>
                    <input type="password" class="form-control border-0 bg-transparent py-3" placeholder="••••••••" name="sandi" required>
                </div>
            </div>
             <div class="mb-3">
                <label class="fw-bold mb-2">Jabatan</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-diagram-2 text-muted"></i>
                    <input type="text" class="form-control border-0 bg-transparent py-3" placeholder="Jabatan Pegawai" name="jabatan" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="fw-bold mb-2">No. HP</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-telephone text-muted"></i>
                    <input type="tel" class="form-control border-0 bg-transparent py-3" placeholder="081xxxxxx" name="no" required>
                </div>
            </div>
            <button type="submit" class="btn btn-info w-100 py-3 fw-bold text-white rounded-3" style="background-color: #b9e3e9; border:none; color: #2d749a !important;">Submit</button>
        </form>

            <div style="text-align: center;">
                <?php
                    if (isset($_POST["email"])) {
                        $email = $_POST["email"];
                        $nama = $_POST["nama"];
                        $sandi = password_hash($_POST["sandi"], PASSWORD_DEFAULT) ;
                        $jabatan = $_POST["jabatan"];
                        $no = $_POST["no"];

                        $query = "INSERT INTO pegawai (nama_pegawai, jabatan, username, password, no_hp)
                                    VALUES ('$nama', '$jabatan', '$email','$sandi', '$no')";
                        
                        mysqli_query($conn, $query);

                        echo "Akun telah berhasil ditambahkan!";
                    } else {
                        echo "Error: " . mysqli_error($conn);
                    }

                ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>