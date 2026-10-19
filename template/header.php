<?php

if (!isset($page_title)) {
    $page_title = "Servis Komputer";
}

$base_url = "/servis-komputer";

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
        <?= htmlspecialchars($page_title); ?>
    </title>

    <link
        rel="stylesheet"
        href="<?= $base_url; ?>/assets/css/style.css"
    >

    <script
        src="<?= $base_url; ?>/assets/js/script.js"
        defer
    ></script>

</head>

<body>

<div class="app-shell">