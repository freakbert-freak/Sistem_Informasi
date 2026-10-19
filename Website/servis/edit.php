<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$id_servis =
    (int) ($_GET["id"] ?? 0);

if ($id_servis <= 0) {
    die("ID servis tidak valid.");
}


/*
 * Ambil servis.
 */
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT *
     FROM servis
     WHERE id_servis = ?
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
    die("Data servis tidak ditemukan.");
}


/*
 * Data perangkat.
 */
$query_perangkat = mysqli_query(
    $koneksi,
    "SELECT
        perangkat.id_perangkat,
        perangkat.jenis_perangkat,
        perangkat.merk,
        perangkat.tipe,

        pelanggan.nama AS nama_pelanggan,
        pelanggan.aktif

     FROM perangkat

     JOIN pelanggan
        ON perangkat.id_pelanggan =
           pelanggan.id_pelanggan

     WHERE pelanggan.aktif = 1
        OR perangkat.id_perangkat = "
        . (int) $data["id_perangkat"] .
    "

     ORDER BY pelanggan.nama ASC"
);


/*
 * Data teknisi.
 */
$query_teknisi = mysqli_query(
    $koneksi,
    "SELECT
        id_teknisi,
        nama_teknisi

     FROM teknisi

     ORDER BY nama_teknisi ASC"
);


$page_title = "Edit Servis";

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Edit Servis</h1>

        <p class="page-description">

            Perbarui data servis

            <strong>
                <?= htmlspecialchars(
                    $data["kode_servis"]
                ); ?>
            </strong>

        </p>

    </div>

</div>


<section class="panel">

    <h2 class="panel-title">
        Data Servis
    </h2>


    <form
        action="proses.php"
        method="POST"
    >

        <input
            type="hidden"
            name="aksi"
            value="edit_admin"
        >

        <input
            type="hidden"
            name="id_servis"
            value="<?= $data["id_servis"]; ?>"
        >


        <div class="form-grid">


            <div class="form-group">

                <label>
                    Kode Servis
                </label>

                <input
                    type="text"
                    value="<?= htmlspecialchars(
                        $data["kode_servis"]
                    ); ?>"
                    disabled
                >

            </div>


            <div class="form-group">

                <label>
                    Tanggal Masuk
                </label>

                <input
                    type="date"
                    name="tanggal_masuk"
                    value="<?= htmlspecialchars(
                        $data["tanggal_masuk"]
                    ); ?>"
                    required
                >

            </div>


            <div class="form-group full">

                <label>
                    Perangkat
                </label>

                <select
                    name="id_perangkat"
                    required
                >

                    <?php while (
                        $perangkat =
                        mysqli_fetch_assoc($query_perangkat)
                    ): ?>

                        <option
                            value="<?= $perangkat["id_perangkat"]; ?>"
                            <?= (int) $perangkat["id_perangkat"] ===
                                (int) $data["id_perangkat"]
                                ? "selected"
                                : ""; ?>
                        >

                            <?= htmlspecialchars(
                                $perangkat["nama_pelanggan"]
                            ); ?>

                            —

                            <?= htmlspecialchars(
                                $perangkat["jenis_perangkat"]
                            ); ?>

                            <?= htmlspecialchars(
                                $perangkat["merk"]
                            ); ?>

                            <?= htmlspecialchars(
                                $perangkat["tipe"]
                            ); ?>

                            <?= (int) $perangkat["aktif"] === 0
                                ? " (Pelanggan Diarsipkan)"
                                : ""; ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <div class="form-group">

                <label>
                    Teknisi
                </label>

                <select
                    name="id_teknisi"
                    required
                >

                    <?php while (
                        $teknisi =
                        mysqli_fetch_assoc($query_teknisi)
                    ): ?>

                        <option
                            value="<?= $teknisi["id_teknisi"]; ?>"
                            <?= (int) $teknisi["id_teknisi"] ===
                                (int) $data["id_teknisi"]
                                ? "selected"
                                : ""; ?>
                        >

                            <?= htmlspecialchars(
                                $teknisi["nama_teknisi"]
                            ); ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <div class="form-group">

                <label>
                    Status
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

                    foreach ($status_list as $status):

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


            <div class="form-group full">

                <label>
                    Keluhan
                </label>

                <textarea
                    name="keluhan"
                    rows="4"
                    required
                ><?= htmlspecialchars(
                    $data["keluhan"]
                ); ?></textarea>

            </div>


            <div class="form-group full">

                <label>
                    Diagnosa
                </label>

                <textarea
                    name="diagnosa"
                    rows="4"
                    placeholder="Diagnosa kerusakan perangkat"
                ><?= htmlspecialchars(
                    $data["diagnosa"] ?? ""
                ); ?></textarea>

            </div>


            <div class="form-group">

                <label>
                    Biaya Jasa
                </label>

                <input
                    type="number"
                    name="biaya_jasa"
                    min="0"
                    step="1"
                    value="<?= htmlspecialchars(
                        $data["biaya_jasa"]
                    ); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    DP (Uang Muka)
                </label>

                <input
                    type="number"
                    name="dp"
                    min="0"
                    step="1"
                    value="<?= htmlspecialchars(
                        $data["dp"]
                    ); ?>"
                    required
                >

            </div>

        </div>


        <div class="toolbar">

            <button
                type="submit"
                class="btn"
            >
                Simpan Perubahan
            </button>

            <a
                href="detail.php?id=<?= $data["id_servis"]; ?>"
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