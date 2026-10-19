<?php

$role = $_SESSION["role"] ?? "";
$nama = $_SESSION["nama"] ?? "User";

?>

<button
    class="sidebar-toggle"
    id="sidebarToggle"
    type="button"
    aria-label="Buka menu"
>
    ☰
</button>

<aside
    class="sidebar"
    id="sidebar"
>

    <div class="sidebar-brand">

        <div class="brand-mark">
            AW
        </div>

        <div>

            <strong>
                ALBERT WORKSHOP
            </strong>

            <span>
                Computer Service System
            </span>

        </div>

    </div>


    <div class="sidebar-user">

        <span class="sidebar-label">
            LOGIN SEBAGAI
        </span>

        <strong>
            <?= htmlspecialchars($nama); ?>
        </strong>

        <span class="role-badge">
            <?= strtoupper(
                htmlspecialchars($role)
            ); ?>
        </span>

    </div>


    <nav class="sidebar-nav">

        <span class="sidebar-label">
            MENU
        </span>

        <a
            href="<?= $base_url; ?>/index.php"
            class="nav-link"
        >
            Dashboard
        </a>


        <?php if ($role === "admin"): ?>

            <a
                href="<?= $base_url; ?>/pelanggan/index.php"
                class="nav-link"
            >
                Pelanggan
            </a>

            <a
                href="<?= $base_url; ?>/perangkat/index.php"
                class="nav-link"
            >
                Perangkat
            </a>

            <a
                href="<?= $base_url; ?>/teknisi/index.php"
                class="nav-link"
            >
                Teknisi
            </a>

            <a
                href="<?= $base_url; ?>/sparepart/index.php"
                class="nav-link"
            >
                Sparepart
            </a>

            <a
                href="<?= $base_url; ?>/servis/index.php"
                class="nav-link"
            >
                Servis
            </a>

            <a
                href="<?= $base_url; ?>/akun/index.php"
                class="nav-link"
            >
                Kelola Akun
            </a>


        <?php elseif ($role === "teknisi"): ?>

            <a
                href="<?= $base_url; ?>/servis/index.php"
                class="nav-link"
            >
                Servis Saya
            </a>

            <a
                href="<?= $base_url; ?>/perangkat/index.php"
                class="nav-link"
            >
                Perangkat
            </a>

            <a
                href="<?= $base_url; ?>/sparepart/index.php"
                class="nav-link"
            >
                Sparepart
            </a>

        <?php endif; ?>

    </nav>


    <div class="sidebar-footer">

        <a
            href="<?= $base_url; ?>/logout.php"
            class="logout-link"
        >
            Logout
        </a>

    </div>

</aside>

<main class="main-content">