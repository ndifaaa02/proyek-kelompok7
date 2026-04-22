<?php include 'includes/header.php'; ?>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card border-0 shadow-sm p-5 rounded-4" style="width: 100%; max-width: 450px;">
        <div class="text-center mb-4">
            <i class="bi bi-star-fill text-warning" style="font-size: 3rem;"></i>
            <h2 class="fw-bold mt-2">Bintang Laundry</h2>
            <p class="text-secondary">Login Admin Dashboard</p>
        </div>
        <form action="admin_dashboard.php">
            <div class="mb-3">
                <label class="fw-bold mb-2">Email</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-envelope text-muted"></i>
                    <input type="email" class="form-control border-0 bg-transparent py-3" placeholder="admin@bintanglaundry.com">
                </div>
            </div>
            <div class="mb-4">
                <label class="fw-bold mb-2">Password</label>
                <div class="input-group bg-light rounded-3 px-3 align-items-center">
                    <i class="bi bi-lock text-muted"></i>
                    <input type="password" class="form-control border-0 bg-transparent py-3" placeholder="••••••••">
                </div>
            </div>
            <button type="submit" class="btn btn-info w-100 py-3 fw-bold text-white rounded-3" style="background-color: #b9e3e9; border:none; color: #2d749a !important;">Login</button>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>