<?php
session_start();
require_once "../config/koneksi.php";

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
    "SELECT *
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
    die("Data admin tidak ditemukan.");
}


/* =========================================
   PROSES UPDATE
========================================= */

if (isset($_POST['simpan'])) {

    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $foto = $admin['foto'] ?? '';


    /* =========================================
       VALIDASI
    ========================================= */

    if (
        $nama == "" ||
        $username == "" ||
        $email == ""
    ) {

        $pesan = "Semua data wajib diisi!";
        $tipe = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $pesan = "Format email tidak valid!";
        $tipe = "error";

    } else {


        /* =========================================
           CEK EMAIL DUPLIKAT
        ========================================= */

        $cekEmail = mysqli_prepare(
            $conn,
            "SELECT id
             FROM admin
             WHERE email = ?
             AND id != ?"
        );

        mysqli_stmt_bind_param(
            $cekEmail,
            "si",
            $email,
            $admin_id
        );

        mysqli_stmt_execute($cekEmail);

        $hasilEmail =
            mysqli_stmt_get_result($cekEmail);


        if (
            mysqli_num_rows($hasilEmail) > 0
        ) {

            $pesan =
                "Email sudah digunakan admin lain.";

            $tipe = "error";

        } else {


            /* =========================================
               UPLOAD FOTO
            ========================================= */

            if (
                isset($_FILES['foto']) &&
                $_FILES['foto']['error']
                != UPLOAD_ERR_NO_FILE
            ) {

                if (
                    $_FILES['foto']['error']
                    != UPLOAD_ERR_OK
                ) {

                    $pesan =
                        "Terjadi kesalahan saat upload foto.";

                    $tipe = "error";

                } else {

                    $namaFile =
                        $_FILES['foto']['name'];

                    $tmpFile =
                        $_FILES['foto']['tmp_name'];

                    $ukuranFile =
                        $_FILES['foto']['size'];


                    $ext = strtolower(
                        pathinfo(
                            $namaFile,
                            PATHINFO_EXTENSION
                        )
                    );


                    $allowed = [
                        'jpg',
                        'jpeg',
                        'png',
                        'webp'
                    ];


                    if (
                        !in_array(
                            $ext,
                            $allowed
                        )
                    ) {

                        $pesan =
                            "Foto harus JPG, JPEG, PNG, atau WEBP.";

                        $tipe = "error";

                    } elseif (
                        $ukuranFile
                        > 2 * 1024 * 1024
                    ) {

                        $pesan =
                            "Ukuran foto maksimal 2 MB.";

                        $tipe = "error";

                    } else {


                        /* CEK GAMBAR */

                        $cekGambar =
                            @getimagesize(
                                $tmpFile
                            );


                        if (
                            $cekGambar === false
                        ) {

                            $pesan =
                                "File yang dipilih bukan gambar valid.";

                            $tipe = "error";

                        } else {


                            /* FOLDER */

                            $folder =
                                "../uploads/admin/";


                            if (
                                !is_dir($folder)
                            ) {

                                mkdir(
                                    $folder,
                                    0777,
                                    true
                                );
                            }


                            /* NAMA FILE BARU */

                            $fotoBaru =
                                "admin_" .
                                $admin_id .
                                "_" .
                                time() .
                                "." .
                                $ext;


                            $lokasi =
                                $folder .
                                $fotoBaru;


                            if (
                                move_uploaded_file(
                                    $tmpFile,
                                    $lokasi
                                )
                            ) {


                                /* HAPUS FOTO LAMA */

                                if (
                                    !empty($foto) &&
                                    is_file(
                                        $folder .
                                        basename($foto)
                                    )
                                ) {

                                    unlink(
                                        $folder .
                                        basename($foto)
                                    );
                                }


                                $foto = $fotoBaru;

                            } else {

                                $pesan =
                                    "Foto gagal disimpan.";

                                $tipe = "error";
                            }
                        }
                    }
                }
            }


            /* =========================================
               UPDATE DATABASE
            ========================================= */

            if ($tipe != "error") {

                $update =
                    mysqli_prepare(
                        $conn,
                        "UPDATE admin
                         SET
                            nama = ?,
                            username = ?,
                            email = ?,
                            foto = ?
                         WHERE id = ?"
                    );


                mysqli_stmt_bind_param(
                    $update,
                    "ssssi",
                    $nama,
                    $username,
                    $email,
                    $foto,
                    $admin_id
                );


                if (
                    mysqli_stmt_execute(
                        $update
                    )
                ) {

                    $_SESSION['admin_nama'] =
                        $nama;

                    $_SESSION['admin_email'] =
                        $email;

                    $_SESSION['admin_foto'] =
                        $foto;


                    header(
                        "Location: profil.php?status=berhasil"
                    );

                    exit;

                } else {

                    $pesan =
                        "Profil gagal diperbarui.";

                    $tipe = "error";
                }
            }
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

    <title>
        Edit Profil Admin - PMB
    </title>

    <link
        rel="stylesheet"
        href="../css/admin_profil.css"
    >

</head>

<body>

<div class="page">


    <!-- SIDEBAR -->

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



    <!-- CONTENT -->

    <main class="content">


        <div class="header">

            <div>

                <p>
                    PMB / Admin / Profil / Edit
                </p>

                <h1>
                    Edit Profil Admin
                </h1>

            </div>


            <a
                href="profil.php"
                class="back"
            >
                ← Kembali
            </a>

        </div>



        <?php if ($pesan != ""): ?>

            <div
                class="message <?= $tipe; ?>"
            >

                <?= htmlspecialchars(
                    $pesan
                ); ?>

            </div>

        <?php endif; ?>



        <div class="edit-card">


            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- FOTO -->

                <div class="photo-section">


                    <div class="big-avatar">


                        <?php if (
                            !empty($admin['foto'])
                        ): ?>

                            <img
                                src="../uploads/admin/<?= htmlspecialchars(
                                    $admin['foto']
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



                    <div class="photo-input">


                        <label>
                            Foto Profil
                        </label>


                        <input
                            type="file"
                            name="foto"
                            accept=".jpg,.jpeg,.png,.webp"
                        >


                        <small>
                            JPG, JPEG, PNG atau WEBP.
                            Maksimal 2 MB.
                        </small>


                    </div>


                </div>



                <!-- NAMA -->

                <div class="form-group">

                    <label>
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="nama"

                        value="<?= htmlspecialchars(
                            $admin['nama']
                        ); ?>"

                        required
                    >

                </div>



                <!-- USERNAME -->

                <div class="form-group">

                    <label>
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"

                        value="<?= htmlspecialchars(
                            $admin['username']
                            ?? ''
                        ); ?>"

                        required
                    >

                </div>



                <!-- EMAIL -->

                <div class="form-group">

                    <label>
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"

                        value="<?= htmlspecialchars(
                            $admin['email']
                        ); ?>"

                        required
                    >

                </div>



                <button
                    type="submit"
                    name="simpan"
                    class="save-button"
                >
                    SIMPAN PERUBAHAN
                </button>


            </form>


        </div>


    </main>


</div>

</body>

</html>