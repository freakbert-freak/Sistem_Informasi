<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_teknisi();

$id_servis =
    (int) ($_GET["id"] ?? 0);

if ($id_servis <= 0) {
    die("ID servis tidak valid.");
}


/*
 * Ambil data servis.
 * Teknisi hanya boleh upload dokumentasi
 * untuk servis miliknya sendiri.
 */
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT
        servis.id_servis,
        servis.kode_servis,
        servis.id_teknisi,
        servis.status,

        pelanggan.nama AS nama_pelanggan,

        perangkat.jenis_perangkat,
        perangkat.merk,
        perangkat.tipe

     FROM servis

     JOIN perangkat
        ON servis.id_perangkat =
           perangkat.id_perangkat

     JOIN pelanggan
        ON perangkat.id_pelanggan =
           pelanggan.id_pelanggan

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


if (
    (int) $data["id_teknisi"] !==
    (int) $_SESSION["id_teknisi"]
) {
    die("Anda tidak memiliki akses ke servis ini.");
}


/*
 * Detail sparepart servis.
 * Digunakan jika dokumentasi berkaitan
 * dengan penggantian sparepart.
 */
$stmt_detail = mysqli_prepare(
    $koneksi,
    "SELECT
        detail_servis.id_detail,
        detail_servis.jumlah,

        sparepart.nama_sparepart

     FROM detail_servis

     JOIN sparepart
        ON detail_servis.id_sparepart =
           sparepart.id_sparepart

     WHERE detail_servis.id_servis = ?

     ORDER BY sparepart.nama_sparepart ASC"
);

mysqli_stmt_bind_param(
    $stmt_detail,
    "i",
    $id_servis
);

mysqli_stmt_execute($stmt_detail);

$query_detail =
    mysqli_stmt_get_result($stmt_detail);


$page_title = "Upload Dokumentasi";

require '../template/header.php';
require '../template/sidebar.php';

?>


<div class="page-header">

    <div>

        <h1>
            Upload Dokumentasi
        </h1>

        <p class="page-description">

            Tambahkan foto dokumentasi untuk servis

            <strong>
                <?= htmlspecialchars(
                    $data["kode_servis"]
                ); ?>
            </strong>

        </p>

    </div>

</div>


<div class="toolbar">

    <a
        href="../servis/detail.php?id=<?= $id_servis; ?>"
        class="btn btn-secondary"
    >
        Kembali ke Detail Servis
    </a>

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
                    Pelanggan
                </span>

                <span>
                    <?= htmlspecialchars(
                        $data["nama_pelanggan"]
                    ); ?>
                </span>

            </div>

        </div>


        <div class="info-list">

            <div class="info-row">

                <span class="info-label">
                    Perangkat
                </span>

                <span>

                    <?= htmlspecialchars(
                        $data["jenis_perangkat"]
                    ); ?>

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
                    Status
                </span>

                <span class="status status-info">

                    <?= htmlspecialchars(
                        $data["status"]
                    ); ?>

                </span>

            </div>

        </div>

    </div>

</section>


<!-- FORM UPLOAD -->

<section class="panel">

    <h2 class="panel-title">
        Data Dokumentasi
    </h2>


    <form
        action="proses_upload.php"
        method="POST"
        enctype="multipart/form-data"
    >

        <input
            type="hidden"
            name="id_servis"
            value="<?= $id_servis; ?>"
        >


        <div class="form-grid">


            <!-- JENIS FOTO -->

            <div class="form-group">

                <label>
                    Jenis Dokumentasi
                </label>

                <select
                    name="jenis_foto"
                    required
                >

                    <option value="">
                        Pilih Jenis Dokumentasi
                    </option>

                    <option value="kerusakan">
                        Kerusakan
                    </option>

                    <option value="penggantian">
                        Penggantian
                    </option>

                    <option value="sparepart">
                        Sparepart
                    </option>

                </select>

            </div>


            <!-- SPAREPART -->

            <div class="form-group">

                <label>
                    Sparepart Terkait
                </label>

                <select
                    name="id_detail"
                >

                    <option value="">
                        Tidak Berkaitan dengan Sparepart
                    </option>


                    <?php while (
                        $detail =
                        mysqli_fetch_assoc($query_detail)
                    ): ?>

                        <option
                            value="<?= $detail["id_detail"]; ?>"
                        >

                            <?= htmlspecialchars(
                                $detail["nama_sparepart"]
                            ); ?>

                            ×
                            <?= (int) $detail["jumlah"]; ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <!-- FOTO -->

            <div class="form-group full">

                <label>
                    Foto Dokumentasi
                </label>

                <input
                    type="file"
                    name="foto"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    required
                >

                <small class="form-hint">
                    Format JPG, PNG, atau WEBP.
                    Ukuran maksimal 5 MB.
                </small>

            </div>


            <!-- KETERANGAN -->

            <div class="form-group full">

                <label>
                    Keterangan
                </label>

                <textarea
                    name="keterangan"
                    rows="5"
                    placeholder="Contoh: Kondisi konektor charger sebelum dilakukan penggantian."
                ></textarea>

            </div>


            <!-- TAMPIL PELANGGAN -->

            <div class="form-group full">

                <label class="checkbox-control">

                    <input
                        type="checkbox"
                        name="tampil_pelanggan"
                        value="1"
                        checked
                    >

                    <span>
                        Tampilkan dokumentasi ini
                        kepada pelanggan
                    </span>

                </label>

                <small class="form-hint">
                    Jika aktif, foto akan muncul pada halaman
                    cek status servis milik pelanggan.
                </small>

            </div>

        </div>


        <div class="toolbar">

            <button
                type="submit"
                class="btn"
            >
                Upload Dokumentasi
            </button>


            <a
                href="../servis/detail.php?id=<?= $id_servis; ?>"
                class="btn btn-secondary"
            >
                Batal
            </a>

        </div>

    </form>

</section>


<?php

require '../template/footer.php';

?>