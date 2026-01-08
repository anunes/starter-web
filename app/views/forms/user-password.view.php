<form action="/password" method="post">
    <?= csrf() ?>

    <div class="form-floating mb-3">
        <input class="form-control" type="password" id="oldpassword" name="old_password" placeholder="<?= t('common.current_password') ?>" required>
        <label for="oldpassword"><?= t('common.current_password') ?></label>
    </div>

    <div class="form-floating mb-3">
        <input class="form-control" type="password" id="password" name="password" placeholder="<?= t('common.password') ?>" required>
        <label for="password"><?= t('common.password') ?></label>
    </div>

    <div class="form-floating mb-3">
        <input class="form-control" type="password" id="confirm_password" name="confirm_password" placeholder="<?= t('common.password_confirmation') ?>" required>
        <label for="confirm_password"><?= t('common.password_confirmation') ?></label>
    </div>

    <div class="mt-3 d-flex justify-content-end">
        <a href="/home" class="btn btn-secondary"><i class="bi bi-x-circle"></i> <?= t('common.cancel') ?></a>&nbsp;&nbsp;
        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2-fill"></i> <?= t('common.update') ?></button>
    </div>

</form>
