-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 29, 2025 at 03:06 AM
-- Server version: 12.0.2-MariaDB
-- PHP Version: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9'),
(2, 'petugas1', 'petugas123'),
(3, 'petugas2', 'petugas321');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id_buku` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `penulis` varchar(150) DEFAULT NULL,
  `penerbit` varchar(150) DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `sampul` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id_buku`, `judul`, `penulis`, `penerbit`, `tahun`, `kategori`, `jumlah`, `sampul`) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', '2005', 'Novel', 0, 'laskar-pelangi.jpg'),
(2, 'Bumi', 'Tere Liye', 'Gramedia', '2014', 'Novel', 8, 'bumi.jpg'),
(3, 'Dilan 1990', 'Pidi Baiq', 'Mizan', '2014', 'Novel', 12, 'dilan-1990.jpg'),
(4, 'Ayat-Ayat Cinta', 'Habiburrahman El Shirazy', 'Republika', '2004', 'Novel', 7, 'ayat-ayat-cinta.jpg'),
(5, 'Perahu Kertas', 'Dewi Lestari', 'Bentang Pustaka', '2009', 'Novel', 9, 'perahu-kertas.jpg'),
(6, 'Kisah Petualangan Si Kancil', 'N/A', 'Erlangga Kids', '2010', 'Cerita Anak', 15, 'kancil.jpg'),
(7, 'Dongeng Nusantara', 'N/A', 'BIP', '2011', 'Cerita Anak', 12, 'dongeng-nusantara.jpg'),
(8, 'Cerita Anak Hebat', 'N/A', 'Tiga Serangkai', '2013', 'Cerita Anak', 10, 'cerita-anak-hebat.jpg'),
(9, 'Putri Salju dan 7 Kurcaci', 'Brothers Grimm', 'Erlangga', '2008', 'Cerita Anak', 11, 'putri-salju.jpg'),
(10, 'Petualangan Si Tudung Merah', 'Charles Perrault', 'BIP', '2009', 'Cerita Anak', 9, 'tudung-merah.jpg'),
(11, 'Filosofi Teras', 'Henry Manampiring', 'Kompas', '2018', 'Motivasi', 7, 'filosofi-teras.jpg'),
(12, 'Rich Dad Poor Dad', 'Robert Kiyosaki', 'Warner Books', '1997', 'Motivasi', 10, 'rich-dad-poor-dad.jpg'),
(13, 'Berani Tidak Disukai', 'Fumitake Koga', 'Gramedia', '2013', 'Motivasi', 8, 'berani-tidak-disukai.jpg'),
(14, 'The Power of Habit', 'Charles Duhigg', 'Random House', '2012', 'Motivasi', 9, 'power-of-habit.jpg'),
(15, 'Atomic Habits', 'James Clear', 'Penguin Random House', '2018', 'Motivasi', 11, 'atomic-habits.jpg'),
(16, 'Kosmos', 'Carl Sagan', 'Random House', '1980', 'Sains', 6, 'kosmos.jpg'),
(17, 'A Brief History of Time', 'Stephen Hawking', 'Bantam Books', '1988', 'Sains', 8, 'a-brief-history-of-time.jpg'),
(18, 'The Selfish Gene', 'Richard Dawkins', 'Oxford University Press', '1976', 'Sains', 5, 'the-selfish-gene.jpg'),
(19, 'The Origin of Species', 'Charles Darwin', 'John Murray', '2003', 'Sains', 4, 'origin-of-species.jpg'),
(20, 'Astrophysics for People in a Hurry', 'Neil deGrasse Tyson', 'W.W. Norton', '2017', 'Sains', 9, 'astrophysics-hurry.jpg'),
(21, 'Sejarah Dunia yang Disembunyikan', 'Jonathan Black', 'Noura Books', '2014', 'Sejarah', 6, 'sejarah-dunia-disembunyikan.jpg'),
(22, 'Indonesia dalam Arus Sejarah', 'Kemendikbud', 'Depdikbud', '2012', 'Sejarah', 10, 'indonesia-arus-sejarah.jpg'),
(23, 'Napoleon: A Life', 'Andrew Roberts', 'Penguin', '2014', 'Sejarah', 5, 'napoleon-life.jpg'),
(24, 'The Diary of Anne Frank', 'Anne Frank', 'Contact Publishing', '1947', 'Sejarah', 7, 'anne-frank-diary.jpg'),
(25, 'Guns, Germs, and Steel', 'Jared Diamond', 'W.W. Norton', '1997', 'Sejarah', 6, 'guns-germs-steel.jpg'),
(26, 'Accusamus magnam natus nostrum delectus.', 'Yohanes Oktanio', 'Recusandae temporibus vitae harum saepe sed voluptas maxime hic cum.', '2024', 'Delectus fuga accusantium deserunt minus tempore asperiores quia officiis laborum.', 10, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_pemustaka` int(11) NOT NULL,
  `id_buku` int(11) DEFAULT NULL,
  `tanggal_pinjam` date DEFAULT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_pemustaka`, `id_buku`, `tanggal_pinjam`, `tanggal_kembali`, `status`) VALUES
(1, 1, 3, '2025-01-10', '2025-01-17', 'Kembali'),
(2, 2, 8, '2025-01-12', '2025-01-20', 'Kembali'),
(3, 3, 15, '2025-01-14', NULL, 'Dipinjam'),
(4, 4, 5, '2025-01-15', '2025-01-22', 'Kembali'),
(5, 1, 25, '2025-11-25', NULL, 'dikembalikan'),
(6, 5, 25, '2025-11-22', NULL, 'dikembalikan');

-- --------------------------------------------------------

--
-- Table structure for table `pemustaka`
--

CREATE TABLE `pemustaka` (
  `id_pemustaka` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pemustaka`
--

INSERT INTO `pemustaka` (`id_pemustaka`, `nama`, `email`, `password`, `alamat`, `no_hp`) VALUES
(1, 'Andi Saputra pratama', 'andi@gmail.com', 'a589ffa7732ffd2f26d23953e26af5c8f6c006690b7982d5f07f671915c0b561', 'Jl. Mawar No. 10', 812345671),
(2, 'Budi Pratama', 'budi@gmail.com', 'budi123', 'Jl. Melati No. 21', 813456782),
(3, 'Citra Lestari', 'citra@gmail.com', 'citra123', 'Jl. Kenanga No. 5', 814567893),
(4, 'Dewi Anggraeni', 'dewi@gmail.com', 'dewi123', 'Jl. Flamboyan No. 3', 815678904),
(5, 'zidan', 'coop4497@gmail.com', 'zidan123', 'kesami porong', 89716321);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `FK_PEMINJAM_REFERENCE_BUKU` (`id_buku`),
  ADD KEY `FK_PEMINJAM_REFERENCE_PEMUSTAKA` (`id_pemustaka`);

--
-- Indexes for table `pemustaka`
--
ALTER TABLE `pemustaka`
  ADD PRIMARY KEY (`id_pemustaka`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pemustaka`
--
ALTER TABLE `pemustaka`
  MODIFY `id_pemustaka` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `FK_PEMINJAM_REFERENCE_PEMUSTAKA` FOREIGN KEY (`id_pemustaka`) REFERENCES `pemustaka` (`id_pemustaka`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
