<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kepegawaian</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
<header>
    <h1>Sistem Informasi Kepegawaian</h1>
    <div class="header-right">
        <div id="clock" class="clock">
            <span class="clock-time"></span>
            <span class="clock-date"></span>
        </div>
        <div class="user-info">
            Halo, <?= htmlspecialchars(Session::get('nama') ?? 'User') ?>
            (<?= Session::get('role') ?? '' ?>)
        </div>
    </div>
</header>
<?php include __DIR__ . '/sidebar.php'; ?>
<main>