<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id <= 0) {
    header("Location: soal.php");
    exit;
}


/* AMBIL SOAL */

$query = mysqli_prepare(
    $conn,
    "SELECT *
     FROM soal
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $query,
    "i",
    $id
);

mysqli_stmt_execute($query);

$hasil = mysqli_stmt_get_result($query);

$soal = mysqli_fetch_assoc($hasil);


if (!$soal) {
    header("Location: soal.php");
    exit;
}


$pesan = "";


/* UPDATE */

if (isset($_POST['simpan'])) {

    $jurusan = trim($_POST['jurusan'] ?? '');

    $pertanyaan =
        trim($_POST['pertanyaan'] ?? '');

    $pilihan_a =
        trim($_POST['pilihan_a'] ?? '');

    $pilihan_b =
        trim($_POST['pilihan_b'] ?? '');

    $pilihan_c =
        trim($_POST['pilihan_c'] ?? '');

    $pilihan_d =
        trim($_POST['pilihan_d'] ?? '');

    $jawaban_benar =
        strtoupper(
            trim($_POST['jawaban_benar'] ?? '')
        );


    if (
        $jurusan == "" ||
        $pertanyaan == "" ||
        $pilihan_a == "" ||
        $pilihan_b == "" ||
        $pilihan_c == "" ||
        $pilihan_d == "" ||
        $jawaban_benar == ""
    ) {

        $pesan = "Semua data wajib diisi!";

    } else {

        $update = mysqli_prepare(
            $conn,
            "UPDATE soal SET

                jurusan = ?,
                pertanyaan = ?,
                pilihan_a = ?,
                pilihan_b = ?,
                pilihan_c = ?,
                pilihan_d = ?,
                jawaban_benar = ?

             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "sssssssi",
            $jurusan,
            $pertanyaan,
            $pilihan_a,
            $pilihan_b,
            $pilihan_c,
            $pilihan_d,
            $jawaban_benar,
            $id
        );


        if (mysqli_stmt_execute($update)) {

            header(
                "Location: soal.php?status=edit"
            );
            exit;

        } else {

            $pesan = "Soal gagal diperbarui.";
        }
    }
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

    <title>Edit Soal - Admin PMB</title>

    <link
        rel="stylesheet"
        href="../css/admin_form_soal.css"
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



    <main class="content">


        <div class="header">

            <div>

                <p>
                    PMB / Admin / Soal / Edit
                </p>

                <h1>
                    Edit Soal
                </h1>

            </div>


            <a
                href="soal.php"
                class="back"
            >
                ← Kembali
            </a>

        </div>


        <?php if ($pesan != ""): ?>

            <div class="message error">
                <?= htmlspecialchars($pesan); ?>
            </div>

        <?php endif; ?>


        <div class="form-card">

            <form method="POST">


                <div class="form-group">

                    <label>
                        Jurusan
                    </label>

                    <select
                        name="jurusan"
                        required
                    >

                        <option
                            value="Teknik Informatika"

                            <?= $soal['jurusan']
                                == 'Teknik Informatika'
                                ? 'selected'
                                : ''; ?>
                        >
                            Teknik Informatika
                        </option>


                        <option
                            value="Sistem Informasi"

                            <?= $soal['jurusan']
                                == 'Sistem Informasi'
                                ? 'selected'
                                : ''; ?>
                        >
                            Sistem Informasi
                        </option>


                        <option
                            value="Manajemen"

                            <?= $soal['jurusan']
                                == 'Manajemen'
                                ? 'selected'
                                : ''; ?>
                        >
                            Manajemen
                        </option>


                        <option
                            value="Akuntansi"

                            <?= $soal['jurusan']
                                == 'Akuntansi'
                                ? 'selected'
                                : ''; ?>
                        >
                            Akuntansi
                        </option>

                    </select>

                </div>



                <div class="form-group">

                    <label>
                        Pertanyaan
                    </label>

                    <textarea
                        name="pertanyaan"
                        required
                    ><?= htmlspecialchars(
                        $soal['pertanyaan']
                    ); ?></textarea>

                </div>



                <div class="form-row">


                    <div class="form-group">

                        <label>
                            Pilihan A
                        </label>

                        <input
                            type="text"
                            name="pilihan_a"

                            value="<?= htmlspecialchars(
                                $soal['pilihan_a']
                            ); ?>"

                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Pilihan B
                        </label>

                        <input
                            type="text"
                            name="pilihan_b"

                            value="<?= htmlspecialchars(
                                $soal['pilihan_b']
                            ); ?>"

                            required
                        >

                    </div>

                </div>



                <div class="form-row">


                    <div class="form-group">

                        <label>
                            Pilihan C
                        </label>

                        <input
                            type="text"
                            name="pilihan_c"

                            value="<?= htmlspecialchars(
                                $soal['pilihan_c']
                            ); ?>"

                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Pilihan D
                        </label>

                        <input
                            type="text"
                            name="pilihan_d"

                            value="<?= htmlspecialchars(
                                $soal['pilihan_d']
                            ); ?>"

                            required
                        >

                    </div>

                </div>



                <div class="form-group">

                    <label>
                        Jawaban Benar
                    </label>

                    <select
                        name="jawaban_benar"
                        required
                    >

                        <?php
                        foreach (
                            ['A','B','C','D']
                            as $opsi
                        ):
                        ?>

                            <option
                                value="<?= $opsi; ?>"

                                <?= $soal['jawaban_benar']
                                    == $opsi
                                    ? 'selected'
                                    : ''; ?>
                            >

                                <?= $opsi; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>



                <button
                    type="submit"
                    name="simpan"
                >
                    SIMPAN PERUBAHAN
                </button>


            </form>

        </div>


    </main>

</div>

</body>

</html>