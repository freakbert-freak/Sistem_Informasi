<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$page_title = "Tambah Teknisi";

require '../template/header.php';
require '../template/sidebar.php';

?>

<div class="page-header">

    <div>

        <h1>Tambah Teknisi</h1>

        <p class="page-description">
            Tambahkan teknisi baru ke dalam sistem servis.
        </p>

    </div>

</div>


<section class="panel">

    <h2 class="panel-title">
        Data Teknisi
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
                    Nama Teknisi
                </label>

                <input
                    type="text"
                    name="nama_teknisi"
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
                    Keahlian
                </label>

                <textarea
                    name="keahlian"
                    rows="4"
                    placeholder="Contoh: Laptop, PC, instalasi OS, troubleshooting hardware"
                    required
                ></textarea>

            </div>

        </div>


        <div class="toolbar">

            <button
                type="submit"
                class="btn"
            >
                Simpan Teknisi
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