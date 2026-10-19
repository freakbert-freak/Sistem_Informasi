<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$page_title = "Tambah Pelanggan";

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>
        <h1>Tambah Pelanggan</h1>

        <p class="page-description">
            Tambahkan pelanggan baru ke dalam sistem servis.
        </p>
    </div>

</div>


<section class="panel">

    <h2 class="panel-title">
        Data Pelanggan
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
                    Nama Pelanggan
                </label>

                <input
                    type="text"
                    name="nama"
                    placeholder="Contoh: Albert"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Nomor HP
                </label>

                <input
                    type="text"
                    name="no_hp"
                    placeholder="Contoh: 081234567890"
                    required
                >

            </div>


            <div class="form-group full">

                <label>
                    Alamat
                </label>

                <textarea
                    name="alamat"
                    rows="4"
                    placeholder="Masukkan alamat pelanggan"
                    required
                ></textarea>

            </div>

        </div>


        <div class="toolbar">

            <button
                type="submit"
                class="btn"
            >
                Simpan Pelanggan
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