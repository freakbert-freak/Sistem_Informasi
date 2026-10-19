<?php

require '../config/koneksi.php';
require '../config/auth.php';

khusus_admin();

$page_title = "Kelola Akun";


$query = mysqli_query(
    $koneksi,
    "SELECT
        users.id_user,
        users.id_teknisi,
        users.username,
        users.role,
        users.nama,

        teknisi.nama_teknisi

     FROM users

     LEFT JOIN teknisi
        ON users.id_teknisi =
           teknisi.id_teknisi

     ORDER BY users.id_user DESC"
);


require '../template/header.php';
require '../template/sidebar.php';

?>


<div class="page-header">

    <div>

        <h1>Kelola Akun</h1>

        <p class="page-description">
            Kelola akun administrator dan teknisi
            yang dapat mengakses sistem.
        </p>

    </div>

</div>


<div class="toolbar">

    <a
        href="tambah.php"
        class="btn"
    >
        Tambah Akun
    </a>

</div>


<section class="panel">

    <h2 class="panel-title">
        Daftar Akun
    </h2>


    <?php if (mysqli_num_rows($query) > 0): ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Teknisi Terkait</th>
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
                            <?= (int) $data["id_user"]; ?>
                        </td>


                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $data["nama"]
                                ); ?>
                            </strong>

                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $data["username"]
                            ); ?>
                        </td>


                        <td>

                            <?php if (
                                $data["role"] === "admin"
                            ): ?>

                                <span class="status status-info">
                                    Admin
                                </span>

                            <?php else: ?>

                                <span class="status status-success">
                                    Teknisi
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php if (
                                !empty($data["nama_teknisi"])
                            ): ?>

                                <?= htmlspecialchars(
                                    $data["nama_teknisi"]
                                ); ?>

                            <?php else: ?>

                                <span class="text-muted">
                                    -
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <div class="toolbar">

                                <a
                                    href="edit.php?id=<?= $data["id_user"]; ?>"
                                    class="btn btn-secondary"
                                >
                                    Edit
                                </a>


                                <?php if (
                                    (int) $data["id_user"] !==
                                    (int) $_SESSION["id_user"]
                                ): ?>

                                    <a
                                        href="hapus.php?id=<?= $data["id_user"]; ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus akun ini?')"
                                    >
                                        Hapus
                                    </a>

                                <?php else: ?>

                                    <span class="status status-warning">
                                        Akun Aktif
                                    </span>

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
                Belum ada akun
            </h3>

            <p>
                Tambahkan akun administrator
                atau teknisi untuk menggunakan sistem.
            </p>

            <a
                href="tambah.php"
                class="btn"
            >
                Tambah Akun
            </a>

        </div>

    <?php endif; ?>

</section>


<?php

require '../template/footer.php';

?>