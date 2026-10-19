<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$aksi = $_POST["aksi"] ?? "";

if ($aksi === "tambah") {

    $nama = trim($_POST["nama"] ?? "");
    $no_hp = trim($_POST["no_hp"] ?? "");
    $alamat = trim($_POST["alamat"] ?? "");

    if ($nama === "" || $no_hp === "" || $alamat === "") {
        die("Semua data wajib diisi.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO pelanggan (nama, no_hp, alamat)
         VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $nama,
        $no_hp,
        $alamat
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: index.php");
        exit;

    } else {

        die(
            "Gagal menambahkan pelanggan: "
            . mysqli_error($koneksi)
        );
    }

}

elseif ($aksi === "edit") {

    $id_pelanggan = (int) ($_POST["id_pelanggan"] ?? 0);

    $nama = trim($_POST["nama"] ?? "");
    $no_hp = trim($_POST["no_hp"] ?? "");
    $alamat = trim($_POST["alamat"] ?? "");

    if (
        $id_pelanggan <= 0 ||
        $nama === "" ||
        $no_hp === "" ||
        $alamat === ""
    ) {
        die("Data tidak valid.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE pelanggan
         SET nama = ?,
             no_hp = ?,
             alamat = ?
         WHERE id_pelanggan = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sssi",
        $nama,
        $no_hp,
        $alamat,
        $id_pelanggan
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: index.php");
        exit;

    } else {

        die(
            "Gagal mengubah pelanggan: "
            . mysqli_error($koneksi)
        );
    }

}

else {

    die("Aksi tidak dikenal.");
}