<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true"><i class="bi bi-check-circle"></i> <?= t('admin.active_users') ?></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false"><i class="bi bi-x-circle"></i> <?= t('admin.inactive_users') ?></button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false"><i class="bi bi-gear"></i> <?= t('admin.settings') ?></button>
    </li>
</ul>

<div class="tab-content border border-top-0 rounded-bottom-3" id="myTabContent">
    <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
        <table class="table table-hover table-striped user-table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col"><?= t('admin.name') ?></th>
                    <th scope="col"><?= t('admin.email') ?></th>
                    <th scope="col"><?= t('admin.role') ?></th>
                    <th scope="col"><?= t('admin.edit') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activeUsers as $user): ?>
                    <tr>
                        <th scope="row"><?= $user->id ?></th>
                        <td><?= $user->name ?></td>
                        <td><?= $user->email ?></td>
                        <td><?= $user->role ?></td>
                        <td>
                            <a href="/admin/edit/<?= $user->id ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i></a>
                            <!--                                       <a href="/admin/delete/<?= $user->id ?>" class="btn btn-sm btn-danger"><i class="bi bi-trash-fill"></i></a> -->
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>
    <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
        <table class="table table-hover table-striped user-table w-100">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col"><?= t('admin.name') ?></th>
                    <th scope="col"><?= t('admin.email') ?></th>
                    <th scope="col"><?= t('admin.role') ?></th>
                    <th scope="col"><?= t('admin.edit') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inactiveUsers as $user): ?>
                    <tr>
                        <th scope="row"><?= $user->id ?></th>
                        <td><?= $user->name ?></td>
                        <td><?= $user->email ?></td>
                        <td><?= $user->role ?></td>
                        <td>
                            <a href="/admin/edit/<?= $user->id ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>
    <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
        <h3><?= t('admin.settings') ?></h3>
        <?= view_partial('forms/admin-settings') ?>
        <hr class="my-4">
        <?= view_partial('forms/admin-logo') ?>
    </div>

</div>
