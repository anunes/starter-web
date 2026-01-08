<div class="row d-flex justify-content-center">
    <div class="col-md-7">
        <div class="card shadow">
            <div class="card-header py-3">
                <h5><i class="bi bi-person-vcard large-icon text-primary"></i> <?= t('profile.title') ?></h5>
                <p class="card-text fs-6 pl-5"><?= t('profile.subtitle') ?></p>
            </div>


            <div class="card-body p-4">
                <ul class="nav nav-tabs" id="profileTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="true"><?= t('profile.user_info') ?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-tab-pane" type="button" role="tab" aria-controls="password-tab-pane" aria-selected="false"><?= t('profile.change_password') ?></button>
                    </li>
                </ul>
                <div class="tab-content border border-top-0 rounded-bottom-3" id="profileTabContent">
                    <div class="tab-pane fade show active" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                        <?= view_partial('forms/user-profile') ?>
                    </div>
                    <div class="tab-pane fade" id="password-tab-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                        <?= view_partial('forms/user-password-inline') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
