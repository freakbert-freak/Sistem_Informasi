<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$id_user = (int) ($_GET["id"] ?? 0);

if ($id_user <= 0) {
    die("ID user tidak valid.");
}

if ($id_user === (int) $_SESSION["id_user"]) {

    echo "Akun yang sedang digunakan tidak dapat dihapus.";

    echo "<br><br>";

    echo '<a href="index.php">Kembali</a>';

    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM users
     WHERE id_user = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_user
);

try {

    mysqli_stmt_execute($stmt);

    header("Location: index.php");
    exit;

} catch (mysqli_sql_exception $e) {

    echo "Akun tidak dapat dihapus karena masih digunakan oleh data lain.";

    echo "<br><br>";

    echo '<a href="index.php">Kembali</a>';
}