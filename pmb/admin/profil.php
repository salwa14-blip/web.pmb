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

$query = mysqli_prepare(
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
    $query,
    "i",
    $admin_id
);

mysqli_stmt_execute($query);

$hasil = mysqli_stmt_get_result($query);

$admin = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($query);


/* =========================================
   CEK DATA ADMIN
========================================= */

if (!$admin) {

    session_destroy();

    header("Location: login.php");
    exit;
}


/* =========================================
   UPDATE SESSION
========================================= */

$_SESSION['admin_nama'] = $admin['nama'];
$_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_foto'] = $admin['foto'] ?? '';

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Profil Admin - PMB</title>

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


        <!-- =========================================
             HEADER
        ========================================== -->

        <div class="header">

            <div>

                <p>
                    PMB / Admin / Profil
                </p>

                <h1>
                    Profil Administrator
                </h1>

            </div>


            <a
                href="dashboard.php"
                class="back"
            >
                ← Kembali
            </a>

        </div>



        <!-- =========================================
             PESAN
        ========================================== -->

        <?php if (
            isset($_GET['status']) &&
            $_GET['status'] === 'berhasil'
        ): ?>

            <div class="message success">
                ✓ Profil berhasil diperbarui.
            </div>

        <?php endif; ?>



        <!-- =========================================
             PROFILE CARD
        ========================================== -->

        <div class="profile-card">


            <!-- =====================================
                 PROFILE KIRI
            ====================================== -->

            <div class="profile-left">


                <!-- FOTO ADMIN -->

                <div class="big-avatar">

                    <?php if (!empty($admin['foto'])): ?>

                        <img
                            src="../uploads/admin/<?= htmlspecialchars(
                                basename($admin['foto'])
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



                <!-- NAMA -->

                <h2>

                    <?= htmlspecialchars(
                        $admin['nama']
                    ); ?>

                </h2>


                <p>
                    Administrator PMB
                </p>



                <!-- BUTTON -->

                <div class="profile-actions">


                    <a
                        href="edit_profil.php"
                        class="edit-button"
                    >
                        ✏️ Edit Profil
                    </a>


                    <a
                        href="ganti_password.php"
                        class="password-button"
                    >
                        🔐 Ganti Password
                    </a>


                </div>


            </div>



            <!-- =====================================
                 PROFILE KANAN
            ====================================== -->

            <div class="profile-right">


                <div class="profile-title">

                    <h2>
                        Informasi Admin
                    </h2>

                    <p>
                        Informasi akun administrator
                        Penerimaan Mahasiswa Baru.
                    </p>

                </div>



                <div class="info">


                    <!-- NAMA -->

                    <div class="info-item">

                        <span>
                            Nama Lengkap
                        </span>

                        <strong>

                            <?= htmlspecialchars(
                                $admin['nama']
                            ); ?>

                        </strong>

                    </div>



                    <!-- USERNAME -->

                    <div class="info-item">

                        <span>
                            Username
                        </span>

                        <strong>

                            <?= !empty($admin['username'])
                                ? htmlspecialchars(
                                    $admin['username']
                                )
                                : '-'; ?>

                        </strong>

                    </div>



                    <!-- EMAIL -->

                    <div class="info-item">

                        <span>
                            Email
                        </span>

                        <strong>

                            <?= !empty($admin['email'])
                                ? htmlspecialchars(
                                    $admin['email']
                                )
                                : '-'; ?>

                        </strong>

                    </div>



                    <!-- JABATAN -->

                    <div class="info-item">

                        <span>
                            Jabatan
                        </span>

                        <strong>
                            Administrator PMB
                        </strong>

                    </div>



                    <!-- ID ADMIN -->

                    <div class="info-item">

                        <span>
                            ID Admin
                        </span>

                        <strong>

                            ADM<?= str_pad(
                                (string) $admin['id'],
                                3,
                                "0",
                                STR_PAD_LEFT
                            ); ?>

                        </strong>

                    </div>



                    <!-- STATUS -->

                    <div class="info-item">

                        <span>
                            Status Akun
                        </span>

                        <strong class="status-active">
                            ● Aktif
                        </strong>

                    </div>


                </div>


            </div>


        </div>


    </main>


</div>

</body>

</html>