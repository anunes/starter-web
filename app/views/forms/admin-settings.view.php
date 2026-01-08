<form class="mt-3" action="/admin/settings" method="post" enctype="multipart/form-data">
    <?= csrf() ?>
    <div class="row g-3 align-items-center">
        <div class="col-auto">
            <label class="col-form-label" for="app_icons_color"><?= t('admin.app_icons_color') ?></label>
        </div>
        <div class="col-auto">
            <input class="form-control" type="text" id="app_icons_color" name="app_icons_color" value="<?= htmlspecialchars($appIconsColor, ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= t('admin.color_placeholder') ?>">
        </div>
        <div class="col-auto">
            <input class="form-control form-control-color" type="color" id="app_icons_color_picker" name="app_icons_color_picker" value="<?= htmlspecialchars($pickerColor, ENT_QUOTES, 'UTF-8') ?>" title="<?= t('admin.app_icons_color') ?>" data-bs-toggle="tooltip">
        </div>
        <div class="col-auto">
            <span class="form-text"><?= t('admin.icons_help') ?></span>
        </div>
    </div>
    <hr>
    <div class="mt-3 settings-switches">
        <div class="form-check form-switch">
            <input type="hidden" name="auth_enabled_present" value="1">
            <input class="form-check-input" type="checkbox" id="auth_enabled" name="auth_enabled" value="1" <?= $authEnabled ? 'checked' : '' ?>>
            <label class="form-check-label" for="auth_enabled"><?= t('admin.enable_user_system') ?></label>
        </div>
        <?php if (!$authAvailable): ?>
            <div class="mt-2 text-danger"><?= t('admin.auth_missing') ?></div>
        <?php endif; ?>
        <div class="form-check form-switch mt-3">
            <input type="hidden" name="registration_enabled_present" value="1">
            <input class="form-check-input" type="checkbox" id="registration_enabled" name="registration_enabled" value="1" <?= $registrationEnabled ? 'checked' : '' ?>>
            <label class="form-check-label" for="registration_enabled"><?= t('admin.enable_registration') ?></label>
        </div>
        <?php if (!$registrationAvailable): ?>
            <div class="mt-2 text-danger"><?= t('admin.registration_missing') ?></div>
        <?php endif; ?>
    </div>
    <div class="mt-3 d-flex justify-content-end">
        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-floppy2-fill"></i> <?= t('admin.save_settings') ?></button>
    </div>
</form>
