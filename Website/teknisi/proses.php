<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$aksi = $_POST["aksi"] ?? "";

if ($aksi === "tambah") {

    $nama_teknisi = trim($_POST["nama_teknisi"] ?? "");
    $no_hp = trim($_POST["no_hp"] ?? "");
    $keahlian = trim($_POST["keahlian"] ?? "");

    if (
        $nama_teknisi === "" ||
        $no_hp === "" ||
        $keahlian === ""
    ) {
        die("Semua data teknisi wajib diisi.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO teknisi (
            nama_teknisi,
            no_hp,
            keahlian
        )
        VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $nama_teknisi,
        $no_hp,
        $keahlian
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: index.php");
        exit;

    } else {

        die(
            "Gagal menambahkan teknisi: "
            . mysqli_error($koneksi)
        );
    }

}

elseif ($aksi === "edit") {

    $id_teknisi = (int) ($_POST["id_teknisi"] ?? 0);

    $nama_teknisi = trim($_POST["nama_teknisi"] ?? "");
    $no_hp = trim($_POST["no_hp"] ?? "");
    $keahlian = trim($_POST["keahlian"] ?? "");

    if (
        $id_teknisi <= 0 ||
        $nama_teknisi === "" ||
        $no_hp === "" ||
        $keahlian === ""
    ) {
        die("Data teknisi tidak valid.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE teknisi
         SET nama_teknisi = ?,
             no_hp = ?,
             keahlian = ?
         WHERE id_teknisi = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sssi",
        $nama_teknisi,
        $no_hp,
        $keahlian,
        $id_teknisi
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: index.php");
        exit;

    } else {

        die(
            "Gagal mengubah teknisi: "
            . mysqli_error($koneksi)
        );
    }

}

else {

    die("Aksi tidak dikenal.");
}