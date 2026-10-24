<?php
require_once "config/koneksi.php";

$nama = "Administrator";
$email = "admin@pmb.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);

$query = mysqli_prepare(
    $conn,
    "INSERT INTO admin (nama, email, password)
     VALUES (?, ?, ?)"
);

mysqli_stmt_bind_param(
    $query,
    "sss",
    $nama,
    $email,
    $password
);

if (mysqli_stmt_execute($query)) {
    echo "Admin berhasil dibuat.";
} else {
    echo "Gagal: " . mysqli_error($conn);
}
?>