<form action="/admin/update/<?= $user->id ?>" method="post">
    <?= csrf() ?>
    <div class="form-floating mb-3">
        <input type="text" class="form-control" id="name" name="name" value="<?= $user->name ?>">
        <label for="name" class="form-label"><?= t('admin.name') ?></label>
    </div>
    <div class="form-floating mb-3">
        <input type="email" class="form-control" id="email" name="email" value="<?= $user->email ?>">
        <label for="email" class="form-label"><?= t('admin.email') ?></label>
    </div>
    <div class=" mb-3">
        <label for="role" class="form-label"><?= t('admin.role') ?></label>
        <select class="form-select" id="role" name="role">
            <option value="admin" <?= $user->role === 'admin' ? 'selected' : '' ?>><?= t('admin.role_admin') ?></option>
            <option value="user" <?= $user->role === 'user' ? 'selected' : '' ?>><?= t('admin.role_user') ?></option>
        </select>
    </div>
    <?php if ($user->active == false): ?>
        <div class="mb-3 form-check">
            <input class="form-check-input" type="checkbox" value="0" id="active" name="active">
            <label class="form-check-label"><?= t('admin.reactivate_user') ?></label>
        </div>
    <?php endif; ?>


    <div class=" row d-flex justify-content-between">
        <div class="col mt-3">
            <button type="button" class="btn btn-danger delete-user-trigger" data-bs-toggle="modal" data-bs-target="#deleteUserModal" href="/admin/delete/<?= $user->id ?>">
                <i class="bi bi-trash-fill"></i> <?= t('admin.delete_user') ?>
            </button>
        </div>
        <div class="col mt-3 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2-fill"></i> <?= t('common.update') ?></button>&nbsp;&nbsp;
            <a href="/admin" class="btn btn-secondary"><i class="bi bi-x-circle"></i> <?= t('common.cancel') ?></a>
        </div>
    </div>
    <p><?= t('admin.deleted_note') ?></p>
</form>
