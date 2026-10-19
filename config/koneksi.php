<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_servis_komputer";

$koneksi = mysqli_connect($host, $user, $pass, $db);

//cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");