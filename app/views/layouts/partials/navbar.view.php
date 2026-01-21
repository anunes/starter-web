<nav class="navbar navbar-expand-lg glass fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/"><img class="brand-logo" src="<?= $brandLogoSrc ?>" alt="<?= t('common.logo_alt', ['name' => APP_NAME]) ?>"> <?= APP_NAME ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="<?= t('nav.toggle') ?>">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/"><i class="bi bi-house-fill large-icon"></i> <?= t('nav.home') ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/about"><i class="bi bi-card-text large-icon"></i> <?= t('nav.about') ?></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/contact"><i class="bi bi-person-rolodex large-icon"></i> <?= t('nav.contact') ?></a>
                </li>
                <?php if ($showGuestLinks): ?>
                    <?php if ($showRegistrationLink): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/register"><i class="bi bi-person-add large-icon"></i> <?= t('nav.register') ?></a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/login"><i class="bi bi-box-arrow-in-right large-icon"></i> <?= t('nav.login') ?></a>
                    </li>
                <?php endif; ?>
                <?php if ($showUserMenu): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="nav-user-chip border rounded bg-light-subtle">
                                <?php if ($hasAvatar): ?>
                                    <img class="nav-avatar" src="<?= $avatarSrc ?>" alt="<?= t('common.user_avatar_alt') ?>">
                                <?php else: ?>
                                    <i class="bi bi-person-circle nav-avatar-placeholder" role="img" aria-label="<?= t('common.user_avatar_alt') ?>"></i>
                                <?php endif; ?>
                                <?= $userName ?>
                            </span>

                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/profile"><i class="bi bi-person-lines-fill"></i> <?= t('nav.profile') ?></a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <?php if ($isAdmin): ?>
                                <li>
                                    <a class="dropdown-item" href="/admin"><i class="bi bi-gear-wide-connected"></i> <?= t('nav.admin') ?>
                                    </a>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-left"></i> <?= t('nav.logout') ?></a></li>
                        </ul>
                    </li>

                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
