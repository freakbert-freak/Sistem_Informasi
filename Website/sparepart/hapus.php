<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$id_sparepart = (int) ($_GET["id"] ?? 0);

if ($id_sparepart <= 0) {
    die("ID sparepart tidak valid.");
}

$cek = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM detail_servis
     WHERE id_sparepart = ?"
);

mysqli_stmt_bind_param(
    $cek,
    "i",
    $id_sparepart
);

mysqli_stmt_execute($cek);

$result = mysqli_stmt_get_result($cek);
$data = mysqli_fetch_assoc($result);

if ((int) $data["jumlah"] > 0) {

    echo "Sparepart tidak dapat dihapus karena sudah pernah digunakan pada servis.";

    echo "<br><br>";

    echo "Jumlah detail servis terkait: "
        . (int) $data["jumlah"];

    echo "<br><br>";

    echo "Data sparepart harus tetap disimpan untuk menjaga histori servis.";

    echo "<br><br>";

    echo '<a href="index.php">Kembali</a>';

    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM sparepart
     WHERE id_sparepart = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_sparepart
);

try {

    mysqli_stmt_execute($stmt);

    header("Location: index.php");
    exit;

} catch (mysqli_sql_exception $e) {

    echo "Sparepart tidak dapat dihapus karena masih digunakan oleh data lain.";

    echo "<br><br>";

    echo '<a href="index.php">Kembali</a>';
}