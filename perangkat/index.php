<?php

require '../config/koneksi.php';
require '../config/auth.php';

wajib_login();

$page_title = "Data Perangkat";

$query = mysqli_query(
    $koneksi,
    "SELECT
        perangkat.*,
        pelanggan.nama AS nama_pelanggan

     FROM perangkat

     JOIN pelanggan
        ON perangkat.id_pelanggan =
           pelanggan.id_pelanggan

     ORDER BY perangkat.id_perangkat DESC"
);

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Data Perangkat</h1>

        <p class="page-description">
            Kelola perangkat milik pelanggan yang terdaftar.
        </p>

    </div>

</div>


<div class="toolbar">

    <?php if ($_SESSION["role"] === "admin"): ?>

        <a
            href="tambah.php"
            class="btn"
        >
            Tambah Perangkat
        </a>

    <?php endif; ?>

</div>


<section class="panel">

    <h2 class="panel-title">
        Daftar Perangkat
    </h2>


    <?php if (mysqli_num_rows($query) > 0): ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Pemilik</th>
                        <th>Jenis</th>
                        <th>Merk</th>
                        <th>Tipe</th>
                        <th>Serial Number</th>

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
                            <?= (int) $data["id_perangkat"]; ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $data["nama_pelanggan"]
                            ); ?>
                        </td>

                        <td>
                            <span class="status status-info">
                                <?= htmlspecialchars(
                                    $data["jenis_perangkat"]
                                ); ?>
                            </span>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $data["merk"]
                            ); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $data["tipe"]
                            ); ?>
                        </td>

                        <td>
                            <?= !empty($data["serial_number"])
                                ? htmlspecialchars($data["serial_number"])
                                : "-"; ?>
                        </td>


                        <?php if ($_SESSION["role"] === "admin"): ?>

                            <td>

                                <div class="toolbar">

                                    <a
                                        href="edit.php?id=<?= $data["id_perangkat"]; ?>"
                                        class="btn btn-secondary"
                                    >
                                        Edit
                                    </a>

                                    <a
                                        href="hapus.php?id=<?= $data["id_perangkat"]; ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus perangkat ini?')"
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

            <h3>Belum ada perangkat</h3>

            <p>
                Tambahkan perangkat pelanggan agar bisa dibuatkan servis.
            </p>

            <?php if ($_SESSION["role"] === "admin"): ?>

                <a
                    href="tambah.php"
                    class="btn"
                >
                    Tambah Perangkat
                </a>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</section>


<?php

require '../template/footer.php';

?>