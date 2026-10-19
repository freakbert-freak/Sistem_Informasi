<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$aksi = $_POST["aksi"] ?? "";

if ($aksi === "tambah") {

    $id_pelanggan = (int) ($_POST["id_pelanggan"] ?? 0);
    $jenis_perangkat = trim($_POST["jenis_perangkat"] ?? "");
    $merk = trim($_POST["merk"] ?? "");
    $tipe = trim($_POST["tipe"] ?? "");
    $serial_number = trim($_POST["serial_number"] ?? "");

    if (
        $id_pelanggan <= 0 ||
        $jenis_perangkat === "" ||
        $merk === "" ||
        $tipe === ""
    ) {
        die("Data perangkat belum lengkap.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO perangkat (
            id_pelanggan,
            jenis_perangkat,
            merk,
            tipe,
            serial_number
        )
        VALUES (?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "issss",
        $id_pelanggan,
        $jenis_perangkat,
        $merk,
        $tipe,
        $serial_number
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: index.php");
        exit;

    } else {

        die(
            "Gagal menambahkan perangkat: "
            . mysqli_error($koneksi)
        );
    }

}

elseif ($aksi === "edit") {

    $id_perangkat = (int) ($_POST["id_perangkat"] ?? 0);
    $id_pelanggan = (int) ($_POST["id_pelanggan"] ?? 0);

    $jenis_perangkat = trim($_POST["jenis_perangkat"] ?? "");
    $merk = trim($_POST["merk"] ?? "");
    $tipe = trim($_POST["tipe"] ?? "");
    $serial_number = trim($_POST["serial_number"] ?? "");

    if (
        $id_perangkat <= 0 ||
        $id_pelanggan <= 0 ||
        $jenis_perangkat === "" ||
        $merk === "" ||
        $tipe === ""
    ) {
        die("Data perangkat tidak valid.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE perangkat
         SET id_pelanggan = ?,
             jenis_perangkat = ?,
             merk = ?,
             tipe = ?,
             serial_number = ?
         WHERE id_perangkat = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "issssi",
        $id_pelanggan,
        $jenis_perangkat,
        $merk,
        $tipe,
        $serial_number,
        $id_perangkat
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: index.php");
        exit;

    } else {

        die(
            "Gagal mengubah perangkat: "
            . mysqli_error($koneksi)
        );
    }

}

else {

    die("Aksi tidak dikenal.");
}