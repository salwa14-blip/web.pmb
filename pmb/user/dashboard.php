<?php
session_start();
require_once "../config/koneksi.php";

$user_id = $_SESSION['user_id'];
$ambilProfil = mysqli_prepare(
    $conn,
    "SELECT foto, nama_lengkap
     FROM calon_maba
     WHERE user_id = ?"
);

mysqli_stmt_bind_param($ambilProfil, "i", $user_id);
mysqli_stmt_execute($ambilProfil);

$hasilProfil = mysqli_stmt_get_result($ambilProfil);
$profil = mysqli_fetch_assoc($hasilProfil);
/* Registrasi */
$reg = mysqli_query($conn,
    "SELECT * FROM calon_maba WHERE user_id='$user_id'");
$registrasi = mysqli_num_rows($reg) > 0;

/* Hasil Tes */
$hasil = mysqli_query($conn,
    "SELECT * FROM hasil_tes WHERE user_id='$user_id' ORDER BY id DESC LIMIT 1");
$dataHasil = mysqli_fetch_assoc($hasil);

/* Daftar Ulang */
$du = mysqli_query($conn,
    "SELECT * FROM daftar_ulang WHERE user_id='$user_id'");
$daftarUlang = mysqli_num_rows($du) > 0;

/* NIM */
$nim = mysqli_query($conn,
    "SELECT * FROM mahasiswa WHERE user_id='$user_id'");
$nimAktif = mysqli_num_rows($nim) > 0;
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard User - PMB</title>

    <link rel="stylesheet" href="../css/dasboard.css">
</head>

<body>

<div class="dashboard">

  
<!-- ================= SIDEBAR ================= -->
<aside class="sidebar">

    <!-- LOGO UNIVERSITAS -->
    <div class="logo">

        <div class="logo-icon">
            <img
                src="../assets/univp.jpeg"
                alt="Logo Universitas Airlangga"
            >
        </div>

        <div class="logo-text">
            <strong>UNIVERSITAS</strong>
            <strong>AIRLANGGA</strong>

            <span>
                Penerimaan<br>
                Mahasiswa Baru
            </span>
        </div>

    </div>

    <!-- MENU SIDEBAR -->
    <nav class="menu">

        <a href="dashboard.php" class="active">
            <span>🏠</span>
            Dashboard
        </a>

        <a href="pendaftaran.php">
            <span>📋</span>
            Registrasi
        </a>

        <a href="tes.php">
            <span>🎓</span>
            Tes
        </a>

        <a href="hasil_tes.php">
            <span>📊</span>
            Hasil Tes
        </a>

        <a href="daftar_ulang.php">
            <span>📝</span>
            Daftar Ulang
        </a>

        <a href="nim.php">
            <span>🪪</span>
            NIM
        </a>

    </nav>

    <!-- LOGOUT -->
    <a href="../logout.php" class="logout">
        🚪 Logout
    </a>

</aside>



    <!-- ================= CONTENT ================= -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="topbar">

            <div>

                <p class="welcome">
                    Selamat Datang
                </p>

                <h1>
                    <?= htmlspecialchars($_SESSION['nama']); ?>
                </h1>

            </div>

<div class="profile">

    <div class="profile-photo">

        <?php if (!empty($profil['foto'])): ?>

            <img
                src="../uploads/foto/<?= htmlspecialchars($profil['foto']); ?>"
                alt="Foto Profil"
            >

        <?php else: ?>

            <span>👤</span>

        <?php endif; ?>

    </div>


    <div class="profile-info">

        <strong>
            <?= htmlspecialchars(
                $profil['nama_lengkap']
                ?? $_SESSION['nama']
            ); ?>
        </strong>

        <span>
            Calon Mahasiswa
        </span>

    </div>

</div>

        </header>



        <!-- JUDUL -->
        <section class="dashboard-title">

            <h2>
                Dashboard Calon Mahasiswa
            </h2>

            <p>
                Silakan selesaikan proses penerimaan mahasiswa baru melalui menu di bawah ini.
            </p>

        </section>



        <!-- ================= CARD ================= -->
        <section class="card-container">


            <!-- CARD REGISTRASI -->
            <a href="pendaftaran.php" class="card">

                <div class="card-icon">
                    📋
                </div>

                <h3>
                    Registrasi
                </h3>

                <p>
                    Lengkapi data diri dan data pendaftaran calon mahasiswa.
                </p>

               <?php if($registrasi): ?>
    <span class="badge success">Sudah Registrasi</span>
<?php else: ?>
    <span class="badge">Belum Mulai</span>
<?php endif; ?>
            </a>



            <!-- CARD TES -->
            <a href="tes.php" class="card">

                <div class="card-icon">
                    🎓
                </div>

                <h3>
                    Tes
                </h3>

                <p>
                    Kerjakan soal tes penerimaan mahasiswa baru.
                </p>

               <?php if($dataHasil): ?>
    <span class="badge success">Sudah Tes</span>
<?php else: ?>
    <span class="badge">Belum Mulai</span>
<?php endif; ?>
            </a>



            <!-- CARD HASIL TES -->
            <a href="hasil_tes.php" class="card">

                <div class="card-icon">
                    📊
                </div>

                <h3>
                    Hasil Tes
                </h3>

                <p>
                    Lihat nilai dan hasil kelulusan tes masuk.
                </p>

              <?php if($dataHasil): ?>

    <?php if($dataHasil['status']=="Lulus"): ?>
        <span class="badge success">Lulus</span>
    <?php else: ?>
        <span class="badge danger">Tidak Lulus</span>
    <?php endif; ?>

<?php else: ?>
    <span class="badge">Belum Tersedia</span>
<?php endif; ?>
            </a>



            <!-- CARD DAFTAR ULANG -->
            <a href="daftar_ulang.php" class="card">

                <div class="card-icon">
                    📝
                </div>

                <h3>
                    Daftar Ulang
                </h3>

                <p>
                    Lakukan daftar ulang setelah dinyatakan lulus.
                </p>

               <?php if($daftarUlang): ?>
    <span class="badge success">Sudah Daftar Ulang</span>
<?php else: ?>
    <span class="badge">Belum Tersedia</span>
<?php endif; ?>
            </a>



            <!-- CARD NIM -->
            <a href="nim.php" class="card">

                <div class="card-icon">
                    🪪
                </div>

                <h3>
                    Nomor Induk Mahasiswa
                </h3>

                <p>
                    Lihat NIM setelah proses daftar ulang berhasil.
                </p>

               <?php if($nimAktif): ?>
    <span class="badge success">NIM Aktif</span>
<?php else: ?>
    <span class="badge">Belum Tersedia</span>
<?php endif; ?>
            </a>

        </section>

    </main>

</div>

</body>
</html>