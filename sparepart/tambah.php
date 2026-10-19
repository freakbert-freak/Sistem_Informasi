<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$page_title = "Tambah Sparepart";

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Tambah Sparepart</h1>

        <p class="page-description">
            Tambahkan sparepart baru beserta stok dan harga.
        </p>

    </div>

</div>


<section class="panel">

    <h2 class="panel-title">
        Data Sparepart
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
                    Nama Sparepart
                </label>

                <input
                    type="text"
                    name="nama_sparepart"
                    placeholder="Contoh: SSD SATA 256GB"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Stok
                </label>

                <input
                    type="number"
                    name="stok"
                    min="0"
                    value="0"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Harga
                </label>

                <input
                    type="number"
                    name="harga"
                    min="0"
                    step="1"
                    placeholder="Contoh: 250000"
                    required
                >

            </div>

        </div>


        <div class="toolbar">

            <button
                type="submit"
                class="btn"
            >
                Simpan Sparepart
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