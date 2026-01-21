<!DOCTYPE html>
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
    <?= view_partial('layouts/partials/navbar') ?>
    <div class='container'>
        <?= $flashHtml ?>
        <?php echo $slot; ?>
    </div>
    <nav class="navbar navbar-expand-lg fixed-bottom bottom-nav glass">
        <div class="container d-flex align-items-center justify-content-between">
            <p class="bnav text-muted mb-0">&copy;
                <?= YEAR_START . '-' . date("Y") . ' ' . APP_AT ?>
            </p>
            <div class="d-flex align-items-center gap-3">
                <div class="lang-switcher">
                    <a class="lang-toggle <?= $currentLang === 'en' ? 'active' : '' ?>" href="/lang/en" aria-label="<?= t('language.english') ?>" title="<?= t('language.english') ?>" data-bs-toggle="tooltip">
                        <img class="lang-flag" src="/assets/img/flags/en.svg" alt="<?= t('language.english') ?>">
                    </a>
                    <a class="lang-toggle <?= $currentLang === 'pt' ? 'active' : '' ?>" href="/lang/pt" aria-label="<?= t('language.portuguese') ?>" title="<?= t('language.portuguese') ?>" data-bs-toggle="tooltip">
                        <img class="lang-flag" src="/assets/img/flags/pt.svg" alt="<?= t('language.portuguese') ?>">
                    </a>
                </div>
                <button class="btn btn-sm theme-toggle" id="themeToggle" type="button" aria-label="<?= t('theme.toggle_dark') ?>" aria-pressed="false" title="<?= t('theme.toggle_dark') ?>" data-bs-toggle="tooltip" data-label-light="<?= t('theme.toggle_dark') ?>" data-label-dark="<?= t('theme.toggle_light') ?>">
                    <i class="bi theme-icon bi-moon" id="themeIcon" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </nav>
    <script src="/core/javascript/jquery.js"></script>
    <script src="/core/javascript/bootstrap.bundle.min.js"></script>
    <script src="/core/javascript/dataTables.js"></script>
    <script src="/core/javascript/dataTables.bootstrap5.js"></script>
    <script src="/core/javascript/app.js"></script>

</body>

</html>
