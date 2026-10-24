<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: calon_maba.php");
    exit;
}

$pesan = "";
$tipe = "";


/* =========================================
   AMBIL DATA
========================================= */

$query = mysqli_prepare(
    $conn,
    "SELECT
        calon_maba.*,
        users.nama AS nama_akun,
        users.email,
        users.no_hp

     FROM calon_maba

     JOIN users
        ON calon_maba.user_id = users.id

     WHERE calon_maba.id = ?

     LIMIT 1"
);

mysqli_stmt_bind_param(
    $query,
    "i",
    $id
);

mysqli_stmt_execute($query);

$hasil = mysqli_stmt_get_result($query);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($query);


if (!$data) {
    header("Location: calon_maba.php");
    exit;
}


/* =========================================
   PROSES UPDATE
========================================= */

if (isset($_POST['simpan'])) {

    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $asal_sekolah = trim($_POST['asal_sekolah'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    if (
        $nama_lengkap == "" ||
        $email == "" ||
        $no_hp == "" ||
        $tempat_lahir == "" ||
        $tanggal_lahir == "" ||
        $jenis_kelamin == "" ||
        $alamat == "" ||
        $asal_sekolah == "" ||
        $jurusan == ""
    ) {

        $pesan = "Semua data wajib diisi!";
        $tipe = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $pesan = "Format email tidak valid!";
        $tipe = "error";

    } else {

        /* =========================================
           UPDATE USERS
        ========================================= */

        $updateUser = mysqli_prepare(
            $conn,
            "UPDATE users
             SET
                nama = ?,
                email = ?,
                no_hp = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $updateUser,
            "sssi",
            $nama_lengkap,
            $email,
            $no_hp,
            $data['user_id']
        );


        /* =========================================
           UPDATE CALON MABA
        ========================================= */

        $updateMaba = mysqli_prepare(
            $conn,
            "UPDATE calon_maba
             SET
                nama_lengkap = ?,
                tempat_lahir = ?,
                tanggal_lahir = ?,
                jenis_kelamin = ?,
                alamat = ?,
                asal_sekolah = ?,
                jurusan = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $updateMaba,
            "sssssssi",
            $nama_lengkap,
            $tempat_lahir,
            $tanggal_lahir,
            $jenis_kelamin,
            $alamat,
            $asal_sekolah,
            $jurusan,
            $id
        );


        mysqli_begin_transaction($conn);

        try {

            if (!mysqli_stmt_execute($updateUser)) {
                throw new Exception(
                    "Data akun gagal diperbarui."
                );
            }

            if (!mysqli_stmt_execute($updateMaba)) {
                throw new Exception(
                    "Data calon mahasiswa gagal diperbarui."
                );
            }

            mysqli_commit($conn);

            $pesan = "Data calon mahasiswa berhasil diperbarui!";
            $tipe = "success";


            /* AMBIL ULANG DATA */

            $query = mysqli_prepare(
                $conn,
                "SELECT
                    calon_maba.*,
                    users.nama AS nama_akun,
                    users.email,
                    users.no_hp

                 FROM calon_maba

                 JOIN users
                    ON calon_maba.user_id = users.id

                 WHERE calon_maba.id = ?

                 LIMIT 1"
            );

            mysqli_stmt_bind_param(
                $query,
                "i",
                $id
            );

            mysqli_stmt_execute($query);

            $hasil = mysqli_stmt_get_result($query);
            $data = mysqli_fetch_assoc($hasil);

        } catch (Exception $e) {

            mysqli_rollback($conn);

            $pesan = $e->getMessage();
            $tipe = "error";
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

    <title>Edit Calon Mahasiswa - PMB</title>

    <link
        rel="stylesheet"
        href="../css/admin_edit_maba.css"
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


    <!-- CONTENT -->

    <main class="content">


        <!-- HEADER -->

        <div class="header">

            <div>

                <p>
                    PMB / Admin / Calon Mahasiswa / Edit
                </p>

                <h1>
                    Edit Calon Mahasiswa
                </h1>

            </div>

            <a
                href="calon_maba.php"
                class="back"
            >
                ← Kembali
            </a>

        </div>


        <?php if ($pesan != ""): ?>

            <div class="message <?= $tipe ?>">
                <?= htmlspecialchars($pesan); ?>
            </div>

        <?php endif; ?>


        <div class="form-card">


            <!-- FOTO -->

            <div class="student-profile">

                <div class="photo">

                    <?php if (!empty($data['foto'])): ?>

                        <img
                            src="../uploads/foto/<?= htmlspecialchars(
                                $data['foto']
                            ); ?>"
                            alt="Foto Mahasiswa"
                        >

                    <?php else: ?>

                        <span>
                            👤
                        </span>

                    <?php endif; ?>

                </div>

                <div>

                    <h2>
                        <?= htmlspecialchars(
                            $data['nama_lengkap']
                        ); ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $data['nomor_tes']
                        ); ?>
                    </p>

                </div>

            </div>


            <form method="POST">


                <div class="form-row">


                    <div class="form-group">

                        <label>
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            name="nama_lengkap"

                            value="<?= htmlspecialchars(
                                $data['nama_lengkap']
                            ); ?>"

                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"

                            value="<?= htmlspecialchars(
                                $data['email']
                            ); ?>"

                            required
                        >

                    </div>


                </div>


                <div class="form-row">


                    <div class="form-group">

                        <label>
                            No. HP
                        </label>

                        <input
                            type="text"
                            name="no_hp"

                            value="<?= htmlspecialchars(
                                $data['no_hp']
                            ); ?>"

                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Nomor Tes
                        </label>

                        <input
                            type="text"

                            value="<?= htmlspecialchars(
                                $data['nomor_tes']
                            ); ?>"

                            disabled
                        >

                    </div>


                </div>


                <div class="form-row">


                    <div class="form-group">

                        <label>
                            Tempat Lahir
                        </label>

                        <input
                            type="text"
                            name="tempat_lahir"

                            value="<?= htmlspecialchars(
                                $data['tempat_lahir']
                            ); ?>"

                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Tanggal Lahir
                        </label>

                        <input
                            type="date"
                            name="tanggal_lahir"

                            value="<?= htmlspecialchars(
                                $data['tanggal_lahir']
                            ); ?>"

                            required
                        >

                    </div>


                </div>


                <div class="form-group">

                    <label>
                        Jenis Kelamin
                    </label>

                    <select
                        name="jenis_kelamin"
                        required
                    >

                        <option
                            value="L"

                            <?= $data['jenis_kelamin'] == 'L'
                                ? 'selected'
                                : ''; ?>
                        >
                            Laki-laki
                        </option>

                        <option
                            value="P"

                            <?= $data['jenis_kelamin'] == 'P'
                                ? 'selected'
                                : ''; ?>
                        >
                            Perempuan
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Alamat
                    </label>

                    <textarea
                        name="alamat"
                        required
                    ><?= htmlspecialchars(
                        $data['alamat']
                    ); ?></textarea>

                </div>


                <div class="form-row">


                    <div class="form-group">

                        <label>
                            Asal Sekolah
                        </label>

                        <input
                            type="text"
                            name="asal_sekolah"

                            value="<?= htmlspecialchars(
                                $data['asal_sekolah']
                            ); ?>"

                            required
                        >

                    </div>


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

                                <?= $data['jurusan']
                                    == 'Teknik Informatika'
                                    ? 'selected'
                                    : ''; ?>
                            >
                                Teknik Informatika
                            </option>


                            <option
                                value="Sistem Informasi"

                                <?= $data['jurusan']
                                    == 'Sistem Informasi'
                                    ? 'selected'
                                    : ''; ?>
                            >
                                Sistem Informasi
                            </option>


                            <option
                                value="Manajemen"

                                <?= $data['jurusan']
                                    == 'Manajemen'
                                    ? 'selected'
                                    : ''; ?>
                            >
                                Manajemen
                            </option>


                            <option
                                value="Akuntansi"

                                <?= $data['jurusan']
                                    == 'Akuntansi'
                                    ? 'selected'
                                    : ''; ?>
                            >
                                Akuntansi
                            </option>

                        </select>

                    </div>


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