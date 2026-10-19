<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$id_teknisi =
    (int) ($_GET["id"] ?? 0);

if ($id_teknisi <= 0) {
    die("ID teknisi tidak valid.");
}


$stmt = mysqli_prepare(
    $koneksi,
    "SELECT *
     FROM teknisi
     WHERE id_teknisi = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_teknisi
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$data =
    mysqli_fetch_assoc($result);

if (!$data) {
    die("Teknisi tidak ditemukan.");
}


$page_title = "Edit Teknisi";

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Edit Teknisi</h1>

        <p class="page-description">
            Perbarui informasi teknisi yang terdaftar.
        </p>

    </div>

</div>


<section class="panel">

    <h2 class="panel-title">
        Data Teknisi
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
            name="id_teknisi"
            value="<?= $data["id_teknisi"]; ?>"
        >


        <div class="form-grid">

            <div class="form-group">

                <label>
                    Nama Teknisi
                </label>

                <input
                    type="text"
                    name="nama_teknisi"
                    value="<?= htmlspecialchars(
                        $data["nama_teknisi"]
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
                    Keahlian
                </label>

                <textarea
                    name="keahlian"
                    rows="4"
                    required
                ><?= htmlspecialchars(
                    $data["keahlian"]
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