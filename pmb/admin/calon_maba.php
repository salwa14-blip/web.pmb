<?php
session_start();
require_once "../config/koneksi.php";

/* =========================
   CEK LOGIN ADMIN
========================= */

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}


/* =========================
   AMBIL DATA PROFIL ADMIN
========================= */

$admin_id = (int) $_SESSION['admin_id'];

$qAdmin = mysqli_prepare(
    $conn,
    "SELECT id, nama, email, foto
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

if (!$admin) {
    session_destroy();

    header("Location: login.php");
    exit;
}


/* Update session */
$_SESSION['admin_nama'] = $admin['nama'];
$_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_foto'] = $admin['foto'] ?? '';



/* =========================
   HAPUS USER + DATA PMB
========================= */

if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    if ($id > 0) {

        /* Ambil user_id dan foto */
        $cek = mysqli_prepare(
            $conn,
            "SELECT user_id, foto
             FROM calon_maba
             WHERE id = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $cek,
            "i",
            $id
        );

        mysqli_stmt_execute($cek);

        $hasil = mysqli_stmt_get_result($cek);

        $mabaHapus = mysqli_fetch_assoc($hasil);

        mysqli_stmt_close($cek);


        if ($mabaHapus) {

            $user_id =
                (int) $mabaHapus['user_id'];

            $namaFoto =
                $mabaHapus['foto'] ?? '';


            /* HAPUS AKUN USER */
            $hapus = mysqli_prepare(
                $conn,
                "DELETE FROM users
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $hapus,
                "i",
                $user_id
            );


            if (mysqli_stmt_execute($hapus)) {

                mysqli_stmt_close($hapus);


                /* HAPUS FILE FOTO USER */
                if (!empty($namaFoto)) {

                    $pathFoto =
                        "../uploads/foto/" .
                        basename($namaFoto);

                    if (is_file($pathFoto)) {
                        unlink($pathFoto);
                    }
                }


                header(
                    "Location: calon_maba.php?status=hapus"
                );

                exit;

            } else {

                $error =
                    mysqli_stmt_error($hapus);

                mysqli_stmt_close($hapus);

                die(
                    "Data gagal dihapus. Error: " .
                    htmlspecialchars($error)
                );
            }

        } else {

            header(
                "Location: calon_maba.php?status=tidak_ditemukan"
            );

            exit;
        }
    }
}



/* =========================
   PENCARIAN
========================= */

$keyword =
    trim($_GET['cari'] ?? '');


if ($keyword != '') {

    $search =
        "%" . $keyword . "%";

    $query = mysqli_prepare(
        $conn,
        "SELECT
            calon_maba.*,
            users.email,
            users.no_hp

         FROM calon_maba

         INNER JOIN users
            ON calon_maba.user_id = users.id

         WHERE
            calon_maba.nama_lengkap LIKE ?
            OR calon_maba.nomor_tes LIKE ?
            OR calon_maba.jurusan LIKE ?
            OR calon_maba.asal_sekolah LIKE ?
            OR users.email LIKE ?
            OR users.no_hp LIKE ?

         ORDER BY calon_maba.id DESC"
    );


    mysqli_stmt_bind_param(
        $query,
        "ssssss",
        $search,
        $search,
        $search,
        $search,
        $search,
        $search
    );

    mysqli_stmt_execute($query);

    $data =
        mysqli_stmt_get_result($query);


} else {

    $query = mysqli_prepare(
        $conn,
        "SELECT
            calon_maba.*,
            users.email,
            users.no_hp

         FROM calon_maba

         INNER JOIN users
            ON calon_maba.user_id = users.id

         ORDER BY calon_maba.id DESC"
    );

    mysqli_stmt_execute($query);

    $data =
        mysqli_stmt_get_result($query);
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
        Calon Mahasiswa - Admin PMB
    </title>

    <link
        rel="stylesheet"
        href="../css/admin_calon_maba.css"
    >

</head>

<body>

<div class="page">


    <!-- =========================
         SIDEBAR
    ========================== -->

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

            <a
                href="calon_maba.php"
                class="active"
            >
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



    <!-- =========================
         CONTENT
    ========================== -->

    <main class="content">


        <!-- =========================
             HEADER
        ========================== -->

        <div class="header">

            <div>

                <p>
                    PMB / Admin / Calon Mahasiswa
                </p>

                <h1>
                    Data Calon Mahasiswa
                </h1>

            </div>


            <!-- =========================
                 PROFIL ADMIN
            ========================== -->

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



        <!-- =========================
             PESAN
        ========================== -->

        <?php if (
            isset($_GET['status']) &&
            $_GET['status'] == 'hapus'
        ): ?>

            <div class="message success">

                Akun dan data calon mahasiswa
                berhasil dihapus.

            </div>

        <?php endif; ?>


        <?php if (
            isset($_GET['status']) &&
            $_GET['status'] == 'tidak_ditemukan'
        ): ?>

            <div class="message error">

                Data calon mahasiswa tidak ditemukan.

            </div>

        <?php endif; ?>



        <!-- =========================
             TOOLBAR
        ========================== -->

        <div class="toolbar">

            <div>

                <h2>
                    Daftar Calon Mahasiswa
                </h2>

                <p>
                    Kelola seluruh data calon
                    mahasiswa baru.
                </p>

            </div>


            <form
                method="GET"
                action="calon_maba.php"
                class="search"
            >

                <input
                    type="text"
                    name="cari"
                    placeholder="Cari nama, jurusan, nomor tes..."
                    value="<?= htmlspecialchars(
                        $keyword
                    ); ?>"
                >


                <button type="submit">
                    Cari
                </button>


                <?php if (
                    $keyword != ''
                ): ?>

                    <a href="calon_maba.php">
                        Reset
                    </a>

                <?php endif; ?>


            </form>

        </div>



        <!-- =========================
             TABLE
        ========================== -->

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
                                Email
                            </th>

                            <th>
                                No. HP
                            </th>

                            <th>
                                Jurusan
                            </th>

                            <th>
                                Asal Sekolah
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        $data &&
                        mysqli_num_rows($data) > 0
                    ): ?>


                        <?php

                        $no = 1;

                        while (
                            $maba =
                            mysqli_fetch_assoc($data)
                        ):

                        ?>


                            <tr>


                                <!-- NO -->

                                <td>
                                    <?= $no++; ?>
                                </td>



                                <!-- MAHASISWA -->

                                <td>

                                    <div class="student">


                                        <div class="photo">

                                            <?php if (
                                                !empty(
                                                    $maba['foto']
                                                )
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



                                        <div>

                                            <strong>
                                                <?= htmlspecialchars(
                                                    $maba['nama_lengkap']
                                                ); ?>
                                            </strong>


                                            <small>

                                                <?php

                                                if (
                                                    $maba['jenis_kelamin']
                                                    == 'L'
                                                ) {

                                                    echo "Laki-laki";

                                                } elseif (
                                                    $maba['jenis_kelamin']
                                                    == 'P'
                                                ) {

                                                    echo "Perempuan";

                                                } else {

                                                    echo "-";
                                                }

                                                ?>

                                            </small>

                                        </div>


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



                                <!-- EMAIL -->

                                <td>

                                    <?= htmlspecialchars(
                                        $maba['email']
                                        ?? '-'
                                    ); ?>

                                </td>



                                <!-- NO HP -->

                                <td>

                                    <?= htmlspecialchars(
                                        $maba['no_hp']
                                        ?? '-'
                                    ); ?>

                                </td>



                                <!-- JURUSAN -->

                                <td>

                                    <?= htmlspecialchars(
                                        $maba['jurusan']
                                        ?? '-'
                                    ); ?>

                                </td>



                                <!-- ASAL SEKOLAH -->

                                <td>

                                    <?= htmlspecialchars(
                                        $maba['asal_sekolah']
                                        ?? '-'
                                    ); ?>

                                </td>



                                <!-- STATUS -->

                                <td>

                                    <?php if (
                                        $maba['status_pendaftaran']
                                        == 'Sudah Lengkap'
                                    ): ?>

                                        <span
                                            class="badge success"
                                        >
                                            Lengkap
                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="badge pending"
                                        >
                                            Belum Lengkap
                                        </span>

                                    <?php endif; ?>

                                </td>



                                <!-- AKSI -->

                                <td>

                                    <div class="action">


                                        <a
                                            href="edit_calon_maba.php?id=<?= (int) $maba['id']; ?>"
                                            class="edit"
                                        >
                                            Edit
                                        </a>


                                        <a
                                            href="calon_maba.php?hapus=<?= (int) $maba['id']; ?>"
                                            class="delete"

                                            onclick="return confirm(
                                                'Yakin ingin menghapus akun ini?\n\nSemua data calon mahasiswa, hasil tes, daftar ulang, dan NIM akan ikut terhapus.'
                                            );"
                                        >
                                            Hapus
                                        </a>


                                    </div>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="9"
                                class="empty"
                            >

                                <?php if (
                                    $keyword != ''
                                ): ?>

                                    Data dengan kata kunci
                                    "<strong><?= htmlspecialchars(
                                        $keyword
                                    ); ?></strong>"
                                    tidak ditemukan.

                                <?php else: ?>

                                    Belum ada data calon mahasiswa.

                                <?php endif; ?>

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