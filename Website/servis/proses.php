<?php

require '../config/koneksi.php';
require '../config/auth.php';

wajib_login();

$aksi = $_POST["aksi"] ?? "";

if ($aksi === "tambah") {

    khusus_admin();

    $id_perangkat =
        (int) ($_POST["id_perangkat"] ?? 0);

    $id_teknisi =
        (int) ($_POST["id_teknisi"] ?? 0);

    $tanggal_masuk =
        $_POST["tanggal_masuk"] ?? "";

    $keluhan =
        trim($_POST["keluhan"] ?? "");

    $biaya_jasa =
        (float) ($_POST["biaya_jasa"] ?? 0);

    $dp =
        (float) ($_POST["dp"] ?? 0);

    if (
        $id_perangkat <= 0 ||
        $id_teknisi <= 0 ||
        $tanggal_masuk === "" ||
        $keluhan === "" ||
        $biaya_jasa < 0 ||
        $dp < 0
    ) {
        die("Data servis tidak valid.");
    }

    /*
     * Membuat kode servis acak.
     *
     * Contoh:
     * SV-A12B34CD56EF7890
     */
    $kode_servis =
        "SV-" . strtoupper(
            bin2hex(random_bytes(8))
        );

    $status = "Menunggu Pemeriksaan";

    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO servis (
            kode_servis,
            id_perangkat,
            id_teknisi,
            tanggal_masuk,
            keluhan,
            diagnosa,
            status,
            biaya_jasa,
            dp
        )
        VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            NULL,
            ?,
            ?,
            ?
        )"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "siisssdd",
        $kode_servis,
        $id_perangkat,
        $id_teknisi,
        $tanggal_masuk,
        $keluhan,
        $status,
        $biaya_jasa,
        $dp
    );

    if (mysqli_stmt_execute($stmt)) {

        $id_servis =
            mysqli_insert_id($koneksi);

        header(
            "Location: detail.php?id=" . $id_servis
        );

        exit;

    } else {

        die(
            "Gagal membuat servis: "
            . mysqli_error($koneksi)
        );
    }
}

elseif ($aksi === "edit_admin") {

    khusus_admin();

    $id_servis =
        (int) ($_POST["id_servis"] ?? 0);

    $id_perangkat =
        (int) ($_POST["id_perangkat"] ?? 0);

    $id_teknisi =
        (int) ($_POST["id_teknisi"] ?? 0);

    $tanggal_masuk =
        $_POST["tanggal_masuk"] ?? "";

    $keluhan =
        trim($_POST["keluhan"] ?? "");

    $diagnosa =
        trim($_POST["diagnosa"] ?? "");

    $status =
        $_POST["status"] ?? "";

    $biaya_jasa =
        (float) ($_POST["biaya_jasa"] ?? 0);

    $dp =
        (float) ($_POST["dp"] ?? 0);

    $status_valid = [
        "Menunggu Pemeriksaan",
        "Sedang Diperiksa",
        "Menunggu Sparepart",
        "Sedang Diperbaiki",
        "Pengujian",
        "Selesai",
        "Sudah Diambil"
    ];

    if (
        $id_servis <= 0 ||
        $id_perangkat <= 0 ||
        $id_teknisi <= 0 ||
        $tanggal_masuk === "" ||
        $keluhan === "" ||
        !in_array($status, $status_valid, true) ||
        $biaya_jasa < 0 ||
        $dp < 0
    ) {
        die("Data servis tidak valid.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE servis
         SET id_perangkat = ?,
             id_teknisi = ?,
             tanggal_masuk = ?,
             keluhan = ?,
             diagnosa = ?,
             status = ?,
             biaya_jasa = ?,
             dp = ?
         WHERE id_servis = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "iissssddi",
        $id_perangkat,
        $id_teknisi,
        $tanggal_masuk,
        $keluhan,
        $diagnosa,
        $status,
        $biaya_jasa,
        $dp,
        $id_servis
    );

    if (mysqli_stmt_execute($stmt)) {

        header(
            "Location: detail.php?id=" . $id_servis
        );
        exit;

    } else {

        die(
            "Gagal mengubah servis: "
            . mysqli_error($koneksi)
        );
    }
}

elseif ($aksi === "update_teknisi") {

    if ($_SESSION["role"] !== "teknisi") {
        die("Akses ditolak.");
    }

    $id_servis =
        (int) ($_POST["id_servis"] ?? 0);

    $diagnosa =
        trim($_POST["diagnosa"] ?? "");

    $status =
        $_POST["status"] ?? "";

    $status_valid = [
        "Menunggu Pemeriksaan",
        "Sedang Diperiksa",
        "Menunggu Sparepart",
        "Sedang Diperbaiki",
        "Pengujian",
        "Selesai",
        "Sudah Diambil"
    ];

    if (
        $id_servis <= 0 ||
        !in_array($status, $status_valid, true)
    ) {
        die("Data servis tidak valid.");
    }

    /*
     * Pastikan servis memang milik teknisi login.
     */
    $id_teknisi =
        (int) $_SESSION["id_teknisi"];

    $cek = mysqli_prepare(
        $koneksi,
        "SELECT id_servis
         FROM servis
         WHERE id_servis = ?
         AND id_teknisi = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $cek,
        "ii",
        $id_servis,
        $id_teknisi
    );

    mysqli_stmt_execute($cek);

    $hasil =
        mysqli_stmt_get_result($cek);

    if (!mysqli_fetch_assoc($hasil)) {
        die("Servis tidak ditemukan atau bukan tugas Anda.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE servis
         SET diagnosa = ?,
             status = ?
         WHERE id_servis = ?
         AND id_teknisi = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssii",
        $diagnosa,
        $status,
        $id_servis,
        $id_teknisi
    );

    if (mysqli_stmt_execute($stmt)) {

        header(
            "Location: detail.php?id=" . $id_servis
        );

        exit;

    } else {

        die(
            "Gagal mengubah servis: "
            . mysqli_error($koneksi)
        );
    }
}
elseif ($aksi === "tambah_sparepart") {

    $id_servis =
        (int) ($_POST["id_servis"] ?? 0);

    $id_sparepart =
        (int) ($_POST["id_sparepart"] ?? 0);

    $jumlah =
        (int) ($_POST["jumlah"] ?? 0);

    if (
        $id_servis <= 0 ||
        $id_sparepart <= 0 ||
        $jumlah <= 0
    ) {
        die("Data sparepart tidak valid.");
    }

    /*
     * Cek servis dan hak akses.
     */
    $cek_servis = mysqli_prepare(
        $koneksi,
        "SELECT id_teknisi
         FROM servis
         WHERE id_servis = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $cek_servis,
        "i",
        $id_servis
    );

    mysqli_stmt_execute($cek_servis);

    $hasil_servis =
        mysqli_stmt_get_result($cek_servis);

    $servis =
        mysqli_fetch_assoc($hasil_servis);

    if (!$servis) {
        die("Servis tidak ditemukan.");
    }

    /*
     * Jika teknisi, servis harus miliknya.
     */
    if (
        $_SESSION["role"] === "teknisi" &&
        (int) $servis["id_teknisi"] !==
        (int) $_SESSION["id_teknisi"]
    ) {
        die("Anda tidak mempunyai akses ke servis ini.");
    }

    /*
     * Mulai transaksi database.
     */
    mysqli_begin_transaction($koneksi);

    try {

        /*
         * Ambil sparepart sekaligus mengunci baris stok.
         */
        $stmt_sparepart = mysqli_prepare(
            $koneksi,
            "SELECT harga, stok
             FROM sparepart
             WHERE id_sparepart = ?
             FOR UPDATE"
        );

        mysqli_stmt_bind_param(
            $stmt_sparepart,
            "i",
            $id_sparepart
        );

        mysqli_stmt_execute($stmt_sparepart);

        $hasil_sparepart =
            mysqli_stmt_get_result($stmt_sparepart);

        $sparepart =
            mysqli_fetch_assoc($hasil_sparepart);

        if (!$sparepart) {
            throw new Exception(
                "Sparepart tidak ditemukan."
            );
        }

        if ($sparepart["stok"] < $jumlah) {
            throw new Exception(
                "Stok sparepart tidak mencukupi."
            );
        }

        /*
         * Hitung subtotal dari harga database.
         */
        $harga =
            (float) $sparepart["harga"];

        $subtotal =
            $harga * $jumlah;

        /*
         * Simpan detail servis.
         */
        $stmt_detail = mysqli_prepare(
            $koneksi,
            "INSERT INTO detail_servis (
                id_servis,
                id_sparepart,
                jumlah,
                subtotal
            )
            VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt_detail,
            "iiid",
            $id_servis,
            $id_sparepart,
            $jumlah,
            $subtotal
        );

        if (!mysqli_stmt_execute($stmt_detail)) {
            throw new Exception(
                "Sparepart tersebut kemungkinan sudah ditambahkan."
            );
        }

        /*
         * Kurangi stok.
         */
        $stmt_stok = mysqli_prepare(
            $koneksi,
            "UPDATE sparepart
             SET stok = stok - ?
             WHERE id_sparepart = ?"
        );

        mysqli_stmt_bind_param(
            $stmt_stok,
            "ii",
            $jumlah,
            $id_sparepart
        );

        if (!mysqli_stmt_execute($stmt_stok)) {
    throw new Exception("Gagal mengurangi stok sparepart.");
}

        /*
         * Semua berhasil.
         */
        mysqli_commit($koneksi);

        header(
            "Location: detail.php?id=" . $id_servis
        );

        exit;

    } catch (Throwable $e) {

        mysqli_rollback($koneksi);

        die(
            "Gagal menambahkan sparepart: "
            . $e->getMessage()
        );
    }
}

elseif ($aksi === "hapus_sparepart") {

    $id_detail =
        (int) ($_POST["id_detail"] ?? 0);

    $id_servis =
        (int) ($_POST["id_servis"] ?? 0);

    if (
        $id_detail <= 0 ||
        $id_servis <= 0
    ) {
        die("Data detail servis tidak valid.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT
            detail_servis.id_sparepart,
            detail_servis.jumlah,
            servis.id_teknisi

         FROM detail_servis

         JOIN servis
            ON detail_servis.id_servis = servis.id_servis

         WHERE detail_servis.id_detail = ?
         AND detail_servis.id_servis = ?

         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $id_detail,
        $id_servis
    );

    mysqli_stmt_execute($stmt);

    $result =
        mysqli_stmt_get_result($stmt);

    $detail =
        mysqli_fetch_assoc($result);

    if (!$detail) {
        die("Detail servis tidak ditemukan.");
    }

    /*
     * Teknisi hanya boleh mengubah servisnya sendiri.
     */
    if (
        $_SESSION["role"] === "teknisi" &&
        (int) $detail["id_teknisi"] !==
        (int) $_SESSION["id_teknisi"]
    ) {
        die("Anda tidak mempunyai akses.");
    }

    mysqli_begin_transaction($koneksi);

    try {

        /*
         * Hapus dari detail servis.
         */
        $hapus = mysqli_prepare(
            $koneksi,
            "DELETE FROM detail_servis
             WHERE id_detail = ?"
        );

        mysqli_stmt_bind_param(
            $hapus,
            "i",
            $id_detail
        );

        mysqli_stmt_execute($hapus);

        /*
         * Kembalikan stok.
         */
        $kembali_stok = mysqli_prepare(
            $koneksi,
            "UPDATE sparepart
             SET stok = stok + ?
             WHERE id_sparepart = ?"
        );

        mysqli_stmt_bind_param(
            $kembali_stok,
            "ii",
            $detail["jumlah"],
            $detail["id_sparepart"]
        );

        if (!mysqli_stmt_execute($kembali_stok)) {
    throw new Exception("Gagal mengembalikan stok sparepart.");
}

        mysqli_commit($koneksi);

        header(
            "Location: detail.php?id=" . $id_servis
        );

        exit;

    } catch (Throwable $e) {

        mysqli_rollback($koneksi);

        die(
            "Gagal menghapus sparepart: "
            . $e->getMessage()
        );
    }
}

else {

    die("Aksi tidak dikenal.");
}