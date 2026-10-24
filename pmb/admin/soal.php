<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   HAPUS SOAL
========================= */

if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    if ($id > 0) {

        $hapus = mysqli_prepare(
            $conn,
            "DELETE FROM soal WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $hapus,
            "i",
            $id
        );

        if (mysqli_stmt_execute($hapus)) {

            header("Location: soal.php?status=hapus");
            exit;

        } else {

            die("Soal gagal dihapus.");
        }
    }
}


/* =========================
   FILTER JURUSAN
========================= */

$jurusan = trim($_GET['jurusan'] ?? '');


if ($jurusan != '') {

    $query = mysqli_prepare(
        $conn,
        "SELECT *
         FROM soal
         WHERE jurusan = ?
         ORDER BY id DESC"
    );

    mysqli_stmt_bind_param(
        $query,
        "s",
        $jurusan
    );

    mysqli_stmt_execute($query);

    $dataSoal = mysqli_stmt_get_result($query);

} else {

    $dataSoal = mysqli_query(
        $conn,
        "SELECT *
         FROM soal
         ORDER BY jurusan ASC, id DESC"
    );
}


/* =========================
   HITUNG JUMLAH SOAL
========================= */

$totalSoal = mysqli_num_rows($dataSoal);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kelola Soal - Admin PMB</title>

    <link
        rel="stylesheet"
        href="../css/admin_soal.css"
    >

</head>

<body>

<div class="page">


    <!-- SIDEBAR -->

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

            <a
                href="soal.php"
                class="active"
            >
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



    <!-- CONTENT -->

    <main class="content">


        <!-- HEADER -->

        <div class="header">

            <div>

                <p>
                    PMB / Admin / Kelola Soal
                </p>

                <h1>
                    Kelola Soal Tes
                </h1>

            </div>


            <a
                href="tambah_soal.php"
                class="add-button"
            >
                + Tambah Soal
            </a>

        </div>



        <!-- PESAN -->

        <?php if (
            isset($_GET['status']) &&
            $_GET['status'] == 'hapus'
        ): ?>

            <div class="message success">
                Soal berhasil dihapus.
            </div>

        <?php endif; ?>


        <?php if (
            isset($_GET['status']) &&
            $_GET['status'] == 'tambah'
        ): ?>

            <div class="message success">
                Soal berhasil ditambahkan.
            </div>

        <?php endif; ?>


        <?php if (
            isset($_GET['status']) &&
            $_GET['status'] == 'edit'
        ): ?>

            <div class="message success">
                Soal berhasil diperbarui.
            </div>

        <?php endif; ?>



        <!-- FILTER -->

        <div class="toolbar">

            <div>

                <h2>
                    Daftar Soal
                </h2>

                <p>
                    Total <?= $totalSoal; ?> soal ditampilkan.
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


                <button type="submit">
                    Filter
                </button>


                <?php if ($jurusan != ''): ?>

                    <a href="soal.php">
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
                            <th>Jurusan</th>
                            <th>Pertanyaan</th>
                            <th>A</th>
                            <th>B</th>
                            <th>C</th>
                            <th>D</th>
                            <th>Jawaban</th>
                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        mysqli_num_rows($dataSoal) > 0
                    ): ?>

                        <?php
                        $no = 1;

                        while (
                            $soal = mysqli_fetch_assoc($dataSoal)
                        ):
                        ?>

                            <tr>

                                <td>
                                    <?= $no++; ?>
                                </td>


                                <td>

                                    <span class="jurusan-badge">
                                        <?= htmlspecialchars(
                                            $soal['jurusan']
                                        ); ?>
                                    </span>

                                </td>


                                <td class="question">
                                    <?= htmlspecialchars(
                                        $soal['pertanyaan']
                                    ); ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $soal['pilihan_a']
                                    ); ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $soal['pilihan_b']
                                    ); ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $soal['pilihan_c']
                                    ); ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $soal['pilihan_d']
                                    ); ?>
                                </td>


                                <td>

                                    <span class="answer-badge">
                                        <?= htmlspecialchars(
                                            $soal['jawaban_benar']
                                        ); ?>
                                    </span>

                                </td>


                                <td>

                                    <div class="action">

                                        <a
                                            href="edit_soal.php?id=<?= (int) $soal['id']; ?>"
                                            class="edit"
                                        >
                                            Edit
                                        </a>


                                        <a
                                            href="soal.php?hapus=<?= (int) $soal['id']; ?>"
                                            class="delete"

                                            onclick="return confirm(
                                                'Yakin ingin menghapus soal ini?'
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
                                Belum ada soal untuk jurusan ini.
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