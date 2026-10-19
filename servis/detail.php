<?php

require '../config/koneksi.php';
require '../config/auth.php';

wajib_login();


$id_servis =
    (int) ($_GET["id"] ?? 0);

if ($id_servis <= 0) {
    die("ID servis tidak valid.");
}


/*
 * =====================================================
 * DATA SERVIS
 * =====================================================
 */

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT
        servis.*,

        perangkat.jenis_perangkat,
        perangkat.merk,
        perangkat.tipe,
        perangkat.serial_number,

        pelanggan.id_pelanggan,
        pelanggan.nama AS nama_pelanggan,
        pelanggan.no_hp,
        pelanggan.alamat,

        teknisi.nama_teknisi

     FROM servis

     JOIN perangkat
        ON servis.id_perangkat =
           perangkat.id_perangkat

     JOIN pelanggan
        ON perangkat.id_pelanggan =
           pelanggan.id_pelanggan

     JOIN teknisi
        ON servis.id_teknisi =
           teknisi.id_teknisi

     WHERE servis.id_servis = ?

     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_servis
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$data =
    mysqli_fetch_assoc($result);


if (!$data) {
    die("Servis tidak ditemukan.");
}


/*
 * Teknisi hanya boleh membuka
 * servis yang ditugaskan kepadanya.
 */
if (
    $_SESSION["role"] === "teknisi" &&
    (int) $data["id_teknisi"] !==
    (int) $_SESSION["id_teknisi"]
) {
    die("Anda tidak mempunyai akses ke servis ini.");
}


/*
 * =====================================================
 * SPAREPART TERSEDIA
 * =====================================================
 */

$query_sparepart = mysqli_query(
    $koneksi,
    "SELECT
        id_sparepart,
        nama_sparepart,
        stok,
        harga

     FROM sparepart

     WHERE stok > 0

     ORDER BY nama_sparepart ASC"
);


/*
 * =====================================================
 * SPAREPART YANG DIGUNAKAN
 * =====================================================
 */

$stmt_detail = mysqli_prepare(
    $koneksi,
    "SELECT
        detail_servis.id_detail,
        detail_servis.id_sparepart,
        detail_servis.jumlah,
        detail_servis.subtotal,

        sparepart.nama_sparepart,
        sparepart.harga

     FROM detail_servis

     JOIN sparepart
        ON detail_servis.id_sparepart =
           sparepart.id_sparepart

     WHERE detail_servis.id_servis = ?

     ORDER BY detail_servis.id_detail ASC"
);

mysqli_stmt_bind_param(
    $stmt_detail,
    "i",
    $id_servis
);

mysqli_stmt_execute($stmt_detail);

$query_detail =
    mysqli_stmt_get_result($stmt_detail);


/*
 * =====================================================
 * TOTAL SPAREPART
 * =====================================================
 */

$stmt_total = mysqli_prepare(
    $koneksi,
    "SELECT
        COALESCE(
            SUM(subtotal),
            0
        ) AS total_sparepart

     FROM detail_servis

     WHERE id_servis = ?"
);

mysqli_stmt_bind_param(
    $stmt_total,
    "i",
    $id_servis
);

mysqli_stmt_execute($stmt_total);

$hasil_total =
    mysqli_stmt_get_result($stmt_total);

$total_data =
    mysqli_fetch_assoc($hasil_total);

$total_sparepart =
    (float) $total_data["total_sparepart"];

$total_servis =
    (float) $data["biaya_jasa"]
    + $total_sparepart;

$dp =
    (float) $data["dp"];

$sisa_bayar =
    max(0, $total_servis - $dp);


/*
 * =====================================================
 * DOKUMENTASI
 * =====================================================
 */

$stmt_dokumentasi = mysqli_prepare(
    $koneksi,
    "SELECT
        dokumentasi_servis.*,

        teknisi.nama_teknisi,
        sparepart.nama_sparepart

     FROM dokumentasi_servis

     JOIN teknisi
        ON dokumentasi_servis.id_teknisi =
           teknisi.id_teknisi

     LEFT JOIN detail_servis
        ON dokumentasi_servis.id_detail =
           detail_servis.id_detail

     LEFT JOIN sparepart
        ON detail_servis.id_sparepart =
           sparepart.id_sparepart

     WHERE dokumentasi_servis.id_servis = ?

     ORDER BY
        dokumentasi_servis.tanggal_upload DESC"
);

mysqli_stmt_bind_param(
    $stmt_dokumentasi,
    "i",
    $id_servis
);

mysqli_stmt_execute($stmt_dokumentasi);

$query_dokumentasi =
    mysqli_stmt_get_result($stmt_dokumentasi);


/*
 * =====================================================
 * STATUS BADGE
 * =====================================================
 */

$status_class = "status-info";

if (
    $data["status"] === "Selesai" ||
    $data["status"] === "Sudah Diambil"
) {

    $status_class = "status-success";

} elseif (
    $data["status"] === "Menunggu Sparepart"
) {

    $status_class = "status-warning";
}


$page_title =
    "Detail Servis - " . $data["kode_servis"];

require '../template/header.php';
require '../template/sidebar.php';

?>


<!-- =====================================================
     HEADER
     ===================================================== -->

<div class="page-header">

    <div>

        <h1>
            Detail Servis
        </h1>

        <p class="page-description">

            Work order

            <strong>
                <?= htmlspecialchars(
                    $data["kode_servis"]
                ); ?>
            </strong>

        </p>

    </div>


    <span class="status <?= $status_class; ?>">

        <?= htmlspecialchars(
            $data["status"]
        ); ?>

    </span>

</div>


<div class="toolbar">

    <a
        href="index.php"
        class="btn btn-secondary"
    >
        Kembali
    </a>


    <?php if ($_SESSION["role"] === "admin"): ?>

        <a
            href="edit.php?id=<?= $id_servis; ?>"
            class="btn"
        >
            Edit Servis
        </a>

    <?php endif; ?>

</div>



<!-- =====================================================
     PELANGGAN + PERANGKAT
     ===================================================== -->

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


            <div class="info-row">

                <span class="info-label">
                    Alamat
                </span>

                <span>
                    <?= nl2br(
                        htmlspecialchars(
                            $data["alamat"]
                        )
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
                    Merk
                </span>

                <span>
                    <?= htmlspecialchars(
                        $data["merk"]
                    ); ?>
                </span>

            </div>


            <div class="info-row">

                <span class="info-label">
                    Tipe
                </span>

                <span>
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
                    <?= !empty($data["serial_number"])
                        ? htmlspecialchars(
                            $data["serial_number"]
                        )
                        : "-"; ?>
                </span>

            </div>

        </div>

    </section>

</div>



<!-- =====================================================
     INFORMASI SERVIS
     ===================================================== -->

<section class="panel">

    <h2 class="panel-title">
        Informasi Servis
    </h2>


    <div class="detail-grid">

        <div class="info-list">

            <div class="info-row">

                <span class="info-label">
                    Kode Servis
                </span>

                <strong>
                    <?= htmlspecialchars(
                        $data["kode_servis"]
                    ); ?>
                </strong>

            </div>


            <div class="info-row">

                <span class="info-label">
                    Tanggal Masuk
                </span>

                <span>
                    <?= htmlspecialchars(
                        $data["tanggal_masuk"]
                    ); ?>
                </span>

            </div>


            <div class="info-row">

                <span class="info-label">
                    Teknisi
                </span>

                <span>
                    <?= htmlspecialchars(
                        $data["nama_teknisi"]
                    ); ?>
                </span>

            </div>


            <div class="info-row">

                <span class="info-label">
                    Status
                </span>

                <span class="status <?= $status_class; ?>">

                    <?= htmlspecialchars(
                        $data["status"]
                    ); ?>

                </span>

            </div>

        </div>


        <div>

            <div class="service-text">

                <span class="info-label">
                    Keluhan Pelanggan
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

                    <?php if (!empty($data["diagnosa"])): ?>

                        <?= nl2br(
                            htmlspecialchars(
                                $data["diagnosa"]
                            )
                        ); ?>

                    <?php else: ?>

                        <span class="text-muted">
                            Belum ada diagnosa.
                        </span>

                    <?php endif; ?>

                </p>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     SPAREPART DIGUNAKAN
     ===================================================== -->

<section class="panel">

    <h2 class="panel-title">
        Sparepart Digunakan
    </h2>


    <?php if (
        mysqli_num_rows($query_detail) > 0
    ): ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Sparepart</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                $no_detail = 1;

                while (
                    $detail =
                    mysqli_fetch_assoc($query_detail)
                ):

                ?>

                    <tr>

                        <td>
                            <?= $no_detail++; ?>
                        </td>


                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $detail["nama_sparepart"]
                                ); ?>
                            </strong>

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


                        <td>

                            <form
                                action="proses.php"
                                method="POST"
                                onsubmit="return confirm('Hapus sparepart dari servis ini?')"
                            >

                                <input
                                    type="hidden"
                                    name="aksi"
                                    value="hapus_sparepart"
                                >

                                <input
                                    type="hidden"
                                    name="id_detail"
                                    value="<?= $detail["id_detail"]; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="id_servis"
                                    value="<?= $id_servis; ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >
                                    Hapus
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>


    <?php else: ?>

        <div class="empty-state compact">

            Belum ada sparepart yang digunakan.

        </div>

    <?php endif; ?>



    <div class="section-divider"></div>


    <h3>
        Tambah Sparepart
    </h3>


    <form
        action="proses.php"
        method="POST"
    >

        <input
            type="hidden"
            name="aksi"
            value="tambah_sparepart"
        >

        <input
            type="hidden"
            name="id_servis"
            value="<?= $id_servis; ?>"
        >


        <div class="form-grid">

            <div class="form-group">

                <label>
                    Sparepart
                </label>

                <select
                    name="id_sparepart"
                    required
                >

                    <option value="">
                        Pilih Sparepart
                    </option>


                    <?php while (
                        $sparepart =
                        mysqli_fetch_assoc(
                            $query_sparepart
                        )
                    ): ?>

                        <option
                            value="<?= $sparepart["id_sparepart"]; ?>"
                        >

                            <?= htmlspecialchars(
                                $sparepart["nama_sparepart"]
                            ); ?>

                            —

                            Stok:
                            <?= (int) $sparepart["stok"]; ?>

                            —

                            Rp<?= number_format(
                                $sparepart["harga"],
                                0,
                                ",",
                                "."
                            ); ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <div class="form-group">

                <label>
                    Jumlah
                </label>

                <input
                    type="number"
                    name="jumlah"
                    min="1"
                    value="1"
                    required
                >

            </div>

        </div>


        <button
            type="submit"
            class="btn"
        >
            Tambahkan Sparepart
        </button>

    </form>

</section>



<!-- =====================================================
     BIAYA
     ===================================================== -->

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
                Total Sparepart
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
                Total Servis
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



<!-- =====================================================
     DOKUMENTASI
     ===================================================== -->

<section class="panel">

    <div class="panel-header">

        <div>

            <h2 class="panel-title">
                Dokumentasi Servis
            </h2>

            <p class="page-description">
                Foto kerusakan, penggantian,
                dan sparepart selama pengerjaan.
            </p>

        </div>


        <?php if (
            $_SESSION["role"] === "teknisi"
        ): ?>

            <a
                href="../dokumentasi/upload.php?id=<?= $id_servis; ?>"
                class="btn"
            >
                Upload Dokumentasi
            </a>

        <?php endif; ?>

    </div>


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
                            src="../<?= htmlspecialchars(
                                $dok["foto"]
                            ); ?>"
                            alt="Dokumentasi Servis"
                        >

                    </div>


                    <div class="documentation-body">

                        <div class="documentation-top">

                            <span class="status status-info">

                                <?= htmlspecialchars(
                                    ucfirst(
                                        $dok["jenis_foto"]
                                    )
                                ); ?>

                            </span>


                            <?php if (
                                $dok["tampil_pelanggan"]
                            ): ?>

                                <span class="status status-success">
                                    Publik
                                </span>

                            <?php else: ?>

                                <span class="status status-warning">
                                    Internal
                                </span>

                            <?php endif; ?>

                        </div>


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


                        <p>

                            <span class="info-label">
                                Keterangan
                            </span>

                            <br>

                            <?= !empty(
                                $dok["keterangan"]
                            )
                                ? nl2br(
                                    htmlspecialchars(
                                        $dok["keterangan"]
                                    )
                                )
                                : "-"; ?>

                        </p>


                        <p>

                            <span class="info-label">
                                Teknisi
                            </span>

                            <br>

                            <?= htmlspecialchars(
                                $dok["nama_teknisi"]
                            ); ?>

                        </p>


                        <p>

                            <span class="info-label">
                                Tanggal
                            </span>

                            <br>

                            <?= htmlspecialchars(
                                $dok["tanggal_upload"]
                            ); ?>

                        </p>


                        <?php if (
                            $_SESSION["role"] === "admin" ||
                            (
                                $_SESSION["role"] === "teknisi" &&
                                (int) $dok["id_teknisi"] ===
                                (int) $_SESSION["id_teknisi"]
                            )
                        ): ?>

                            <form
                                action="../dokumentasi/hapus.php"
                                method="POST"
                                onsubmit="return confirm('Hapus dokumentasi ini?')"
                            >

                                <input
                                    type="hidden"
                                    name="id_dokumentasi"
                                    value="<?= $dok["id_dokumentasi"]; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="id_servis"
                                    value="<?= $id_servis; ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >
                                    Hapus Foto
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                </article>

            <?php endwhile; ?>

        </div>


    <?php else: ?>

        <div class="empty-state">

            <h3>
                Belum ada dokumentasi
            </h3>

            <p>
                Foto proses pengerjaan akan
                ditampilkan di bagian ini.
            </p>

        </div>

    <?php endif; ?>

</section>



<!-- =====================================================
     UPDATE TEKNISI
     ===================================================== -->

<?php if (
    $_SESSION["role"] === "teknisi"
): ?>

    <section class="panel">

        <h2 class="panel-title">
            Update Pengerjaan
        </h2>


        <form
            action="proses.php"
            method="POST"
        >

            <input
                type="hidden"
                name="aksi"
                value="update_teknisi"
            >

            <input
                type="hidden"
                name="id_servis"
                value="<?= $id_servis; ?>"
            >


            <div class="form-grid">

                <div class="form-group full">

                    <label>
                        Diagnosa
                    </label>

                    <textarea
                        name="diagnosa"
                        rows="5"
                        placeholder="Masukkan hasil diagnosa..."
                    ><?= htmlspecialchars(
                        $data["diagnosa"] ?? ""
                    ); ?></textarea>

                </div>


                <div class="form-group full">

                    <label>
                        Status Pengerjaan
                    </label>

                    <select
                        name="status"
                        required
                    >

                        <?php

                        $status_list = [
                            "Menunggu Pemeriksaan",
                            "Sedang Diperiksa",
                            "Menunggu Sparepart",
                            "Sedang Diperbaiki",
                            "Pengujian",
                            "Selesai",
                            "Sudah Diambil"
                        ];

                        foreach (
                            $status_list as $status
                        ):

                        ?>

                            <option
                                value="<?= $status; ?>"
                                <?= $data["status"] === $status
                                    ? "selected"
                                    : ""; ?>
                            >
                                <?= htmlspecialchars($status); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>


            <button
                type="submit"
                class="btn"
            >
                Simpan Update
            </button>

        </form>

    </section>

<?php endif; ?>



<!-- =====================================================
     ARSIP PELANGGAN
     ===================================================== -->

<?php if (
    $_SESSION["role"] === "admin" &&
    $data["status"] === "Sudah Diambil"
): ?>

    <section class="panel">

        <h2 class="panel-title">
            Arsip Pelanggan
        </h2>

        <p class="page-description">
            Servis sudah selesai dan perangkat
            sudah diambil. Pelanggan dapat
            diarsipkan jika tidak memiliki
            servis aktif lainnya.
        </p>

        <br>


        <form
            action="../pelanggan/arsip.php"
            method="POST"
            onsubmit="return confirm('Arsipkan pelanggan ini?')"
        >

            <input
                type="hidden"
                name="aksi"
                value="arsipkan"
            >

            <input
                type="hidden"
                name="id_pelanggan"
                value="<?= $data["id_pelanggan"]; ?>"
            >

            <button
                type="submit"
                class="btn btn-secondary"
            >
                Arsipkan Pelanggan
            </button>

        </form>

    </section>

<?php endif; ?>


<?php

require '../template/footer.php';

?>