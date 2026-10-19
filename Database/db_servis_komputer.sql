-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260707.3e756d69dd
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 19, 2026 at 06:05 AM
-- Server version: 8.4.3
-- PHP Version: 8.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_servis_komputer`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_servis`
--

CREATE TABLE `detail_servis` (
  `id_detail` int NOT NULL,
  `id_servis` int NOT NULL,
  `id_sparepart` int NOT NULL,
  `jumlah` int NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_servis`
--

INSERT INTO `detail_servis` (`id_detail`, `id_servis`, `id_sparepart`, `jumlah`, `subtotal`) VALUES
(9, 8, 1, 1, 200000.00);

-- --------------------------------------------------------

--
-- Table structure for table `dokumentasi_servis`
--

CREATE TABLE `dokumentasi_servis` (
  `id_dokumentasi` int NOT NULL,
  `id_servis` int NOT NULL,
  `id_teknisi` int NOT NULL,
  `id_detail` int DEFAULT NULL,
  `jenis_foto` enum('kerusakan','penggantian','sparepart') NOT NULL,
  `foto` varchar(255) NOT NULL,
  `keterangan` text,
  `tanggal_upload` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tampil_pelanggan` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama`, `no_hp`, `alamat`, `aktif`) VALUES
(10, 'Albert', '082354393331', 'BANJARBARU', 1),
(11, 'abror', '0813412', 'Jl.kesehatan', 1);

-- --------------------------------------------------------

--
-- Table structure for table `perangkat`
--

CREATE TABLE `perangkat` (
  `id_perangkat` int NOT NULL,
  `id_pelanggan` int NOT NULL,
  `jenis_perangkat` varchar(50) NOT NULL,
  `merk` varchar(50) NOT NULL,
  `tipe` varchar(100) NOT NULL,
  `serial_number` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `perangkat`
--

INSERT INTO `perangkat` (`id_perangkat`, `id_pelanggan`, `jenis_perangkat`, `merk`, `tipe`, `serial_number`) VALUES
(9, 10, 'Laptop', 'ACER LITE', 'LITE', '');

-- --------------------------------------------------------

--
-- Table structure for table `servis`
--

CREATE TABLE `servis` (
  `id_servis` int NOT NULL,
  `kode_servis` varchar(20) NOT NULL,
  `id_perangkat` int NOT NULL,
  `id_teknisi` int NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `keluhan` text NOT NULL,
  `diagnosa` text,
  `status` varchar(30) NOT NULL,
  `biaya_jasa` decimal(12,2) NOT NULL,
  `dp` decimal(12,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `servis`
--

INSERT INTO `servis` (`id_servis`, `kode_servis`, `id_perangkat`, `id_teknisi`, `tanggal_masuk`, `keluhan`, `diagnosa`, `status`, `biaya_jasa`, `dp`) VALUES
(8, 'SV-9AA34F3073DC3C8E', 9, 2, '2026-10-01', 'ADALAH', '', 'Menunggu Pemeriksaan', 500000.00, 500000.00);

-- --------------------------------------------------------

--
-- Table structure for table `sparepart`
--

CREATE TABLE `sparepart` (
  `id_sparepart` int NOT NULL,
  `nama_sparepart` varchar(100) NOT NULL,
  `stok` int NOT NULL,
  `harga` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sparepart`
--

INSERT INTO `sparepart` (`id_sparepart`, `nama_sparepart`, `stok`, `harga`) VALUES
(1, 'SSD 128GB', 9, 200000.00),
(2, 'RAM DDR 4 8GB', 10, 400000.00),
(3, 'CMOS BATTERY', 5, 3000.00),
(5, 'SSD 256 GB', 20, 500000.00);

-- --------------------------------------------------------

--
-- Table structure for table `teknisi`
--

CREATE TABLE `teknisi` (
  `id_teknisi` int NOT NULL,
  `nama_teknisi` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `keahlian` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `teknisi`
--

INSERT INTO `teknisi` (`id_teknisi`, `nama_teknisi`, `no_hp`, `keahlian`) VALUES
(1, 'Budi', '089691304711', 'Ganti Thermal'),
(2, 'Radit', '08888888888', 'Guring');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `id_teknisi` int DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teknisi') NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `id_teknisi`, `username`, `password`, `role`, `nama`) VALUES
(1, NULL, 'admin', '$2y$12$dtsFKZpWKBd/jyCj1bYk0eUnV02ag5mCHUMM5Q3fhf0lSt5tyXJGq', 'admin', 'Administrator'),
(2, 1, 'Budi123', '$2y$12$JKdHLMTY9VDNzoAPDqIvROkP2QAl/O6RM.GesHcIqcet40hyhDw4y', 'teknisi', 'Budi'),
(3, 2, 'Radit', '$2y$12$1juAFCVSRtvODoi9kZGKqeO4RaQiTQWFcWLF5Hrsyn/qWOR5WMDsu', 'teknisi', 'Radit');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_servis`
--
ALTER TABLE `detail_servis`
  ADD PRIMARY KEY (`id_detail`),
  ADD UNIQUE KEY `uq_servis_sparepart` (`id_servis`,`id_sparepart`),
  ADD KEY `id_servis` (`id_servis`),
  ADD KEY `id_sparepart` (`id_sparepart`);

--
-- Indexes for table `dokumentasi_servis`
--
ALTER TABLE `dokumentasi_servis`
  ADD PRIMARY KEY (`id_dokumentasi`),
  ADD KEY `id_servis` (`id_servis`),
  ADD KEY `id_teknisi` (`id_teknisi`),
  ADD KEY `id_detail` (`id_detail`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `perangkat`
--
ALTER TABLE `perangkat`
  ADD PRIMARY KEY (`id_perangkat`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `servis`
--
ALTER TABLE `servis`
  ADD PRIMARY KEY (`id_servis`),
  ADD UNIQUE KEY `kode_servis` (`kode_servis`),
  ADD KEY `id_perangkat` (`id_perangkat`),
  ADD KEY `id_teknisi` (`id_teknisi`);

--
-- Indexes for table `sparepart`
--
ALTER TABLE `sparepart`
  ADD PRIMARY KEY (`id_sparepart`);

--
-- Indexes for table `teknisi`
--
ALTER TABLE `teknisi`
  ADD PRIMARY KEY (`id_teknisi`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_users_teknisi` (`id_teknisi`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_servis`
--
ALTER TABLE `detail_servis`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `dokumentasi_servis`
--
ALTER TABLE `dokumentasi_servis`
  MODIFY `id_dokumentasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `perangkat`
--
ALTER TABLE `perangkat`
  MODIFY `id_perangkat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `servis`
--
ALTER TABLE `servis`
  MODIFY `id_servis` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sparepart`
--
ALTER TABLE `sparepart`
  MODIFY `id_sparepart` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teknisi`
--
ALTER TABLE `teknisi`
  MODIFY `id_teknisi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_servis`
--
ALTER TABLE `detail_servis`
  ADD CONSTRAINT `fk_detail_servis_servis` FOREIGN KEY (`id_servis`) REFERENCES `servis` (`id_servis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_servis_sparepart` FOREIGN KEY (`id_sparepart`) REFERENCES `sparepart` (`id_sparepart`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `dokumentasi_servis`
--
ALTER TABLE `dokumentasi_servis`
  ADD CONSTRAINT `fk_dokumentasi_detail` FOREIGN KEY (`id_detail`) REFERENCES `detail_servis` (`id_detail`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dokumentasi_servis` FOREIGN KEY (`id_servis`) REFERENCES `servis` (`id_servis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dokumentasi_teknisi` FOREIGN KEY (`id_teknisi`) REFERENCES `teknisi` (`id_teknisi`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `perangkat`
--
ALTER TABLE `perangkat`
  ADD CONSTRAINT `fk_perangkat_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `servis`
--
ALTER TABLE `servis`
  ADD CONSTRAINT `fk_servis_perangkat` FOREIGN KEY (`id_perangkat`) REFERENCES `perangkat` (`id_perangkat`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_servis_teknisi` FOREIGN KEY (`id_teknisi`) REFERENCES `teknisi` (`id_teknisi`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_teknisi` FOREIGN KEY (`id_teknisi`) REFERENCES `teknisi` (`id_teknisi`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
