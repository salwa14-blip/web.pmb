<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";

if (isset($_POST['simpan'])) {

    $jurusan = trim($_POST['jurusan'] ?? '');
    $pertanyaan = trim($_POST['pertanyaan'] ?? '');

    $pilihan_a = trim($_POST['pilihan_a'] ?? '');
    $pilihan_b = trim($_POST['pilihan_b'] ?? '');
    $pilihan_c = trim($_POST['pilihan_c'] ?? '');
    $pilihan_d = trim($_POST['pilihan_d'] ?? '');

    $jawaban_benar =
        strtoupper(trim($_POST['jawaban_benar'] ?? ''));


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

    } elseif (
        !in_array(
            $jawaban_benar,
            ['A', 'B', 'C', 'D']
        )
    ) {

        $pesan = "Jawaban benar tidak valid.";

    } else {

        $query = mysqli_prepare(
            $conn,
            "INSERT INTO soal
            (
                jurusan,
                pertanyaan,
                pilihan_a,
                pilihan_b,
                pilihan_c,
                pilihan_d,
                jawaban_benar
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $query,
            "sssssss",
            $jurusan,
            $pertanyaan,
            $pilihan_a,
            $pilihan_b,
            $pilihan_c,
            $pilihan_d,
            $jawaban_benar
        );


        if (mysqli_stmt_execute($query)) {

            header(
                "Location: soal.php?status=tambah"
            );
            exit;

        } else {

            $pesan = "Soal gagal ditambahkan.";
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

    <title>Tambah Soal - Admin PMB</title>

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
                    PMB / Admin / Soal / Tambah
                </p>

                <h1>
                    Tambah Soal Tes
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

                        <option value="">
                            Pilih Jurusan
                        </option>

                        <option value="Teknik Informatika">
                            Teknik Informatika
                        </option>

                        <option value="Sistem Informasi">
                            Sistem Informasi
                        </option>

                        <option value="Manajemen">
                            Manajemen
                        </option>

                        <option value="Akuntansi">
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
                        placeholder="Masukkan pertanyaan..."
                        required
                    ></textarea>

                </div>



                <div class="form-row">


                    <div class="form-group">

                        <label>
                            Pilihan A
                        </label>

                        <input
                            type="text"
                            name="pilihan_a"
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

                        <option value="">
                            Pilih Jawaban
                        </option>

                        <option value="A">
                            A
                        </option>

                        <option value="B">
                            B
                        </option>

                        <option value="C">
                            C
                        </option>

                        <option value="D">
                            D
                        </option>

                    </select>

                </div>



                <button
                    type="submit"
                    name="simpan"
                >
                    SIMPAN SOAL
                </button>


            </form>

        </div>

    </main>

</div>

</body>
</html>