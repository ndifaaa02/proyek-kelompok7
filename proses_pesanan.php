<?php
include 'includes.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil data dari form
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat'] ?? '-');
    $id_layanan = $_POST['id_layanan'] ?? null; 
    $berat = $_POST['berat'] ?? 0;
    $tgl_masuk = date("Y-m-d");

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

    // 2. Simpan ke tabel pelanggan
    $query_pelanggan = "INSERT INTO pelanggan (nama_pelanggan, no_hp, alamat) VALUES ('$nama', '$telepon', '$alamat')";
    mysqli_query($conn, $query_pelanggan);
    $id_pelanggan = mysqli_insert_id($conn);

    // 3. Simpan ke tabel pesanan 
    $query_pesanan = "INSERT INTO pesanan (tanggal_masuk, status_pesanan, id_pelanggan) 
                      VALUES ('$tgl_masuk', 'Proses', '$id_pelanggan')";
    
    if (mysqli_query($conn, $query_pesanan)) {
        $id_order = mysqli_insert_id($conn);

        // 4. Simpan ke tabel detail_pesanan (SNAPSHOT HARGA DI SINI)
        // Menyimpan $harga_saat_ini ke kolom harga_layanan
        $query_detail = "INSERT INTO detail_pesanan (id_pesanan, id_layanan, kuantitas, harga_layanan, subtotal) 
                         VALUES ('$id_order', '$id_layanan', '$berat', '$harga_saat_ini', '$subtotal')";

        if (mysqli_query($conn, $query_detail)) {
            header("Location: nota.php?id=" . $id_order);
            exit();
        } else {
            die("Error detail_pesanan: " . mysqli_error($conn));
        }
    } else {
        die("Error pesanan: " . mysqli_error($conn));
    }
}
?>