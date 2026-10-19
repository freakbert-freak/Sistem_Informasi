<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$id_servis = (int) ($_GET["id"] ?? 0);

if ($id_servis <= 0) {
    die("ID servis tidak valid.");
}

/*
 * Pastikan servis benar-benar ada.
 */
$cek = mysqli_prepare(
    $koneksi,
    "SELECT id_servis, kode_servis
     FROM servis
     WHERE id_servis = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $cek,
    "i",
    $id_servis
);

mysqli_stmt_execute($cek);

$hasil_cek = mysqli_stmt_get_result($cek);

$servis = mysqli_fetch_assoc($hasil_cek);

if (!$servis) {
    die("Servis tidak ditemukan.");
}


/*
 * Mulai transaction.
 *
 * Tujuannya:
 * 1. Kembalikan stok
 * 2. Hapus servis
 *
 * Kalau salah satu gagal, semuanya dibatalkan.
 */
mysqli_begin_transaction($koneksi);

try {

    /*
     * Ambil seluruh sparepart yang digunakan
     * pada servis ini.
     */
    $stmt_detail = mysqli_prepare(
        $koneksi,
        "SELECT
            id_sparepart,
            jumlah
         FROM detail_servis
         WHERE id_servis = ?
         FOR UPDATE"
    );

    mysqli_stmt_bind_param(
        $stmt_detail,
        "i",
        $id_servis
    );

    if (!mysqli_stmt_execute($stmt_detail)) {
        throw new Exception(
            "Gagal mengambil detail sparepart."
        );
    }

    $hasil_detail =
        mysqli_stmt_get_result($stmt_detail);


    /*
     * Kembalikan stok setiap sparepart.
     */
    while ($detail = mysqli_fetch_assoc($hasil_detail)) {

        $id_sparepart =
            (int) $detail["id_sparepart"];

        $jumlah =
            (int) $detail["jumlah"];

        $stmt_stok = mysqli_prepare(
            $koneksi,
            "UPDATE sparepart
             SET stok = stok + ?
             WHERE id_sparepart = ?"
        );

        mysqli_stmt_bind_param(
            $stmt_stok,
            "ii",
            $jumlah,
            $id_sparepart
        );

        if (!mysqli_stmt_execute($stmt_stok)) {
            throw new Exception(
                "Gagal mengembalikan stok sparepart."
            );
        }
    }


    /*
     * Setelah stok kembali,
     * hapus servis.
     *
     * detail_servis akan ikut terhapus
     * karena ON DELETE CASCADE.
     */
    $stmt_hapus = mysqli_prepare(
        $koneksi,
        "DELETE FROM servis
         WHERE id_servis = ?"
    );

    mysqli_stmt_bind_param(
        $stmt_hapus,
        "i",
        $id_servis
    );

    if (!mysqli_stmt_execute($stmt_hapus)) {
        throw new Exception(
            "Gagal menghapus servis."
        );
    }


    /*
     * Pastikan ada servis yang benar-benar terhapus.
     */
    if (mysqli_stmt_affected_rows($stmt_hapus) <= 0) {
        throw new Exception(
            "Servis tidak ditemukan atau gagal dihapus."
        );
    }


    /*
     * Semua berhasil.
     */
    mysqli_commit($koneksi);

    header("Location: index.php");
    exit;

} catch (Throwable $e) {

    /*
     * Kalau terjadi kesalahan,
     * semua perubahan dibatalkan.
     */
    mysqli_rollback($koneksi);

    die(
        "Gagal menghapus servis: "
        . $e->getMessage()
    );
}