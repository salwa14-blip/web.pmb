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

$pesan = "";
$tipe = "";


/* =========================================
   AMBIL DATA ADMIN
========================================= */

$query = mysqli_prepare(
    $conn,
    "SELECT
        id,
        nama,
        email,
        password,
        foto
     FROM admin
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $query,
    "i",
    $admin_id
);

mysqli_stmt_execute($query);

$hasil = mysqli_stmt_get_result($query);

$admin = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($query);


if (!$admin) {

    session_destroy();

    header("Location: login.php");
    exit;
}


/* =========================================
   PROSES GANTI PASSWORD
========================================= */

if (isset($_POST['simpan'])) {

    $password_lama =
        $_POST['password_lama'] ?? '';

    $password_baru =
        $_POST['password_baru'] ?? '';

    $konfirmasi_password =
        $_POST['konfirmasi_password'] ?? '';


    /* VALIDASI KOSONG */

    if (
        $password_lama == "" ||
        $password_baru == "" ||
        $konfirmasi_password == ""
    ) {

        $pesan =
            "Semua kolom password wajib diisi.";

        $tipe = "error";

    }


    /* CEK PASSWORD LAMA */

    elseif (
        !password_verify(
            $password_lama,
            $admin['password']
        )
    ) {

        $pesan =
            "Password lama salah.";

        $tipe = "error";

    }


    /* PANJANG PASSWORD BARU */

    elseif (
        strlen($password_baru) < 6
    ) {

        $pesan =
            "Password baru minimal 6 karakter.";

        $tipe = "error";

    }


    /* PASSWORD BARU SAMA DENGAN LAMA */

    elseif (
        password_verify(
            $password_baru,
            $admin['password']
        )
    ) {

        $pesan =
            "Password baru tidak boleh sama dengan password lama.";

        $tipe = "error";

    }


    /* KONFIRMASI */

    elseif (
        $password_baru !==
        $konfirmasi_password
    ) {

        $pesan =
            "Konfirmasi password baru tidak sama.";

        $tipe = "error";

    }


    /* UPDATE PASSWORD */

    else {

        $passwordHash =
            password_hash(
                $password_baru,
                PASSWORD_DEFAULT
            );


        $update = mysqli_prepare(
            $conn,
            "UPDATE admin
             SET password = ?
             WHERE id = ?"
        );


        mysqli_stmt_bind_param(
            $update,
            "si",
            $passwordHash,
            $admin_id
        );


        if (
            mysqli_stmt_execute($update)
        ) {

            $pesan =
                "Password berhasil diubah.";

            $tipe = "success";


            /* AMBIL HASH TERBARU */

            $admin['password'] =
                $passwordHash;

        } else {

            $pesan =
                "Password gagal diubah.";

            $tipe = "error";
        }


        mysqli_stmt_close($update);
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

    <title>
        Ganti Password Admin - PMB
    </title>

    <link
        rel="stylesheet"
        href="../css/admin_profil.css"
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



    <!-- =========================================
         CONTENT
    ========================================== -->

    <main class="content">


        <!-- HEADER -->

        <div class="header">

            <div>

                <p>
                    PMB / Admin / Profil / Ganti Password
                </p>

                <h1>
                    Ganti Password
                </h1>

            </div>


            <a
                href="profil.php"
                class="back"
            >
                ← Kembali
            </a>

        </div>



        <!-- =========================================
             PESAN
        ========================================== -->

        <?php if ($pesan != ""): ?>

            <div
                class="message <?= htmlspecialchars(
                    $tipe
                ); ?>"
            >

                <?= htmlspecialchars(
                    $pesan
                ); ?>

            </div>

        <?php endif; ?>



        <!-- =========================================
             FORM PASSWORD
        ========================================== -->

        <div class="edit-card">


            <div
                style="
                    margin-bottom:25px;
                "
            >

                <h2
                    style="
                        font-family:Georgia,serif;
                        font-size:23px;
                        margin-bottom:7px;
                    "
                >
                    Keamanan Akun
                </h2>

                <p
                    style="
                        color:#888;
                        font-size:13px;
                        line-height:1.6;
                    "
                >
                    Masukkan password lama terlebih dahulu,
                    kemudian buat password baru untuk akun admin.
                </p>

            </div>


            <form method="POST">


                <!-- PASSWORD LAMA -->

                <div class="form-group">

                    <label>
                        Password Lama
                    </label>

                    <input
                        type="password"
                        name="password_lama"
                        placeholder="Masukkan password lama"
                        autocomplete="current-password"
                        required
                    >

                </div>



                <!-- PASSWORD BARU -->

                <div class="form-group">

                    <label>
                        Password Baru
                    </label>

                    <input
                        type="password"
                        name="password_baru"
                        placeholder="Minimal 6 karakter"
                        autocomplete="new-password"
                        required
                    >

                </div>



                <!-- KONFIRMASI -->

                <div class="form-group">

                    <label>
                        Konfirmasi Password Baru
                    </label>

                    <input
                        type="password"
                        name="konfirmasi_password"
                        placeholder="Masukkan ulang password baru"
                        autocomplete="new-password"
                        required
                    >

                </div>



                <button
                    type="submit"
                    name="simpan"
                    class="save-button"
                >
                    SIMPAN PASSWORD BARU
                </button>


            </form>


        </div>


    </main>

</div>

</body>

</html>