<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$id_pelanggan =
    (int) ($_GET["id"] ?? 0);

if ($id_pelanggan <= 0) {
    die("ID pelanggan tidak valid.");
}


/*
 * Ambil data pelanggan.
 */
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT *
     FROM pelanggan
     WHERE id_pelanggan = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_pelanggan
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$data =
    mysqli_fetch_assoc($result);

if (!$data) {
    die("Pelanggan tidak ditemukan.");
}


$page_title = "Edit Pelanggan";

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Edit Pelanggan</h1>

        <p class="page-description">
            Perbarui informasi pelanggan yang terdaftar.
        </p>

    </div>

</div>


<section class="panel">

    <h2 class="panel-title">
        Data Pelanggan
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
            name="id_pelanggan"
            value="<?= $data["id_pelanggan"]; ?>"
        >


        <div class="form-grid">


            <div class="form-group">

                <label>
                    Nama Pelanggan
                </label>

                <input
                    type="text"
                    name="nama"
                    value="<?= htmlspecialchars(
                        $data["nama"]
                    ); ?>"
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
                        $data["no_hp"]
                    ); ?>"
                    required
                >

            </div>


            <div class="form-group full">

                <label>
                    Alamat
                </label>

                <textarea
                    name="alamat"
                    rows="4"
                    required
                ><?= htmlspecialchars(
                    $data["alamat"]
                ); ?></textarea>

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