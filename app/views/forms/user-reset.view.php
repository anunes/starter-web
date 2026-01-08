<form action="/reset?token=<?= urlencode($token) ?>&id=<?= urlencode($id) ?>" method="post">
    <?= csrf() ?>
    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">

    <div class="form-floating mb-3">
        <input class="form-control" type="password" id="password" name="password" placeholder="<?= t('common.password') ?>" required>
        <label for="password"><?= t('common.password') ?></label>
    </div>

    <div class="form-floating mb-3">
        <input class="form-control" type="password" id="confirm_password" name="confirm_password" placeholder="<?= t('common.password_confirmation') ?>" required>
        <label for="confirm_password"><?= t('common.password_confirmation') ?></label>
    </div>

    <div class="mt-3 d-flex justify-content-end">
        <a href="/login" class="btn btn-secondary"><i class="bi bi-x-circle"></i> <?= t('common.cancel') ?></a>&nbsp;&nbsp;
        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2-fill"></i> <?= t('common.update') ?></button>
    </div>

</form>
