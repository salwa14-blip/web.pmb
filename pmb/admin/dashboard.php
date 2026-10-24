<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$admin_id = (int) $_SESSION['admin_id'];


/* =========================================
   AMBIL DATA ADMIN
========================================= */

$qAdmin = mysqli_prepare(
    $conn,
    "SELECT id, nama, username, email, foto
     FROM admin
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $qAdmin,
    "i",
    $admin_id
);

mysqli_stmt_execute($qAdmin);

$hasilAdmin =
    mysqli_stmt_get_result($qAdmin);

$admin =
    mysqli_fetch_assoc($hasilAdmin);


/* Kalau data admin tidak ditemukan */
if (!$admin) {
    session_destroy();

    header("Location: login.php");
    exit;
}


/* Update session agar nama/foto terbaru langsung dipakai */
$_SESSION['admin_nama'] =
    $admin['nama'];

$_SESSION['admin_foto'] =
    $admin['foto'] ?? '';


/* =========================================
   HITUNG CALON MAHASISWA
========================================= */

$qMaba = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM calon_maba"
);

$totalMaba =
    (int) mysqli_fetch_assoc($qMaba)['total'];


/* =========================================
   HITUNG YANG SUDAH TES
========================================= */

$qTes = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM hasil_tes"
);

$totalTes =
    (int) mysqli_fetch_assoc($qTes)['total'];


/* =========================================
   HITUNG LULUS
========================================= */

$qLulus = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM hasil_tes
     WHERE status = 'Lulus'"
);

$totalLulus =
    (int) mysqli_fetch_assoc($qLulus)['total'];


/* =========================================
   HITUNG TIDAK LULUS
========================================= */

$qTidakLulus = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM hasil_tes
     WHERE status = 'Tidak Lulus'"
);

$totalTidakLulus =
    (int) mysqli_fetch_assoc(
        $qTidakLulus
    )['total'];


/* =========================================
   HITUNG DAFTAR ULANG
========================================= */

$qDaftar = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM daftar_ulang
     WHERE status = 'Sudah Daftar Ulang'"
);

$totalDaftar =
    (int) mysqli_fetch_assoc(
        $qDaftar
    )['total'];


/* =========================================
   DATA PENDAFTAR TERBARU
========================================= */

$qTerbaru = mysqli_query(
    $conn,
    "SELECT
        calon_maba.nama_lengkap,
        calon_maba.nomor_tes,
        calon_maba.jurusan,
        calon_maba.foto,
        calon_maba.status_pendaftaran

     FROM calon_maba

     ORDER BY calon_maba.id DESC

     LIMIT 5"
);

if (!$qTerbaru) {
    die(
        "Query Error: " .
        mysqli_error($conn)
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard Admin - PMB
    </title>

    <link
        rel="stylesheet"
        href="../css/admin_dashboard.css"
    >

</head>

<body>

<div class="page">


    <!-- =====================================
         SIDEBAR
    ====================================== -->

    <aside class="sidebar">

        <div class="logo">
            🎓 <strong>PMB</strong>
        </div>

        <p class="admin-label">
            ADMIN PANEL
        </p>


        <nav>

            <a
                href="dashboard.php"
                class="active"
            >
                🏠 Dashboard
            </a>

            <a href="calon_maba.php">
                👥 Calon Mahasiswa
            </a>

            <a href="soal.php">
                📝 Kelola Soal
            </a>

            <a href="hasil_tes.php">
                📊 Hasil Tes
            </a>

            <a href="daftar_ulang.php">
                📋 Daftar Ulang
            </a>

        </nav>


        <a
            href="logout.php"
            class="logout"
        >
            🚪 Logout
        </a>

    </aside>



    <!-- =====================================
         CONTENT
    ====================================== -->

    <main class="content">


        <!-- =====================================
             HEADER
        ====================================== -->

        <div class="header">


            <div>

                <p>
                    PMB / Admin
                </p>

                <h1>
                    Dashboard Admin
                </h1>

            </div>



            <!-- =================================
                 PROFILE ADMIN
            ================================== -->

            <a
                href="profil.php"
                class="admin-profile"
            >


                <div class="admin-avatar">


                    <?php if (
                        !empty($admin['foto'])
                    ): ?>


                        <img
                            src="../uploads/admin/<?= htmlspecialchars(
                                $admin['foto']
                            ); ?>"
                            alt="Foto Admin"
                        >


                    <?php else: ?>


                        <span>

                            <?= strtoupper(
                                substr(
                                    $admin['nama'],
                                    0,
                                    1
                                )
                            ); ?>

                        </span>


                    <?php endif; ?>


                </div>


                <div class="admin-info">


                    <strong>

                        <?= htmlspecialchars(
                            $admin['nama']
                        ); ?>

                    </strong>


                    <span>
                        Administrator
                    </span>


                </div>


            </a>


        </div>



        <!-- =====================================
             WELCOME
        ====================================== -->

        <div class="welcome">


            <div>

                <p>
                    ADMINISTRATOR
                </p>


                <h2>

                    Selamat Datang,
                    <?= htmlspecialchars(
                        $admin['nama']
                    ); ?>!

                </h2>


                <span>

                    Pantau dan kelola proses
                    penerimaan mahasiswa baru
                    dari sini.

                </span>

            </div>


            <div class="welcome-icon">
                🎓
            </div>


        </div>



        <!-- =====================================
             STATISTIK
        ====================================== -->

        <div class="stats">


            <!-- CALON MAHASISWA -->

            <div class="stat-card">


                <div class="stat-icon">
                    👥
                </div>


                <div>

                    <span>
                        Calon Mahasiswa
                    </span>

                    <h2>
                        <?= $totalMaba; ?>
                    </h2>

                </div>


            </div>



            <!-- SUDAH TES -->

            <div class="stat-card">


                <div class="stat-icon">
                    📝
                </div>


                <div>

                    <span>
                        Sudah Tes
                    </span>

                    <h2>
                        <?= $totalTes; ?>
                    </h2>

                </div>


            </div>



            <!-- LULUS -->

            <div class="stat-card">


                <div class="stat-icon success-icon">
                    ✓
                </div>


                <div>

                    <span>
                        Lulus
                    </span>

                    <h2>
                        <?= $totalLulus; ?>
                    </h2>

                </div>


            </div>



            <!-- TIDAK LULUS -->

            <div class="stat-card">


                <div class="stat-icon danger-icon">
                    ✕
                </div>


                <div>

                    <span>
                        Tidak Lulus
                    </span>

                    <h2>
                        <?= $totalTidakLulus; ?>
                    </h2>

                </div>


            </div>



            <!-- DAFTAR ULANG -->

            <div class="stat-card">


                <div class="stat-icon">
                    📋
                </div>


                <div>

                    <span>
                        Daftar Ulang
                    </span>

                    <h2>
                        <?= $totalDaftar; ?>
                    </h2>

                </div>


            </div>


        </div>



        <!-- =====================================
             PENDAFTAR TERBARU
        ====================================== -->

        <div class="table-card">


            <div class="table-header">


                <div>

                    <h2>
                        Pendaftar Terbaru
                    </h2>

                    <p>
                        Calon mahasiswa yang baru
                        melakukan registrasi.
                    </p>

                </div>


                <a href="calon_maba.php">
                    Lihat Semua →
                </a>


            </div>



            <div class="table-responsive">


                <table>


                    <thead>


                        <tr>

                            <th>
                                Mahasiswa
                            </th>

                            <th>
                                Nomor Tes
                            </th>

                            <th>
                                Jurusan
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>


                    </thead>



                    <tbody>


                    <?php if (
                        mysqli_num_rows($qTerbaru) > 0
                    ): ?>


                        <?php while (
                            $maba =
                            mysqli_fetch_assoc($qTerbaru)
                        ): ?>


                            <tr>


                                <!-- MAHASISWA -->

                                <td>


                                    <div class="student">


                                        <div class="student-photo">


                                            <?php if (
                                                !empty($maba['foto'])
                                            ): ?>


                                                <img
                                                    src="../uploads/foto/<?= htmlspecialchars(
                                                        $maba['foto']
                                                    ); ?>"
                                                    alt="Foto <?= htmlspecialchars(
                                                        $maba['nama_lengkap']
                                                    ); ?>"
                                                >


                                            <?php else: ?>


                                                <span>
                                                    👤
                                                </span>


                                            <?php endif; ?>


                                        </div>



                                        <strong>

                                            <?= htmlspecialchars(
                                                $maba['nama_lengkap']
                                            ); ?>

                                        </strong>


                                    </div>


                                </td>



                                <!-- NOMOR TES -->

                                <td>

                                    <?= !empty(
                                        $maba['nomor_tes']
                                    )
                                        ? htmlspecialchars(
                                            $maba['nomor_tes']
                                        )
                                        : '-'; ?>

                                </td>



                                <!-- JURUSAN -->

                                <td>

                                    <?= !empty(
                                        $maba['jurusan']
                                    )
                                        ? htmlspecialchars(
                                            $maba['jurusan']
                                        )
                                        : '-'; ?>

                                </td>



                                <!-- STATUS -->

                                <td>


                                    <?php if (
                                        $maba['status_pendaftaran']
                                        == 'Sudah Lengkap'
                                    ): ?>


                                        <span class="badge-success">
                                            Lengkap
                                        </span>


                                    <?php else: ?>


                                        <span class="badge-pending">
                                            Belum Lengkap
                                        </span>


                                    <?php endif; ?>


                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="4"
                                class="empty"
                            >

                                Belum ada calon mahasiswa.

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>


                </table>


            </div>


        </div>


    </main>


</div>


</body>

</html>