<?php

require 'config/koneksi.php';
require 'config/auth.php';

wajib_login();

$page_title = "Dashboard";

$nama = $_SESSION["nama"];
$role = $_SESSION["role"];


/*
 * DATA DASHBOARD
 */
if ($role === "admin") {

    $pelanggan = mysqli_fetch_assoc(
        mysqli_query(
            $koneksi,
            "SELECT COUNT(*) AS total
             FROM pelanggan
             WHERE aktif = 1"
        )
    )["total"];

    $perangkat = mysqli_fetch_assoc(
        mysqli_query(
            $koneksi,
            "SELECT COUNT(*) AS total
             FROM perangkat"
        )
    )["total"];

    $teknisi = mysqli_fetch_assoc(
        mysqli_query(
            $koneksi,
            "SELECT COUNT(*) AS total
             FROM teknisi"
        )
    )["total"];

    $servis = mysqli_fetch_assoc(
        mysqli_query(
            $koneksi,
            "SELECT COUNT(*) AS total
             FROM servis
             WHERE status != 'Sudah Diambil'"
        )
    )["total"];

} else {

    $id_teknisi =
        (int) $_SESSION["id_teknisi"];

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT COUNT(*) AS total
         FROM servis
         WHERE id_teknisi = ?
         AND status != 'Sudah Diambil'"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id_teknisi
    );

    mysqli_stmt_execute($stmt);

    $hasil =
        mysqli_stmt_get_result($stmt);

    $servis =
        mysqli_fetch_assoc($hasil)["total"];
}


require 'template/header.php';
require 'template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Dashboard</h1>

        <p class="page-description">
            Selamat datang,
            <?= htmlspecialchars($nama); ?>.
        </p>

    </div>

</div>


<?php if ($role === "admin"): ?>

    <div class="dashboard-grid">

        <div class="metric">

            <span class="metric-label">
                Pelanggan Aktif
            </span>

            <div class="metric-value">
                <?= $pelanggan; ?>
            </div>

        </div>


        <div class="metric">

            <span class="metric-label">
                Perangkat
            </span>

            <div class="metric-value">
                <?= $perangkat; ?>
            </div>

        </div>


        <div class="metric">

            <span class="metric-label">
                Teknisi
            </span>

            <div class="metric-value">
                <?= $teknisi; ?>
            </div>

        </div>


        <div class="metric">

            <span class="metric-label">
                Servis Aktif
            </span>

            <div class="metric-value">
                <?= $servis; ?>
            </div>

        </div>

    </div>


    <section class="panel">

        <h2 class="panel-title">
            Administrasi
        </h2>

        <div class="toolbar">

            <a
                class="btn"
                href="servis/tambah.php"
            >
                Tambah Servis
            </a>

            <a
                class="btn btn-secondary"
                href="pelanggan/tambah.php"
            >
                Tambah Pelanggan
            </a>

            <a
                class="btn btn-secondary"
                href="servis/index.php"
            >
                Lihat Servis
            </a>

        </div>

    </section>


<?php else: ?>

    <div class="dashboard-grid">

        <div class="metric">

            <span class="metric-label">
                Servis Aktif Saya
            </span>

            <div class="metric-value">
                <?= $servis; ?>
            </div>

        </div>

    </div>


    <section class="panel">

        <h2 class="panel-title">
            Pekerjaan Teknisi
        </h2>

        <p class="page-description">
            Lihat servis yang ditugaskan kepada
            Anda dan perbarui progres pengerjaan.
        </p>

        <br>

        <a
            class="btn"
            href="servis/index.php"
        >
            Buka Servis Saya
        </a>

    </section>

<?php endif; ?>


<?php

require 'template/footer.php';

?>