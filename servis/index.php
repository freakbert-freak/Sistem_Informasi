<?php

require '../config/koneksi.php';
require '../config/auth.php';

wajib_login();

$page_title = "Data Servis";


/*
 * ADMIN melihat semua servis.
 * TEKNISI hanya melihat servis miliknya.
 */
if ($_SESSION["role"] === "admin") {

    $query = mysqli_query(
        $koneksi,
        "SELECT
            servis.*,

            perangkat.jenis_perangkat,
            perangkat.merk,
            perangkat.tipe,

            pelanggan.nama AS nama_pelanggan,

            teknisi.nama_teknisi

         FROM servis

         JOIN perangkat
            ON servis.id_perangkat =
               perangkat.id_perangkat

         JOIN pelanggan
            ON perangkat.id_pelanggan =
               pelanggan.id_pelanggan

         LEFT JOIN teknisi
            ON servis.id_teknisi =
               teknisi.id_teknisi

         ORDER BY servis.id_servis DESC"
    );

} else {

    $id_teknisi =
        (int) id_teknisi_login();

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT
            servis.*,

            perangkat.jenis_perangkat,
            perangkat.merk,
            perangkat.tipe,

            pelanggan.nama AS nama_pelanggan,

            teknisi.nama_teknisi

         FROM servis

         JOIN perangkat
            ON servis.id_perangkat =
               perangkat.id_perangkat

         JOIN pelanggan
            ON perangkat.id_pelanggan =
               pelanggan.id_pelanggan

         LEFT JOIN teknisi
            ON servis.id_teknisi =
               teknisi.id_teknisi

         WHERE servis.id_teknisi = ?

         ORDER BY servis.id_servis DESC"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id_teknisi
    );

    mysqli_stmt_execute($stmt);

    $query =
        mysqli_stmt_get_result($stmt);
}


require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>
            <?= $_SESSION["role"] === "admin"
                ? "Data Servis"
                : "Servis Saya"; ?>
        </h1>

        <p class="page-description">

            <?php if ($_SESSION["role"] === "admin"): ?>

                Kelola seluruh pekerjaan servis perangkat pelanggan.

            <?php else: ?>

                Daftar pekerjaan servis yang ditugaskan kepada Anda.

            <?php endif; ?>

        </p>

    </div>

</div>


<div class="toolbar">

    <?php if ($_SESSION["role"] === "admin"): ?>

        <a
            href="tambah.php"
            class="btn"
        >
            Tambah Servis
        </a>

    <?php endif; ?>

</div>


<section class="panel">

    <h2 class="panel-title">
        Daftar Servis
    </h2>


    <?php if (mysqli_num_rows($query) > 0): ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Perangkat</th>
                        <th>Teknisi</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                $no = 1;

                while ($data = mysqli_fetch_assoc($query)):

                    $status_class = "status-info";

                    if (
                        $data["status"] === "Selesai" ||
                        $data["status"] === "Sudah Diambil"
                    ) {
                        $status_class = "status-success";

                    } elseif (
                        $data["status"] === "Menunggu Sparepart"
                    ) {
                        $status_class = "status-warning";

                    } elseif (
                        $data["status"] === "Sedang Diperbaiki" ||
                        $data["status"] === "Pengujian"
                    ) {
                        $status_class = "status-info";
                    }

                ?>

                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>


                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $data["kode_servis"]
                                ); ?>
                            </strong>

                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $data["nama_pelanggan"]
                            ); ?>
                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $data["jenis_perangkat"]
                            ); ?>

                            <br>

                            <span class="text-muted">

                                <?= htmlspecialchars(
                                    $data["merk"]
                                ); ?>

                                <?= htmlspecialchars(
                                    $data["tipe"]
                                ); ?>

                            </span>

                        </td>


                        <td>

                            <?= !empty($data["nama_teknisi"])
                                ? htmlspecialchars(
                                    $data["nama_teknisi"]
                                )
                                : "-"; ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $data["tanggal_masuk"]
                            ); ?>

                        </td>


                        <td>

                            <span class="status <?= $status_class; ?>">

                                <?= htmlspecialchars(
                                    $data["status"]
                                ); ?>

                            </span>

                        </td>


                        <td>

                            <div class="toolbar">

                                <a
                                    href="detail.php?id=<?= $data["id_servis"]; ?>"
                                    class="btn"
                                >
                                    Detail
                                </a>


                                <?php if ($_SESSION["role"] === "admin"): ?>

                                    <a
                                        href="edit.php?id=<?= $data["id_servis"]; ?>"
                                        class="btn btn-secondary"
                                    >
                                        Edit
                                    </a>


                                    <a
                                        href="hapus.php?id=<?= $data["id_servis"]; ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus servis ini? Stok sparepart yang digunakan akan dikembalikan.')"
                                    >
                                        Hapus
                                    </a>

                                <?php endif; ?>

                            </div>

                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>


    <?php else: ?>

        <div class="empty-state">

            <h3>
                Belum ada data servis
            </h3>

            <p>
                Belum ada pekerjaan servis yang tersedia.
            </p>


            <?php if ($_SESSION["role"] === "admin"): ?>

                <a
                    href="tambah.php"
                    class="btn"
                >
                    Tambah Servis
                </a>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</section>


<?php

require '../template/footer.php';

?>