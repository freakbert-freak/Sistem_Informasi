<?php

require '../config/koneksi.php';
require '../config/auth.php';

wajib_login();

$page_title = "Data Pelanggan";

/*
 * Ambil hanya pelanggan aktif.
 */
$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM pelanggan
     WHERE aktif = 1
     ORDER BY id_pelanggan DESC"
);


/*
 * Template utama.
 */
require '../template/header.php';
require '../template/sidebar.php';

?>


<div class="page-header">

    <div>

        <h1>Data Pelanggan</h1>

        <p class="page-description">
            Kelola data pelanggan aktif yang terdaftar.
        </p>

    </div>

</div>


<!-- ============================= -->
<!-- TOOLBAR -->
<!-- ============================= -->

<div class="toolbar">

    <?php if ($_SESSION["role"] === "admin"): ?>

        <a
            href="tambah.php"
            class="btn"
        >
            Tambah Pelanggan
        </a>


        <a
            href="arsip.php"
            class="btn btn-secondary"
        >
            Pelanggan Diarsipkan
        </a>

    <?php endif; ?>

</div>


<!-- ============================= -->
<!-- DATA PELANGGAN -->
<!-- ============================= -->

<section class="panel">

    <h2 class="panel-title">
        Daftar Pelanggan
    </h2>


    <?php if (mysqli_num_rows($query) > 0): ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>ID Pelanggan</th>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <th>Status</th>

                        <?php if ($_SESSION["role"] === "admin"): ?>

                            <th>Aksi</th>

                        <?php endif; ?>

                    </tr>

                </thead>


                <tbody>

                <?php

                $no = 1;

                while ($data = mysqli_fetch_assoc($query)):

                ?>

                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>


                        <td>
                            <?= (int) $data["id_pelanggan"]; ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $data["nama"]
                            ); ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $data["no_hp"]
                            ); ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $data["alamat"]
                            ); ?>
                        </td>


                        <td>

                            <span class="status status-success">
                                Aktif
                            </span>

                        </td>


                        <?php if ($_SESSION["role"] === "admin"): ?>

                            <td>

                                <div class="toolbar">


                                    <!-- EDIT -->

                                    <a
                                        href="edit.php?id=<?= $data["id_pelanggan"]; ?>"
                                        class="btn btn-secondary"
                                    >
                                        Edit
                                    </a>


                                    <!-- HAPUS -->

                                    <a
                                        href="hapus.php?id=<?= $data["id_pelanggan"]; ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus pelanggan ini?')"
                                    >
                                        Hapus
                                    </a>


                                    <!-- ARSIPKAN -->

                                    <form
                                        action="arsip.php"
                                        method="POST"
                                        style="display:inline;"
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
                                            Arsipkan
                                        </button>

                                    </form>

                                </div>

                            </td>

                        <?php endif; ?>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>


    <?php else: ?>

        <div class="empty-state">

            <h3>Belum ada pelanggan aktif</h3>

            <p>
                Tambahkan pelanggan baru untuk mulai
                mencatat perangkat dan servis.
            </p>

            <?php if ($_SESSION["role"] === "admin"): ?>

                <a
                    href="tambah.php"
                    class="btn"
                >
                    Tambah Pelanggan
                </a>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</section>


<?php

require '../template/footer.php';

?>