<?php
session_start();
require_once "../config/koneksi.php";

/* =========================================
   CEK LOGIN ADMIN
========================================= */

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
    "SELECT
        id,
        nama,
        username,
        email,
        foto
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

$hasilAdmin = mysqli_stmt_get_result($qAdmin);
$admin = mysqli_fetch_assoc($hasilAdmin);

mysqli_stmt_close($qAdmin);


/* JIKA ADMIN TIDAK DITEMUKAN */

if (!$admin) {

    session_destroy();

    header("Location: login.php");
    exit;
}


/* UPDATE SESSION */

$_SESSION['admin_nama'] = $admin['nama'];
$_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_foto'] = $admin['foto'] ?? '';



/* =========================================
   FILTER
========================================= */

$jurusan = trim($_GET['jurusan'] ?? '');
$status = trim($_GET['status'] ?? '');


/* =========================================
   QUERY DATA HASIL TES
========================================= */

$sql = "
    SELECT
        hasil_tes.id,
        hasil_tes.user_id,
        hasil_tes.nilai,
        hasil_tes.status,
        hasil_tes.tanggal_tes,

        calon_maba.nama_lengkap,
        calon_maba.nomor_tes,
        calon_maba.jurusan,
        calon_maba.foto,

        users.email

    FROM hasil_tes

    INNER JOIN users
        ON hasil_tes.user_id = users.id

    INNER JOIN calon_maba
        ON hasil_tes.user_id = calon_maba.user_id

    WHERE 1=1
";


/* =========================================
   FILTER JURUSAN
========================================= */

if ($jurusan != '') {

    $jurusanSafe = mysqli_real_escape_string(
        $conn,
        $jurusan
    );

    $sql .= "
        AND calon_maba.jurusan = '$jurusanSafe'
    ";
}


/* =========================================
   FILTER STATUS
========================================= */

if ($status != '') {

    $statusSafe = mysqli_real_escape_string(
        $conn,
        $status
    );

    $sql .= "
        AND hasil_tes.status = '$statusSafe'
    ";
}


/* =========================================
   ORDER
========================================= */

$sql .= "
    ORDER BY hasil_tes.id DESC
";


$dataTes = mysqli_query(
    $conn,
    $sql
);


if (!$dataTes) {

    die(
        "Query Error: " .
        mysqli_error($conn)
    );
}


/* =========================================
   STATISTIK
========================================= */

$totalPeserta = 0;
$totalLulus = 0;
$totalTidakLulus = 0;


/* TOTAL PESERTA */

$queryTotal = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM hasil_tes"
);

if ($queryTotal) {

    $hasilTotal =
        mysqli_fetch_assoc($queryTotal);

    $totalPeserta =
        (int) ($hasilTotal['total'] ?? 0);
}


/* TOTAL LULUS */

$queryLulus = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM hasil_tes
     WHERE status = 'Lulus'"
);

if ($queryLulus) {

    $hasilLulus =
        mysqli_fetch_assoc($queryLulus);

    $totalLulus =
        (int) ($hasilLulus['total'] ?? 0);
}


/* TOTAL TIDAK LULUS */

$queryTidakLulus = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM hasil_tes
     WHERE status = 'Tidak Lulus'"
);

if ($queryTidakLulus) {

    $hasilTidakLulus =
        mysqli_fetch_assoc($queryTidakLulus);

    $totalTidakLulus =
        (int) ($hasilTidakLulus['total'] ?? 0);
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
        Hasil Tes - Admin PMB
    </title>

    <link
        rel="stylesheet"
        href="../css/admin_hasil_tes.css"
    >

</head>

<body>

<div class="page">


    <!-- =========================================
         SIDEBAR
    ========================================== -->

    <aside class="sidebar">

        <div class="logo">
            🎓 <strong>PMB</strong>
        </div>

        <p class="admin-label">
            ADMIN PANEL
        </p>


        <nav>

            <a href="dashboard.php">
                🏠 Dashboard
            </a>

            <a href="calon_maba.php">
                👥 Calon Mahasiswa
            </a>

            <a href="soal.php">
                📝 Kelola Soal
            </a>

            <a
                href="hasil_tes.php"
                class="active"
            >
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



    <!-- =========================================
         CONTENT
    ========================================== -->

    <main class="content">


        <!-- =========================================
             HEADER
        ========================================== -->

        <div class="header">


            <div>

                <p>
                    PMB / Admin / Hasil Tes
                </p>

                <h1>
                    Hasil Tes Calon Mahasiswa
                </h1>

            </div>



            <!-- =====================================
                 PROFIL ADMIN
            ====================================== -->

            <a
                href="profil.php"
                class="admin-profile"
            >


                <div class="admin-avatar">


                    <?php if (!empty($admin['foto'])): ?>


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



        <!-- =========================================
             STATISTIK
        ========================================== -->

        <div class="stats">


            <div class="stat-card">

                <div class="stat-icon blue">
                    👥
                </div>

                <div>

                    <span>
                        Peserta Tes
                    </span>

                    <h2>
                        <?= $totalPeserta; ?>
                    </h2>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon green">
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



            <div class="stat-card">

                <div class="stat-icon red">
                    ×
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


        </div>



        <!-- =========================================
             FILTER
        ========================================== -->

        <div class="toolbar">


            <div>

                <h2>
                    Daftar Hasil Tes
                </h2>

                <p>
                    Lihat hasil tes berdasarkan
                    jurusan dan status kelulusan.
                </p>

            </div>



            <form
                method="GET"
                action="hasil_tes.php"
                class="filter"
            >


                <select name="jurusan">


                    <option value="">
                        Semua Jurusan
                    </option>


                    <option
                        value="Teknik Informatika"
                        <?= $jurusan == 'Teknik Informatika'
                            ? 'selected'
                            : ''; ?>
                    >
                        Teknik Informatika
                    </option>


                    <option
                        value="Sistem Informasi"
                        <?= $jurusan == 'Sistem Informasi'
                            ? 'selected'
                            : ''; ?>
                    >
                        Sistem Informasi
                    </option>


                    <option
                        value="Manajemen"
                        <?= $jurusan == 'Manajemen'
                            ? 'selected'
                            : ''; ?>
                    >
                        Manajemen
                    </option>


                    <option
                        value="Akuntansi"
                        <?= $jurusan == 'Akuntansi'
                            ? 'selected'
                            : ''; ?>
                    >
                        Akuntansi
                    </option>


                </select>



                <select name="status">


                    <option value="">
                        Semua Status
                    </option>


                    <option
                        value="Lulus"
                        <?= $status == 'Lulus'
                            ? 'selected'
                            : ''; ?>
                    >
                        Lulus
                    </option>


                    <option
                        value="Tidak Lulus"
                        <?= $status == 'Tidak Lulus'
                            ? 'selected'
                            : ''; ?>
                    >
                        Tidak Lulus
                    </option>


                </select>



                <button type="submit">
                    Filter
                </button>



                <?php if (
                    $jurusan != '' ||
                    $status != ''
                ): ?>

                    <a
                        href="hasil_tes.php"
                        class="reset"
                    >
                        Reset
                    </a>

                <?php endif; ?>


            </form>


        </div>



        <!-- =========================================
             TABEL HASIL TES
        ========================================== -->

        <div class="table-card">


            <div class="table-responsive">


                <table>


                    <thead>

                        <tr>

                            <th>No</th>

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
                                Nilai
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Tanggal Tes
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        $dataTes &&
                        mysqli_num_rows($dataTes) > 0
                    ): ?>


                        <?php

                        $no = 1;

                        while (
                            $tes =
                            mysqli_fetch_assoc($dataTes)
                        ):

                        ?>


                            <tr>


                                <td>
                                    <?= $no++; ?>
                                </td>



                                <!-- MAHASISWA -->

                                <td>


                                    <div class="student">


                                        <div class="photo">


                                            <?php if (
                                                !empty($tes['foto'])
                                            ): ?>


                                                <img
                                                    src="../uploads/foto/<?= htmlspecialchars(
                                                        $tes['foto']
                                                    ); ?>"
                                                    alt="Foto <?= htmlspecialchars(
                                                        $tes['nama_lengkap']
                                                    ); ?>"
                                                >


                                            <?php else: ?>


                                                <span>
                                                    👤
                                                </span>


                                            <?php endif; ?>


                                        </div>



                                        <div>


                                            <strong>

                                                <?= htmlspecialchars(
                                                    $tes['nama_lengkap']
                                                ); ?>

                                            </strong>


                                            <small>

                                                <?= htmlspecialchars(
                                                    $tes['email']
                                                ); ?>

                                            </small>


                                        </div>


                                    </div>


                                </td>



                                <!-- NOMOR TES -->

                                <td>

                                    <?= htmlspecialchars(
                                        $tes['nomor_tes']
                                    ); ?>

                                </td>



                                <!-- JURUSAN -->

                                <td>

                                    <span class="jurusan">

                                        <?= htmlspecialchars(
                                            $tes['jurusan']
                                        ); ?>

                                    </span>

                                </td>



                                <!-- NILAI -->

                                <td>

                                    <strong class="nilai">

                                        <?= number_format(
                                            (float)
                                            $tes['nilai'],
                                            0
                                        ); ?>

                                    </strong>

                                </td>



                                <!-- STATUS -->

                                <td>


                                    <?php if (
                                        $tes['status']
                                        == 'Lulus'
                                    ): ?>


                                        <span
                                            class="badge lulus"
                                        >
                                            ✓ Lulus
                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="badge gagal"
                                        >
                                            × Tidak Lulus
                                        </span>


                                    <?php endif; ?>


                                </td>



                                <!-- TANGGAL TES -->

                                <td>

                                    <?php

                                    if (
                                        !empty(
                                            $tes['tanggal_tes']
                                        )
                                    ) {

                                        echo date(
                                            "d-m-Y H:i",
                                            strtotime(
                                                $tes['tanggal_tes']
                                            )
                                        );

                                    } else {

                                        echo "-";
                                    }

                                    ?>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="7"
                                class="empty"
                            >

                                Belum ada hasil tes
                                yang ditemukan.

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