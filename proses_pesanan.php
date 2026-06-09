<?php
/** @var mysqli $conn */
include 'includes.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil data mentah informasi pelanggan dari form input
    $nama = isset($_POST['nama_pelanggan']) ? mysqli_real_escape_string($conn, trim($_POST['nama_pelanggan'])) : '';
    $telepon = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : '';
    $alamat = isset($_POST['alamat']) ? mysqli_real_escape_string($conn, trim($_POST['alamat'])) : '-';
    
    // Data pesanan berupa array karena user bisa memilih lebih dari 1 layanan
    $arr_id_layanan = isset($_POST['id_layanan']) ? $_POST['id_layanan'] : []; 
    $arr_berat      = isset($_POST['berat']) ? $_POST['berat'] : [];
    $arr_catatan    = isset($_POST['catatan']) ? $_POST['catatan'] : [];
    
    $tgl_masuk = date("Y-m-d H:i:s"); // Menggunakan format datetime lengkap

    // Validasi dasar field wajib pelanggan
    if (empty($nama)) {
        die("Error: Gagal memproses data. Nama lengkap wajib diisi!");
    }

    // --- VALIDASI BACKEND: PROTEKSI NOMOR TELEPON ---
    if (strlen($telepon) < 10) {
        die("Error: Gagal memproses data. Nomor telepon minimal harus 10 digit!");
    }
    if (strlen($telepon) > 13) {
        die("Error: Gagal memproses data. Nomor telepon maksimal adalah 13 digit!");
    }
    if (!preg_match("/^[0-9]+$/", $telepon)) {
        die("Error: Gagal memproses data. Format nomor telepon tidak valid! Hanya boleh berisi angka.");
    }

    // 2. Ambil atau Buat Data Pelanggan Baru di Database
    $nama_clean = mysqli_real_escape_string($conn, $nama);
    $telepon_clean = mysqli_real_escape_string($conn, $telepon);

    $query_cek_pelanggan = mysqli_query($conn, "SELECT id_pelanggan FROM pelanggan WHERE no_hp = '$telepon_clean' LIMIT 1");
    $data_pelanggan = mysqli_fetch_assoc($query_cek_pelanggan);

    if ($data_pelanggan) {
        $id_pelanggan = $data_pelanggan['id_pelanggan'];
        $query_update_alamat = "UPDATE pelanggan SET alamat = '$alamat' WHERE id_pelanggan = '$id_pelanggan'";
        mysqli_query($conn, $query_update_alamat);
    } else {
        $query_insert_pelanggan = "INSERT INTO pelanggan (nama_pelanggan, no_hp, alamat) VALUES ('$nama_clean', '$telepon_clean', '$alamat')";
        if (mysqli_query($conn, $query_insert_pelanggan)) {
            $id_pelanggan = mysqli_insert_id($conn);
        } else {
            die("Error: Gagal mendaftarkan data pelanggan baru.");
        }
    }

    // Ambil ID pegawai default agar tidak error constraint
    $query_pegawai = mysqli_query($conn, "SELECT id_pegawai FROM pegawai LIMIT 1");
    $data_pegawai = mysqli_fetch_assoc($query_pegawai);
    $id_pegawai_default = $data_pegawai['id_pegawai'] ?? 1;

    $total_layanan_diproses = 0;

    // 3. LOOPING UNTUK MEMBUAT SATU PESANAN PER LAYANAN
    foreach ($arr_id_layanan as $i => $id_layanan) {
        $id_layanan_clean = mysqli_real_escape_string($conn, $id_layanan);
        $berat_clean      = isset($arr_berat[$i]) ? (float)$arr_berat[$i] : 0.0;
        $catatan_clean    = isset($arr_catatan[$i]) ? mysqli_real_escape_string($conn, $arr_catatan[$i]) : '';

        // Lewati jika id_layanan kosong
        if (empty($id_layanan_clean)) {
            continue;
        }

        // Ambil harga snapshot master dari database saat ini
        $query_harga = mysqli_query($conn, "SELECT harga_perkg FROM layanan WHERE id_layanan = '$id_layanan_clean'");
        $data_layanan = mysqli_fetch_assoc($query_harga);
        
        if ($data_layanan) {
            $harga_saat_ini = (int)$data_layanan['harga_perkg'];
            $subtotal = ceil($berat_clean * $harga_saat_ini);

            // REVISI FIX: Menghapus kolom 'metode_pembayaran' karena kolom tersebut hanya ada di tabel transaksi
            $query_pesanan = "INSERT INTO pesanan (id_pelanggan, id_pegawai, tanggal_masuk, total_harga, status_pesanan, status_pembayaran) 
                              VALUES ('$id_pelanggan', '$id_pegawai_default', '$tgl_masuk', '$subtotal', 'belum_diambil', 'belum_bayar')";
            
            if (mysqli_query($conn, $query_pesanan)) {
                $id_order_baru = mysqli_insert_id($conn);

                // Masukkan data ke detail_pesanan (berat masuk ke kolom 'kuantitas')
                $query_detail = "INSERT INTO detail_pesanan (id_pesanan, id_layanan, kuantitas, harga_layanan, subtotal) 
                                 VALUES ('$id_order_baru', '$id_layanan_clean', '$berat_clean', '$harga_saat_ini', '$subtotal')";
                mysqli_query($conn, $query_detail);
                
                $total_layanan_diproses++;
            }
        }
    }

    if ($total_layanan_diproses > 0) {
        // Redirect ke halaman sukses jika berhasil
        echo "<script>
                alert('Berhasil! " . $total_layanan_diproses . " layanan pesanan Anda telah berhasil dibuat secara terpisah.');
                window.location.href = 'index.php';
              </script>";
        exit;
    } else {
        die("Error: Tidak ada layanan valid yang dipilih untuk diproses.");
    }
} else {
    header("Location: pesan.php");
    exit;
}