<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$page_title = "Tambah Perangkat";

$query_pelanggan = mysqli_query(
    $koneksi,
    "SELECT
        id_pelanggan,
        nama

     FROM pelanggan

     WHERE aktif = 1

     ORDER BY nama ASC"
);

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Tambah Perangkat</h1>

        <p class="page-description">
            Daftarkan perangkat baru milik pelanggan.
        </p>

    </div>

</div>


<section class="panel">

    <h2 class="panel-title">
        Data Perangkat
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
                    Pemilik
                </label>

                <select
                    name="id_pelanggan"
                    required
                >

                    <option value="">
                        Pilih Pelanggan
                    </option>

                    <?php while (
                        $pelanggan =
                        mysqli_fetch_assoc($query_pelanggan)
                    ): ?>

                        <option
                            value="<?= $pelanggan["id_pelanggan"]; ?>"
                        >
                            <?= htmlspecialchars(
                                $pelanggan["nama"]
                            ); ?>
                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <div class="form-group">

                <label>
                    Jenis Perangkat
                </label>

                <select
                    name="jenis_perangkat"
                    required
                >

                    <option value="">
                        Pilih Jenis
                    </option>

                    <option value="Laptop">
                        Laptop
                    </option>

                    <option value="PC">
                        PC
                    </option>

                    <option value="Printer">
                        Printer
                    </option>

                    <option value="Monitor">
                        Monitor
                    </option>

                    <option value="Lainnya">
                        Lainnya
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label>
                    Merk
                </label>

                <input
                    type="text"
                    name="merk"
                    placeholder="Contoh: MSI"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Tipe
                </label>

                <input
                    type="text"
                    name="tipe"
                    placeholder="Contoh: Thin 15 B12UCX"
                    required
                >

            </div>


            <div class="form-group full">

                <label>
                    Serial Number
                </label>

                <input
                    type="text"
                    name="serial_number"
                    placeholder="Masukkan serial number jika tersedia"
                >

            </div>

        </div>


        <div class="toolbar">

            <button
                type="submit"
                class="btn"
            >
                Simpan Perangkat
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