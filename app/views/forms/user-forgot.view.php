<form action="/forgot" method="post">
    <?= csrf() ?>

    <div class="form-floating mb-3">
        <input class="form-control" type="email" id="email" name="email" placeholder="<?= t('common.email') ?>" required>
        <label for="email"><?= t('common.email') ?></label>
    </div>

    <div class="mt-3 d-flex justify-content-end">
        <a href="/home" class="btn btn-secondary"><i class="bi bi-x-circle"></i> <?= t('common.cancel') ?></a>&nbsp;&nbsp;
        <button type="submit" class="btn btn-primary"><i class="bi bi-envelope-arrow-up"></i> <?= t('common.send') ?></button>
    </div>

</form>
