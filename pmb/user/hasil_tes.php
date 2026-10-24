<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


/* =========================
   AMBIL DATA HASIL TES
========================= */

$query = mysqli_prepare(
    $conn,
    "SELECT * FROM hasil_tes
     WHERE user_id = ?
     ORDER BY id DESC
     LIMIT 1"
);

mysqli_stmt_bind_param($query, "i", $user_id);
mysqli_stmt_execute($query);

$hasil = mysqli_stmt_get_result($query);
$data_hasil = mysqli_fetch_assoc($hasil);


/* =========================
   AMBIL DATA CALON MABA
========================= */

$query_maba = mysqli_prepare(
    $conn,
    "SELECT nama_lengkap, nomor_tes, jurusan
     FROM calon_maba
     WHERE user_id = ?"
);

mysqli_stmt_bind_param($query_maba, "i", $user_id);
mysqli_stmt_execute($query_maba);

$hasil_maba = mysqli_stmt_get_result($query_maba);
$maba = mysqli_fetch_assoc($hasil_maba);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Hasil Tes - PMB</title>

    <link rel="stylesheet" href="../css/hasil_tes.css">

</head>

<body>

<div class="page">

<!-- =========================
     SIDEBAR
========================== -->

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

        <a href="hasil_tes.php" class="active">
            📊 Hasil Tes
        </a>

        <a href="daftar_ulang.php">
            📝 Daftar Ulang
        </a>

        <a href="nim.php">
            🪪 NIM
        </a>
    </nav>

    <a href="../logout.php" class="logout">
        🚪 Logout
    </a>

</aside>
    <!-- =========================
         CONTENT
    ========================== -->

    <main class="content">

        <div class="header">

            <div>

                <p>
                    PMB / Hasil Tes
                </p>

                <h1>
                    Hasil Tes
                </h1>

            </div>

            <a href="dashboard.php" class="back">
                ← Kembali
            </a>

        </div>



        <?php if (!$data_hasil): ?>

            <!-- BELUM TES -->

            <div class="empty-card">

                <div class="empty-icon">
                    📊
                </div>

                <h2>
                    Hasil Tes Belum Tersedia
                </h2>

                <p>
                    Kamu belum mengerjakan tes penerimaan mahasiswa baru.
                </p>

                <a href="tes.php">
                    MULAI TES
                </a>

            </div>


        <?php else: ?>


            <!-- =========================
                 DATA PESERTA
            ========================== -->

            <div class="participant-card">

                <div>

                    <span>
                        Nama Peserta
                    </span>

                    <strong>
                        <?= htmlspecialchars(
                            $maba['nama_lengkap'] ?? $_SESSION['nama']
                        ); ?>
                    </strong>

                </div>


                <div>

                    <span>
                        Nomor Tes
                    </span>

                    <strong>
                        <?= htmlspecialchars(
                            $maba['nomor_tes'] ?? '-'
                        ); ?>
                    </strong>

                </div>


                <div>

                    <span>
                        Jurusan
                    </span>

                    <strong>
                        <?= htmlspecialchars(
                            $maba['jurusan'] ?? '-'
                        ); ?>
                    </strong>

                </div>

            </div>



            <!-- =========================
                 RESULT
            ========================== -->

            <div class="result-card">


                <?php if ($data_hasil['status'] == 'Lulus'): ?>

                    <div class="result-icon success">
                        ✓
                    </div>

                    <p class="result-label">
                        HASIL SELEKSI
                    </p>

                    <h2 class="lulus">
                        SELAMAT, KAMU LULUS!
                    </h2>

                    <p class="description">
                        Kamu dinyatakan lulus tes penerimaan mahasiswa baru.
                        Silakan lanjutkan ke proses daftar ulang.
                    </p>


                <?php else: ?>


                    <div class="result-icon failed">
                        ×
                    </div>

                    <p class="result-label">
                        HASIL SELEKSI
                    </p>

                    <h2 class="tidak-lulus">
                        TIDAK LULUS
                    </h2>

                    <p class="description">
                        Kamu belum berhasil memenuhi nilai kelulusan
                        tes penerimaan mahasiswa baru.
                    </p>


                <?php endif; ?>



                <!-- NILAI -->

                <div class="score">

                    <span>
                        Nilai Tes
                    </span>

                    <strong>
                        <?= htmlspecialchars($data_hasil['nilai']); ?>
                    </strong>

                    <small>
                        / 100
                    </small>

                </div>



                <!-- STATUS -->

                <div class="result-information">

                    <div>

                        <span>
                            Status
                        </span>

                        <?php if ($data_hasil['status'] == 'Lulus'): ?>

                            <strong class="status-success">
                                Lulus
                            </strong>

                        <?php else: ?>

                            <strong class="status-failed">
                                Tidak Lulus
                            </strong>

                        <?php endif; ?>

                    </div>


                    <div>

                        <span>
                            Nilai Minimum
                        </span>

                        <strong>
                            70
                        </strong>

                    </div>


                    <div>

                        <span>
                            Tanggal Tes
                        </span>

                        <strong>
                            <?= date(
                                "d-m-Y",
                                strtotime($data_hasil['tanggal_tes'])
                            ); ?>
                        </strong>

                    </div>

                </div>



                <!-- BUTTON -->

                <?php if ($data_hasil['status'] == 'Lulus'): ?>

                    <a
                        href="daftar_ulang.php"
                        class="next-button"
                    >
                        LANJUT DAFTAR ULANG →
                    </a>

                <?php else: ?>

                    <a
                        href="dashboard.php"
                        class="next-button"
                    >
                        KEMBALI KE DASHBOARD
                    </a>

                <?php endif; ?>


            </div>


        <?php endif; ?>

    </main>

</div>

</body>
</html>