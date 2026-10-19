<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$aksi = $_POST["aksi"] ?? "";

if ($aksi === "tambah") {

    $nama = trim($_POST["nama"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "";

    $id_teknisi_input = $_POST["id_teknisi"] ?? "";

    if (
        $nama === "" ||
        $username === "" ||
        $password === "" ||
        !in_array($role, ["admin", "teknisi"], true)
    ) {
        die("Data akun tidak lengkap.");
    }

    /*
     * Admin tidak terhubung ke teknisi.
     * Teknisi wajib mempunyai id_teknisi.
     */
    if ($role === "admin") {

        $id_teknisi = null;

    } else {

        $id_teknisi = (int) $id_teknisi_input;

        if ($id_teknisi <= 0) {
            die("Akun teknisi harus memilih data teknisi.");
        }
    }

    /*
     * Cek apakah username sudah digunakan.
     */
    $cek = mysqli_prepare(
        $koneksi,
        "SELECT id_user
         FROM users
         WHERE username = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $cek,
        "s",
        $username
    );

    mysqli_stmt_execute($cek);

    $hasil_cek = mysqli_stmt_get_result($cek);

    if (mysqli_fetch_assoc($hasil_cek)) {
        die("Username sudah digunakan.");
    }

    /*
     * Hash password.
     */
    $password_hash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    if ($role === "admin") {

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO users (
                id_teknisi,
                username,
                password,
                role,
                nama
            )
            VALUES (
                NULL,
                ?,
                ?,
                ?,
                ?
            )"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $username,
            $password_hash,
            $role,
            $nama
        );

    } else {

        /*
         * Pastikan teknisi belum mempunyai akun.
         */
        $cek_teknisi = mysqli_prepare(
            $koneksi,
            "SELECT id_user
             FROM users
             WHERE id_teknisi = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $cek_teknisi,
            "i",
            $id_teknisi
        );

        mysqli_stmt_execute($cek_teknisi);

        $hasil_teknisi =
            mysqli_stmt_get_result($cek_teknisi);

        if (mysqli_fetch_assoc($hasil_teknisi)) {
            die("Teknisi tersebut sudah mempunyai akun.");
        }

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO users (
                id_teknisi,
                username,
                password,
                role,
                nama
            )
            VALUES (?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "issss",
            $id_teknisi,
            $username,
            $password_hash,
            $role,
            $nama
        );
    }

    if (mysqli_stmt_execute($stmt)) {

        header("Location: index.php");
        exit;

    } else {

        die(
            "Gagal membuat akun: "
            . mysqli_error($koneksi)
        );
    }
}
elseif ($aksi === "edit") {

    $id_user = (int) ($_POST["id_user"] ?? 0);

    $nama = trim($_POST["nama"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (
        $id_user <= 0 ||
        $nama === "" ||
        $username === ""
    ) {
        die("Data akun tidak valid.");
    }

    /*
     * Cek username agar tidak sama dengan user lain.
     */
    $cek = mysqli_prepare(
        $koneksi,
        "SELECT id_user
         FROM users
         WHERE username = ?
         AND id_user != ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $cek,
        "si",
        $username,
        $id_user
    );

    mysqli_stmt_execute($cek);

    $hasil = mysqli_stmt_get_result($cek);

    if (mysqli_fetch_assoc($hasil)) {
        die("Username sudah digunakan akun lain.");
    }

    /*
     * Kalau password kosong, password lama tidak diubah.
     */
    if ($password === "") {

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE users
             SET nama = ?,
                 username = ?
             WHERE id_user = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssi",
            $nama,
            $username,
            $id_user
        );

    } else {

        $password_hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE users
             SET nama = ?,
                 username = ?,
                 password = ?
             WHERE id_user = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssi",
            $nama,
            $username,
            $password_hash,
            $id_user
        );
    }

    if (mysqli_stmt_execute($stmt)) {

        /*
         * Kalau admin mengedit akun sendiri,
         * update juga nama dan username di session.
         */
        if ($id_user === (int) $_SESSION["id_user"]) {

            $_SESSION["nama"] = $nama;
            $_SESSION["username"] = $username;
        }

        header("Location: index.php");
        exit;

    } else {

        die(
            "Gagal mengubah akun: "
            . mysqli_error($koneksi)
        );
    }
}

else {

    die("Aksi tidak dikenal.");
}