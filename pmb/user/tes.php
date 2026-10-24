
<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$pesan = "";

/* CEK PENDAFTARAN */
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM calon_maba WHERE user_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$maba = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$belum_daftar = !$maba || empty($maba['nomor_tes']);

/* CEK HASIL TES */
$stmt = mysqli_prepare(
    $conn,
    "SELECT id FROM hasil_tes WHERE user_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$hasil_lama = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

/* CEK SESI TES */
if (
    !isset($_SESSION['tes_verified_user']) ||
    (int) $_SESSION['tes_verified_user'] !== $user_id
) {
    unset(
        $_SESSION['tes_verified_user'],
        $_SESSION['tes_started_at']
    );
}

/* VERIFIKASI NOMOR TES */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['verifikasi_nomor']) &&
    !$belum_daftar &&
    !$hasil_lama
) {
    $nomor_input = trim($_POST['nomor_tes'] ?? '');

    if (
        $nomor_input !== '' &&
        hash_equals((string) $maba['nomor_tes'], $nomor_input)
    ) {
        $_SESSION['tes_verified_user'] = $user_id;
        $_SESSION['tes_started_at'] = time();

        header("Location: tes.php");
        exit;
    } else {
        $pesan = "Nomor tes salah. Silakan periksa kembali.";
    }
}

/* STATUS VERIFIKASI */
$tes_terverifikasi = (
    !$belum_daftar &&
    isset(
        $_SESSION['tes_verified_user'],
        $_SESSION['tes_started_at']
    ) &&
    (int) $_SESSION['tes_verified_user'] === $user_id
);

/* WAKTU TES 5 MENIT */
$batas_waktu = 300;

$waktu_mulai = $tes_terverifikasi
    ? (int) $_SESSION['tes_started_at']
    : 0;

$sisa_waktu = $tes_terverifikasi
    ? max(0, $batas_waktu - (time() - $waktu_mulai))
    : $batas_waktu;

/* AMBIL SOAL SESUAI JURUSAN */
$soal_data = [];

if (!$belum_daftar) {
    $jurusan = $maba['jurusan'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM soal WHERE jurusan = ? ORDER BY id ASC"
    );
    mysqli_stmt_bind_param($stmt, "s", $jurusan);
    mysqli_stmt_execute($stmt);

    $result_soal = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result_soal)) {
        $soal_data[] = $row;
    }
}

/* PROSES PENGUMPULAN TES */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['submit_tes']) &&
    !$belum_daftar &&
    !$hasil_lama &&
    $tes_terverifikasi
) {
    $waktu_habis = (time() - $waktu_mulai) >= $batas_waktu;

    if ($waktu_habis || isset($_POST['submit_tes'])) {
        $jumlah_benar = 0;
        $jumlah_soal = count($soal_data);
        $jawaban = $_POST['jawaban'] ?? [];

        foreach ($soal_data as $row) {
            $id_soal = (string) $row['id'];
            $jawaban_user = $jawaban[$id_soal] ?? '';

            if (
                $jawaban_user !== '' &&
                strtoupper((string) $jawaban_user) ===
                strtoupper((string) $row['jawaban_benar'])
            ) {
                $jumlah_benar++;
            }
        }

        $nilai = $jumlah_soal > 0
            ? (int) round(($jumlah_benar / $jumlah_soal) * 100)
            : 0;

        $status = $nilai >= 70 ? "Lulus" : "Tidak Lulus";

        if ($jumlah_soal === 0) {
            $pesan = "Soal tes belum tersedia.";
        } else {
            /*
             * Cegah penyimpanan hasil lebih dari sekali.
             */
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO hasil_tes (user_id, nilai, status)
                 SELECT ?, ?, ?
                 WHERE NOT EXISTS (
                     SELECT 1 FROM hasil_tes WHERE user_id = ?
                 )"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "iisi",
                $user_id,
                $nilai,
                $status,
                $user_id
            );

            if (
                mysqli_stmt_execute($stmt) &&
                mysqli_stmt_affected_rows($stmt) === 1
            ) {
                unset(
                    $_SESSION['tes_verified_user'],
                    $_SESSION['tes_started_at']
                );

                header("Location: hasil_tes.php");
                exit;
            } else {
                $pesan = "Hasil tes gagal disimpan atau tes sudah dikumpulkan.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tes Calon Mahasiswa - PMB</title>

    <link rel="stylesheet" href="../css/tes.css">
</head>

<body>

<div class="page">

    <!-- SIDEBAR -->
    <aside class="sidebar">

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
                <span>Penerimaan<br>Mahasiswa Baru</span>
            </div>
        </div>

        <nav class="menu">

            <a href="dashboard.php">
                <span>🏠</span> Dashboard
            </a>

            <a href="pendaftaran.php">
                <span>📋</span> Registrasi
            </a>

            <a href="tes.php" class="active">
                <span>🎓</span> Tes
            </a>

            <a href="hasil_tes.php">
                <span>📊</span> Hasil Tes
            </a>

            <a href="daftar_ulang.php">
                <span>📝</span> Daftar Ulang
            </a>

            <a href="nim.php">
                <span>🪪</span> NIM
            </a>

        </nav>

        <a href="../logout.php" class="logout">
            🚪 Logout
        </a>

    </aside>

    <!-- KONTEN UTAMA -->
    <main class="content">

        <div class="header">
            <div>
                <p>PMB / Tes</p>
                <h1>Tes Calon Mahasiswa</h1>
            </div>

            <a href="dashboard.php" class="back">
                ← Kembali
            </a>
        </div>

        <?php if ($pesan !== ""): ?>
            <div class="message error">
                <?= htmlspecialchars($pesan); ?>
            </div>
        <?php endif; ?>

        <!-- BELUM MENDAFTAR -->
        <?php if ($belum_daftar): ?>

            <div class="warning-card">
                <div class="warning-icon">📋</div>

                <h2>Registrasi Belum Selesai</h2>

                <p>
                    Lengkapi data pendaftaran terlebih dahulu
                    sebelum mengikuti tes.
                </p>

                <a href="pendaftaran.php">
                    LENGKAPI REGISTRASI
                </a>
            </div>

        <!-- TES SUDAH DIKERJAKAN -->
        <?php elseif ($hasil_lama): ?>

            <div class="warning-card">
                <div class="warning-icon">✅</div>

                <h2>Tes Sudah Dikerjakan</h2>

                <p>
                    Kamu sudah menyelesaikan tes penerimaan
                    mahasiswa baru.
                </p>

                <a href="hasil_tes.php">
                    LIHAT HASIL TES
                </a>
            </div>

        <!-- FORM VERIFIKASI NOMOR TES -->
        <?php elseif (!$tes_terverifikasi): ?>

            <div class="warning-card">

                <div class="warning-icon">🔐</div>

                <h2>Verifikasi Nomor Tes</h2>

                <p>
                    Masukkan nomor tes dari halaman Pendaftaran
                    untuk mulai mengerjakan tes.
                </p>

                <form
                    method="POST"
                    action="tes.php"
                    class="form-nomor-tes"
                >

                    <label for="nomor_tes">
                        Nomor Tes
                    </label>

                    <input
                        type="text"
                        id="nomor_tes"
                        name="nomor_tes"
                        placeholder="Masukkan nomor tes kamu"
                        required
                        autocomplete="off"
                    >

                    <button
                        type="submit"
                        name="verifikasi_nomor"
                        value="1"
                        class="submit-test"
                    >
                        MULAI TES
                    </button>

                </form>

            </div>

        <!-- HALAMAN PENGERJAAN TES -->
        <?php else: ?>

            <div class="test-info">

                <div>
                    <span>Nomor Tes</span>

                    <strong>
                        <?= htmlspecialchars($maba['nomor_tes']); ?>
                    </strong>
                </div>

                <div>
                    <span>Nama</span>

                    <strong>
                        <?= htmlspecialchars($maba['nama_lengkap']); ?>
                    </strong>
                </div>

            </div>

            <?php if (count($soal_data) === 0): ?>

                <div class="warning-card">
                    <div class="warning-icon">📝</div>

                    <h2>Soal Belum Tersedia</h2>

                    <p>
                        Admin belum menambahkan soal untuk jurusan kamu.
                    </p>
                </div>

            <?php else: ?>

                <form method="POST" id="formTes">

                    <div class="instruction">

                        <h3>
                            Waktu Tes:
                            <span id="timer">05:00</span>
                        </h3>

                        <p>
                            Waktu pengerjaan adalah 5 menit.
                            Jawaban akan dikumpulkan otomatis
                            ketika waktu habis.
                        </p>

                        <h3>Petunjuk Tes</h3>

                        <p>
                            Pilih satu jawaban yang paling benar
                            untuk setiap soal.
                        </p>

                    </div>

                    <?php $nomor = 1; ?>

                    <?php foreach ($soal_data as $row): ?>

                        <div class="question-card">

                            <div class="question-number">
                                Soal <?= $nomor++; ?>
                            </div>

                            <h3>
                                <?= htmlspecialchars($row['pertanyaan']); ?>
                            </h3>

                            <?php
                            $pilihan = [
                                'A' => 'pilihan_a',
                                'B' => 'pilihan_b',
                                'C' => 'pilihan_c',
                                'D' => 'pilihan_d'
                            ];
                            ?>

                            <?php foreach ($pilihan as $huruf => $kolom): ?>

                                <label class="answer">

                                    <input
                                        type="radio"
                                        name="jawaban[<?= (int) $row['id']; ?>]"
                                        value="<?= $huruf; ?>"
                                    >

                                    <span class="option">
                                        <?= $huruf; ?>
                                    </span>

                                    <span>
                                        <?= htmlspecialchars($row[$kolom]); ?>
                                    </span>

                                </label>

                            <?php endforeach; ?>

                        </div>

                    <?php endforeach; ?>

                    <button
                        type="submit"
                        name="submit_tes"
                        value="1"
                        class="submit-test"
                        onclick="return confirm('Yakin ingin mengumpulkan jawaban? Tes tidak dapat diulang setelah dikirim.');"
                    >
                        KUMPULKAN JAWABAN
                    </button>

                </form>

            <?php endif; ?>

        <?php endif; ?>

    </main>

</div>

<!-- TIMER TES -->
<?php
if (
    $tes_terverifikasi &&
    !$hasil_lama &&
    !$belum_daftar &&
    count($soal_data) > 0
):
?>

<script>
(function () {
    const timerElement = document.getElementById("timer");
    const formTes = document.getElementById("formTes");

    if (!timerElement || !formTes) {
        return;
    }

    let sisa = <?= (int) $sisa_waktu ?>;
    let sudahKirim = false;

    function kumpulkanOtomatis() {
        if (sudahKirim) {
            return;
        }

        sudahKirim = true;

        const tombol = document.createElement("input");
        tombol.type = "hidden";
        tombol.name = "submit_tes";
        tombol.value = "1";

        formTes.appendChild(tombol);

        HTMLFormElement.prototype.submit.call(formTes);
    }

    function tampilkanWaktu() {
        const menit = Math.floor(sisa / 60);
        const detik = sisa % 60;

        timerElement.textContent =
            String(menit).padStart(2, "0") + ":" +
            String(detik).padStart(2, "0");

        if (sisa <= 0) {
            timerElement.textContent = "00:00";
            kumpulkanOtomatis();
            return;
        }

        sisa--;

        window.setTimeout(tampilkanWaktu, 1000);
    }

    tampilkanWaktu();
})();
</script>

<?php endif; ?>

</body>
</html>
