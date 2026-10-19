<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$page_title = "Tambah Servis";


/*
 * Ambil perangkat beserta pemilik.
 */
$query_perangkat = mysqli_query(
    $koneksi,
    "SELECT
        perangkat.id_perangkat,
        perangkat.jenis_perangkat,
        perangkat.merk,
        perangkat.tipe,
        perangkat.serial_number,

        pelanggan.nama AS nama_pelanggan

     FROM perangkat

     JOIN pelanggan
        ON perangkat.id_pelanggan =
           pelanggan.id_pelanggan

     WHERE pelanggan.aktif = 1

     ORDER BY pelanggan.nama ASC"
);


/*
 * Ambil teknisi.
 */
$query_teknisi = mysqli_query(
    $koneksi,
    "SELECT
        id_teknisi,
        nama_teknisi

     FROM teknisi

     ORDER BY nama_teknisi ASC"
);


require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Tambah Servis</h1>

        <p class="page-description">
            Buat pekerjaan servis baru untuk perangkat pelanggan.
        </p>

    </div>

</div>


<section class="panel">

    <h2 class="panel-title">
        Data Servis
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


            <div class="form-group full">

                <label>
                    Perangkat Pelanggan
                </label>

                <select
                    name="id_perangkat"
                    required
                >

                    <option value="">
                        Pilih Perangkat
                    </option>

                    <?php while (
                        $perangkat =
                        mysqli_fetch_assoc($query_perangkat)
                    ): ?>

                        <option
                            value="<?= $perangkat["id_perangkat"]; ?>"
                        >

                            <?= htmlspecialchars(
                                $perangkat["nama_pelanggan"]
                            ); ?>

                            —

                            <?= htmlspecialchars(
                                $perangkat["jenis_perangkat"]
                            ); ?>

                            <?= htmlspecialchars(
                                $perangkat["merk"]
                            ); ?>

                            <?= htmlspecialchars(
                                $perangkat["tipe"]
                            ); ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <div class="form-group">

                <label>
                    Teknisi
                </label>

                <select
                    name="id_teknisi"
                    required
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

            </div>


            <div class="form-group">

                <label>
                    Tanggal Masuk
                </label>

                <input
                    type="date"
                    name="tanggal_masuk"
                    value="<?= date('Y-m-d'); ?>"
                    required
                >

            </div>


            <div class="form-group full">

                <label>
                    Keluhan Pelanggan
                </label>

                <textarea
                    name="keluhan"
                    rows="5"
                    placeholder="Jelaskan keluhan perangkat pelanggan..."
                    required
                ></textarea>

            </div>


            <div class="form-group">

                <label>
                    Biaya Jasa Awal
                </label>

                <input
                    type="number"
                    name="biaya_jasa"
                    min="0"
                    step="1"
                    value="0"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    DP (Uang Muka)
                </label>

                <input
                    type="number"
                    name="dp"
                    min="0"
                    step="1"
                    value="0"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Status Awal
                </label>

                <select
                    name="status"
                    required
                >

                    <option value="Menunggu Pemeriksaan">
                        Menunggu Pemeriksaan
                    </option>

                    <option value="Sedang Diperiksa">
                        Sedang Diperiksa
                    </option>

                    <option value="Menunggu Sparepart">
                        Menunggu Sparepart
                    </option>

                    <option value="Sedang Diperbaiki">
                        Sedang Diperbaiki
                    </option>

                    <option value="Pengujian">
                        Pengujian
                    </option>

                    <option value="Selesai">
                        Selesai
                    </option>

                    <option value="Sudah Diambil">
                        Sudah Diambil
                    </option>

                </select>

            </div>

        </div>


        <div class="toolbar">

            <button
                type="submit"
                class="btn"
            >
                Simpan Servis
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