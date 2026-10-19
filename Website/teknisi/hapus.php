<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$id_teknisi = (int) ($_GET["id"] ?? 0);

if ($id_teknisi <= 0) {
    die("ID teknisi tidak valid.");
}


/*
 * Cek servis.
 */
$cek_servis = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM servis
     WHERE id_teknisi = ?"
);

mysqli_stmt_bind_param(
    $cek_servis,
    "i",
    $id_teknisi
);

mysqli_stmt_execute($cek_servis);

$result_servis =
    mysqli_stmt_get_result($cek_servis);

$data_servis =
    mysqli_fetch_assoc($result_servis);

$jumlah_servis =
    (int) $data_servis["jumlah"];


/*
 * Cek dokumentasi.
 */
$cek_dokumentasi = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM dokumentasi_servis
     WHERE id_teknisi = ?"
);

mysqli_stmt_bind_param(
    $cek_dokumentasi,
    "i",
    $id_teknisi
);

mysqli_stmt_execute($cek_dokumentasi);

$result_dokumentasi =
    mysqli_stmt_get_result($cek_dokumentasi);

$data_dokumentasi =
    mysqli_fetch_assoc($result_dokumentasi);

$jumlah_dokumentasi =
    (int) $data_dokumentasi["jumlah"];


/*
 * Kalau masih dipakai, jangan hapus.
 */
if (
    $jumlah_servis > 0 ||
    $jumlah_dokumentasi > 0
) {

    echo "Teknisi tidak dapat dihapus karena masih mempunyai riwayat pekerjaan.";

    echo "<br><br>";

    echo "Servis terkait: "
        . $jumlah_servis;

    echo "<br>";

    echo "Dokumentasi terkait: "
        . $jumlah_dokumentasi;

    echo "<br><br>";

    echo "Data teknisi tetap disimpan untuk menjaga histori servis.";

    echo "<br><br>";

    echo '<a href="index.php">Kembali</a>';

    exit;
}


/*
 * Kalau tidak memiliki relasi, hapus.
 */
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM teknisi
     WHERE id_teknisi = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_teknisi
);

try {

    mysqli_stmt_execute($stmt);

    header("Location: index.php");
    exit;

} catch (mysqli_sql_exception $e) {

    echo "Teknisi tidak dapat dihapus karena masih digunakan oleh data lain.";

    echo "<br><br>";

    echo '<a href="index.php">Kembali</a>';
}