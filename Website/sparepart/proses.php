<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$aksi = $_POST["aksi"] ?? "";

if ($aksi === "tambah") {

    $nama_sparepart =
        trim($_POST["nama_sparepart"] ?? "");

    $stok =
        (int) ($_POST["stok"] ?? 0);

    $harga =
        (float) ($_POST["harga"] ?? 0);

    if (
        $nama_sparepart === "" ||
        $stok < 0 ||
        $harga < 0
    ) {
        die("Data sparepart tidak valid.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO sparepart (
            nama_sparepart,
            stok,
            harga
        )
        VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sid",
        $nama_sparepart,
        $stok,
        $harga
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: index.php");
        exit;

    } else {

        die(
            "Gagal menambahkan sparepart: "
            . mysqli_error($koneksi)
        );
    }

}

elseif ($aksi === "edit") {

    $id_sparepart =
        (int) ($_POST["id_sparepart"] ?? 0);

    $nama_sparepart =
        trim($_POST["nama_sparepart"] ?? "");

    $stok =
        (int) ($_POST["stok"] ?? 0);

    $harga =
        (float) ($_POST["harga"] ?? 0);

    if (
        $id_sparepart <= 0 ||
        $nama_sparepart === "" ||
        $stok < 0 ||
        $harga < 0
    ) {
        die("Data sparepart tidak valid.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE sparepart
         SET nama_sparepart = ?,
             stok = ?,
             harga = ?
         WHERE id_sparepart = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sidi",
        $nama_sparepart,
        $stok,
        $harga,
        $id_sparepart
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: index.php");
        exit;

    } else {

        die(
            "Gagal mengubah sparepart: "
            . mysqli_error($koneksi)
        );
    }

}

else {

    die("Aksi tidak dikenal.");
}