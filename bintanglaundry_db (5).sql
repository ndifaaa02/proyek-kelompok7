-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 09, 2026 at 01:39 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bintanglaundry_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int NOT NULL,
  `id_pesanan` int NOT NULL,
  `id_layanan` int NOT NULL,
  `kuantitas` decimal(5,2) NOT NULL,
  `harga_layanan` int DEFAULT NULL,
  `subtotal` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_layanan`, `kuantitas`, `harga_layanan`, `subtotal`) VALUES
(1, 1, 1, 2.00, 5000, 10000),
(2, 3, 2, 4.00, 7000, 28000),
(3, 4, 4, 14.00, 0, 0),
(4, 5, 2, 2.00, 0, 0),
(5, 6, 1, 5.00, 0, 0),
(6, 7, 4, 2.00, 0, 0),
(7, 8, 4, 1.00, 10000, 10000),
(8, 9, 4, 2.00, 10000, 20000),
(9, 10, 2, 3.00, 7000, 21000),
(10, 11, 4, 1.00, 10000, 10000),
(12, 13, 4, 4.00, 10000, 40000),
(13, 14, 2, 3.00, 7000, 21000),
(18, 18, 3, 3.00, 3000, 9000),
(19, 19, 3, 5.00, 3000, 15000),
(20, 20, 3, 10.00, 3000, 30000),
(21, 21, 4, 2.00, 10000, 20000),
(22, 22, 4, 3.00, 10000, 30000),
(25, 24, 4, 0.00, 10000, 0),
(26, 25, 7, 0.00, 15000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `kategori_pengeluaran`
--

CREATE TABLE `kategori_pengeluaran` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori_pengeluaran`
--

INSERT INTO `kategori_pengeluaran` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Listrik'),
(2, 'Air'),
(3, 'Gaji Pegawai'),
(4, 'Sabun'),
(5, 'Pewangi');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_keuangan`
--

CREATE TABLE `laporan_keuangan` (
  `id_laporan` int NOT NULL,
  `periode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_pendapatan` int NOT NULL,
  `total_pengeluaran` int NOT NULL,
  `laba_rugi` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_keuangan`
--

INSERT INTO `laporan_keuangan` (`id_laporan`, `periode`, `total_pendapatan`, `total_pengeluaran`, `laba_rugi`) VALUES
(1, 'April 2026', 10000, 50000, -40000);

-- --------------------------------------------------------

--
-- Table structure for table `layanan`
--

CREATE TABLE `layanan` (
  `id_layanan` int NOT NULL,
  `nama_layanan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `harga_perkg` int NOT NULL,
  `harga_layanan` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `layanan`
--

INSERT INTO `layanan` (`id_layanan`, `nama_layanan`, `deskripsi`, `harga_perkg`, `harga_layanan`) VALUES
(1, 'Cuci Kering', 'Pakaian di cuci dan dikeringkan', 3000, 0),
(2, 'Cuci Setrika', 'Cuci kering dan setrika rapi', 7000, 0),
(3, 'Setrika Saja', 'Hanya layanan setrika', 3000, 0),
(4, 'Express', 'Selesai dalam 24 jam', 10000, 0),
(7, 'Super Express', 'setengah hari selesai', 15000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `nota`
--

CREATE TABLE `nota` (
  `id_nota` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `tanggal_cetak` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nota`
--

INSERT INTO `nota` (`id_nota`, `id_transaksi`, `tanggal_cetak`) VALUES
(1, 1, '2026-04-09');

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `id_pegawai` int NOT NULL,
  `nama_pegawai` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jabatan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_hp` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pegawai`
--

INSERT INTO `pegawai` (`id_pegawai`, `nama_pegawai`, `jabatan`, `username`, `password`, `no_hp`) VALUES
(1, 'Andi', 'Admin', '', '', '0811111111'),
(2, 'Budi', 'Kasir', '', '', '0822222222'),
(14, 'aryadi', 'ceo', 'arr@gmail.com', '$2y$10$Rz9xWEKBL.aEWua95tMAseRxxf3/PW5BLjkTTTWDjezrf2Wp5N/ry', '081'),
(15, 'a', 'a', 'a@a', '$2y$10$dgM/A95CR1KgWegCZl3mHeZh4xi8PyDfW4RqszroPj4FVM.7p6/4u', '0'),
(17, 'AnggiksPolindra', 'kasir', 'admin@laundry.com', '$2y$10$p./w7edfgbipF04mRGmkWOg7GrZRnhkI.dpWzUngMCJu/TwlWoX9K', '081123');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int NOT NULL,
  `nama_pelanggan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_hp` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `alamat`, `no_hp`) VALUES
(1, 'Nadifa', 'Bandung', '08123456789'),
(2, 'Anggi', 'Indramayu', '082345678901'),
(3, 'Aryadi', 'Indramayu', '083456789012'),
(4, 'aryadi', 'jalan-jalan', '08'),
(5, 'riyad', 'jlan baru', '0819087654'),
(6, 'arr', 'jalan-jalan-jalan', '097'),
(7, 'arr', 'jalan-jalan-jalan', '097'),
(8, 'b', 'nomor', '088'),
(9, 'seseorang', 'Rumah-rumahan', '08765432190'),
(10, 'siapa ya', 'kecamatan', '0987654321'),
(11, 'coba lagi', 'coba-coba', '088888888'),
(12, 'Aryadi', 'hvghgchg', '0819087654'),
(13, 'anggiks', 'Polindra', '0812345678'),
(14, 'Dexter', 'Jln. Netherlands lama No. 7, Kec. Amsterdam,  Kab. Belanda, Eropa 321654', '0231987456'),
(15, 'Dexter', 'Jln. Netherlands lama No. 7, Kec. Amsterdam,  Kab. Belanda, Eropa 321654', '0231987456'),
(16, 'Dexter', 'jln. amsterdam, kec. belanda, kab. eropa', '0231987456'),
(17, 'nama pelanggan', 'Jalan lohbener', '0800000000'),
(18, 'abdul', 'makassar', '0811111111'),
(19, 'ali', 'blok roma Jatibarang', '085167126440'),
(20, 'orang', 'lurah', '0811111111'),
(21, 'orang', 'lurah', '0811111111'),
(22, 'ali', 'jalan lohbener', '0811111111'),
(23, 'fikri', 'cirebon', '08222222222'),
(24, 'Razan Aryadi', 'Jl. K.H. Hasyim, No. 27, 001/001', '0881234567'),
(25, 'Razan Aryadi Rahman', 'Jl. K.H. Hasyim, No. 27, 001/001', '08812345678'),
(26, 'seseorang', 'Jl. K.H. Hasyim, No. 27, 001/001', '08811122223');

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id_pengeluaran` int NOT NULL,
  `tanggal_pengeluaran` date NOT NULL,
  `keterangan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jumlah` int NOT NULL,
  `id_kategori` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengeluaran`
--

INSERT INTO `pengeluaran` (`id_pengeluaran`, `tanggal_pengeluaran`, `keterangan`, `jumlah`, `id_kategori`) VALUES
(1, '2026-04-09', 'Beli deterjen', 50000, NULL),
(3, '2026-04-22', 'gaji aryadi', 1000000, 3),
(4, '2026-05-23', 'bayar listrik', 1000, 1),
(5, '2026-03-27', 'Beli deterjen', 50000, 4),
(6, '2026-03-25', 'bayar listrik', 100000, 1),
(9, '2026-06-05', 'beli deterjen', 20000, 4),
(10, '2026-06-05', 'bayar listrik', 300000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_pesanan` enum('belum_diambil','diproses','selesai','diambil') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'belum_diambil',
  `id_pelanggan` int NOT NULL,
  `id_pegawai` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `total_harga` int NOT NULL DEFAULT '0',
  `status_pembayaran` enum('belum_bayar','dp','lunas') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'belum_bayar',
  `catatan` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `tanggal_masuk`, `tanggal_selesai`, `status_pesanan`, `id_pelanggan`, `id_pegawai`, `updated_at`, `total_harga`, `status_pembayaran`, `catatan`) VALUES
(1, '2026-04-09', '2026-05-22', 'selesai', 1, 1, '2026-05-22 14:11:04', 0, 'lunas', NULL),
(3, '2026-04-12', '2026-05-22', 'selesai', 4, 1, '2026-05-22 14:11:26', 0, 'lunas', NULL),
(4, '2026-04-22', NULL, 'diproses', 6, NULL, '2026-05-21 02:10:08', 0, 'belum_bayar', NULL),
(5, '2026-04-22', NULL, 'diproses', 7, NULL, '2026-05-21 02:10:08', 0, 'belum_bayar', NULL),
(6, '2026-04-22', NULL, 'diproses', 8, NULL, '2026-05-21 02:10:08', 0, 'belum_bayar', NULL),
(7, '2026-04-23', NULL, 'diproses', 9, NULL, '2026-05-21 02:10:08', 0, 'belum_bayar', NULL),
(8, '2026-04-23', NULL, 'diproses', 10, NULL, '2026-05-23 01:03:49', 0, 'lunas', NULL),
(9, '2026-04-29', '2026-05-23', 'selesai', 11, NULL, '2026-05-22 19:53:34', 0, 'lunas', NULL),
(10, '2026-05-07', '2026-05-22', 'selesai', 12, NULL, '2026-05-22 14:28:48', 0, 'lunas', NULL),
(11, '2026-05-07', '2026-05-22', 'selesai', 13, NULL, '2026-05-22 14:10:26', 0, 'lunas', NULL),
(13, '2026-05-22', '2026-05-31', 'diambil', 16, NULL, '2026-05-31 07:54:52', 0, 'lunas', NULL),
(14, '2026-05-31', NULL, 'diproses', 17, NULL, '2026-06-05 03:29:04', 0, 'dp', 'semangat mas'),
(18, '2026-06-05', NULL, 'diproses', 21, NULL, '2026-06-05 09:40:42', 0, 'belum_bayar', '-'),
(19, '2026-06-05', NULL, 'selesai', 22, NULL, '2026-06-05 09:41:39', 0, 'dp', '-'),
(20, '2026-06-05', NULL, 'belum_diambil', 23, NULL, '2026-06-08 07:52:07', 0, 'dp', '-'),
(21, '2026-06-05', '2026-06-05', 'diambil', 24, NULL, '2026-06-05 10:25:45', 0, 'lunas', '-'),
(22, '2026-06-05', '2026-06-05', 'diambil', 25, NULL, '2026-06-05 10:38:34', 0, 'lunas', '-'),
(24, '2026-06-09', NULL, 'belum_diambil', 26, 1, '2026-06-09 01:36:31', 0, 'belum_bayar', NULL),
(25, '2026-06-09', NULL, 'belum_diambil', 26, 1, '2026-06-09 01:36:31', 0, 'belum_bayar', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_pesanan` int NOT NULL,
  `tanggal_bayar` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `metode_pembayaran` enum('cash','transfer','qris') COLLATE utf8mb4_general_ci DEFAULT 'cash',
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_bayar` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pesanan`, `tanggal_bayar`, `metode_pembayaran`, `bukti_pembayaran`, `total_bayar`) VALUES
(1, 1, '2026-05-21 17:00:00', 'cash', NULL, 10000),
(3, 3, '2026-05-21 17:00:00', 'cash', NULL, 28000),
(4, 11, '2026-05-21 17:00:00', 'cash', NULL, 10000),
(5, 9, '2026-05-22 17:00:00', 'cash', NULL, 20000),
(6, 8, '2026-05-22 17:00:00', 'cash', NULL, 10000),
(7, 10, '2026-05-30 17:00:00', 'cash', NULL, 21000),
(8, 13, '2026-05-30 17:00:00', 'cash', NULL, 40000),
(9, 14, '2026-06-02 17:00:00', 'cash', NULL, 10000),
(15, 19, '2026-06-04 17:00:00', 'cash', NULL, 10000),
(16, 21, '2026-06-04 17:00:00', 'cash', NULL, 20000),
(17, 22, '2026-06-04 17:00:00', 'cash', NULL, 30000),
(18, 20, '2026-06-07 17:00:00', 'cash', NULL, 1000),
(19, 20, '2026-06-07 17:00:00', 'transfer', NULL, 2000),
(20, 20, '2026-06-07 17:00:00', 'transfer', NULL, 2000),
(21, 20, '2026-06-07 17:00:00', 'transfer', NULL, 1000),
(22, 20, '2026-06-07 17:00:00', 'cash', NULL, 4000),
(23, 20, '2026-06-08 17:00:00', 'transfer', 'bukti_20_1780968389.jpg', 3000),
(24, 20, '2026-06-08 17:00:00', 'transfer', 'bukti_20_1780968474.jpg', 3000),
(25, 20, '2026-06-08 17:00:00', 'transfer', 'bukti_20_1780968798.jpg', 3000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_layanan` (`id_layanan`);

--
-- Indexes for table `kategori_pengeluaran`
--
ALTER TABLE `kategori_pengeluaran`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `laporan_keuangan`
--
ALTER TABLE `laporan_keuangan`
  ADD PRIMARY KEY (`id_laporan`);

--
-- Indexes for table `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id_layanan`);

--
-- Indexes for table `nota`
--
ALTER TABLE `nota`
  ADD PRIMARY KEY (`id_nota`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id_pegawai`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id_pengeluaran`),
  ADD KEY `fk_kategori_pengeluaran` (`id_kategori`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_pegawai` (`id_pegawai`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `kategori_pengeluaran`
--
ALTER TABLE `kategori_pengeluaran`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `laporan_keuangan`
--
ALTER TABLE `laporan_keuangan`
  MODIFY `id_laporan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id_layanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `nota`
--
ALTER TABLE `nota`
  MODIFY `id_nota` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id_pegawai` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id_pengeluaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`),
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`);

--
-- Constraints for table `nota`
--
ALTER TABLE `nota`
  ADD CONSTRAINT `nota_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`);

--
-- Constraints for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD CONSTRAINT `fk_kategori_pengeluaran` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_pengeluaran` (`id_kategori`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`),
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`);

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`),
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
