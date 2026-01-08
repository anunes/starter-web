<form method="post" action="/register">

    <?= csrf(); ?>

    <div class="form-floating mb-3 mt-4">
        <input class="form-control" type="text" id="name" name="name" placeholder="<?= t('common.full_name') ?>" required>
        <label for="name"><?= t('common.full_name') ?></label>
    </div>
    <div class="form-floating mb-3">
        <input class="form-control" type="email" id="email" name="email" placeholder="<?= t('common.email') ?>" required>
        <label for="email"><?= t('common.email') ?></label>
    </div>
    <div class="form-floating mb-3">
        <label for="password"><?= t('common.password') ?>:</label>
        <input class="form-control" type="password" id="password" name="password" required>
    </div>

    <div class="form-floating mb-3">
        <input class="form-control" type="password" id="confirm_password" name="confirm_password" placeholder="<?= t('common.password_confirmation') ?>" required>
        <label for="confirm_password"><?= t('common.password_confirmation') ?></label>
    </div>

    <div class="row my-3 d-flex justify-content-end">
        <div class="col-md-6 mt-3">
            <a href="/login"><?= t('auth.already_have') ?></a>
        </div>
        <div class="col-md-6 mt-3 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2-fill"></i> <?= t('common.register') ?></button>&nbsp;&nbsp;
            <a href="/" class="btn btn-secondary"><i class="bi bi-x-circle"></i> <?= t('common.cancel') ?></a>
        </div>

    </div>
</form>
