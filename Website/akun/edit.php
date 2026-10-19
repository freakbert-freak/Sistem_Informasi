<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();


$id_user =
    (int) ($_GET["id"] ?? 0);

if ($id_user <= 0) {
    die("ID akun tidak valid.");
}


/*
 * Data akun.
 */
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT *
     FROM users
     WHERE id_user = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_user
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$data =
    mysqli_fetch_assoc($result);


if (!$data) {
    die("Akun tidak ditemukan.");
}


/*
 * Teknisi yang belum mempunyai akun,
 * ditambah teknisi milik akun ini.
 */
$current_teknisi =
    (int) ($data["id_teknisi"] ?? 0);


$query_teknisi = mysqli_query(
    $koneksi,
    "SELECT
        teknisi.id_teknisi,
        teknisi.nama_teknisi

     FROM teknisi

     LEFT JOIN users
        ON teknisi.id_teknisi =
           users.id_teknisi
        AND users.id_user != "
        . (int) $id_user .
    "

     WHERE users.id_user IS NULL

     ORDER BY teknisi.nama_teknisi ASC"
);


$page_title = "Edit Akun";

require '../template/header.php';
require '../template/sidebar.php';

?>


<div class="page-header">

    <div>

        <h1>Edit Akun</h1>

        <p class="page-description">
            Perbarui informasi dan hak akses akun.
        </p>

    </div>

</div>


<section class="panel">

    <h2 class="panel-title">
        Data Akun
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
            name="id_user"
            value="<?= $data["id_user"]; ?>"
        >


        <div class="form-grid">


            <div class="form-group">

                <label>
                    Nama
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
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    value="<?= htmlspecialchars(
                        $data["username"]
                    ); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Password Baru
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Kosongkan jika tidak ingin mengganti password"
                    autocomplete="new-password"
                >

                <small class="form-hint">
                    Password lama tetap digunakan jika
                    kolom ini dikosongkan.
                </small>

            </div>


            <div class="form-group">

                <label>
                    Role
                </label>

                <select
                    name="role"
                    id="role"
                    required
                >

                    <option
                        value="admin"
                        <?= $data["role"] === "admin"
                            ? "selected"
                            : ""; ?>
                    >
                        Administrator
                    </option>


                    <option
                        value="teknisi"
                        <?= $data["role"] === "teknisi"
                            ? "selected"
                            : ""; ?>
                    >
                        Teknisi
                    </option>

                </select>

            </div>


            <div
                class="form-group full"
                id="teknisiGroup"
            >

                <label>
                    Teknisi Terkait
                </label>

                <select
                    name="id_teknisi"
                    id="idTeknisi"
                >

                    <option value="">
                        Pilih Teknisi
                    </option>


                    <?php while (
                        $teknisi =
                        mysqli_fetch_assoc($query_teknisi)
                    ): ?>

                        <option
                            value="<?= $teknisi["id_teknisi"]; ?>"
                            <?= (int) $teknisi["id_teknisi"] ===
                                $current_teknisi
                                ? "selected"
                                : ""; ?>
                        >

                            <?= htmlspecialchars(
                                $teknisi["nama_teknisi"]
                            ); ?>

                        </option>

                    <?php endwhile; ?>

                </select>

                <small class="form-hint">
                    Digunakan untuk menghubungkan akun
                    dengan data teknisi.
                </small>

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


<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const role =
            document.getElementById("role");

        const teknisiGroup =
            document.getElementById("teknisiGroup");

        const idTeknisi =
            document.getElementById("idTeknisi");


        function updateTeknisiField() {

            if (role.value === "teknisi") {

                teknisiGroup.style.display = "block";

                idTeknisi.required = true;

            } else {

                teknisiGroup.style.display = "none";

                idTeknisi.required = false;

                idTeknisi.value = "";

            }

        }


        role.addEventListener(
            "change",
            updateTeknisiField
        );


        updateTeknisiField();

    }
);

</script>


<?php

require '../template/footer.php';

?>