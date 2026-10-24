
<?php
session_start();
require_once "config/koneksi.php";

$pesan = "";
$tipe = "";

if (isset($_GET['register']) && $_GET['register'] === 'success') {
    $pesan = "Registrasi berhasil! Silakan login dengan akun kamu.";
    $tipe = "success";
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === "" || $password === "") {
        $pesan = "Email dan password wajib diisi!";
        $tipe = "error";
    } else {
        $query = mysqli_prepare(
            $conn,
            "SELECT id, nama, email, password, role
             FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param($query, "s", $email);
        mysqli_stmt_execute($query);
        $hasil = mysqli_stmt_get_result($query);

        if ($user = mysqli_fetch_assoc($hasil)) {
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                mysqli_stmt_close($query);

                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: user/dashboard.php");
                }
                exit;
            } else {
                $pesan = "Password salah!";
                $tipe = "error";
            }
        } else {
            $pesan = "Email belum terdaftar!";
            $tipe = "error";
        }

        mysqli_stmt_close($query);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login PMB | Universitas Airlangga</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>

<div class="auth-page">

    <!-- PANEL KIRI -->
    <section class="auth-left">
        <a href="index.php" class="auth-brand">
            <img src="assets/univp.jpeg"
                 alt="Logo Universitas Harapan Bangsa">

            <span class="brand-univ">
                <span>UNIVERSITAS</span>
                <span>Unair</span>
            </span>

            <span class="brand-divider"></span>
            <span class="brand-pmb">PMB</span>

            <span class="brand-caption">
                Penerimaan<br>Mahasiswa Baru
            </span>
        </a>

        <div class="auth-intro">
            <span class="auth-badge">PORTAL MAHASISWA</span>

            <h1>
                Satu Langkah<br>
                <span>Menuju Masa</span><br>
                Depanmu.
            </h1>

            <p class="auth-description">
                Masuk dan lanjutkan perjalananmu bersama
                Universitas Airlangga. Raih potensi,
                kembangkan prestasi, dan wujudkan masa depan
                yang lebih cerah.
            </p>
        </div>

        <div class="auth-illustration">
            <img src="assets/login.png"
                 alt="Ilustrasi login calon mahasiswa">
        </div>
    </section>

    <!-- PANEL KANAN -->
    <main class="auth-right">
        <div class="auth-form-heading">
            <span class="auth-eyebrow">SELAMAT DATANG KEMBALI</span>
            <h2>Masuk ke Akun</h2>
            <p>
                Silakan masukkan email dan password untuk
                melanjutkan ke portal PMB.
            </p>
        </div>

        <?php if ($pesan !== ""): ?>
            <div class="auth-message <?= htmlspecialchars($tipe) ?>">
                <?= htmlspecialchars($pesan) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="auth-group">
                <label for="email">Alamat Email</label>
                <div class="auth-input">
                    <span class="auth-input-icon" aria-hidden="true">✉</span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="nama@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        autocomplete="email"
                        required
                    >
                </div>
            </div>

            <div class="auth-group">
                <label for="password">Password</label>
                <div class="auth-input">
                    <span class="auth-input-icon" aria-hidden="true">♙</span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >
                </div>
            </div>

            <button type="submit" name="login" class="auth-submit">
                <span>Masuk Sekarang</span>
                <span class="auth-submit-arrow" aria-hidden="true">→</span>
            </button>
        </form>

        <div class="auth-separator">PORTAL PMB</div>

        <p class="auth-switch">
            Belum memiliki akun?
            <a href="register.php">Daftar sekarang</a>
        </p>

        <div class="auth-back">
            <a href="index.php">← &nbsp; Kembali ke Beranda</a>
        </div>
    </main>

</div>

</body>
</html>
