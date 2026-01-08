<form action="/profile" method="post" enctype="multipart/form-data">
    <?= csrf() ?>
    <div class="d-flex flex-column flex-md-row align-items-md-center gap-3 mb-4">
        <?php if ($hasAvatar): ?>
            <img class="profile-avatar" src="<?= $avatarSrc ?>" alt="<?= t('common.user_avatar_alt') ?>">
        <?php else: ?>
            <i class="bi bi-person-circle profile-avatar-placeholder" role="img" aria-label="<?= t('common.user_avatar_alt') ?>"></i>
        <?php endif; ?>
        <div class="w-100">
            <label class="form-label" for="avatar"><?= t('profile.avatar') ?></label>
            <input class="visually-hidden" type="file" id="avatar" name="avatar" accept="image/*">
            <label class="btn btn-outline-primary btn-sm" for="avatar"><?= $avatarButtonLabel ?></label>
            <div class="form-text"><?= t('profile.avatar_help') ?></div>
        </div>
    </div>
    <div class="form-floating mb-3 mt-4">
        <input class="form-control" type="text" id="name" name="name" placeholder="<?= t('common.full_name') ?>" value="<?= $user->name ?>" required>
        <label for="name"><?= t('common.full_name') ?></label>
    </div>
    <div class="form-floating mb-3">
        <input class="form-control" type="email" id="email" name="email" placeholder="<?= t('common.email') ?>" value="<?= $user->email ?>" required>
        <label for="email"><?= t('common.email') ?></label>
    </div>

    <div class="mt-3 d-flex justify-content-end">
        <a href="/home" class="btn btn-secondary"><i class="bi bi-x-circle"></i> <?= t('common.cancel') ?></a>&nbsp;&nbsp;
        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2-fill"></i> <?= t('common.update') ?></button>
    </div>

</form>
