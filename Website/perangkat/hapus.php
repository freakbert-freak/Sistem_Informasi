<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$id_perangkat = (int) ($_GET["id"] ?? 0);

if ($id_perangkat <= 0) {
    die("ID perangkat tidak valid.");
}

$cek = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM servis
     WHERE id_perangkat = ?"
);

mysqli_stmt_bind_param(
    $cek,
    "i",
    $id_perangkat
);

mysqli_stmt_execute($cek);

$result = mysqli_stmt_get_result($cek);
$data = mysqli_fetch_assoc($result);

if ((int) $data["jumlah"] > 0) {

    echo "Perangkat tidak dapat dihapus karena sudah mempunyai riwayat servis.";

    echo "<br><br>";

    echo "Jumlah servis terkait: "
        . (int) $data["jumlah"];

    echo "<br><br>";

    echo '<a href="index.php">Kembali</a>';

    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM perangkat
     WHERE id_perangkat = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_perangkat
);

try {

    mysqli_stmt_execute($stmt);

    header("Location: index.php");
    exit;

} catch (mysqli_sql_exception $e) {

    echo "Perangkat tidak dapat dihapus karena masih digunakan oleh data lain.";

    echo "<br><br>";

    echo '<a href="index.php">Kembali</a>';
}