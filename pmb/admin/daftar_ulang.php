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

$admin_id =
    (int) $_SESSION['admin_id'];


/* =========================================
   DATA ADMIN
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

$hasilAdmin =
    mysqli_stmt_get_result($qAdmin);

$admin =
    mysqli_fetch_assoc($hasilAdmin);

mysqli_stmt_close($qAdmin);


if (!$admin) {

    session_destroy();

    header("Location: login.php");
    exit;
}


/* =========================================
   SETUJUI DAFTAR ULANG
========================================= */

if (isset($_GET['setujui'])) {

    $id =
        (int) $_GET['setujui'];


    /* AMBIL DATA DAFTAR ULANG */

    $cek = mysqli_prepare(
        $conn,
        "SELECT
            daftar_ulang.user_id,
            daftar_ulang.status_verifikasi

         FROM daftar_ulang

         WHERE daftar_ulang.id = ?

         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $cek,
        "i",
        $id
    );

    mysqli_stmt_execute($cek);

    $hasilCek =
        mysqli_stmt_get_result($cek);

    $dataCek =
        mysqli_fetch_assoc($hasilCek);

    mysqli_stmt_close($cek);


    if ($dataCek) {

        $user_id =
            (int) $dataCek['user_id'];


        mysqli_begin_transaction($conn);


        try {


            /* =========================
               UPDATE VERIFIKASI
            ========================== */

            $update = mysqli_prepare(
                $conn,
                "UPDATE daftar_ulang
                 SET status_verifikasi =
                     'Disetujui'
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update,
                "i",
                $id
            );


            if (
                !mysqli_stmt_execute($update)
            ) {

                throw new Exception(
                    "Status verifikasi gagal diperbarui."
                );
            }

            mysqli_stmt_close($update);



            /* =========================
               CEK NIM
            ========================== */

            $cekNim = mysqli_prepare(
                $conn,
                "SELECT id
                 FROM mahasiswa
                 WHERE user_id = ?
                 LIMIT 1"
            );

            mysqli_stmt_bind_param(
                $cekNim,
                "i",
                $user_id
            );

            mysqli_stmt_execute($cekNim);

            $hasilNim =
                mysqli_stmt_get_result($cekNim);


            /* =========================
               BUAT NIM
            ========================== */

            if (
                mysqli_num_rows($hasilNim)
                == 0
            ) {

                /*
                 * Contoh:
                 *
                 * user_id = 3
                 * tahun = 2026
                 *
                 * NIM = 20260003
                 */

                $nim =
                    date("Y") .
                    str_pad(
                        $user_id,
                        4,
                        "0",
                        STR_PAD_LEFT
                    );


                $buatNim = mysqli_prepare(
                    $conn,
                    "INSERT INTO mahasiswa
                    (
                        user_id,
                        nim,
                        tanggal_diterima
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        CURDATE()
                    )"
                );

                mysqli_stmt_bind_param(
                    $buatNim,
                    "is",
                    $user_id,
                    $nim
                );


                if (
                    !mysqli_stmt_execute(
                        $buatNim
                    )
                ) {

                    throw new Exception(
                        "NIM gagal dibuat."
                    );
                }

                mysqli_stmt_close($buatNim);
            }

            mysqli_stmt_close($cekNim);


            mysqli_commit($conn);


            header(
                "Location: daftar_ulang.php?status=setujui"
            );

            exit;


        } catch (Exception $e) {

            mysqli_rollback($conn);

            die(
                htmlspecialchars(
                    $e->getMessage()
                )
            );
        }
    }
}


/* =========================================
   TOLAK
========================================= */

if (isset($_GET['tolak'])) {

    $id =
        (int) $_GET['tolak'];


    $tolak = mysqli_prepare(
        $conn,
        "UPDATE daftar_ulang
         SET status_verifikasi = 'Ditolak'
         WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $tolak,
        "i",
        $id
    );


    if (mysqli_stmt_execute($tolak)) {

        mysqli_stmt_close($tolak);

        header(
            "Location: daftar_ulang.php?status=tolak"
        );

        exit;
    }

    mysqli_stmt_close($tolak);
}


/* =========================================
   FILTER
========================================= */

$jurusan =
    trim($_GET['jurusan'] ?? '');


/* =========================================
   QUERY
========================================= */

$sql = "
    SELECT

        daftar_ulang.id,
        daftar_ulang.user_id,
        daftar_ulang.status,
        daftar_ulang.status_verifikasi,
        daftar_ulang.tanggal_daftar_ulang,

        calon_maba.nama_lengkap,
        calon_maba.nomor_tes,
        calon_maba.jurusan,
        calon_maba.asal_sekolah,
        calon_maba.foto,

        users.email,
        users.no_hp,

        mahasiswa.nim

    FROM daftar_ulang

    INNER JOIN users
        ON daftar_ulang.user_id =
           users.id

    INNER JOIN calon_maba
        ON daftar_ulang.user_id =
           calon_maba.user_id

    LEFT JOIN mahasiswa
        ON daftar_ulang.user_id =
           mahasiswa.user_id

    WHERE daftar_ulang.status =
          'Sudah Daftar Ulang'
";


if ($jurusan != '') {

    $jurusanSafe =
        mysqli_real_escape_string(
            $conn,
            $jurusan
        );

    $sql .= "
        AND calon_maba.jurusan =
            '$jurusanSafe'
    ";
}


$sql .= "
    ORDER BY daftar_ulang.id DESC
";


$dataDaftar =
    mysqli_query(
        $conn,
        $sql
    );


if (!$dataDaftar) {

    die(
        "Query Error: " .
        mysqli_error($conn)
    );
}


/* =========================================
   STATISTIK
========================================= */

$qTotal = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM daftar_ulang"
);

$totalDaftarUlang =
    (int) (
        mysqli_fetch_assoc(
            $qTotal
        )['total']
        ?? 0
    );


$qMenunggu = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM daftar_ulang
     WHERE status_verifikasi =
           'Menunggu'"
);

$totalMenunggu =
    (int) (
        mysqli_fetch_assoc(
            $qMenunggu
        )['total']
        ?? 0
    );


$qNim = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM mahasiswa"
);

$totalPunyaNim =
    (int) (
        mysqli_fetch_assoc(
            $qNim
        )['total']
        ?? 0
    );

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
        Daftar Ulang - Admin PMB
    </title>

    <link
        rel="stylesheet"
        href="../css/admin_daftar_ulang.css"
    >

</head>

<body>

<div class="page">


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

            <a href="hasil_tes.php">
                📊 Hasil Tes
            </a>

            <a
                href="daftar_ulang.php"
                class="active"
            >
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



    <main class="content">


        <!-- HEADER -->

        <div class="header">


            <div>

                <p>
                    PMB / Admin / Daftar Ulang
                </p>

                <h1>
                    Verifikasi Daftar Ulang
                </h1>

            </div>


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



        <!-- PESAN -->

        <?php if (
            ($_GET['status'] ?? '')
            == 'setujui'
        ): ?>

            <div class="message success">
                ✓ Daftar ulang berhasil disetujui dan NIM berhasil dibuat.
            </div>

        <?php endif; ?>


        <?php if (
            ($_GET['status'] ?? '')
            == 'tolak'
        ): ?>

            <div class="message error">
                Data daftar ulang telah ditolak.
            </div>

        <?php endif; ?>



        <!-- STATISTIK -->

        <div class="stats">


            <div class="stat-card">

                <div class="stat-icon blue">
                    📋
                </div>

                <div>

                    <span>
                        Daftar Ulang
                    </span>

                    <h2>
                        <?= $totalDaftarUlang; ?>
                    </h2>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon blue">
                    ⏳
                </div>

                <div>

                    <span>
                        Menunggu Verifikasi
                    </span>

                    <h2>
                        <?= $totalMenunggu; ?>
                    </h2>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon green">
                    🎓
                </div>

                <div>

                    <span>
                        Sudah Punya NIM
                    </span>

                    <h2>
                        <?= $totalPunyaNim; ?>
                    </h2>

                </div>

            </div>


        </div>



        <!-- TOOLBAR -->

        <div class="toolbar">


            <div>

                <h2>
                    Daftar Mahasiswa
                </h2>

                <p>
                    Verifikasi mahasiswa sebelum
                    mendapatkan NIM.
                </p>

            </div>


            <form
                method="GET"
                class="filter"
            >


                <select name="jurusan">

                    <option value="">
                        Semua Jurusan
                    </option>

                    <option
                        value="Teknik Informatika"
                        <?= $jurusan ==
                        'Teknik Informatika'
                            ? 'selected'
                            : ''; ?>
                    >
                        Teknik Informatika
                    </option>

                    <option
                        value="Sistem Informasi"
                        <?= $jurusan ==
                        'Sistem Informasi'
                            ? 'selected'
                            : ''; ?>
                    >
                        Sistem Informasi
                    </option>

                    <option
                        value="Manajemen"
                        <?= $jurusan ==
                        'Manajemen'
                            ? 'selected'
                            : ''; ?>
                    >
                        Manajemen
                    </option>

                    <option
                        value="Akuntansi"
                        <?= $jurusan ==
                        'Akuntansi'
                            ? 'selected'
                            : ''; ?>
                    >
                        Akuntansi
                    </option>

                </select>


                <button type="submit">
                    Filter
                </button>


                <?php if (
                    $jurusan != ''
                ): ?>

                    <a
                        href="daftar_ulang.php"
                        class="reset"
                    >
                        Reset
                    </a>

                <?php endif; ?>


            </form>


        </div>



        <!-- TABLE -->

        <div class="table-card">

            <div class="table-responsive">

                <table>

                    <thead>

                        <tr>

                            <th>No</th>
                            <th>Mahasiswa</th>
                            <th>Nomor Tes</th>
                            <th>Jurusan</th>
                            <th>Tanggal</th>
                            <th>NIM</th>
                            <th>Verifikasi</th>
                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        mysqli_num_rows(
                            $dataDaftar
                        ) > 0
                    ): ?>


                        <?php

                        $no = 1;

                        while (
                            $row =
                            mysqli_fetch_assoc(
                                $dataDaftar
                            )
                        ):

                        ?>


                            <tr>


                                <td>
                                    <?= $no++; ?>
                                </td>



                                <td>

                                    <div class="student">

                                        <div class="photo">


                                            <?php if (
                                                !empty(
                                                    $row['foto']
                                                )
                                            ): ?>


                                                <img
                                                    src="../uploads/foto/<?= htmlspecialchars(
                                                        $row['foto']
                                                    ); ?>"
                                                    alt="Foto"
                                                >


                                            <?php else: ?>

                                                👤

                                            <?php endif; ?>


                                        </div>


                                        <div>

                                            <strong>

                                                <?= htmlspecialchars(
                                                    $row['nama_lengkap']
                                                ); ?>

                                            </strong>

                                            <small>

                                                <?= htmlspecialchars(
                                                    $row['email']
                                                ); ?>

                                            </small>

                                        </div>

                                    </div>

                                </td>



                                <td>

                                    <?= htmlspecialchars(
                                        $row['nomor_tes']
                                    ); ?>

                                </td>



                                <td>

                                    <span class="jurusan">

                                        <?= htmlspecialchars(
                                            $row['jurusan']
                                        ); ?>

                                    </span>

                                </td>



                                <td>

                                    <?= date(
                                        "d-m-Y",
                                        strtotime(
                                            $row['tanggal_daftar_ulang']
                                        )
                                    ); ?>

                                </td>



                                <td>


                                    <?php if (
                                        !empty($row['nim'])
                                    ): ?>

                                        <strong class="nim">

                                            <?= htmlspecialchars(
                                                $row['nim']
                                            ); ?>

                                        </strong>

                                    <?php else: ?>

                                        <span class="belum-nim">
                                            Belum tersedia
                                        </span>

                                    <?php endif; ?>


                                </td>



                                <!-- STATUS -->

                                <td>


                                    <?php if (
                                        $row['status_verifikasi']
                                        == 'Menunggu'
                                    ): ?>

                                        <span class="badge proses">
                                            ⏳ Menunggu
                                        </span>


                                    <?php elseif (
                                        $row['status_verifikasi']
                                        == 'Disetujui'
                                    ): ?>

                                        <span class="badge selesai">
                                            ✓ Disetujui
                                        </span>


                                    <?php else: ?>

                                        <span class="badge ditolak">
                                            ✕ Ditolak
                                        </span>

                                    <?php endif; ?>


                                </td>



                                <!-- AKSI -->

                                <td>


                                    <?php if (
                                        $row['status_verifikasi']
                                        == 'Menunggu'
                                    ): ?>


                                        <div class="action-verifikasi">


                                            <a
                                                href="daftar_ulang.php?setujui=<?= (int) $row['id']; ?>"
                                                class="btn-setujui"

                                                onclick="return confirm(
                                                    'Setujui daftar ulang mahasiswa ini dan buat NIM?'
                                                )"
                                            >
                                                ✓ Setujui
                                            </a>


                                            <a
                                                href="daftar_ulang.php?tolak=<?= (int) $row['id']; ?>"
                                                class="btn-tolak"

                                                onclick="return confirm(
                                                    'Yakin ingin menolak daftar ulang mahasiswa ini?'
                                                )"
                                            >
                                                ✕ Tolak
                                            </a>


                                        </div>


                                    <?php elseif (
                                        $row['status_verifikasi']
                                        == 'Disetujui'
                                    ): ?>


                                        <span class="selesai-text">
                                            Selesai
                                        </span>


                                    <?php else: ?>


                                        <span class="ditolak-text">
                                            Ditolak
                                        </span>


                                    <?php endif; ?>


                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="8"
                                class="empty"
                            >
                                Belum ada data daftar ulang.
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