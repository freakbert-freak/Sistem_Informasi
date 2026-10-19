<?php

require 'config/koneksi.php';
require 'config/auth.php';

$base_url = "/servis-komputer";


/*
 * Jika sudah login,
 * langsung masuk dashboard.
 */
if (isset($_SESSION["id_user"])) {

    header(
        "Location: "
        . $base_url
        . "/index.php"
    );

    exit;
}


$error = "";


/*
 * =====================================================
 * PROSES LOGIN
 * =====================================================
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username =
        trim($_POST["username"] ?? "");

    $password =
        $_POST["password"] ?? "";


    if (
        $username === "" ||
        $password === ""
    ) {

        $error =
            "Username dan password harus diisi.";

    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT
                id_user,
                id_teknisi,
                username,
                password,
                role,
                nama

             FROM users

             WHERE username = ?

             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $username
        );

        mysqli_stmt_execute($stmt);

        $result =
            mysqli_stmt_get_result($stmt);

        $user =
            mysqli_fetch_assoc($result);


        if (
            $user &&
            password_verify(
                $password,
                $user["password"]
            )
        ) {

            session_regenerate_id(true);

            $_SESSION["id_user"] =
                (int) $user["id_user"];

            $_SESSION["id_teknisi"] =
                $user["id_teknisi"] !== null
                ? (int) $user["id_teknisi"]
                : null;

            $_SESSION["username"] =
                $user["username"];

            $_SESSION["nama"] =
                $user["nama"];

            $_SESSION["role"] =
                $user["role"];


            header(
                "Location: "
                . $base_url
                . "/index.php"
            );

            exit;

        } else {

            $error =
                "Username atau password salah.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Login - Albert Workshop
    </title>

   <link
        rel="stylesheet"
        href="<?= $base_url; ?>/assets/css/style.css"
    >

</head>
 

<body class="public-page login-page">



<!-- =========================================
     LOGIN
     ========================================= -->

<div class="public-shell login-shell">


    <div class="public-brand">

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


    <section class="public-card login-card">

        <div class="public-card-header">

            <span class="public-eyebrow">
                SYSTEM ACCESS
            </span>

            <h1>
                Login
            </h1>

            <p>
                Masuk menggunakan akun
                administrator atau teknisi.
            </p>

        </div>


        <?php if ($error !== ""): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action=""
        >

            <div class="form-group">

                <label>
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    value="<?= htmlspecialchars(
                        $_POST["username"] ?? ""
                    ); ?>"
                    placeholder="Masukkan username"
                    autocomplete="username"
                    required
                    autofocus
                >

            </div>


            <div class="form-group">

                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn public-submit"
            >
                Masuk ke Sistem
            </button>

        </form>


        <div class="public-divider"></div>


        <div class="public-bottom">

            <span>
                Pelanggan?
            </span>

            <a
                href="<?= $base_url; ?>/cek-servis.php"
            >
                Cek Status Servis
            </a>

        </div>

    </section>


    <div class="public-footer">

        <span>
            Albert Workshop
        </span>

        <span>
            Sistem Informasi Servis Komputer
        </span>

    </div>

</div>


</body>
</html>