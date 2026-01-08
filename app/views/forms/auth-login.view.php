<form method="post" action="/login">

    <?= csrf(); ?>

    <div class="form-floating mb-3 mt-4">
        <input class="form-control" type="email" id="email" name="email" placeholder="<?= t('common.email') ?>">
        <label for="email"><?= t('common.email') ?></label>
    </div>
    <div class="form-floating mb-3">
        <input class="form-control" type="password" id="password" name="password" placeholder="<?= t('common.password') ?>">
        <label for="password"><?= t('common.password') ?></label>
    </div>


    <div class="mt-4 d-flex justify-content-between">
        <?php if ($registrationEnabled): ?>
            <a href="/register"><?= t('auth.dont_have') ?></a>
        <?php else: ?>
            <span></span>
        <?php endif; ?>
        <a href="/forgot"><?= t('auth.forgot_link') ?></a>
    </div>
    <div class="mt-4 d-flex justify-content-end">
        <button type="submit" class="btn btn-primary px-5"><i class="bi bi-send"></i> <?= t('common.login') ?></button>
        <a href="/" class="btn btn-secondary ms-2"><i class="bi bi-x-circle"></i> <?= t('common.cancel') ?></a>
    </div>
</form>
