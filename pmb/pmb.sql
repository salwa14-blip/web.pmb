-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 24 Okt 2026 pada 09.22
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pmb`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `nama`, `username`, `email`, `password`, `foto`) VALUES
(1, 'salwaa nur saadah', 'wawa', 'admin@pmb.com', '$2y$10$XM8DddNBixh1ISX2yb37J.BB3.KLai3ujyGwMI6X2TGfm7K4VvG1e', 'admin_1_1787655783.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `calon_maba`
--

CREATE TABLE `calon_maba` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nomor_tes` varchar(30) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `asal_sekolah` varchar(150) DEFAULT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status_pendaftaran` enum('Belum Lengkap','Sudah Lengkap') DEFAULT 'Belum Lengkap'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `calon_maba`
--

INSERT INTO `calon_maba` (`id`, `user_id`, `nomor_tes`, `nama_lengkap`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `asal_sekolah`, `jurusan`, `foto`, `status_pendaftaran`) VALUES
(1, 1, 'PMB20260001', 'salwaa', 'jakarta', '2008-12-05', 'P', 'kp.jembatan', 'SMKN 65 JAKARTA', 'Teknik Informatika', 'siswa_1_1787648303.jpg', 'Sudah Lengkap'),
(3, 4, 'PMB20260004', 'saadah', 'banten', '2007-12-31', 'P', 'jl.leuwipapan', 'SMKN 70 JAKARTA', 'Sistem Informasi', 'siswa_4_1787716393.jpg', 'Sudah Lengkap'),
(6, 5, 'PMB20260005', 'nur', 'padang', '0005-02-02', 'P', 'jalann jalan', 'SMKN 65 JAKARTA', 'Manajemen', 'siswa_5_1791946440.jpeg', 'Sudah Lengkap'),
(8, 8, 'PMB20260008', 'awa', 'jakarta', '2026-10-30', 'L', 'sdfg', '123123', 'Teknik Informatika', 'siswa_8_1792823817.png', 'Sudah Lengkap'),
(9, 9, 'PMB20260009', 'salwaa nur', 'jakarta', '2026-10-29', 'P', 'lkjh', 'SMA 107 JAKARTA', 'Teknik Informatika', 'siswa_9_1792825200.jpg', 'Sudah Lengkap');

-- --------------------------------------------------------

--
-- Struktur dari tabel `daftar_ulang`
--

CREATE TABLE `daftar_ulang` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `nama_ortu` varchar(100) DEFAULT NULL,
  `pekerjaan_ortu` varchar(100) DEFAULT NULL,
  `penghasilan_ortu` varchar(100) DEFAULT NULL,
  `status` enum('Belum Daftar Ulang','Sudah Daftar Ulang') DEFAULT 'Belum Daftar Ulang',
  `tanggal_daftar_ulang` timestamp NULL DEFAULT NULL,
  `status_verifikasi` enum('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `daftar_ulang`
--

INSERT INTO `daftar_ulang` (`id`, `user_id`, `alamat`, `no_hp`, `nama_ortu`, `pekerjaan_ortu`, `penghasilan_ortu`, `status`, `tanggal_daftar_ulang`, `status_verifikasi`) VALUES
(3, 4, 'salwa nur saadah', '089523695064', 'sudirman', 'wiraswasta', '> Rp5.000.000', 'Sudah Daftar Ulang', '2026-08-26 04:46:37', 'Disetujui'),
(4, 1, 'jalan marzuki', '089523695064', 'sudirman', 'wiraswasta', '> Rp5.000.000', 'Sudah Daftar Ulang', '2026-10-14 02:21:28', 'Disetujui'),
(5, 5, 'nur', '0895236950648', 'surnah', 'pejabat', '> Rp5.000.000', 'Sudah Daftar Ulang', '2026-10-14 03:00:11', 'Disetujui');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_tes`
--

CREATE TABLE `hasil_tes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nilai` int(11) NOT NULL,
  `status` enum('Lulus','Tidak Lulus') NOT NULL,
  `tanggal_tes` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hasil_tes`
--

INSERT INTO `hasil_tes` (`id`, `user_id`, `nilai`, `status`, `tanggal_tes`) VALUES
(1, 1, 100, 'Lulus', '2026-08-25 08:22:39'),
(3, 4, 90, 'Lulus', '2026-08-26 03:55:18'),
(4, 5, 90, 'Lulus', '2026-10-14 02:59:17'),
(6, 8, 0, 'Tidak Lulus', '2026-10-24 06:55:04'),
(7, 9, 100, 'Lulus', '2026-10-24 07:11:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nim` varchar(30) NOT NULL,
  `tanggal_diterima` date DEFAULT NULL,
  `status_nim` enum('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `user_id`, `nim`, `tanggal_diterima`, `status_nim`) VALUES
(1, 1, '20260001', '2026-08-25', 'Menunggu'),
(2, 4, '20260004', '2026-08-26', 'Menunggu'),
(3, 5, '20260005', '2026-10-14', 'Menunggu');

-- --------------------------------------------------------

--
-- Struktur dari tabel `soal`
--

CREATE TABLE `soal` (
  `id` int(11) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `pertanyaan` text NOT NULL,
  `pilihan_a` text NOT NULL,
  `pilihan_b` text NOT NULL,
  `pilihan_c` text NOT NULL,
  `pilihan_d` text NOT NULL,
  `jawaban_benar` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `soal`
--

INSERT INTO `soal` (`id`, `jurusan`, `pertanyaan`, `pilihan_a`, `pilihan_b`, `pilihan_c`, `pilihan_d`, `jawaban_benar`) VALUES
(1, '', 'Ibukota Indonesia adalah?', 'Jakarta', 'Bandung', 'Surabaya', 'Semarang', 'A'),
(2, '', 'Hasil dari 10 + 5 adalah?', '10', '12', '15', '20', 'C'),
(3, '', 'HTML digunakan untuk?', 'Mengatur database', 'Membuat struktur halaman web', 'Mengedit video', 'Membuat jaringan internet', 'B'),
(4, '', 'CSS digunakan untuk?', 'Mengatur tampilan halaman web', 'Menyimpan database', 'Membuat server', 'Menghapus file', 'A'),
(5, '', 'PHP termasuk bahasa?', 'Desain grafis', 'Pemrograman', 'Pengolah kata', 'Spreadsheet', 'B'),
(6, 'Teknik Informatika', 'HTML digunakan untuk?', 'Membuat struktur halaman web', 'Mengatur database', 'Mengedit video', 'Membuat jaringan', 'A'),
(7, 'Teknik Informatika', 'CSS digunakan untuk?', 'Mengatur tampilan halaman web', 'Menyimpan data', 'Membuat sistem operasi', 'Mengolah suara', 'A'),
(8, 'Teknik Informatika', 'Bahasa yang digunakan untuk membuat halaman web lebih interaktif adalah?', 'JavaScript', 'Microsoft Word', 'Excel', 'Photoshop', 'A'),
(9, 'Teknik Informatika', 'Database digunakan untuk?', 'Menyimpan dan mengelola data', 'Mengedit gambar', 'Membuat video', 'Mencetak dokumen', 'A'),
(10, 'Teknik Informatika', 'Perintah PHP untuk menampilkan teks adalah?', 'echo', 'scan', 'input', 'read', 'A'),
(11, 'Teknik Informatika', 'Manakah yang termasuk DBMS?', 'MySQL', 'HTML', 'CSS', 'Canva', 'A'),
(12, 'Teknik Informatika', 'Ekstensi file PHP adalah?', '.php', '.html', '.css', '.jpg', 'A'),
(13, 'Teknik Informatika', 'Perintah SQL untuk mengambil data adalah?', 'SELECT', 'DELETE', 'UPDATE', 'DROP', 'A'),
(14, 'Teknik Informatika', 'Fungsi utama algoritma adalah?', 'Menyusun langkah penyelesaian masalah', 'Mengubah warna gambar', 'Mencetak dokumen', 'Membuat tabel manual', 'A'),
(15, 'Teknik Informatika', 'Perangkat yang berfungsi sebagai pusat pemrosesan komputer adalah?', 'CPU', 'Mouse', 'Keyboard', 'Speaker', 'A'),
(16, 'Sistem Informasi', 'Data yang telah diolah sehingga memiliki arti disebut?', 'Informasi', 'Program', 'Kode', 'File kosong', 'A'),
(17, 'Sistem Informasi', 'Sistem informasi digunakan untuk?', 'Mengelola informasi dalam organisasi', 'Mengedit video', 'Membuat animasi', 'Membuat poster', 'A'),
(18, 'Sistem Informasi', 'Salah satu komponen sistem informasi adalah?', 'Manusia', 'Cat minyak', 'Mesin jahit', 'Kamera analog', 'A'),
(19, 'Sistem Informasi', 'Database dalam sistem informasi berfungsi untuk?', 'Menyimpan data', 'Memutar musik', 'Menggambar poster', 'Mengedit suara', 'A'),
(20, 'Sistem Informasi', 'Sistem informasi dapat membantu perusahaan dalam?', 'Pengambilan keputusan', 'Menggambar manual', 'Membuat kerajinan', 'Mencetak foto', 'A'),
(21, 'Sistem Informasi', 'Perangkat lunak yang digunakan pengguna untuk menjalankan suatu tugas disebut?', 'Aplikasi', 'Keyboard', 'Monitor', 'Printer', 'A'),
(22, 'Sistem Informasi', 'Orang yang menggunakan sistem informasi disebut?', 'User', 'Server', 'Database', 'Router', 'A'),
(23, 'Sistem Informasi', 'Proses memasukkan data ke dalam sistem disebut?', 'Input', 'Output', 'Delete', 'Print', 'A'),
(24, 'Sistem Informasi', 'Hasil dari proses pengolahan data disebut?', 'Output', 'Input', 'Server', 'Folder', 'A'),
(25, 'Sistem Informasi', 'Informasi yang baik harus bersifat?', 'Akurat dan relevan', 'Acak', 'Tidak jelas', 'Selalu panjang', 'A'),
(26, 'Manajemen', 'Salah satu fungsi utama manajemen adalah?', 'Perencanaan', 'Pemrograman', 'Menggambar', 'Mencetak foto', 'A'),
(27, 'Manajemen', 'Kegiatan mengatur sumber daya agar tujuan tercapai disebut?', 'Manajemen', 'Akuntansi', 'Desain', 'Produksi', 'A'),
(28, 'Manajemen', 'Orang yang mengatur kegiatan dalam organisasi disebut?', 'Manajer', 'Programmer', 'Operator', 'Desainer', 'A'),
(29, 'Manajemen', 'Perencanaan dalam manajemen bertujuan untuk?', 'Menentukan tujuan dan langkah kerja', 'Menghapus seluruh data', 'Mengganti logo perusahaan', 'Membuat animasi', 'A'),
(30, 'Manajemen', 'Pengawasan dalam manajemen bertujuan untuk?', 'Memastikan kegiatan sesuai rencana', 'Mengubah sistem operasi', 'Membuat poster', 'Mencetak foto', 'A'),
(31, 'Manajemen', 'Pembagian tugas kepada anggota organisasi termasuk fungsi?', 'Pengorganisasian', 'Pemasaran', 'Akuntansi', 'Produksi', 'A'),
(32, 'Manajemen', 'Sumber daya manusia dalam organisasi biasanya disebut?', 'SDM', 'CPU', 'RAM', 'HTML', 'A'),
(33, 'Manajemen', 'Tujuan utama manajemen adalah?', 'Mencapai tujuan secara efektif dan efisien', 'Mengurangi semua pekerja', 'Menghilangkan perencanaan', 'Menghindari evaluasi', 'A'),
(34, 'Manajemen', 'Kegiatan memberi arahan kepada anggota organisasi disebut?', 'Pengarahan', 'Penghapusan', 'Pemrograman', 'Pencetakan', 'A'),
(35, 'Manajemen', 'Evaluasi dilakukan untuk?', 'Menilai hasil kegiatan', 'Menambah kesalahan', 'Menghapus tujuan', 'Mengurangi informasi', 'A'),
(36, 'Akuntansi', 'Persamaan dasar akuntansi adalah?', 'Aset = Liabilitas + Ekuitas', 'Aset = Beban + Pendapatan', 'Modal = Utang - Kas', 'Kas = Beban + Modal', 'A'),
(37, 'Akuntansi', 'Catatan transaksi keuangan pertama kali dicatat dalam?', 'Jurnal', 'Poster', 'Browser', 'Server', 'A'),
(38, 'Akuntansi', 'Uang tunai yang dimiliki perusahaan disebut?', 'Kas', 'Utang', 'Beban', 'Piutang', 'A'),
(39, 'Akuntansi', 'Kewajiban perusahaan kepada pihak lain disebut?', 'Utang', 'Aset', 'Pendapatan', 'Kas', 'A'),
(40, 'Akuntansi', 'Laporan yang menunjukkan posisi aset, utang, dan modal disebut?', 'Neraca', 'Jurnal', 'Nota', 'Kwitansi', 'A'),
(41, 'Akuntansi', 'Pendapatan perusahaan berasal dari?', 'Kegiatan usaha', 'Pembayaran utang', 'Pembelian barang pribadi', 'Penghapusan data', 'A'),
(42, 'Akuntansi', 'Beban adalah?', 'Pengorbanan ekonomi untuk menjalankan usaha', 'Jumlah aset perusahaan', 'Modal pemilik', 'Uang yang dipinjamkan', 'A'),
(43, 'Akuntansi', 'Piutang adalah?', 'Hak perusahaan untuk menerima pembayaran', 'Kewajiban perusahaan', 'Barang rusak', 'Modal perusahaan', 'A'),
(44, 'Akuntansi', 'Dokumen bukti pembayaran biasanya disebut?', 'Kwitansi', 'Database', 'Browser', 'Server', 'A'),
(45, 'Akuntansi', 'Laporan laba rugi digunakan untuk mengetahui?', 'Laba atau rugi perusahaan', 'Jumlah komputer', 'Jumlah pegawai saja', 'Alamat perusahaan', 'A');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `no_hp` varchar(20) DEFAULT NULL,
  `role` enum('user','admin','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `created_at`, `no_hp`, `role`) VALUES
(1, 'salwaa', 'salwa@gmail.com', '$2y$10$LQTf8MWVAX8QP.z/uwAtRej.rUyHnKw8dzmusrS25ZIoafYmKMO7y', '2026-08-25 07:33:02', '089523695064', 'user'),
(4, 'saadah', 'saadah@gmail.com', '$2y$10$z1MQxtMynT72tSmTBoqMCeZHaGpeJqXBfRZMCxcsCFMszfvdIDjWC', '2026-08-26 03:38:14', '98877', 'user'),
(5, 'nur', 'nur@gmail.com', '$2y$10$4BfV4.GTCu.SYNmgHpxcs.fEnheVU9nyYSWtRc3TG.EwjFgdVxb0O', '2026-10-14 02:53:12', '0999887766', 'user'),
(7, 'kkk', 'kk@gmail.com', '$2y$10$3L7wA5/qhIEqHcaGZTU84.oygxwzuroJx4f2lGymXp3t0GZI09N.K', '2026-10-21 00:32:39', '99999', 'user'),
(8, 'awa', 'awa@gmail.com', '$2y$10$.5epCvfWeVof1iUOypUjKuyD..fS1Y.JbnY.lAUNniprzBpcpjF2u', '2026-10-24 06:30:47', '099888', 'user'),
(9, 'salwaa nur', 'howaisuuu@gmail.com', '$2y$10$yT/M2bQg4BUKiQik/uTzEOJAoyrp11/rnKrOO6C0ouzuVQrTpmzau', '2026-10-24 06:58:59', '0000', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `calon_maba`
--
ALTER TABLE `calon_maba`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_tes` (`nomor_tes`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `daftar_ulang`
--
ALTER TABLE `daftar_ulang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `hasil_tes`
--
ALTER TABLE `hasil_tes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim` (`nim`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `soal`
--
ALTER TABLE `soal`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `calon_maba`
--
ALTER TABLE `calon_maba`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `daftar_ulang`
--
ALTER TABLE `daftar_ulang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `hasil_tes`
--
ALTER TABLE `hasil_tes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `soal`
--
ALTER TABLE `soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `calon_maba`
--
ALTER TABLE `calon_maba`
  ADD CONSTRAINT `calon_maba_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `daftar_ulang`
--
ALTER TABLE `daftar_ulang`
  ADD CONSTRAINT `daftar_ulang_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `hasil_tes`
--
ALTER TABLE `hasil_tes`
  ADD CONSTRAINT `hasil_tes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mahasiswa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
