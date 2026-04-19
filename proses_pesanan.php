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
        die("Error: Layanan belum dipilih! Pastikan name di HTML adalah id_layanan.");
    }

    // 2. Simpan ke tabel pelanggan
    $query_pelanggan = "INSERT INTO pelanggan (nama_pelanggan, no_hp, alamat) VALUES ('$nama', '$telepon', '$alamat')";
    mysqli_query($conn, $query_pelanggan);
    $id_pelanggan = mysqli_insert_id($conn);

    // 3. Simpan ke tabel pesanan 
    // Pastikan id_pegawai dan tanggal_selesai sudah di-set NULL di phpMyAdmin
    $query_pesanan = "INSERT INTO pesanan (tanggal_masuk, status_pesanan, id_pelanggan) 
                      VALUES ('$tgl_masuk', 'Proses', '$id_pelanggan')";
    
    if (mysqli_query($conn, $query_pesanan)) {
        $id_order = mysqli_insert_id($conn);

        // 4. Simpan ke tabel detail_pesanan
        // PERUBAHAN: 'jumlah' diganti 'kuantitas'
        // PERUBAHAN: Menambah 'harga_layanan' & 'subtotal' karena di database NOT NULL (wajib diisi)
        $query_detail = "INSERT INTO detail_pesanan (id_pesanan, id_layanan, kuantitas, harga_layanan, subtotal) 
                         VALUES ('$id_order', '$id_layanan', '$berat', 0, 0)";

        if (mysqli_query($conn, $query_detail)) {
            // Jika berhasil, langsung ke nota
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