-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 22, 2026 at 09:03 AM
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
-- Database: `db_hemata`
--

CREATE DATABASE IF NOT EXISTS db_hemata;

USE db_hemata;

DROP TABLE IF EXISTS `pemakaian`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------

--
-- Table structure for table `pemakaian`
--

CREATE TABLE `pemakaian` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `jenis` enum('listrik','air') NOT NULL,
  `daya_va` int NOT NULL DEFAULT '0',
  `kwh` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tarif_per_kwh` decimal(12,2) NOT NULL DEFAULT '0.00',
  `liter` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_rupiah` decimal(14,2) NOT NULL DEFAULT '0.00',
  `keterangan` varchar(255) NOT NULL DEFAULT '',
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pemakaian`
--

INSERT INTO `pemakaian` (`id`, `user_id`, `jenis`, `daya_va`, `kwh`, `tarif_per_kwh`, `liter`, `total_rupiah`, `keterangan`, `tanggal`) VALUES
(1, 1, 'listrik', 900, 45.00, 1352.00, 0.00, 60840.00, 'Pemakaian awal bulan', '2026-11-01'),
(2, 2, 'listrik', 2200, 60.00, 1444.70, 0.00, 86682.00, 'Pemakaian awal bulan', '2026-11-02'),
(3, 3, 'listrik', 1300, 38.00, 1444.70, 0.00, 54898.60, 'Pemakaian awal bulan', '2026-11-03'),
(4, 1, 'air', 0, 0.00, 0.00, 20000.00, 100000.00, 'Tagihan air', '2026-11-04'),
(5, 2, 'air', 0, 0.00, 0.00, 10000.00, 50000.00, 'Tagihan air', '2026-11-05'),
(6, 3, 'air', 0, 0.00, 0.00, 30000.00, 150000.00, 'Tagihan air', '2026-11-06'),
(7, 1, 'listrik', 900, 40.00, 1352.00, 0.00, 54080.00, 'Pemakaian Oktober', '2026-10-05'),
(8, 2, 'listrik', 2200, 40.00, 1444.70, 0.00, 57788.00, 'Pemakaian Oktober', '2026-10-06'),
(9, 3, 'listrik', 1300, 42.00, 1444.70, 0.00, 60677.40, 'Pemakaian Oktober', '2026-10-07'),
(10, 1, 'air', 0, 0.00, 0.00, 18000.00, 90000.00, 'Tagihan air Oktober', '2026-10-08'),
(11, 2, 'air', 0, 0.00, 0.00, 8000.00, 40000.00, 'Tagihan air Oktober', '2026-10-09'),
(12, 3, 'air', 0, 0.00, 0.00, 32000.00, 160000.00, 'Tagihan air Oktober', '2026-10-10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`) VALUES
(1, 'Kos Melati', 'kos_melati', '$2y$10$fLnhwclEn27lsoRbIO/tt.MaBgXxyXz2qoVRPpu5rpl3n7IUIHZtu'),
(2, 'Rumah Cempaka', 'rumah_cempaka', '$2y$10$fLnhwclEn27lsoRbIO/tt.MaBgXxyXz2qoVRPpu5rpl3n7IUIHZtu'),
(3, 'Kos Mawar', 'kos_mawar', '$2y$10$fLnhwclEn27lsoRbIO/tt.MaBgXxyXz2qoVRPpu5rpl3n7IUIHZtu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pemakaian`
--
ALTER TABLE `pemakaian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pemakaian`
--
ALTER TABLE `pemakaian`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pemakaian`
--
ALTER TABLE `pemakaian`
  ADD CONSTRAINT `fk_pemakaian_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
