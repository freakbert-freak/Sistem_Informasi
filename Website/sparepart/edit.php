<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$id_sparepart =
    (int) ($_GET["id"] ?? 0);

if ($id_sparepart <= 0) {
    die("ID sparepart tidak valid.");
}


$stmt = mysqli_prepare(
    $koneksi,
    "SELECT *
     FROM sparepart
     WHERE id_sparepart = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_sparepart
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$data =
    mysqli_fetch_assoc($result);

if (!$data) {
    die("Sparepart tidak ditemukan.");
}


$page_title = "Edit Sparepart";

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Edit Sparepart</h1>

        <p class="page-description">
            Perbarui informasi stok dan harga sparepart.
        </p>

    </div>

</div>


<section class="panel">

    <h2 class="panel-title">
        Data Sparepart
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
            name="id_sparepart"
            value="<?= $data["id_sparepart"]; ?>"
        >


        <div class="form-grid">


            <div class="form-group full">

                <label>
                    Nama Sparepart
                </label>

                <input
                    type="text"
                    name="nama_sparepart"
                    value="<?= htmlspecialchars(
                        $data["nama_sparepart"]
                    ); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Stok
                </label>

                <input
                    type="number"
                    name="stok"
                    min="0"
                    value="<?= (int) $data["stok"]; ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Harga
                </label>

                <input
                    type="number"
                    name="harga"
                    min="0"
                    step="1"
                    value="<?= htmlspecialchars(
                        $data["harga"]
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