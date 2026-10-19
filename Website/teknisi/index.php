<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$page_title = "Data Teknisi";

$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM teknisi
     ORDER BY id_teknisi DESC"
);

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Data Teknisi</h1>

        <p class="page-description">
            Kelola teknisi yang menangani servis perangkat pelanggan.
        </p>

    </div>

</div>


<div class="toolbar">

    <a
        href="tambah.php"
        class="btn"
    >
        Tambah Teknisi
    </a>

</div>


<section class="panel">

    <h2 class="panel-title">
        Daftar Teknisi
    </h2>


    <?php if (mysqli_num_rows($query) > 0): ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Nama Teknisi</th>
                        <th>No HP</th>
                        <th>Keahlian</th>
                        <th>Aksi</th>
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
                            <?= (int) $data["id_teknisi"]; ?>
                        </td>

                        <td>
                            <strong>
                                <?= htmlspecialchars(
                                    $data["nama_teknisi"]
                                ); ?>
                            </strong>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $data["no_hp"]
                            ); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $data["keahlian"]
                            ); ?>
                        </td>

                        <td>

                            <div class="toolbar">

                                <a
                                    href="edit.php?id=<?= $data["id_teknisi"]; ?>"
                                    class="btn btn-secondary"
                                >
                                    Edit
                                </a>

                                <a
                                    href="hapus.php?id=<?= $data["id_teknisi"]; ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus teknisi ini?')"
                                >
                                    Hapus
                                </a>

                            </div>

                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>


    <?php else: ?>

        <div class="empty-state">

            <h3>Belum ada teknisi</h3>

            <p>
                Tambahkan teknisi agar dapat ditugaskan
                menangani pekerjaan servis.
            </p>

            <a
                href="tambah.php"
                class="btn"
            >
                Tambah Teknisi
            </a>

        </div>

    <?php endif; ?>

</section>


<?php

require '../template/footer.php';

?>