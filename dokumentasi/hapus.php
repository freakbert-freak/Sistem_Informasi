<?php

require '../config/koneksi.php';
require '../config/auth.php';

wajib_login();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Metode request tidak valid.");
}

$id_dokumentasi =
    (int) ($_POST["id_dokumentasi"] ?? 0);

$id_servis =
    (int) ($_POST["id_servis"] ?? 0);

if (
    $id_dokumentasi <= 0 ||
    $id_servis <= 0
) {
    die("Data dokumentasi tidak valid.");
}

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT
        id_dokumentasi,
        id_servis,
        id_teknisi,
        foto

     FROM dokumentasi_servis

     WHERE id_dokumentasi = ?
     AND id_servis = ?

     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $id_dokumentasi,
    $id_servis
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$data =
    mysqli_fetch_assoc($result);

if (!$data) {
    die("Dokumentasi tidak ditemukan.");
}


/*
 * Admin boleh hapus semuanya.
 * Teknisi hanya dokumentasinya sendiri.
 */
if (
    $_SESSION["role"] !== "admin" &&
    (
        $_SESSION["role"] !== "teknisi" ||
        (int) $data["id_teknisi"] !==
        (int) $_SESSION["id_teknisi"]
    )
) {
    die("Anda tidak mempunyai akses.");
}


/*
 * Teknisi juga harus memastikan
 * servis tersebut memang miliknya.
 */
if ($_SESSION["role"] === "teknisi") {

    $cek_servis = mysqli_prepare(
        $koneksi,
        "SELECT id_servis
         FROM servis
         WHERE id_servis = ?
         AND id_teknisi = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $cek_servis,
        "ii",
        $id_servis,
        $_SESSION["id_teknisi"]
    );

    mysqli_stmt_execute($cek_servis);

    $hasil_servis =
        mysqli_stmt_get_result($cek_servis);

    if (!mysqli_fetch_assoc($hasil_servis)) {
        die("Anda tidak mempunyai akses.");
    }
}


/*
 * Hapus record database.
 */
$hapus = mysqli_prepare(
    $koneksi,
    "DELETE FROM dokumentasi_servis
     WHERE id_dokumentasi = ?"
);

mysqli_stmt_bind_param(
    $hapus,
    "i",
    $id_dokumentasi
);

if (!mysqli_stmt_execute($hapus)) {
    die("Gagal menghapus dokumentasi.");
}


/*
 * Baru hapus file fisik.
 */
$path =
    "../" . $data["foto"];

if (
    file_exists($path) &&
    is_file($path)
) {
    unlink($path);
}

header(
    "Location: ../servis/detail.php?id="
    . $id_servis
);

exit;