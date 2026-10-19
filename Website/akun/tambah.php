<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$page_title = "Tambah Akun";


$query_teknisi = mysqli_query(
    $koneksi,
    "SELECT
        teknisi.id_teknisi,
        teknisi.nama_teknisi

     FROM teknisi

     LEFT JOIN users
        ON teknisi.id_teknisi =
           users.id_teknisi

     WHERE users.id_user IS NULL

     ORDER BY teknisi.nama_teknisi ASC"
);


require '../template/header.php';
require '../template/sidebar.php';

?>


<div class="page-header">

    <div>

        <h1>Tambah Akun</h1>

        <p class="page-description">
            Buat akun baru untuk administrator
            atau teknisi.
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
            value="tambah"
        >


        <div class="form-grid">


            <div class="form-group">

                <label>
                    Nama
                </label>

                <input
                    type="text"
                    name="nama"
                    placeholder="Nama pengguna"
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
                    placeholder="Username login"
                    autocomplete="off"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    autocomplete="new-password"
                    required
                >

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

                    <option value="">
                        Pilih Role
                    </option>

                    <option value="admin">
                        Administrator
                    </option>

                    <option value="teknisi">
                        Teknisi
                    </option>

                </select>

            </div>


            <div
                class="form-group full"
                id="teknisiGroup"
                style="display: none;"
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
                        >
                            <?= htmlspecialchars(
                                $teknisi["nama_teknisi"]
                            ); ?>
                        </option>

                    <?php endwhile; ?>

                </select>

                <small class="form-hint">
                    Wajib dipilih apabila role akun adalah Teknisi.
                </small>

            </div>

        </div>


        <div class="toolbar">

            <button
                type="submit"
                class="btn"
            >
                Simpan Akun
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