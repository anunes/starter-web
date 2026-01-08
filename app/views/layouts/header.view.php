<!DOCTYPE>
<html lang='<?= lang() ?>'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <script src='/core/javascript/theme.js'></script>
    <link rel='stylesheet' href='/assets/css/bootstrap.min.css'>
    <link rel='stylesheet' href='/assets/icons/font/bootstrap-icons.min.css'>
    <link rel='stylesheet' href='/assets/css/dataTables_bootstrap5.css'>
    <link rel='stylesheet' href='/assets/css/style.css'>
    <link rel='stylesheet' href='/assets/css/theme.css'>
    <style>
        :root {
            --app-icons-color: <?= htmlspecialchars($appIconsColor, ENT_QUOTES, 'UTF-8') ?>;
        }
    </style>
    <link rel='icon' type='image/x-icon' href='/favicon/favicon.ico?v=<?= $faviconVersion ?>'>
    <link rel='icon' type='image/png' sizes='16x16' href='/favicon/favicon-16x16.png?v=<?= $faviconVersion ?>'>
    <link rel='icon' type='image/png' sizes='32x32' href='/favicon/favicon-32x32.png?v=<?= $faviconVersion ?>'>
    <link rel='apple-touch-icon' sizes='180x180' href='/favicon/apple-touch-icon.png?v=<?= $faviconVersion ?>'>
    <link rel='icon' type='image/png' sizes='192x192' href='/favicon/android-chrome-192x192.png?v=<?= $faviconVersion ?>'>
    <link rel='icon' type='image/png' sizes='512x512' href='/favicon/android-chrome-512x512.png?v=<?= $faviconVersion ?>'>
    <link rel='manifest' href='/favicon/site.webmanifest?v=<?= $faviconVersion ?>'>
    <title><?= APP_NAME ?></title>
</head>

<body>
    <?= view_partial('layouts/navbar') ?>
    <div class='container'>
        <?= $flashHtml ?>
