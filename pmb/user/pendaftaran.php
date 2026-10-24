<?php
session_start();
require_once "../config/koneksi.php";

/* =========================================
   CEK LOGIN
========================================= */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$pesan = "";
$tipe = "";


/* =========================================
   AMBIL DATA CALON MABA
========================================= */

$cekData = mysqli_prepare(
    $conn,
    "SELECT * FROM calon_maba WHERE user_id = ?"
);

mysqli_stmt_bind_param(
    $cekData,
    "i",
    $user_id
);

mysqli_stmt_execute($cekData);

$hasilData = mysqli_stmt_get_result($cekData);

$data = mysqli_fetch_assoc($hasilData);

mysqli_stmt_close($cekData);


/* =========================================
   PROSES SIMPAN DATA
========================================= */

if (isset($_POST['simpan'])) {

    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $asal_sekolah = trim($_POST['asal_sekolah'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    /*
     * Kalau sebelumnya sudah punya foto,
     * gunakan foto lama terlebih dahulu.
     */
    $foto = $data['foto'] ?? null;


    /* =========================================
       VALIDASI DATA
    ========================================= */

    if (
        $nama_lengkap == "" ||
        $tempat_lahir == "" ||
        $tanggal_lahir == "" ||
        $jenis_kelamin == "" ||
        $alamat == "" ||
        $asal_sekolah == "" ||
        $jurusan == ""
    ) {

        $pesan = "Semua data wajib diisi!";
        $tipe = "error";

    } else {


        /* =========================================
           PROSES UPLOAD FOTO
        ========================================= */

        $uploadBerhasil = true;

        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['foto']['error'] != UPLOAD_ERR_OK) {

                $pesan = "Terjadi kesalahan saat mengupload foto.";
                $tipe = "error";

                $uploadBerhasil = false;

            } else {

                $namaFile = $_FILES['foto']['name'];
                $tmpFile = $_FILES['foto']['tmp_name'];
                $ukuranFile = $_FILES['foto']['size'];

                $extension = strtolower(
                    pathinfo(
                        $namaFile,
                        PATHINFO_EXTENSION
                    )
                );


                /* FORMAT YANG DIIZINKAN */

                $formatDiizinkan = [
                    'jpg',
                    'jpeg',
                    'png',
                    'webp'
                ];


                if (!in_array($extension, $formatDiizinkan)) {

                    $pesan = "Foto harus berformat JPG, JPEG, PNG, atau WEBP.";
                    $tipe = "error";

                    $uploadBerhasil = false;

                } elseif ($ukuranFile > 2 * 1024 * 1024) {

                    $pesan = "Ukuran foto maksimal 2 MB.";
                    $tipe = "error";

                    $uploadBerhasil = false;

                } else {


                    /* =========================================
                       CEK FILE BENAR-BENAR GAMBAR
                    ========================================= */

                    $cekGambar = @getimagesize($tmpFile);

                    if ($cekGambar === false) {

                        $pesan = "File yang dipilih bukan gambar yang valid.";
                        $tipe = "error";

                        $uploadBerhasil = false;

                    } else {


                        /* =========================================
                           FOLDER FOTO
                        ========================================= */

                        $folderFoto = "../uploads/foto/";

                        if (!is_dir($folderFoto)) {

                            mkdir(
                                $folderFoto,
                                0777,
                                true
                            );
                        }


                        /* =========================================
                           NAMA FOTO BARU
                        ========================================= */

                        $namaFotoBaru =
                            "siswa_" .
                            $user_id .
                            "_" .
                            time() .
                            "." .
                            $extension;


                        $lokasiFoto =
                            $folderFoto .
                            $namaFotoBaru;


                        /* =========================================
                           PINDAHKAN FOTO
                        ========================================= */

                        if (
                            move_uploaded_file(
                                $tmpFile,
                                $lokasiFoto
                            )
                        ) {


                            /*
                             * Hapus foto lama jika user
                             * mengganti foto.
                             */

                            if (
                                !empty($foto) &&
                                file_exists(
                                    $folderFoto . $foto
                                )
                            ) {

                                unlink(
                                    $folderFoto . $foto
                                );
                            }


                            $foto = $namaFotoBaru;


                        } else {

                            $pesan = "Foto gagal disimpan ke folder uploads.";
                            $tipe = "error";

                            $uploadBerhasil = false;
                        }
                    }
                }
            }
        }


        /* =========================================
           LANJUT JIKA UPLOAD AMAN
        ========================================= */

        if ($uploadBerhasil) {


            /* =========================================
               DATA BELUM ADA → INSERT
            ========================================= */

            if (!$data) {


                /* FOTO WAJIB SAAT PERTAMA DAFTAR */

                if (empty($foto)) {

                    $pesan = "Silakan upload foto siswa terlebih dahulu.";
                    $tipe = "error";

                } else {


                    /* =========================================
                       BUAT NOMOR TES
                    ========================================= */

                    $nomor_tes =
                        "PMB" .
                        date("Y") .
                        str_pad(
                            $user_id,
                            4,
                            "0",
                            STR_PAD_LEFT
                        );


                    /* =========================================
                       INSERT DATABASE
                    ========================================= */

                    $query = mysqli_prepare(
                        $conn,
                        "INSERT INTO calon_maba
                        (
                            user_id,
                            nomor_tes,
                            nama_lengkap,
                            tempat_lahir,
                            tanggal_lahir,
                            jenis_kelamin,
                            alamat,
                            asal_sekolah,
                            jurusan,
                            foto,
                            status_pendaftaran
                        )
                        VALUES
                        (
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                            'Sudah Lengkap'
                        )"
                    );


                    mysqli_stmt_bind_param(
                        $query,
                        "isssssssss",
                        $user_id,
                        $nomor_tes,
                        $nama_lengkap,
                        $tempat_lahir,
                        $tanggal_lahir,
                        $jenis_kelamin,
                        $alamat,
                        $asal_sekolah,
                        $jurusan,
                        $foto
                    );


                    if (mysqli_stmt_execute($query)) {

                        $pesan = "Data berhasil disimpan dan nomor tes berhasil dibuat!";
                        $tipe = "success";

                    } else {

                        $pesan =
                            "Data gagal disimpan: " .
                            mysqli_error($conn);

                        $tipe = "error";
                    }


                    mysqli_stmt_close($query);
                }


            } else {


                /* =========================================
                   DATA SUDAH ADA → UPDATE
                ========================================= */

                $query = mysqli_prepare(
                    $conn,
                    "UPDATE calon_maba SET

                        nama_lengkap = ?,
                        tempat_lahir = ?,
                        tanggal_lahir = ?,
                        jenis_kelamin = ?,
                        alamat = ?,
                        asal_sekolah = ?,
                        jurusan = ?,
                        foto = ?,
                        status_pendaftaran = 'Sudah Lengkap'

                     WHERE user_id = ?"
                );


                mysqli_stmt_bind_param(
                    $query,
                    "ssssssssi",
                    $nama_lengkap,
                    $tempat_lahir,
                    $tanggal_lahir,
                    $jenis_kelamin,
                    $alamat,
                    $asal_sekolah,
                    $jurusan,
                    $foto,
                    $user_id
                );


                if (mysqli_stmt_execute($query)) {

                    $pesan = "Data berhasil diperbarui!";
                    $tipe = "success";

                } else {

                    $pesan =
                        "Data gagal diperbarui: " .
                        mysqli_error($conn);

                    $tipe = "error";
                }


                mysqli_stmt_close($query);
            }
        }
    }


    /* =========================================
       AMBIL ULANG DATA TERBARU
    ========================================= */

    $cekData = mysqli_prepare(
        $conn,
        "SELECT * FROM calon_maba WHERE user_id = ?"
    );

    mysqli_stmt_bind_param(
        $cekData,
        "i",
        $user_id
    );

    mysqli_stmt_execute($cekData);

    $hasilData = mysqli_stmt_get_result($cekData);

    $data = mysqli_fetch_assoc($hasilData);

    mysqli_stmt_close($cekData);
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
        Registrasi Calon Maba - PMB
    </title>

    <link
        rel="stylesheet"
        href="../css/pendaftaran.css"
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

    <nav class="menu">
        <a href="dashboard.php">
            <span>🏠</span> Dashboard
        </a>

        <a href="pendaftaran.php" class="active">
            <span>📋</span> Registrasi
        </a>

        <a href="tes.php">
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


    <!-- =========================================
         CONTENT
    ========================================== -->

    <main class="content">


        <!-- HEADER -->

        <div class="header">


            <div>

                <p>
                    PMB / Registrasi
                </p>

                <h1>
                    Registrasi Calon Mahasiswa
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

        <?php if ($pesan != ""): ?>

            <div class="message <?= $tipe ?>">

                <?= htmlspecialchars($pesan); ?>

            </div>

        <?php endif; ?>



        <!-- =========================================
             NOMOR TES
        ========================================== -->

<?php
// Ambil nomor tes milik pengguna yang sedang login
$nomorTes = '';

if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];

    $stmtNomor = mysqli_prepare(
        $conn,
        "SELECT nomor_tes
         FROM calon_maba
         WHERE user_id = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param($stmtNomor, "i", $user_id);
    mysqli_stmt_execute($stmtNomor);

    $resultNomor = mysqli_stmt_get_result($stmtNomor);
    $dataNomor = mysqli_fetch_assoc($resultNomor);

    if ($dataNomor) {
        $nomorTes = $dataNomor['nomor_tes'] ?? '';
    }

    mysqli_stmt_close($stmtNomor);
}
?>

<div class="nomor-tes-card">
    <div class="nomor-tes-info">
        <span>Nomor Tes Kamu</span>

        <?php if ($nomorTes !== ''): ?>

            <strong id="nomorTes">
                <?= htmlspecialchars($nomorTes); ?>
            </strong>

            <button
                type="button"
                class="btn-salin-nomor"
                onclick="salinNomorTes()"
            >
                📋 Salin Nomor Tes
            </button>

        <?php else: ?>

            <strong>Nomor tes belum tersedia</strong>

            <p>
                Pastikan kamu sudah menyelesaikan pendaftaran.
            </p>

        <?php endif; ?>
    </div>

    <div class="nomor-tes-check">✓</div>
</div>

<script>
function salinNomorTes() {
    const elemen = document.getElementById("nomorTes");

    if (!elemen) {
        alert("Nomor tes belum tersedia.");
        return;
    }

    const nomor = elemen.textContent.trim();

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(nomor)
            .then(() => {
                alert("Nomor tes berhasil disalin: " + nomor);
            })
            .catch(() => salinManual(nomor));
    } else {
        salinManual(nomor);
    }
}

function salinManual(nomor) {
    const input = document.createElement("textarea");
    input.value = nomor;
    input.style.position = "fixed";
    input.style.opacity = "0";

    document.body.appendChild(input);
    input.select();

    try {
        const berhasil = document.execCommand("copy");

        alert(berhasil
            ? "Nomor tes berhasil disalin: " + nomor
            : "Silakan salin nomor tes secara manual: " + nomor
        );
    } catch (error) {
        alert("Silakan salin nomor tes secara manual: " + nomor);
    }

    input.remove();
}
</script>


        <!-- =========================================
             FORM
        ========================================== -->

        <div class="form-card">


            <div class="form-title">

                <h2>
                    Data Diri
                </h2>

                <p>
                    Lengkapi data diri kamu dengan benar.
                </p>

            </div>



            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- =================================
                     FOTO SISWA
                ================================== -->

                <div class="form-group full">

                    <label>
                        Foto Siswa
                    </label>


                    <?php if (
                        !empty($data['foto'])
                    ): ?>


                        <div class="preview-foto">

                            <img
                                src="../uploads/foto/<?= htmlspecialchars(
                                    $data['foto']
                                ); ?>"
                                alt="Foto Siswa"
                            >

                        </div>


                        <p class="foto-terpasang">
                            Foto saat ini
                        </p>


                    <?php else: ?>


                        <div class="foto-kosong">

                            <span>
                                Belum ada foto
                            </span>

                        </div>


                    <?php endif; ?>


                    <input
                        type="file"
                        name="foto"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        <?= empty($data['foto'])
                            ? 'required'
                            : ''; ?>
                    >


                    <small class="foto-info">

                        Format JPG, JPEG, PNG atau WEBP.
                        Maksimal 2 MB.

                    </small>

                </div>



                <!-- =================================
                     NAMA
                ================================== -->

                <div class="form-group full">

                    <label>
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="nama_lengkap"
                        placeholder="Masukkan nama lengkap"

                        value="<?= htmlspecialchars(
                            $data['nama_lengkap']
                            ?? $_SESSION['nama']
                        ); ?>"

                        required
                    >

                </div>



                <!-- =================================
                     TEMPAT & TANGGAL LAHIR
                ================================== -->

                <div class="form-row">


                    <div class="form-group">

                        <label>
                            Tempat Lahir
                        </label>

                        <input
                            type="text"
                            name="tempat_lahir"
                            placeholder="Contoh: Jakarta"

                            value="<?= htmlspecialchars(
                                $data['tempat_lahir']
                                ?? ''
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
                                ?? ''
                            ); ?>"

                            required
                        >

                    </div>


                </div>



                <!-- =================================
                     JENIS KELAMIN
                ================================== -->

                <div class="form-group full">

                    <label>
                        Jenis Kelamin
                    </label>


                    <select
                        name="jenis_kelamin"
                        required
                    >

                        <option value="">
                            Pilih jenis kelamin
                        </option>


                        <option
                            value="L"

                            <?= isset(
                                $data['jenis_kelamin']
                            ) &&
                            $data['jenis_kelamin'] == 'L'
                                ? 'selected'
                                : ''; ?>
                        >
                            Laki-laki
                        </option>


                        <option
                            value="P"

                            <?= isset(
                                $data['jenis_kelamin']
                            ) &&
                            $data['jenis_kelamin'] == 'P'
                                ? 'selected'
                                : ''; ?>
                        >
                            Perempuan
                        </option>

                    </select>

                </div>



                <!-- =================================
                     ALAMAT
                ================================== -->

                <div class="form-group full">

                    <label>
                        Alamat
                    </label>

                    <textarea
                        name="alamat"
                        placeholder="Masukkan alamat lengkap"
                        required
                    ><?= htmlspecialchars(
                        $data['alamat'] ?? ''
                    ); ?></textarea>

                </div>



                <!-- =================================
                     SEKOLAH & JURUSAN
                ================================== -->

                <div class="form-row">


                    <div class="form-group">

                        <label>
                            Asal Sekolah
                        </label>

                        <input
                            type="text"
                            name="asal_sekolah"
                            placeholder="Nama sekolah"

                            value="<?= htmlspecialchars(
                                $data['asal_sekolah']
                                ?? ''
                            ); ?>"

                            required
                        >

                    </div>



                    <div class="form-group">

                        <label>
                            Pilihan Jurusan
                        </label>


                        <select
                            name="jurusan"
                            required
                        >

                            <option value="">
                                Pilih jurusan
                            </option>


                            <option
                                value="Teknik Informatika"

                                <?= isset($data['jurusan'])
                                &&
                                $data['jurusan']
                                == 'Teknik Informatika'

                                    ? 'selected'
                                    : ''; ?>
                            >
                                Teknik Informatika
                            </option>


                            <option
                                value="Sistem Informasi"

                                <?= isset($data['jurusan'])
                                &&
                                $data['jurusan']
                                == 'Sistem Informasi'

                                    ? 'selected'
                                    : ''; ?>
                            >
                                Sistem Informasi
                            </option>


                            <option
                                value="Manajemen"

                                <?= isset($data['jurusan'])
                                &&
                                $data['jurusan']
                                == 'Manajemen'

                                    ? 'selected'
                                    : ''; ?>
                            >
                                Manajemen
                            </option>


                            <option
                                value="Akuntansi"

                                <?= isset($data['jurusan'])
                                &&
                                $data['jurusan']
                                == 'Akuntansi'

                                    ? 'selected'
                                    : ''; ?>
                            >
                                Akuntansi
                            </option>


                        </select>

                    </div>


                </div>



                <!-- =================================
                     BUTTON
                ================================== -->

                <button
                    type="submit"
                    name="simpan"
                    class="save"
                >

                    <?= $data
                        ? 'SIMPAN PERUBAHAN'
                        : 'SIMPAN DATA'; ?>

                </button>


            </form>


        </div>


    </main>


</div>


</body>

</html>