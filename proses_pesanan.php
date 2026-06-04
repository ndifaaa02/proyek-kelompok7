<?php
/** @var mysqli $conn */
include 'includes.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil data mentah dari form input
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $telepon = $_POST['telepon'];
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat'] ?? '-');
    $id_layanan = $_POST['id_layanan'] ?? null; 
    $berat = $_POST['berat'] ?? 0;
    $tgl_masuk = date("Y-m-d");

    // --- VALIDASI BACKEND: PROTEKSI NOMOR TELEPON ---
    $telepon_bersih = trim($telepon); // Menghilangkan spasi tak sengaja

    // Cek A: Apakah jumlah karakter kurang dari 10 digit?
    if (strlen($telepon_bersih) < 10) {
        die("Error: Gagal memproses data. Nomor telepon minimal harus 10 digit angka!");
    }
    
    // Cek B: Apakah input mengandung karakter selain angka?
    if (!is_numeric($telepon_bersih)) {
        die("Error: Gagal memproses data. Nomor telepon hanya boleh berisi angka!");
    }
    // ------------------------------------------------

    if (!$id_layanan) {
        die("Error: Layanan belum dipilih!");
    }

    // --- LANGKAH PENTING: AMBIL HARGA MASTER SAAT INI ---
    $query_harga = mysqli_query($conn, "SELECT harga_perkg FROM layanan WHERE id_layanan = '$id_layanan'");
    $data_layanan = mysqli_fetch_assoc($query_harga);
    
    if (!$data_layanan) {
        die("Error: Layanan tidak ditemukan di database.");
    }

    $harga_saat_ini = $data_layanan['harga_perkg'];
    $subtotal = $berat * $harga_saat_ini;
    // ----------------------------------------------------

    // 2. Simpan data ke dalam tabel pelanggan
    $query_pelanggan = "INSERT INTO pelanggan (nama_pelanggan, no_hp, alamat) VALUES ('$nama', '$telepon_bersih', '$alamat')";
    mysqli_query($conn, $query_pelanggan);
    $id_pelanggan = mysqli_insert_id($conn);

    // 3. Simpan data ke dalam tabel induk pesanan 
    $query_pesanan = "INSERT INTO pesanan (tanggal_masuk, status_pesanan, id_pelanggan) 
                      VALUES ('$tgl_masuk', 'belum_diambil', '$id_pelanggan')";
    
    if (mysqli_query($conn, $query_pesanan)) {
        $id_order = mysqli_insert_id($conn);

        // 4. Simpan data ke dalam tabel detail_pesanan (Snapshot Harga)
        $query_detail = "INSERT INTO detail_pesanan (id_pesanan, id_layanan, kuantitas, harga_layanan, subtotal) 
                         VALUES ('$id_order', '$id_layanan', '$berat', '$harga_saat_ini', '$subtotal')";

        if (mysqli_query($conn, $query_detail)) {
            // Jika semua sukses, langsung alihkan ke halaman nota
            header("Location: terimakasih.php?id=" . $id_order);
            exit();
        } else {
            die("Error detail_pesanan: " . mysqli_error($conn));
        }
    } else {
        die("Error pesanan: " . mysqli_error($conn));
    }
}
?>