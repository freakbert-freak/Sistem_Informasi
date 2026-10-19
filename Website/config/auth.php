<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sesuaikan kalau nama folder project kamu berbeda
$base_url = "/servis-komputer";

/**
 * Memastikan user sudah login.
 */
function wajib_login()
{
    global $base_url;

    if (!isset($_SESSION["id_user"])) {
        header("Location: " . $base_url . "/login.php");
        exit;
    }
}

/**
 * Halaman khusus admin.
 */
function khusus_admin()
{
    global $base_url;

    wajib_login();

    if ($_SESSION["role"] !== "admin") {
        header("Location: " . $base_url . "/index.php");
        exit;
    }
}

/**
 * Halaman khusus teknisi.
 */
function khusus_teknisi()
{
    global $base_url;

    wajib_login();

    if ($_SESSION["role"] !== "teknisi") {
        header("Location: " . $base_url . "/index.php");
        exit;
    }
}

/**
 * Mengambil ID teknisi yang sedang login.
 */
function id_teknisi_login()
{
    return $_SESSION["id_teknisi"] ?? null;
}