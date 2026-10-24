<?php
session_start();
require_once "../config/koneksi.php";

/* =========================================
   CEK LOGIN
========================================= */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


/* =========================================
   AMBIL DATA MAHASISWA + CALON MABA
========================================= */

$query = mysqli_prepare(
    $conn,
    "SELECT
        mahasiswa.nim,
        mahasiswa.tanggal_diterima,

        calon_maba.nama_lengkap,
        calon_maba.nomor_tes,
        calon_maba.jurusan,
        calon_maba.asal_sekolah,
        calon_maba.foto

     FROM mahasiswa

     INNER JOIN calon_maba
        ON mahasiswa.user_id = calon_maba.user_id

     WHERE mahasiswa.user_id = ?

     LIMIT 1"
);

mysqli_stmt_bind_param(
    $query,
    "i",
    $user_id
);

mysqli_stmt_execute($query);

$hasil = mysqli_stmt_get_result($query);

$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($query);


/* =========================================
   CEK DAFTAR ULANG
========================================= */

$query_daftar = mysqli_prepare(
    $conn,
    "SELECT
        status,
        tanggal_daftar_ulang

     FROM daftar_ulang

     WHERE user_id = ?

     ORDER BY id DESC

     LIMIT 1"
);

mysqli_stmt_bind_param(
    $query_daftar,
    "i",
    $user_id
);

mysqli_stmt_execute($query_daftar);

$hasil_daftar = mysqli_stmt_get_result($query_daftar);

$daftar = mysqli_fetch_assoc($hasil_daftar);

mysqli_stmt_close($query_daftar);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>NIM Mahasiswa - PMB</title>

    <link
        rel="stylesheet"
        href="../css/nim.css"
    >

</head>

<body>


<div class="page">


   
<!-- =========================================
     SIDEBAR
========================================== -->

<aside class="sidebar">

    <div class="logo">
        <div class="logo-icon">
            <img src="../assets/univp.jpeg"
                 alt="Logo Universitas Airlangga">
        </div>

        <div class="logo-text">
            <strong>UNIVERSITAS</strong>
            <strong>AIRLANGGA</strong>
            <span>Penerimaan<br>Mahasiswa Baru</span>
        </div>
    </div>

    <nav>
        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="pendaftaran.php">
            📋 Registrasi
        </a>

        <a href="tes.php">
            🎓 Tes
        </a>

        <a href="hasil_tes.php">
            📊 Hasil Tes
        </a>

        <a href="daftar_ulang.php">
            📝 Daftar Ulang
        </a>

        <a href="nim.php" class="active">
            🪪 NIM
        </a>
    </nav>

    <a href="../logout.php" class="logout">
        🚪 Logout
    </a>

</aside>



    <!-- =========================================
         CONTENT
    ========================================== -->

    <main class="content">


        <!-- =====================================
             HEADER
        ====================================== -->

        <div class="header">


            <div>

                <p>
                    PMB / Nomor Induk Mahasiswa
                </p>

                <h1>
                    Nomor Induk Mahasiswa
                </h1>

            </div>


            <a
                href="dashboard.php"
                class="back"
            >
                ← Kembali
            </a>


        </div>



        <!-- =====================================
             NIM BELUM TERSEDIA
        ====================================== -->

        <?php if (!$data): ?>


            <div class="empty-card">


                <div class="empty-icon">
                    🪪
                </div>


                <h2>
                    NIM Belum Tersedia
                </h2>



                <?php if (!$daftar): ?>


                    <p>
                        Kamu harus menyelesaikan proses
                        daftar ulang terlebih dahulu
                        untuk mendapatkan Nomor Induk
                        Mahasiswa.
                    </p>


                    <a href="daftar_ulang.php">
                        DAFTAR ULANG
                    </a>


                <?php else: ?>


                    <p>
                        Proses daftar ulang sudah dilakukan,
                        tetapi NIM belum tersedia.
                    </p>


                    <a href="daftar_ulang.php">
                        CEK DAFTAR ULANG
                    </a>


                <?php endif; ?>


            </div>



        <?php else: ?>


            <!-- =====================================
                 PESAN BERHASIL
            ====================================== -->

            <div class="success-banner">


                <div class="success-icon">
                    ✓
                </div>


                <div>

                    <h2>
                        Selamat, Kamu Resmi Menjadi Mahasiswa!
                    </h2>

                    <p>
                        Proses penerimaan mahasiswa baru
                        telah berhasil diselesaikan.
                    </p>

                </div>


            </div>



            <!-- =====================================
                 KARTU MAHASISWA
            ====================================== -->

            <div class="student-card">


                <!-- HEADER KARTU -->

                <div class="card-header">


                    <div class="campus-logo">
                        🎓
                    </div>


                    <div>

                        <h3>
                            PMB
                        </h3>

                        <p>
                            Penerimaan Mahasiswa Baru
                        </p>

                    </div>


                </div>



                <!-- ISI KARTU -->

                <div class="card-content">


                    <!-- =========================
                         FOTO SISWA
                    ========================== -->

                    <div class="student-photo">


                        <?php if (!empty($data['foto'])): ?>


                            <img
                                src="../uploads/foto/<?= htmlspecialchars(
                                    $data['foto']
                                ); ?>"
                                alt="Foto <?= htmlspecialchars(
                                    $data['nama_lengkap']
                                ); ?>"
                            >


                        <?php else: ?>


                            <div class="no-photo">

                                <span>
                                    Foto belum tersedia
                                </span>

                            </div>


                        <?php endif; ?>


                    </div>



                    <!-- =========================
                         DATA MAHASISWA
                    ========================== -->

                    <div class="student-data">


                        <span class="nim-label">
                            NOMOR INDUK MAHASISWA
                        </span>


                        <h1>
                            <?= htmlspecialchars(
                                $data['nim']
                            ); ?>
                        </h1>



                        <div class="identity">


                            <!-- NAMA -->

                            <div>

                                <small>
                                    Nama Mahasiswa
                                </small>

                                <strong>
                                    <?= htmlspecialchars(
                                        $data['nama_lengkap']
                                    ); ?>
                                </strong>

                            </div>



                            <!-- PROGRAM STUDI -->

                            <div>

                                <small>
                                    Program Studi
                                </small>

                                <strong>
                                    <?= htmlspecialchars(
                                        $data['jurusan']
                                    ); ?>
                                </strong>

                            </div>



                            <!-- NOMOR TES -->

                            <div>

                                <small>
                                    Nomor Tes
                                </small>

                                <strong>
                                    <?= htmlspecialchars(
                                        $data['nomor_tes']
                                    ); ?>
                                </strong>

                            </div>



                            <!-- TANGGAL DITERIMA -->

                            <div>

                                <small>
                                    Tanggal Diterima
                                </small>

                                <strong>

                                    <?php

                                    if (
                                        !empty(
                                            $data['tanggal_diterima']
                                        )
                                    ) {

                                        echo date(
                                            "d-m-Y",
                                            strtotime(
                                                $data['tanggal_diterima']
                                            )
                                        );

                                    } else {

                                        echo "-";
                                    }

                                    ?>

                                </strong>

                            </div>


                        </div>


                    </div>


                </div>



                <!-- =================================
                     FOOTER KARTU
                ================================== -->

                <div class="card-footer">


                    <span>
                        STATUS MAHASISWA
                    </span>


                    <strong>
                        AKTIF
                    </strong>


                </div>


            </div>



            <!-- =====================================
                 INFORMASI MAHASISWA
            ====================================== -->

            <div class="detail-card">


                <h2>
                    Informasi Mahasiswa
                </h2>


                <div class="detail-grid">


                    <!-- NAMA -->

                    <div>

                        <span>
                            Nama Lengkap
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $data['nama_lengkap']
                            ); ?>
                        </strong>

                    </div>



                    <!-- NIM -->

                    <div>

                        <span>
                            NIM
                        </span>

                        <strong class="blue">
                            <?= htmlspecialchars(
                                $data['nim']
                            ); ?>
                        </strong>

                    </div>



                    <!-- ASAL SEKOLAH -->

                    <div>

                        <span>
                            Asal Sekolah
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $data['asal_sekolah']
                            ); ?>
                        </strong>

                    </div>



                    <!-- PROGRAM STUDI -->

                    <div>

                        <span>
                            Program Studi
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $data['jurusan']
                            ); ?>
                        </strong>

                    </div>



                    <!-- NOMOR TES -->

                    <div>

                        <span>
                            Nomor Tes
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $data['nomor_tes']
                            ); ?>
                        </strong>

                    </div>



                    <!-- STATUS -->

                    <div>

                        <span>
                            Status Mahasiswa
                        </span>

                        <strong class="status-aktif">
                            AKTIF
                        </strong>

                    </div>


                </div>


            </div>


        <?php endif; ?>


    </main>


</div>


</body>

</html>