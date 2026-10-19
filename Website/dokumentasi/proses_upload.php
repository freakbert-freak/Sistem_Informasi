<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_teknisi();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Metode request tidak valid.");
}

$id_servis =
    (int) ($_POST["id_servis"] ?? 0);

$id_teknisi =
    (int) $_SESSION["id_teknisi"];

$jenis_foto =
    $_POST["jenis_foto"] ?? "";

$id_detail_input =
    $_POST["id_detail"] ?? "";

$keterangan =
    trim($_POST["keterangan"] ?? "");

$tampil_pelanggan =
    isset($_POST["tampil_pelanggan"])
        ? 1
        : 0;

$jenis_valid = [
    "kerusakan",
    "penggantian",
    "sparepart"
];

if (
    $id_servis <= 0 ||
    !in_array(
        $jenis_foto,
        $jenis_valid,
        true
    )
) {
    die("Data dokumentasi tidak valid.");
}


/*
 * Pastikan servis milik teknisi login.
 */
$stmt_servis = mysqli_prepare(
    $koneksi,
    "SELECT id_servis
     FROM servis
     WHERE id_servis = ?
     AND id_teknisi = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt_servis,
    "ii",
    $id_servis,
    $id_teknisi
);

mysqli_stmt_execute($stmt_servis);

$hasil_servis =
    mysqli_stmt_get_result($stmt_servis);

if (!mysqli_fetch_assoc($hasil_servis)) {
    die("Servis tidak ditemukan atau bukan tugas Anda.");
}


/*
 * Proses id_detail.
 */
$id_detail = null;

if ($id_detail_input !== "") {

    $id_detail =
        (int) $id_detail_input;

    if ($id_detail <= 0) {
        die("Detail servis tidak valid.");
    }

    /*
     * Pastikan detail tersebut memang milik
     * servis yang sedang dibuka.
     */
    $cek_detail = mysqli_prepare(
        $koneksi,
        "SELECT id_detail
         FROM detail_servis
         WHERE id_detail = ?
         AND id_servis = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $cek_detail,
        "ii",
        $id_detail,
        $id_servis
    );

    mysqli_stmt_execute($cek_detail);

    $hasil_detail =
        mysqli_stmt_get_result($cek_detail);

    if (!mysqli_fetch_assoc($hasil_detail)) {
        die("Sparepart terkait tidak valid.");
    }
}


/*
 * Validasi file.
 */
if (
    !isset($_FILES["foto"]) ||
    $_FILES["foto"]["error"] !== UPLOAD_ERR_OK
) {
    die("File foto gagal diupload.");
}

$file =
    $_FILES["foto"];


/*
 * Maksimal 5 MB.
 */
$max_size =
    5 * 1024 * 1024;

if ($file["size"] > $max_size) {
    die("Ukuran foto maksimal 5 MB.");
}


/*
 * Deteksi MIME asli file.
 */
$finfo =
    new finfo(FILEINFO_MIME_TYPE);

$mime =
    $finfo->file($file["tmp_name"]);

$mime_valid = [
    "image/jpeg" => "jpg",
    "image/png"  => "png",
    "image/webp" => "webp"
];

if (!isset($mime_valid[$mime])) {
    die("Format file tidak diperbolehkan.");
}

$extension =
    $mime_valid[$mime];


/*
 * Tentukan folder berdasarkan jenis foto.
 */
$folder_rel = "uploads/servis/" . $jenis_foto . "/";

$folder_fisik =
    "../" . $folder_rel;


/*
 * Pastikan folder tersedia.
 */
if (!is_dir($folder_fisik)) {

    if (
        !mkdir(
            $folder_fisik,
            0755,
            true
        )
    ) {
        die("Folder upload tidak dapat dibuat.");
    }
}


/*
 * Nama file acak.
 */
$nama_file =
    bin2hex(random_bytes(16))
    . "."
    . $extension;

$path_fisik =
    $folder_fisik
    . $nama_file;

$path_database =
    $folder_rel
    . $nama_file;


/*
 * Pindahkan upload.
 */
if (
    !move_uploaded_file(
        $file["tmp_name"],
        $path_fisik
    )
) {
    die("Gagal menyimpan file foto.");
}


/*
 * Simpan database.
 */
if ($id_detail === null) {

    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO dokumentasi_servis (
            id_servis,
            id_teknisi,
            id_detail,
            jenis_foto,
            foto,
            keterangan,
            tampil_pelanggan
        )
        VALUES (
            ?,
            ?,
            NULL,
            ?,
            ?,
            ?,
            ?
        )"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "iisssi",
        $id_servis,
        $id_teknisi,
        $jenis_foto,
        $path_database,
        $keterangan,
        $tampil_pelanggan
    );

} else {

    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO dokumentasi_servis (
            id_servis,
            id_teknisi,
            id_detail,
            jenis_foto,
            foto,
            keterangan,
            tampil_pelanggan
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "iiisssi",
        $id_servis,
        $id_teknisi,
        $id_detail,
        $jenis_foto,
        $path_database,
        $keterangan,
        $tampil_pelanggan
    );
}


if (!mysqli_stmt_execute($stmt)) {

    /*
     * Kalau INSERT database gagal,
     * hapus file supaya tidak menjadi
     * file yatim.
     */
    if (file_exists($path_fisik)) {
        unlink($path_fisik);
    }

    die(
        "Gagal menyimpan dokumentasi: "
        . mysqli_error($koneksi)
    );
}

header(
    "Location: ../servis/detail.php?id="
    . $id_servis
);

exit;