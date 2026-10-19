<?php

require 'config/koneksi.php';

$base_url = "/servis-komputer";

$data = null;
$error = "";

$query_detail = null;
$query_dokumentasi = null;

$total_sparepart = 0;
$total_servis = 0;


/*
 * =====================================================
 * CEK SERVIS
 * =====================================================
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $kode_servis =
        trim($_POST["kode_servis"] ?? "");

    $no_hp =
        trim($_POST["no_hp"] ?? "");


    if (
        $kode_servis === "" ||
        $no_hp === ""
    ) {

        $error =
            "Kode servis dan nomor HP harus diisi.";

    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT
                servis.*,

                perangkat.id_perangkat,
                perangkat.jenis_perangkat,
                perangkat.merk,
                perangkat.tipe,
                perangkat.serial_number,

                pelanggan.nama AS nama_pelanggan,
                pelanggan.no_hp,

                teknisi.nama_teknisi

             FROM servis

             JOIN perangkat
                ON servis.id_perangkat =
                   perangkat.id_perangkat

             JOIN pelanggan
                ON perangkat.id_pelanggan =
                   pelanggan.id_pelanggan

             LEFT JOIN teknisi
                ON servis.id_teknisi =
                   teknisi.id_teknisi

             WHERE servis.kode_servis = ?
               AND pelanggan.no_hp = ?

             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $kode_servis,
            $no_hp
        );

        mysqli_stmt_execute($stmt);

        $result =
            mysqli_stmt_get_result($stmt);

        $data =
            mysqli_fetch_assoc($result);


        if (!$data) {

            $error =
                "Data servis tidak ditemukan. "
                . "Periksa kembali kode servis "
                . "dan nomor HP.";

        } else {

            /*
             * =============================================
             * SPAREPART SERVIS
             * =============================================
             */

            $stmt_detail = mysqli_prepare(
                $koneksi,
                "SELECT
                    detail_servis.jumlah,
                    detail_servis.subtotal,

                    sparepart.nama_sparepart,
                    sparepart.harga

                 FROM detail_servis

                 JOIN sparepart
                    ON detail_servis.id_sparepart =
                       sparepart.id_sparepart

                 WHERE detail_servis.id_servis = ?

                 ORDER BY
                    detail_servis.id_detail ASC"
            );

            mysqli_stmt_bind_param(
                $stmt_detail,
                "i",
                $data["id_servis"]
            );

            mysqli_stmt_execute(
                $stmt_detail
            );

            $query_detail =
                mysqli_stmt_get_result(
                    $stmt_detail
                );


            /*
             * =============================================
             * TOTAL SPAREPART
             * =============================================
             */

            $stmt_total = mysqli_prepare(
                $koneksi,
                "SELECT
                    COALESCE(
                        SUM(subtotal),
                        0
                    ) AS total

                 FROM detail_servis

                 WHERE id_servis = ?"
            );

            mysqli_stmt_bind_param(
                $stmt_total,
                "i",
                $data["id_servis"]
            );

            mysqli_stmt_execute(
                $stmt_total
            );

            $hasil_total =
                mysqli_stmt_get_result(
                    $stmt_total
                );

            $row_total =
                mysqli_fetch_assoc(
                    $hasil_total
                );

            $total_sparepart =
                (float) $row_total["total"];

            $total_servis =
                (float) $data["biaya_jasa"]
                + $total_sparepart;

            $dp =
                (float) $data["dp"];

            $sisa_bayar =
                max(0, $total_servis - $dp);


            /*
             * =============================================
             * DOKUMENTASI PUBLIK
             * =============================================
             */

            $stmt_dokumentasi =
                mysqli_prepare(
                    $koneksi,
                    "SELECT
                        dokumentasi_servis.*,

                        sparepart.nama_sparepart

                     FROM dokumentasi_servis

                     LEFT JOIN detail_servis
                        ON dokumentasi_servis.id_detail =
                           detail_servis.id_detail

                     LEFT JOIN sparepart
                        ON detail_servis.id_sparepart =
                           sparepart.id_sparepart

                     WHERE
                        dokumentasi_servis.id_servis = ?

                     AND
                        dokumentasi_servis.tampil_pelanggan = 1

                     ORDER BY
                        dokumentasi_servis.tanggal_upload DESC"
                );

            mysqli_stmt_bind_param(
                $stmt_dokumentasi,
                "i",
                $data["id_servis"]
            );

            mysqli_stmt_execute(
                $stmt_dokumentasi
            );

            $query_dokumentasi =
                mysqli_stmt_get_result(
                    $stmt_dokumentasi
                );
        }
    }
}


/*
 * =====================================================
 * STATUS CLASS
 * =====================================================
 */

$status_class = "status-info";

if ($data) {

    if (
        $data["status"] === "Selesai" ||
        $data["status"] === "Sudah Diambil"
    ) {

        $status_class =
            "status-success";

    } elseif (
        $data["status"] ===
        "Menunggu Sparepart"
    ) {

        $status_class =
            "status-warning";
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Cek Servis - Albert Workshop
    </title>

    <link
        rel="stylesheet"
        href="<?= $base_url; ?>/assets/css/style.css"
    >

</head>


<body class="public-page">


<div class="tracking-shell">


    <!-- TOP -->

    <header class="public-topbar">

        <div class="public-brand">

            <div class="brand-mark">
                AW
            </div>

            <div>

                <strong>
                    ALBERT WORKSHOP
                </strong>

                <span>
                    Computer Service System
                </span>

            </div>

        </div>


        <a
            href="<?= $base_url; ?>/login.php"
            class="btn btn-secondary"
        >
            Login Staff
        </a>

    </header>



    <!-- HERO -->

    <section class="tracking-hero">

        <span class="public-eyebrow">
            SERVICE TRACKING
        </span>

        <h1>
            Cek Status Servis
        </h1>

        <p>
            Masukkan kode servis dan nomor HP
            yang digunakan saat perangkat didaftarkan.
        </p>

    </section>



    <!-- FORM -->

    <section class="public-card tracking-form-card">

        <?php if ($error !== ""): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action=""
        >

            <div class="tracking-form-grid">


                <div class="form-group">

                    <label>
                        Kode Servis
                    </label>

                    <input
                        type="text"
                        name="kode_servis"
                        value="<?= htmlspecialchars(
                            $_POST["kode_servis"] ?? ""
                        ); ?>"
                        placeholder="Contoh: SV-XXXXXXXX"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Nomor HP
                    </label>

                    <input
                        type="text"
                        name="no_hp"
                        value="<?= htmlspecialchars(
                            $_POST["no_hp"] ?? ""
                        ); ?>"
                        placeholder="Contoh: 081234567890"
                        required
                    >

                </div>

            </div>


            <button
                type="submit"
                class="btn"
            >
                Cek Status
            </button>

        </form>

    </section>



    <?php if ($data): ?>


        <!-- STATUS UTAMA -->

        <section class="tracking-status-card">

            <div>

                <span class="public-eyebrow">
                    STATUS SERVIS
                </span>

                <h2>
                    <?= htmlspecialchars(
                        $data["kode_servis"]
                    ); ?>
                </h2>

            </div>


            <span
                class="status tracking-status <?= $status_class; ?>"
            >
                <?= htmlspecialchars(
                    $data["status"]
                ); ?>
            </span>

        </section>



        <!-- PELANGGAN DAN PERANGKAT -->

        <div class="detail-grid">


            <section class="panel">

                <h2 class="panel-title">
                    Pelanggan
                </h2>


                <div class="info-list">

                    <div class="info-row">

                        <span class="info-label">
                            Nama
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $data["nama_pelanggan"]
                            ); ?>
                        </strong>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Nomor HP
                        </span>

                        <span>
                            <?= htmlspecialchars(
                                $data["no_hp"]
                            ); ?>
                        </span>

                    </div>

                </div>

            </section>



            <section class="panel">

                <h2 class="panel-title">
                    Perangkat
                </h2>


                <div class="info-list">

                    <div class="info-row">

                        <span class="info-label">
                            Jenis
                        </span>

                        <span>
                            <?= htmlspecialchars(
                                $data["jenis_perangkat"]
                            ); ?>
                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Merk / Tipe
                        </span>

                        <span>

                            <?= htmlspecialchars(
                                $data["merk"]
                            ); ?>

                            <?= htmlspecialchars(
                                $data["tipe"]
                            ); ?>

                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Serial Number
                        </span>

                        <span>

                            <?= !empty(
                                $data["serial_number"]
                            )
                                ? htmlspecialchars(
                                    $data["serial_number"]
                                )
                                : "-"; ?>

                        </span>

                    </div>

                </div>

            </section>

        </div>



        <!-- INFORMASI SERVIS -->

        <section class="panel">

            <h2 class="panel-title">
                Informasi Servis
            </h2>


            <div class="detail-grid">


                <div class="info-list">

                    <div class="info-row">

                        <span class="info-label">
                            Tanggal Masuk
                        </span>

                        <span>

                            <?= date(
                                "d/m/Y",
                                strtotime(
                                    $data["tanggal_masuk"]
                                )
                            ); ?>

                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Teknisi
                        </span>

                        <span>

                            <?= !empty(
                                $data["nama_teknisi"]
                            )
                                ? htmlspecialchars(
                                    $data["nama_teknisi"]
                                )
                                : "-"; ?>

                        </span>

                    </div>


                    <div class="info-row">

                        <span class="info-label">
                            Status
                        </span>

                        <span
                            class="status <?= $status_class; ?>"
                        >

                            <?= htmlspecialchars(
                                $data["status"]
                            ); ?>

                        </span>

                    </div>

                </div>


                <div>

                    <div class="service-text">

                        <span class="info-label">
                            Keluhan
                        </span>

                        <p>
                            <?= nl2br(
                                htmlspecialchars(
                                    $data["keluhan"]
                                )
                            ); ?>
                        </p>

                    </div>


                    <div class="service-text">

                        <span class="info-label">
                            Diagnosa
                        </span>

                        <p>

                            <?php if (
                                !empty(
                                    $data["diagnosa"]
                                )
                            ): ?>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $data["diagnosa"]
                                    )
                                ); ?>

                            <?php else: ?>

                                <span class="text-muted">
                                    Belum ada hasil diagnosa.
                                </span>

                            <?php endif; ?>

                        </p>

                    </div>

                </div>

            </div>

        </section>



        <!-- SPAREPART -->

        <section class="panel">

            <h2 class="panel-title">
                Sparepart Digunakan
            </h2>


            <?php if (
                mysqli_num_rows(
                    $query_detail
                ) > 0
            ): ?>

                <div class="table-wrap">

                    <table>

                        <thead>

                            <tr>
                                <th>Sparepart</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>

                        </thead>


                        <tbody>

                        <?php while (
                            $detail =
                            mysqli_fetch_assoc(
                                $query_detail
                            )
                        ): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars(
                                        $detail["nama_sparepart"]
                                    ); ?>
                                </td>

                                <td>

                                    Rp<?= number_format(
                                        $detail["harga"],
                                        0,
                                        ",",
                                        "."
                                    ); ?>

                                </td>

                                <td>
                                    <?= (int) $detail["jumlah"]; ?>
                                </td>

                                <td>

                                    Rp<?= number_format(
                                        $detail["subtotal"],
                                        0,
                                        ",",
                                        "."
                                    ); ?>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>


            <?php else: ?>

                <div class="empty-state compact">
                    Tidak ada sparepart yang digunakan.
                </div>

            <?php endif; ?>

        </section>



        <!-- BIAYA -->

        <section class="panel">

            <h2 class="panel-title">
                Rincian Biaya
            </h2>


            <div class="cost-summary">

                <div class="cost-row">

                    <span>
                        Biaya Jasa
                    </span>

                    <strong>

                        Rp<?= number_format(
                            $data["biaya_jasa"],
                            0,
                            ",",
                            "."
                        ); ?>

                    </strong>

                </div>


                <div class="cost-row">

                    <span>
                        Sparepart
                    </span>

                    <strong>

                        Rp<?= number_format(
                            $total_sparepart,
                            0,
                            ",",
                            "."
                        ); ?>

                    </strong>

                </div>


                <div class="cost-row cost-total">

                    <span>
                        Total
                    </span>

                    <strong>

                        Rp<?= number_format(
                            $total_servis,
                            0,
                            ",",
                            "."
                        ); ?>

                    </strong>

                </div>


                <div class="cost-row">

                    <span>
                        DP Dibayar
                    </span>

                    <strong>

                        Rp<?= number_format(
                            $dp,
                            0,
                            ",",
                            "."
                        ); ?>

                    </strong>

                </div>


                <div class="cost-row cost-total">

                    <span>
                        Sisa Pembayaran
                    </span>

                    <strong>

                        Rp<?= number_format(
                            $sisa_bayar,
                            0,
                            ",",
                            "."
                        ); ?>

                    </strong>

                </div>

            </div>

        </section>



        <!-- DOKUMENTASI -->

        <section class="panel">

            <h2 class="panel-title">
                Dokumentasi Servis
            </h2>

            <p class="page-description tracking-description">
                Dokumentasi yang dibagikan
                oleh teknisi selama proses pengerjaan.
            </p>


            <?php if (
                mysqli_num_rows(
                    $query_dokumentasi
                ) > 0
            ): ?>


                <div class="documentation-grid">


                    <?php while (
                        $dok =
                        mysqli_fetch_assoc(
                            $query_dokumentasi
                        )
                    ): ?>


                        <article class="documentation-card">

                            <div class="documentation-image">

                                <img
                                    src="<?= htmlspecialchars(
                                        $dok["foto"]
                                    ); ?>"
                                    alt="Dokumentasi servis"
                                >

                            </div>


                            <div class="documentation-body">

                                <span class="status status-info">

                                    <?= htmlspecialchars(
                                        ucfirst(
                                            $dok["jenis_foto"]
                                        )
                                    ); ?>

                                </span>


                                <?php if (
                                    !empty(
                                        $dok["nama_sparepart"]
                                    )
                                ): ?>

                                    <p>

                                        <span class="info-label">
                                            Sparepart
                                        </span>

                                        <br>

                                        <?= htmlspecialchars(
                                            $dok["nama_sparepart"]
                                        ); ?>

                                    </p>

                                <?php endif; ?>


                                <?php if (
                                    !empty(
                                        $dok["keterangan"]
                                    )
                                ): ?>

                                    <p>

                                        <?= nl2br(
                                            htmlspecialchars(
                                                $dok["keterangan"]
                                            )
                                        ); ?>

                                    </p>

                                <?php endif; ?>


                                <span class="text-muted">

                                    <?= htmlspecialchars(
                                        $dok["tanggal_upload"]
                                    ); ?>

                                </span>

                            </div>

                        </article>

                    <?php endwhile; ?>

                </div>


            <?php else: ?>

                <div class="empty-state compact">
                    Belum ada dokumentasi
                    yang dibagikan kepada pelanggan.
                </div>

            <?php endif; ?>

        </section>


    <?php endif; ?>



    <footer class="public-footer tracking-footer">

        <span>
            Albert Workshop
        </span>

        <span>
            Sistem Informasi Servis Komputer
        </span>

    </footer>

</div>


</body>
</html>