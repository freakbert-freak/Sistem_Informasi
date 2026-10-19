<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$id_pelanggan = (int) ($_GET["id"] ?? 0);

if ($id_pelanggan <= 0) {
    die("ID pelanggan tidak valid.");
}

/*
 * Cek apakah pelanggan masih mempunyai perangkat.
 */
$cek = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS jumlah
     FROM perangkat
     WHERE id_pelanggan = ?"
);

mysqli_stmt_bind_param(
    $cek,
    "i",
    $id_pelanggan
);

mysqli_stmt_execute($cek);

$result = mysqli_stmt_get_result($cek);

$data = mysqli_fetch_assoc($result);

if ((int) $data["jumlah"] > 0) {

    echo "Pelanggan tidak dapat dihapus karena masih mempunyai perangkat.";

    echo "<br><br>";

    echo "Hapus perangkat terlebih dahulu jika perangkat tersebut belum memiliki histori servis.";

    echo "<br><br>";

    echo '<a href="index.php">Kembali</a>';

    exit;
}

/*
 * Kalau tidak mempunyai perangkat,
 * pelanggan boleh dihapus.
 */
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM pelanggan
     WHERE id_pelanggan = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_pelanggan
);

if (mysqli_stmt_execute($stmt)) {

    header("Location: index.php");
    exit;

} else {

    die(
        "Gagal menghapus pelanggan: "
        . mysqli_error($koneksi)
    );
}