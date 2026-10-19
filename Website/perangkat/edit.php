<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$id_perangkat =
    (int) ($_GET["id"] ?? 0);

if ($id_perangkat <= 0) {
    die("ID perangkat tidak valid.");
}


$stmt = mysqli_prepare(
    $koneksi,
    "SELECT *
     FROM perangkat
     WHERE id_perangkat = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_perangkat
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$data =
    mysqli_fetch_assoc($result);

if (!$data) {
    die("Perangkat tidak ditemukan.");
}


$query_pelanggan = mysqli_query(
    $koneksi,
    "SELECT
        id_pelanggan,
        nama,
        aktif

     FROM pelanggan

     WHERE aktif = 1
        OR id_pelanggan = "
        . (int) $data["id_pelanggan"] .
    "

     ORDER BY nama ASC"
);


$page_title = "Edit Perangkat";

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Edit Perangkat</h1>

        <p class="page-description">
            Perbarui data perangkat yang terdaftar.
        </p>

    </div>

</div>


<section class="panel">

    <h2 class="panel-title">
        Data Perangkat
    </h2>


    <form
        action="proses.php"
        method="POST"
    >

        <input
            type="hidden"
            name="aksi"
            value="edit"
        >

        <input
            type="hidden"
            name="id_perangkat"
            value="<?= $data["id_perangkat"]; ?>"
        >


        <div class="form-grid">

            <div class="form-group">

                <label>
                    Pemilik
                </label>

                <select
                    name="id_pelanggan"
                    required
                >

                    <?php while (
                        $pelanggan =
                        mysqli_fetch_assoc($query_pelanggan)
                    ): ?>

                        <option
                            value="<?= $pelanggan["id_pelanggan"]; ?>"
                            <?= (int) $pelanggan["id_pelanggan"] ===
                                (int) $data["id_pelanggan"]
                                ? "selected"
                                : ""; ?>
                        >

                            <?= htmlspecialchars(
                                $pelanggan["nama"]
                            ); ?>

                            <?= (int) $pelanggan["aktif"] === 0
                                ? " (Diarsipkan)"
                                : ""; ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <div class="form-group">

                <label>
                    Jenis Perangkat
                </label>

                <select
                    name="jenis_perangkat"
                    required
                >

                    <?php

                    $jenis_list = [
                        "Laptop",
                        "PC",
                        "Printer",
                        "Monitor",
                        "Lainnya"
                    ];

                    foreach ($jenis_list as $jenis):

                    ?>

                        <option
                            value="<?= $jenis; ?>"
                            <?= $data["jenis_perangkat"] === $jenis
                                ? "selected"
                                : ""; ?>
                        >
                            <?= htmlspecialchars($jenis); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="form-group">

                <label>
                    Merk
                </label>

                <input
                    type="text"
                    name="merk"
                    value="<?= htmlspecialchars(
                        $data["merk"]
                    ); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Tipe
                </label>

                <input
                    type="text"
                    name="tipe"
                    value="<?= htmlspecialchars(
                        $data["tipe"]
                    ); ?>"
                    required
                >

            </div>


            <div class="form-group full">

                <label>
                    Serial Number
                </label>

                <input
                    type="text"
                    name="serial_number"
                    value="<?= htmlspecialchars(
                        $data["serial_number"] ?? ""
                    ); ?>"
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
                href="index.php"
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