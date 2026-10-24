<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$pesan = "";
$tipe = "";


/* =========================================
   AMBIL DATA CALON MABA
========================================= */

$query_maba = mysqli_prepare(
    $conn,
    "SELECT *
     FROM calon_maba
     WHERE user_id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $query_maba,
    "i",
    $user_id
);

mysqli_stmt_execute($query_maba);

$hasil_maba =
    mysqli_stmt_get_result($query_maba);

$maba =
    mysqli_fetch_assoc($hasil_maba);

mysqli_stmt_close($query_maba);


/* =========================================
   CEK HASIL TES
========================================= */

$query_hasil = mysqli_prepare(
    $conn,
    "SELECT *
     FROM hasil_tes
     WHERE user_id = ?
     ORDER BY id DESC
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $query_hasil,
    "i",
    $user_id
);

mysqli_stmt_execute($query_hasil);

$hasil_tes =
    mysqli_stmt_get_result($query_hasil);

$tes =
    mysqli_fetch_assoc($hasil_tes);

mysqli_stmt_close($query_hasil);


/* =========================================
   CEK DAFTAR ULANG
========================================= */

$query_daftar = mysqli_prepare(
    $conn,
    "SELECT *
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

$hasil_daftar =
    mysqli_stmt_get_result($query_daftar);

$daftar_ulang =
    mysqli_fetch_assoc($hasil_daftar);

mysqli_stmt_close($query_daftar);


/* =========================================
   PROSES DAFTAR ULANG
========================================= */

if (
    isset($_POST['daftar_ulang']) &&
    $tes &&
    $tes['status'] == 'Lulus' &&
    !$daftar_ulang
) {

    $alamat =
        trim($_POST['alamat'] ?? '');

    $no_hp =
        trim($_POST['no_hp'] ?? '');

    $nama_ortu =
        trim($_POST['nama_ortu'] ?? '');

    $pekerjaan_ortu =
        trim($_POST['pekerjaan_ortu'] ?? '');

    $penghasilan_ortu =
        trim($_POST['penghasilan_ortu'] ?? '');

    $konfirmasi =
        $_POST['konfirmasi'] ?? '';


    if (
        $alamat == "" ||
        $no_hp == "" ||
        $nama_ortu == "" ||
        $pekerjaan_ortu == "" ||
        $penghasilan_ortu == ""
    ) {

        $pesan =
            "Semua data daftar ulang wajib diisi.";

        $tipe = "error";

    } elseif ($konfirmasi != 'ya') {

        $pesan =
            "Silakan centang konfirmasi terlebih dahulu.";

        $tipe = "error";

    } else {

        $simpan = mysqli_prepare(
            $conn,
            "INSERT INTO daftar_ulang
            (
                user_id,
                alamat,
                no_hp,
                nama_ortu,
                pekerjaan_ortu,
                penghasilan_ortu,
                status,
                status_verifikasi,
                tanggal_daftar_ulang
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                'Sudah Daftar Ulang',
                'Menunggu',
                NOW()
            )"
        );

        mysqli_stmt_bind_param(
            $simpan,
            "isssss",
            $user_id,
            $alamat,
            $no_hp,
            $nama_ortu,
            $pekerjaan_ortu,
            $penghasilan_ortu
        );


        if (mysqli_stmt_execute($simpan)) {

            mysqli_stmt_close($simpan);

            header(
                "Location: daftar_ulang.php?success=1"
            );

            exit;

        } else {

            $pesan =
                "Data daftar ulang gagal disimpan.";

            $tipe = "error";

            mysqli_stmt_close($simpan);
        }
    }
}


/* =========================================
   AMBIL ULANG DATA
========================================= */

$query_daftar = mysqli_prepare(
    $conn,
    "SELECT *
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

$hasil_daftar =
    mysqli_stmt_get_result($query_daftar);

$daftar_ulang =
    mysqli_fetch_assoc($hasil_daftar);

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

    <title>
        Daftar Ulang - PMB
    </title>

    <link
        rel="stylesheet"
        href="../css/daftar_ulang.css"
    >

</head>

<body>

<div class="page">

    
<!-- SIDEBAR -->

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

        <a href="daftar_ulang.php" class="active">
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


    <!-- CONTENT -->

    <main class="content">

        <div class="header">

            <div>

                <p>
                    PMB / Daftar Ulang
                </p>

                <h1>
                    Daftar Ulang
                </h1>

            </div>

            <a
                href="dashboard.php"
                class="back"
            >
                ← Kembali
            </a>

        </div>


        <?php if ($pesan != ""): ?>

            <div class="message <?= $tipe; ?>">

                <?= htmlspecialchars(
                    $pesan
                ); ?>

            </div>

        <?php endif; ?>


        <!-- BELUM TES -->

        <?php if (!$tes): ?>

            <div class="status-card">

                <div class="big-icon">
                    📝
                </div>

                <h2>
                    Tes Belum Dikerjakan
                </h2>

                <p>
                    Kamu harus mengikuti tes penerimaan
                    mahasiswa baru terlebih dahulu.
                </p>

                <a href="tes.php">
                    IKUTI TES
                </a>

            </div>


        <!-- TIDAK LULUS -->

        <?php elseif (
            $tes['status'] != 'Lulus'
        ): ?>

            <div class="status-card">

                <div class="big-icon">
                    📊
                </div>

                <h2>
                    Daftar Ulang Belum Tersedia
                </h2>

                <p>
                    Daftar ulang hanya dapat dilakukan
                    oleh calon mahasiswa yang dinyatakan
                    lulus tes.
                </p>

                <a href="hasil_tes.php">
                    LIHAT HASIL TES
                </a>

            </div>


        <!-- SUDAH KIRIM -->

        <?php elseif (
            $daftar_ulang &&
            $daftar_ulang['status_verifikasi']
            == 'Menunggu'
        ): ?>

            <div class="status-card">

                <div class="big-icon">
                    ⏳
                </div>

                <h2>
                    Menunggu Verifikasi Admin
                </h2>

                <p>
                    Data daftar ulang kamu sudah berhasil
                    dikirim. Admin PMB akan memeriksa data
                    sebelum NIM diterbitkan.
                </p>

                <a href="nim.php">
                    CEK STATUS NIM →
                </a>

            </div>


        <!-- DISETUJUI -->

        <?php elseif (
            $daftar_ulang &&
            $daftar_ulang['status_verifikasi']
            == 'Disetujui'
        ): ?>

            <div class="status-card success-card">

                <div class="success-circle">
                    ✓
                </div>

                <h2>
                    Daftar Ulang Disetujui
                </h2>

                <p>
                    Data daftar ulang kamu sudah
                    disetujui oleh Admin PMB.
                </p>

                <a href="nim.php">
                    LIHAT NIM →
                </a>

            </div>


        <!-- DITOLAK -->

        <?php elseif (
            $daftar_ulang &&
            $daftar_ulang['status_verifikasi']
            == 'Ditolak'
        ): ?>

            <div class="status-card">

                <div class="big-icon">
                    ❌
                </div>

                <h2>
                    Daftar Ulang Ditolak
                </h2>

                <p>
                    Data daftar ulang kamu belum
                    disetujui oleh Admin PMB.
                </p>

            </div>


        <!-- FORM -->

        <?php else: ?>

            <div class="information">

                <div class="information-icon">
                    ✓
                </div>

                <div>

                    <h3>
                        Selamat! Kamu dinyatakan lulus.
                    </h3>

                    <p>
                        Lengkapi formulir daftar ulang
                        di bawah ini.
                    </p>

                </div>

            </div>


            <div class="card">

                <div class="card-title">

                    <h2>
                        Form Daftar Ulang
                    </h2>

                    <p>
                        Isi data berikut dengan benar.
                    </p>

                </div>


                <div class="data-grid">

                    <div class="data-item">

                        <span>
                            Nama Lengkap
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $maba['nama_lengkap'] ?? '-'
                            ); ?>
                        </strong>

                    </div>


                    <div class="data-item">

                        <span>
                            Nomor Tes
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $maba['nomor_tes'] ?? '-'
                            ); ?>
                        </strong>

                    </div>


                    <div class="data-item">

                        <span>
                            Jurusan
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $maba['jurusan'] ?? '-'
                            ); ?>
                        </strong>

                    </div>


                    <div class="data-item">

                        <span>
                            Nilai Tes
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $tes['nilai']
                            ); ?>
                        </strong>

                    </div>

                </div>


                <form method="POST">

                    <div class="form-group">

                        <label>
                            Alamat Lengkap
                        </label>

                        <textarea
                            name="alamat"
                            placeholder="Masukkan alamat lengkap"
                            required
                        ></textarea>

                    </div>


                    <div class="form-group">

                        <label>
                            Nomor HP Aktif
                        </label>

                        <input
                            type="text"
                            name="no_hp"
                            placeholder="Contoh: 081234567890"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Nama Orang Tua / Wali
                        </label>

                        <input
                            type="text"
                            name="nama_ortu"
                            placeholder="Masukkan nama orang tua / wali"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Pekerjaan Orang Tua / Wali
                        </label>

                        <input
                            type="text"
                            name="pekerjaan_ortu"
                            placeholder="Contoh: Karyawan Swasta"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Penghasilan Orang Tua / Wali
                        </label>

                        <select
                            name="penghasilan_ortu"
                            required
                        >

                            <option value="">
                                Pilih penghasilan
                            </option>

                            <option value="< Rp1.000.000">
                                < Rp1.000.000
                            </option>

                            <option value="Rp1.000.000 - Rp3.000.000">
                                Rp1.000.000 - Rp3.000.000
                            </option>

                            <option value="Rp3.000.000 - Rp5.000.000">
                                Rp3.000.000 - Rp5.000.000
                            </option>

                            <option value="> Rp5.000.000">
                                > Rp5.000.000
                            </option>

                        </select>

                    </div>


                    <label class="confirmation">

                        <input
                            type="checkbox"
                            name="konfirmasi"
                            value="ya"
                            required
                        >

                        <span>
                            Saya menyatakan bahwa data
                            yang saya isi sudah benar.
                        </span>

                    </label>


                    <button
                        type="submit"
                        name="daftar_ulang"
                        class="submit-button"
                        onclick="return confirm(
                            'Yakin ingin mengirim data daftar ulang?'
                        )"
                    >
                        KIRIM DAFTAR ULANG
                    </button>

                </form>

            </div>

        <?php endif; ?>

    </main>

</div>

</body>
</html>