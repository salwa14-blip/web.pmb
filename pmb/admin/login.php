<?php
session_start();
require_once "../config/koneksi.php";

/* Kalau admin sudah login */
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$pesan = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email == "" || $password == "") {

        $pesan = "Email dan password wajib diisi!";

    } else {

        $query = mysqli_prepare(
            $conn,
            "SELECT * FROM admin WHERE email = ? LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $query,
            "s",
            $email
        );

        mysqli_stmt_execute($query);

        $hasil = mysqli_stmt_get_result($query);

        $admin = mysqli_fetch_assoc($hasil);

        if (
            $admin &&
            password_verify($password, $admin['password'])
        ) {

            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            $_SESSION['admin_email'] = $admin['email'];

            header("Location: dashboard.php");
            exit;

        } else {

            $pesan = "Email atau password salah!";
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

    <title>Login Admin - PMB</title>

    <link
        rel="stylesheet"
        href="../css/admin_login.css"
    >

</head>

<body>

<div class="login-page">

   
<!-- BAGIAN KIRI -->
<div class="login-left">

    <div class="brand-header">
        <div class="unair-brand">
            <div class="logo-circle">
                <img src="../assets/univp.jpeg"
                     alt="Logo Universitas Airlangga">
            </div>

            <div class="unair-text">
                <strong>UNIVERSITAS</strong>
                <strong>AIRLANGGA</strong>
            </div>
        </div>

        <div class="brand-divider"></div>

        <div class="pmb-brand">
            <h2>PMB</h2>
            <span>Penerimaan<br>Mahasiswa Baru</span>
        </div>
    </div>

    <div class="portal-label">
        <span class="status-dot"></span>
        PORTAL ADMINISTRATOR
    </div>

    <div class="hero-text">
        <h1>
            Bersama Membangun
            <span>Masa Depan.</span>
        </h1>

        <p>
            Kelola proses Penerimaan Mahasiswa Baru
            Universitas Airlangga dengan mudah,
            aman, dan terorganisir.
        </p>
    </div>

    <div class="hero-decoration"></div>

</div>


    <!-- KANAN -->

    <div class="login-right">

        <div class="login-box">

            <p class="small-title">
                ADMINISTRATOR
            </p>

            <h2>
                Selamat Datang!
            </h2>

            <p class="description">
                Silakan login untuk masuk ke dashboard admin.
            </p>


            <?php if ($pesan != ""): ?>

                <div class="error">
                    <?= htmlspecialchars($pesan); ?>
                </div>

            <?php endif; ?>


            <form method="POST">

                <div class="form-group">

                    <label>
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Masukkan email admin"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    name="login"
                >
                    LOGIN ADMIN
                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>