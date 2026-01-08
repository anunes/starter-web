<form class="mt-3" action="/admin/settings" method="post" enctype="multipart/form-data">
    <?= csrf() ?>
    <div class="row g-3 align-items-center">
        <div class="col-12">
            <label class="col-form-label" for="company_logo"><?= t('admin.company_logo') ?></label>
        </div>
        <div class="col-12 col-md-auto">
            <img class="profile-avatar" src="<?= $logoSrc ?>" alt="<?= t('admin.company_logo') ?>">
        </div>
        <div class="col-12 col-md">
            <input class="visually-hidden" type="file" id="company_logo" name="company_logo" accept="image/*">
            <label class="btn btn-outline-primary btn-sm" for="company_logo"><?= $logoButtonLabel ?></label>
            <div class="form-text"><?= t('admin.logo_help') ?></div>
        </div>
    </div>
    <?php if (!$logoAvailable): ?>
        <div class="mt-2 text-danger"><?= t('admin.logo_missing') ?></div>
    <?php endif; ?>
    <div class="mt-3 d-flex justify-content-end">
        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-floppy2-fill"></i> <?= t('admin.update_logo') ?></button>
    </div>
</form>
