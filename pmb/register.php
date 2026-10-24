
<?php
require_once "config/koneksi.php";

$pesan = "";
$tipe = "";

$nama = "";
$email = "";
$no_hp = "";

if (isset($_POST['register'])) {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    if (
        $nama === "" ||
        $email === "" ||
        $no_hp === "" ||
        $password === "" ||
        $konfirmasi === ""
    ) {
        $pesan = "Semua data wajib diisi!";
        $tipe = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesan = "Format email tidak valid!";
        $tipe = "error";
    } elseif ($password !== $konfirmasi) {
        $pesan = "Konfirmasi password tidak sama!";
        $tipe = "error";
    } else {
        $cek = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param($cek, "s", $email);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $pesan = "Email sudah terdaftar!";
            $tipe = "error";
        } else {
            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $query = mysqli_prepare(
                $conn,
                "INSERT INTO users
                (nama, email, no_hp, password, role)
                VALUES (?, ?, ?, ?, 'user')"
            );

            mysqli_stmt_bind_param(
                $query,
                "ssss",
                $nama,
                $email,
                $no_hp,
                $password_hash
            );

            if (mysqli_stmt_execute($query)) {
                mysqli_stmt_close($query);
                mysqli_stmt_close($cek);

                header("Location: login.php?register=success");
                exit;
            } else {
                $pesan = "Registrasi gagal. Silakan coba kembali.";
                $tipe = "error";
            }

            mysqli_stmt_close($query);
        }

        mysqli_stmt_close($cek);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi PMB | Universitas Unair</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>

<div class="auth-page">

    <!-- PANEL KIRI -->
    <section class="auth-left">
        <a href="index.php" class="auth-brand">
            <img src="assets/univp.jpeg"
                 alt="Logo Universitas Unair">

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
            <span class="auth-badge">PENERIMAAN MAHASISWA BARU</span>

            <h1>
                Awali Langkah<br>
                <span>Menuju Masa</span><br>
                Depanmu.
            </h1>

            <p class="auth-description">
                Buat akun calon mahasiswa dan mulai perjalanan
                akademikmu bersama Universitas Airlangga.
                Persiapkan dirimu untuk masa depan yang lebih cerah.
            </p>
        </div>

        <div class="auth-illustration">
            <img src="assets/register.png"
                 alt="Ilustrasi registrasi calon mahasiswa">
        </div>
    </section>

    <!-- PANEL KANAN -->
    <main class="auth-right">
        <div class="auth-form-heading">
            <span class="auth-eyebrow">MULAI PERJALANANMU</span>
            <h2>Buat Akun Baru</h2>
            <p>
                Lengkapi data berikut untuk mendaftar sebagai
                calon mahasiswa Universitas Airlangga.
            </p>
        </div>

        <?php if ($pesan !== ""): ?>
            <div class="auth-message <?= htmlspecialchars($tipe) ?>">
                <?= htmlspecialchars($pesan) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="auth-group">
                <label for="nama">Nama Lengkap</label>
                <div class="auth-input">
                    <span class="auth-input-icon" aria-hidden="true">♙</span>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Masukkan nama lengkap"
                        value="<?= htmlspecialchars($nama) ?>"
                        autocomplete="name"
                        required
                    >
                </div>
            </div>

            <div class="auth-group">
                <label for="email">Alamat Email</label>
                <div class="auth-input">
                    <span class="auth-input-icon" aria-hidden="true">✉</span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="nama@email.com"
                        value="<?= htmlspecialchars($email) ?>"
                        autocomplete="email"
                        required
                    >
                </div>
            </div>

            <div class="auth-group">
                <label for="no_hp">Nomor HP</label>
                <div class="auth-input">
                    <span class="auth-input-icon" aria-hidden="true">☎</span>
                    <input
                        type="tel"
                        id="no_hp"
                        name="no_hp"
                        placeholder="Masukkan nomor HP"
                        value="<?= htmlspecialchars($no_hp) ?>"
                        autocomplete="tel"
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
                        placeholder="Buat password"
                        autocomplete="new-password"
                        required
                    >
                </div>
            </div>

            <div class="auth-group">
                <label for="konfirmasi">Konfirmasi Password</label>
                <div class="auth-input">
                    <span class="auth-input-icon" aria-hidden="true">✓</span>
                    <input
                        type="password"
                        id="konfirmasi"
                        name="konfirmasi"
                        placeholder="Ulangi password"
                        autocomplete="new-password"
                        required
                    >
                </div>
            </div>

            <button type="submit" name="register" class="auth-submit">
                <span>Daftar Sekarang</span>
                <span class="auth-submit-arrow" aria-hidden="true">→</span>
            </button>
        </form>

        <div class="auth-separator">PORTAL PMB</div>

        <p class="auth-switch">
            Sudah memiliki akun?
            <a href="login.php">Masuk sekarang</a>
        </p>

        <div class="auth-back">
            <a href="index.php">← &nbsp; Kembali ke Beranda</a>
        </div>
    </main>

</div>

</body>
</html>
