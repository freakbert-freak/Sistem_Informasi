<?php

require '../config/koneksi.php';
require '../config/auth.php';

wajib_login();

$page_title = "Data Sparepart";

$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM sparepart
     ORDER BY id_sparepart DESC"
);

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Data Sparepart</h1>

        <p class="page-description">
            Kelola stok dan harga sparepart untuk kebutuhan servis.
        </p>

    </div>

</div>


<div class="toolbar">

    <?php if ($_SESSION["role"] === "admin"): ?>

        <a
            href="tambah.php"
            class="btn"
        >
            Tambah Sparepart
        </a>

    <?php endif; ?>

</div>


<section class="panel">

    <h2 class="panel-title">
        Daftar Sparepart
    </h2>


    <?php if (mysqli_num_rows($query) > 0): ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>ID</th>
                        <th>Nama Sparepart</th>
                        <th>Stok</th>
                        <th>Harga</th>

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
                            <?= (int) $data["id_sparepart"]; ?>
                        </td>


                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $data["nama_sparepart"]
                                ); ?>
                            </strong>

                        </td>


                        <td>

                            <?php if ((int) $data["stok"] <= 0): ?>

                                <span class="status status-danger">
                                    Habis
                                </span>

                            <?php elseif ((int) $data["stok"] <= 5): ?>

                                <span class="status status-warning">
                                    <?= (int) $data["stok"]; ?> Tersisa
                                </span>

                            <?php else: ?>

                                <span class="status status-success">
                                    <?= (int) $data["stok"]; ?> Tersedia
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            Rp <?= number_format(
                                (float) $data["harga"],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </td>


                        <?php if ($_SESSION["role"] === "admin"): ?>

                            <td>

                                <div class="toolbar">

                                    <a
                                        href="edit.php?id=<?= $data["id_sparepart"]; ?>"
                                        class="btn btn-secondary"
                                    >
                                        Edit
                                    </a>


                                    <a
                                        href="hapus.php?id=<?= $data["id_sparepart"]; ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus sparepart ini?')"
                                    >
                                        Hapus
                                    </a>

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

            <h3>
                Belum ada sparepart
            </h3>

            <p>
                Tambahkan sparepart agar dapat digunakan
                dalam proses servis.
            </p>

            <?php if ($_SESSION["role"] === "admin"): ?>

                <a
                    href="tambah.php"
                    class="btn"
                >
                    Tambah Sparepart
                </a>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</section>


<?php

require '../template/footer.php';

?>