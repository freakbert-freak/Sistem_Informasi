<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();


/*
 * =====================================================
 * ARSIPKAN PELANGGAN
 * =====================================================
 */
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    ($_POST["aksi"] ?? "") === "arsipkan"
) {

    $id_pelanggan =
        (int) ($_POST["id_pelanggan"] ?? 0);

    if ($id_pelanggan <= 0) {
        die("ID pelanggan tidak valid.");
    }


    /*
     * Cek apakah masih ada servis
     * yang belum berstatus Sudah Diambil.
     */
    $cek_servis = mysqli_prepare(
        $koneksi,
        "SELECT COUNT(*) AS jumlah

         FROM servis

         JOIN perangkat
            ON servis.id_perangkat =
               perangkat.id_perangkat

         WHERE perangkat.id_pelanggan = ?

         AND servis.status != 'Sudah Diambil'"
    );

    mysqli_stmt_bind_param(
        $cek_servis,
        "i",
        $id_pelanggan
    );

    mysqli_stmt_execute($cek_servis);

    $hasil_cek =
        mysqli_stmt_get_result($cek_servis);

    $data_cek =
        mysqli_fetch_assoc($hasil_cek);

    $servis_aktif =
        (int) $data_cek["jumlah"];


    if ($servis_aktif > 0) {

        die(
            "Pelanggan belum dapat diarsipkan karena masih memiliki "
            . $servis_aktif
            . " servis yang belum berstatus Sudah Diambil."
        );
    }


    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE pelanggan
         SET aktif = 0
         WHERE id_pelanggan = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id_pelanggan
    );

    mysqli_stmt_execute($stmt);

    header("Location: arsip.php");
    exit;
}


/*
 * =====================================================
 * AKTIFKAN KEMBALI
 * =====================================================
 */
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    ($_POST["aksi"] ?? "") === "aktifkan"
) {

    $id_pelanggan =
        (int) ($_POST["id_pelanggan"] ?? 0);

    if ($id_pelanggan <= 0) {
        die("ID pelanggan tidak valid.");
    }


    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE pelanggan
         SET aktif = 1
         WHERE id_pelanggan = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id_pelanggan
    );

    mysqli_stmt_execute($stmt);

    header("Location: arsip.php");
    exit;
}


/*
 * =====================================================
 * DATA PELANGGAN ARSIP
 * =====================================================
 */
$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM pelanggan
     WHERE aktif = 0
     ORDER BY id_pelanggan DESC"
);


$page_title = "Pelanggan Diarsipkan";

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Pelanggan Diarsipkan</h1>

        <p class="page-description">
            Data pelanggan yang sudah tidak aktif
            tetapi histori servisnya tetap tersimpan.
        </p>

    </div>

</div>


<div class="toolbar">

    <a
        href="index.php"
        class="btn btn-secondary"
    >
        Kembali ke Pelanggan
    </a>

</div>


<section class="panel">

    <h2 class="panel-title">
        Daftar Arsip Pelanggan
    </h2>


    <?php if (mysqli_num_rows($query) > 0): ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>

                </thead>


                <tbody>

                <?php

                $no = 1;

                while (
                    $data =
                    mysqli_fetch_assoc($query)
                ):

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

                            <span class="status status-warning">
                                Diarsipkan
                            </span>

                        </td>

                        <td>

                            <form
                                method="POST"
                                onsubmit="return confirm('Aktifkan kembali pelanggan ini?')"
                            >

                                <input
                                    type="hidden"
                                    name="aksi"
                                    value="aktifkan"
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
                                    Aktifkan Kembali
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>


    <?php else: ?>

        <div class="empty-state">

            <h3>
                Belum ada pelanggan diarsipkan
            </h3>

            <p>
                Pelanggan yang diarsipkan akan
                muncul di halaman ini.
            </p>

            <a
                href="index.php"
                class="btn btn-secondary"
            >
                Kembali
            </a>

        </div>

    <?php endif; ?>

</section>


<?php

require '../template/footer.php';

?>