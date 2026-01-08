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
